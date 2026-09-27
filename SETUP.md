# MarketLink — Installation & Credentials

## Option 0 — Instant run (pre-configured zip) ⭐
The zip already contains a ready `.env` + pre-seeded SQLite database.
Just extract and run — **no setup commands needed**:
```bash
cd MarketLink
php artisan serve
```
Open http://127.0.0.1:8000 and log in with the demo accounts below.
(If file uploads should show images, also run `php artisan storage:link` once.)

## If you see an error (500 / blank page)
Run the self-check — it tells you exactly what is missing:
```bash
php diagnose.php
```
Fix the `[FAIL]` lines it shows (usually: extract into an **empty**
folder so no old `.env` stays behind, or enable `pdo_sqlite` in php.ini),
then run `php artisan serve` again.

## Requirements
- PHP 8.2+ with extensions: `pdo_mysql` (or `pdo_sqlite`), `mbstring`, `xml`, `curl`, `zip`
- MySQL 8 / MariaDB (XAMPP works) — or SQLite for a zero-config setup
- The `vendor/` folder is already included in the zip, so Composer install is optional
  (run `composer install` only if you change PHP versions and get dependency errors).

## Option A — XAMPP / MySQL (recommended for evaluation)
1. Start Apache + MySQL in XAMPP. Create database `marketlink` (utf8mb4).
2. Copy the project to `C:\xampp\htdocs\MarketLink` and open a terminal there.
3. Install dependencies:
   ```bash
   composer install
   copy .env.example .env        # Windows (Linux/Mac: cp .env.example .env)
   php artisan key:generate
   ```
4. Edit `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=marketlink
   DB_USERNAME=root
   DB_PASSWORD=
   ```
5. Migrate + seed demo accounts + storage link:
   ```bash
   php artisan migrate --seed
   php artisan storage:link
   ```
6. Run:
   ```bash
   php artisan serve
   ```
   Open http://127.0.0.1:8000

## Option B — SQLite (quickest, no MySQL needed)
```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
# in .env set: DB_CONNECTION=sqlite  and  DB_DATABASE=/absolute/path/database/database.sqlite
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

## Demo credentials (seeded)
| Role     | Login page       | Email                     | Password     |
|----------|------------------|---------------------------|--------------|
| Customer | `/login`         | customer@marketlink.com   | Customer@123 |
| Farmer   | `/farmer-login`  | farmer@marketlink.com     | Farmer@123   |
| Admin    | `/admin-login`   | admin@marketlink.com      | Admin@123    |

## What's implemented (per SRS)
- **Customer**: register (name, phone, email, address), login, dashboard, profile view/edit, password change.
- **Farmer**: register (stall name, contact person, phone, email, address, markets, days, categories),
  login, dashboard, public stall profile (live data + map pin), enhance-profile form
  (markets, operating days, pickup windows, lat/lng, description, photo), password change.
- **Admin**: secure login, dashboard with live farmer/customer counts, live farmer & customer lists.
- **Role-based access**: every dashboard/profile route is guarded by `auth` + `role:*` middleware;
  wrong-role access bounces back to the user's own dashboard with an error message.
- Validation on every form (server-side + inline `@error` messages), hashed passwords,
  CSRF protection, “remember me”, logout with session invalidation.

## Notes / assumptions
- Pre-orders, products, markets and reviews modules are UI-complete (from the previous
  phase) and will be wired to the database in the next phase.
- No payment gateway (per SRS — payment is in person at pickup).
- Avatars upload to `storage/app/public/avatars` (needs `php artisan storage:link`).
