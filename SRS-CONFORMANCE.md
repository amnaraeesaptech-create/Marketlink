# MarketLink — SRS Conformance Report (final)

**SRS:** *MarketLink End-to-End Web Solutions — Software Requirements Specification* (v1.0, theme "eGreen Basket")
**App:** `/home/user/marketlink-final` — Laravel 13, PHP 8.4, SQLite (real database)
**Report date:** 25 September 2026
**Verdict:** ✅ **SRS ke saare mandatory functional + non-functional requirements implement hain** — 38/38 closure tests, 70/70 real-data tests, 15/15 admin-dashboard tests, 126/126 UI renders (0 layout problems), har authenticated page HTTP 200.

SRS khud kehta hai: *"The functional and non-functional requirements listed here are the bare minimum… a must to implement. You are allowed to add extra creativity."* — Neeche har bare-minimum item ke sath uska implementation aur proof diya gaya hai.

---

## 1. Customer / Client Requirements

| # | SRS requirement | Implementation | Evidence |
|---|---|---|---|
| C1 | Register/login: name, contact no., email, address | `AuthController::register` / `login` — pure DB auth, role-based landing | `verify-real-data.sh` → client signup row + login 302 |
| C2 | Browse markets & farmers by location / day | `/markets` (city + day filter), `/farmers` (search, location, market, category, "Open Today") | `ui-audit` farmers/markets renders; filter JS reads live DB rows |
| C3 | Embedded map with markers + directions to pickup point | OpenStreetMap iframe (`openstreetmap.org/export/embed.html`) + "Directions to pickup" (OSRM) on market page, farmer profile and market admin form; pin from real `latitude`/`longitude` columns | rows `markets.latitude/longitude`, `farmers.latitude/longitude`; render check `market coordinates editable` |
| C4 | Product browse/filter (category, price, market, day) + sort/search | `/products` listing widgets over `window.ML` (live `/data-feed.js`) — category, market, day, price range, search, sort | categories now come from admin master data (`Category::names()`) |
| C5 | Product details: price, unit, quantity, farmer | `/product-details?id=` — price, unit, stock, sold-by stall, pickup table | smoke `/product-details?id=1` 200 |
| C6 | Cart + pre-order limited to available stock | localStorage cart + server-side re-validation in `PreorderController::store()` (stock re-checked, per-farmer cut-off, stock decrement) | `verify-real-data` → "stock decremented by 2" |
| C7 | Pickup date + time slot selection | Preorder form + `Order::pickupAt()`; slots come from farmer `pickup_windows` | orders rows carry `pickup_date`/`pickup_slot` |
| C8 | Order status flow: placed → accepted → ready-for-pickup → completed | `Order::STATUSES` = **Placed / Accepted / Ready for Pickup / Completed** (+ Cancelled); farmer actions accept / decline / mark-ready; customer timeline | label aligned to SRS wording in orders, order-details, admin filter |
| C9 | Cancel / modify before cut-off (payment at pickup only) | `Order::canBeCancelledByCustomer()` + `canBeModifiedByCustomer()` = pickup − `cutoff_hours` (default 12h). `/customer-orders/{id}/update` edits quantities + pickup date/slot + notes, re-balances stock; empty cart auto-cancels and returns stock. No payment gateway anywhere | `verify-srs-closure.sh` steps 4 & 8 (in-window edit works, past-cut-off edit blocked) |
| C10 | Order history + **reorder** | `/customer-orders` with status tabs/search + per-order **Reorder**; `POST /customer-orders/{id}/reorder` re-checks live stock, drops unavailable items, seeds the browser cart (`window.ML_REORDER` → `cart.js` merge) and reports what was skipped | `verify-srs-closure.sh` step 3 (seed present on `/cart`) |
| C11 | Favourite farmers & products + restock alerts | 3-way `POST /favorites/toggle` (product / farmer / market) + tabbed `/customer-favorites` (Products / Farmers / Markets). Restock alert fans out to everyone who saved the product when stock/availability flips positive | closure step 1, 2, 7 ("restock alert sent to the client who saved it") |
| C12 | Reviews/ratings for farmers **and** individual products after a completed order | `reviews.product_id` (one review per product per order) + stall review; product ratings shown on `/product-details` and on the order page; average stall score uses stall reviews only | closure step 5 + `/product-details?id=1` → "Ratings for this product (1)" + "Stall reviews (1)" |
| C13 | In-app or email notifications (order confirmation, ready-for-pickup) | In-app alerts: `app_notifications` + navbar bell with unread badge + `/notifications` page. Triggers: order placed, status change, cancel, review, farmer reply, restock, announcements | 11 demo notifications, closure steps 6 & 7 |
| C14 | (Optional) AI chatbot | `/ai-assistant` page + `chatbot.js`, answers from the live DB feed | smoke `/ai-assistant` 200 |

