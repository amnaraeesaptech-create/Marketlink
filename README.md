# MarketLink — End-to-End Web Solution
**Theme:** eGreen Basket | **Category:** End-to-End Web Solutions

MarketLink is a full-stack web application that connects local farmers-market sellers with customers. Farmers publish their weekly stock, manage pre-orders, and track revenue. Customers discover nearby markets, browse products, place pre-orders for pickup, and leave reviews — all without any online payment.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.4, Laravel 13 (MVC) |
| Frontend | Blade templates, Bootstrap 5, vanilla JavaScript |
| Database | SQLite (pre-seeded, zero-config) |
| Maps | OpenStreetMap + OSRM directions (no API key required) |
| Auth | Laravel session auth, role-based middleware |
| Notifications | In-app alerts (app_notifications table + navbar bell) |

---

## Quick Start (Instant Run)

The project ships with a pre-configured `.env` and a pre-seeded SQLite database. No setup commands needed:

```bash
cd marketlink-final
php artisan serve --host=127.0.0.1 --port=8000
```

Open **http://127.0.0.1:8000** and log in with the credentials below.

> **Requires PHP 8.4+**. Install via [php.new](https://php.new/install/windows/8.4) if needed.
> Run `php artisan storage:link` once to enable avatar/image display.

---

## User Credentials

| Role | Login URL | Email | Password |
|---|---|---|---|
| Customer | `/login` | customer@marketlink.com | Customer@123 |
| Farmer | `/farmer-login` | farmer@marketlink.com | Farmer@123 |
| Admin | `/admin-login` | admin@marketlink.com | Admin@123 |

---

## Features Implemented (per SRS)

### Customer
- Register / login / profile edit / password change
- Browse markets by city + operating day (with OSM map per market)
- Browse farmers — search, filter by location / market / category / "Open Today"
- Browse & filter products by category, price, market, day
- View product details including stall info and live reviews
- Cart + pre-order — stock re-validated server-side at submit
- Pickup date + time slot selection (farmer's actual windows shown dynamically)
- Order status tracking: Placed → Accepted → Ready for Pickup → Completed
- Modify or cancel orders before farmer's cutoff — live countdown timer shown
- Order history + one-click reorder (live stock re-checked)
- Favourite products, farmers, and markets (with restock alerts)
- In-app notifications — navbar bell + `/notifications` page
- Rate and review farmer stall + individual products after completed pickup
- OSM map + directions link on every order's pickup details
- AI assistant chatbot (`/ai-assistant`) with live DB data

### Farmer
- Register stall (starts unapproved until admin approves)
- Enhance profile: markets, operating days, pickup windows, lat/lng map pin
- Product CRUD: name, category, price, unit, quantity, description, image
- Availability toggle per product + bulk weekly stock template
- Manage incoming pre-orders: accept / decline / mark ready / complete
- Set order cut-off hours (validated 1–168h)
- Dashboard: total orders, pending, revenue, 7-day chart, top products, low/out-of-stock
- Respond to customer reviews (reply stored + customer notified)

### Admin
- Dedicated login + dashboard with live platform totals
- Approve / suspend farmer registrations
- Activate / deactivate customer accounts
- Manage markets: name, address, days, timings, OSM coordinates
- Content moderation: unpublish listings, remove reviews
- Reports: monthly revenue chart, category mix, top farmers by revenue
- Product category master data (rename cascades to all products)
- Platform announcements with per-audience in-app fan-out

---

## Assumptions

1. **In-app notifications** are used instead of email (SRS says "in-app or email"). No SMTP setup required — all alerts are stored in `app_notifications` and shown in the navbar bell.
2. **SQLite** is the database for the submitted zip. MySQL/MariaDB is fully supported — see `SETUP.md` for instructions.
3. **Payment is in person at pickup.** No payment gateway is implemented, per SRS constraints.
4. **Farmer identity/certification verification** is not implemented, per SRS constraints.
5. **Delivery/courier logistics** are out of scope. The app supports pickup at the market only.
6. **OpenStreetMap** is used for all map embeds and direction links — no Google Maps API key is required.
7. The AI assistant (`/ai-assistant`) is a rule-based chatbot using live DB data, not a third-party AI service.
8. **Vendor folder is included** in the zip for zero-dependency setup. Run `composer install` only if changing PHP versions.
9. Admin credentials are pre-seeded. In production, the admin password should be changed immediately after deployment.
10. Product categories are managed by the admin. New registrations cannot add categories; they select from the admin-defined list.

---

## Project Structure

```
marketlink-final/
├── app/
│   ├── Http/Controllers/   # 14 controllers (Admin, Auth, Customer, Farmer, ...)
│   ├── Models/             # 12 Eloquent models
│   ├── Support/            # Catalog.php, Notifier.php, Placeholder.php
│   └── Http/Middleware/    # EnsureRole.php (RBAC)
├── database/
│   ├── migrations/         # 15 migrations
│   └── database.sqlite     # Pre-seeded SQLite database
├── docs/
│   ├── database_export.sql # Full schema + data SQL dump
│   └── AI-ACKNOWLEDGMENT.md
├── resources/views/        # 49 Blade templates
│   ├── admin/              # 11 admin views
│   ├── farmer/             # 11 farmer views
│   └── customer/           # 5 customer views
├── routes/web.php          # 65 named routes
├── public/assets/js/       # cart.js, listings.js, admin.js, chatbot.js
├── SETUP.md                # Full installation instructions
└── SRS-CONFORMANCE.md      # Requirement-by-requirement compliance report
```

---

## Database Design

See `docs/database_export.sql` for the full schema. Key tables:

| Table | Purpose |
|---|---|
| `users` | All roles (role column: customer / farmer / admin) |
| `farmers` | Farmer profile extension (stall info, days, lat/lng, cutoff) |
| `customers` | Customer profile extension (city) |
| `products` | Product catalogue (farmer_id → users) |
| `orders` | Pre-orders (customer_id + farmer_id → users) |
| `order_items` | Line items per order |
| `reviews` | Stall + product reviews (product_id nullable) |
| `favorites` | Product / farmer / market favorites |
| `markets` | Market locations with OSM lat/lng |
| `farmer_market` | Pivot: which farmers sell at which markets |
| `app_notifications` | In-app alerts |
| `categories` | Admin-managed product categories |
| `announcements` | Platform-wide announcements |

---

## Installation (Full Setup)

See **SETUP.md** for detailed instructions including MySQL/XAMPP setup and all demo credentials.

---

## AI Tools Used

See **docs/AI-ACKNOWLEDGMENT.md** for a full list of AI tools used during development and the extent of their use.
