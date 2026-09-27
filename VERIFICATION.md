# MarketLink — Real-Data Verification Report

App: `marketlink-final/` (Laravel 13.33 · PHP 8.4.24 · SQLite) — extracted from `marketlink-final (2).zip`.
Everything else from the original repo (clone folder, both ZIPs) has been removed from the workspace.

Run: `cd marketlink-final && php artisan serve --host=0.0.0.0 --port=8000`

## Demo accounts (real rows in `users`, real password hashes)

| Role | Login page | Email | Password | Lands on |
|---|---|---|---|---|
| Farmer | `/farmer-login` | farmer@marketlink.com | Farmer@123 | `/farmer-dashboard` |
| Client (customer) | `/login` | customer@marketlink.com | Customer@123 | `/customer-dashboard` |
| Admin | `/admin-login` | admin@marketlink.com | Admin@123 | `/admin-dashboard` |

## Verification scripts (in `scripts/`)

| Script | What it does |
|---|---|
| `verify-real-data.sh` | End-to-end test: registers a farmer + client, does product CRUD, pre-order, cancel, favorite toggle, review create/delete, farmer order status transitions, admin market CRUD, admin suspend/delete — and checks the SQLite row after **every** write. Also tests role isolation. Self-cleaning (deletes its own test data). |
| `smoke-authenticated-pages.sh` | Logs in as each account through the real forms and requests every public + protected page, printing HTTP status + title. |

## Last run

```
verify-real-data.sh        → 66 passed, 0 failed
smoke-authenticated-pages.sh → all pages 200 (42 pages)
```

## What "only real data" means here

* **Auth** — `AuthController` checks email + password hash + `is_active`, then role-gates the redirect. Wrong-role logins fail; a logged-in farmer cannot open `/customer-orders` or `/admin-*` and vice versa. Guests are bounced to the login page for the area they tried to reach (`/farmer-*` → `/farmer-login`, `/admin-*` → `/admin-login`, else `/login`).
* **Navbar** — no generic “Login / Sign Up”; only “Client Login” and “Farmer Login”.
* **Writes** — every create/update/delete hits the database and takes effect on the public pages immediately: farmer product CRUD + availability toggle, pre-order (stock decremented, restored on cancel), favorite toggle, review (only after a completed order, one per order), farmer order status flow (pending → confirmed → ready → completed/cancelled), stall profile edit incl. market links (`farmer_market` pivot), admin farmer/client suspend + delete (cascades), admin market CRUD.
* **Reads** — dashboards, storefront pages, market/farmer/product pages, `window.ML` feed (`/data-feed.js`), chatbot and AI-assistant answers all read from the database. No mock rows, no demo fallbacks left in the views, and no external stock images (`picsum` / `unsplash` / `randomuser` / `via.placeholder` → 0 hits; images use `App\Support\Placeholder` SVG data-URIs when a real upload is missing).
* **Markets** — real `markets` table + `farmer_market` pivot. Farmer signup/edit links to real market rows, and an admin rename cascades to `orders.market`.

## Database after seeding (fresh state)

`users=3 · markets=2 · farmer_market=2 · products=10 · orders=3 · order_items=4 · reviews=1 · favorites=2`
Orders: #1 completed / PKR 500, #2 ready / PKR 350, #3 pending / PKR 240.