## 2. Farmer Requirements

| # | SRS requirement | Implementation | Evidence |
|---|---|---|---|
| F1 | Register: stall name, contact person, phone, email, address | `/farmer-register` → `users` + `farmers` rows; new stalls start **unapproved** and are emailed-free notified in-app | `verify-real-data` → "new farmer starts un-approved (SRS admin approval)" |
| F2 | Profile: markets, operating days, pickup windows, address + lat/long | `/farmer-profile/edit` — markets, day chips, pickup windows repeater, address, latitude/longitude, categories, description, avatar | smoke `/farmer-profile/edit` 200 |
| F3 | Product CRUD: name, category, price, unit, quantity, description, image | `/farmer-products`, `/farmer-add-product`, `/farmer-products/{id}` (PUT), delete, availability toggle; category list from admin master data; new products need an approved stall | `verify-real-data` → product CREATE/UPDATE/TOGGLE persisted |
| F4 | Recurring weekly stock template | `/farmer-stock-template` — one pass over every product: quantity + "on sale" switch, live counters (in stock / sold out / last change), bulk select; saving pushes numbers to the storefront | closure step 7 |
| F5 | Sold-out marking | `is_available` switch in the template and product pages; sold-out items leave the storefront and favourite lists show "Unavailable" | closure step 7 (0 → hidden) |
| F6 | Manage pre-orders: accept / decline / mark ready | `/farmer-orders` — status tabs + Accept / Decline (returns stock) / Mark as Ready / Complete, each sending an alert to the client | smoke + Notifier hooks |
| F7 | Order cut-off times + pickup slots | `farmers.cutoff_hours` (validated 1–168) edited in the profile; customer-side cancel/modify window derives from it; pickup windows shown on the stall | closure step 8 (past cut-off blocked) |
| F8 | Insights: total orders, pending orders, revenue summary | `/farmer-dashboard` — Total Orders (+ this month, unique customers), Pending, Ready for Pickup, Revenue (completed), 7-day value chart, top products by units/revenue, low-stock and out-of-stock lists | render check + real rows |
| F9 | Respond to reviews | `/farmer-reviews` — reply / edit-reply form per review, "replied" and "awaiting reply" counters, product-vs-stall badge; reply is stored (`farmer_reply`, `replied_at`) and alerts the client | closure step 6 |

## 3. Administrator Requirements

| # | SRS requirement | Implementation | Evidence |
|---|---|---|---|
| A1 | Dedicated admin login + dashboard with platform totals (farmers, customers, markets, orders) | `/admin-login` (own door, pure DB auth) + `/admin-dashboard` totals from live aggregates | `verify-admin-views.sh` 15/15 |
| A2 | Approve / suspend farmers | `/admin-farmers` — Active/Suspended toggle **and** an approval column with Approve / Un-approve; unapproved stalls' products disappear from listings, feed and market pages (`Product::scopeFromApprovedStall`) | `verify-real-data` → feed check before/after approval |
| A3 | Activate / deactivate customers | `/admin-customers` — activate/deactivate + delete; deactivated users are logged out by `EnsureRole` | smoke + RBAC checks |
| A4 | Manage markets: name, address, days, timings, map coordinates | `/admin-markets` CRUD — name, city, address, day checkboxes, open/close time, Google Maps URL **and** latitude/longitude that drive the OSM embed | closure step 9 "market coordinates editable" |
| A5 | Content moderation (remove listings / reviews) | `/admin-products` Publish/Unpublish per listing; `/admin-reviews` Remove review (with farmer-reply preview and stall link) | closure step 9 |
| A6 | Reports: orders, revenue across markets, most active farmers | `/admin-reports` — order/revenue aggregates per market, revenue + orders charts, top farmers, category mix | render check + real rows |
| A7 | System config: product categories master data + platform announcements | `/admin-categories` CRUD (rename cascades to products, in-use delete guard, active/sort) drives every product form and filter; `/admin-announcements` CRUD with audience (all / clients / farmers), live banner + optional in-app alert | closure step 9 (both pages 200, master data listed) |

## 4. Other / Cross-cutting Requirements

| # | SRS requirement | Implementation | Evidence |
|---|---|---|---|
| X1 | Role-based access control | `EnsureRole` middleware; separate login doors for farmer and admin; wrong-role access redirects with an error | `verify-real-data` → 7 RBAC checks pass |
| X2 | Search / sort / filter | Admin tables (client-side filter, column select, CSV export), storefront listing widgets, order + review filters | `admin.js` + `listings.js` |
| X3 | Responsive design | Phone 390px / tablet 768px / desktop 1440px audit, off-canvas admin drawer, no horizontal overflow anywhere | `ui-audit.js` → **126 renders, 0 problems** |
| X4 | Notifications | See C13 — bell in the navbar for all three roles, `/notifications` page with mark-read / mark-all-read | smoke `/notifications` 200 on all roles |
| X5 | About Us / Contact Us | `/about`, `/contact` (live market + support data, working contact form) | smoke + audit renders |
| X6 | **No** payment gateway, **no** delivery/courier, **no** farmer identity/certification verification | Nothing in the code touches a payment provider, courier or document verification; money is "pay at pickup" and shown as such on cart, order and dashboard | grep for payment providers = 0 hits; cart banner "No online payment required" |

## 5. Verification Runs (25 Sep 2026, latest)

| Suite | Command | Result |
|---|---|---|
| Real-data / CRUD / RBAC | `bash scripts/verify-real-data.sh` | **70 passed, 0 failed** |
| Admin dashboard content traced to DB | `bash scripts/verify-admin-views.sh` | **15 passed, 0 failed** |
| SRS closure (new features end-to-end) | `bash scripts/verify-srs-closure.sh` | **38 passed, 0 failed** |
| Authenticated page sweep | `bash scripts/smoke-authenticated-pages.sh` | **every page HTTP 200** |
| Responsive/UI audit | `node /home/user/tools/ui-audit.js` | **126 renders, 0 problems** |

Database after the runs: 3 users (admin / farmer / client), 1 stall, 10 products, 3 orders, 3 reviews, 2 product favourites + 1 farmer + 1 market favourite, 6 categories, 3 announcements, 11 notifications — all created through the real app.

## 6. Notes / deliberately not built

- **Email notifications** — SRS me "in-app **or** email" likha hai; in-app alerts implement kiye gaye hain (koi SMTP setup ke bina demo me reliably chalta hai).
- **AI chatbot** — SRS me optional tha; ek rule-based assistant `/ai-assistant` par live-data ke sath maujood hai.
- **Submission paperwork** (docs/, SQL script, ReadMe, demo video, hosting) — SRS ka deliverable section hai, chalne wale app ka requirement nahi; bolo to wo bhi bana dete hain.
