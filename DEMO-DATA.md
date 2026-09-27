# Demo Data — MarketLink

## How to load it

```bash
php artisan migrate:fresh --seed
```

This wipes and rebuilds the DB, then runs `DatabaseSeeder` → `DemoSeeder`. Safe to
re-run any time. If you only want to re-seed without touching migrations:

```bash
php artisan db:seed
```

Orders/reviews/favorites are only created once (the seeder skips them if any
orders already exist), so re-running `db:seed` alone won't duplicate them —
use `migrate:fresh --seed` if you want a totally clean slate.

## Login credentials

Every account uses the same password: **`Test@123`**

| Role | Emails |
|---|---|
| Admin | `admin@gmail.com` |
| Farmer | `farmer1@gmail.com` … `farmer8@gmail.com` |
| Customer | `user1@gmail.com` … `user10@gmail.com` |

**Farmer stalls:** farmer1 Green Valley Farms (Vegetables), farmer2 Sunny
Orchard (Fruits), farmer3 Pure Dairy Co. (Dairy), farmer4 Homestead Bakery
(Baked Goods), farmer5 Khan Poultry & Meat (Meat), farmer6 Golden Hive Honey
(Other/Honey), farmer7 Margalla Greens (Vegetables), farmer8 Orchard Fresh
(Fruits).

## What gets created

- 1 admin, 8 farmers, 10 customers
- 6 markets (Islamabad + Rawalpindi), each with real address/hours/lat-long
- 58 products across all 6 categories (one item per stall seeded out-of-stock
  on purpose, so the "sold out" UI has something to show)
- 30 orders spread across every status (pending, confirmed, ready, completed,
  cancelled) with realistic pickup dates matched to each farmer's actual
  pickup-window days
- 8 reviews on completed orders
- 20 favorites

## Adding real photos (optional)

**None of this is required** — every image field is optional, and the app
renders a generated colour-tile placeholder from the record's name if no
file is found. Drop photos in only if/when you want the real thing.

**Step 1 — link storage (one-time):**
```bash
php artisan storage:link
```
This creates `public/storage` → `storage/app/public`, which is where all
three folders below live.

**Step 2 — create these three folders** (if they don't already exist):
```
storage/app/public/avatars/
storage/app/public/markets/
storage/app/public/products/
```

**Step 3 — rename your photos to match exactly** (case-sensitive, `.jpg`)
and drop them in the matching folder. Nothing else needs to change — the
seeder already points every record at these exact paths.

### Farmer avatars → `storage/app/public/avatars/`
```
farmer1.jpg   Green Valley Farms
farmer2.jpg   Sunny Orchard
farmer3.jpg   Pure Dairy Co.
farmer4.jpg   Homestead Bakery
farmer5.jpg   Khan Poultry & Meat
farmer6.jpg   Golden Hive Honey
farmer7.jpg   Margalla Greens
farmer8.jpg   Orchard Fresh
```

### Market cover photos → `storage/app/public/markets/`
```
downtown-farmers-market.jpg
westside-weekend-market.jpg
blue-area-green-bazaar.jpg
saidpur-village-market.jpg
rawalpindi-sunday-bazaar.jpg
bahria-town-farmers-hub.jpg
```

### Product photos → `storage/app/public/products/` (42 files, shared across
stalls that sell the same item — e.g. both `farmer1` and `farmer7` sell
tomatoes and both use `tomatoes.jpg`)
```
tomatoes.jpg            Fresh Red Tomatoes
onions.jpg               Organic Onions
potatoes.jpg             Farm Potatoes
carrots.jpg               Fresh Carrots
spinach.jpg               Fresh Spinach
green-chillies.jpg        Green Chillies
cucumbers.jpg             Cucumbers
bell-peppers.jpg          Bell Peppers

apples.jpg                Sweet Apples
bananas.jpg               Ripe Bananas
mangoes.jpg               Seasonal Mangoes
oranges.jpg               Juicy Oranges
grapes.jpg                Fresh Grapes
pomegranates.jpg          Pomegranates
guavas.jpg                Guavas
watermelon.jpg            Watermelon

milk.jpg                  Farm Fresh Milk
yogurt.jpg                Homemade Yogurt
butter.jpg                White Butter
ghee.jpg                  Desi Ghee
paneer.jpg                Fresh Paneer
cream.jpg                 Fresh Cream
eggs.jpg                  Free Range Eggs

wheat-bread.jpg           Whole Wheat Bread
sourdough-loaf.jpg        Sourdough Loaf
croissants.jpg            Butter Croissants
banana-bread.jpg          Banana Bread
chocolate-cookies.jpg     Chocolate Chip Cookies
cinnamon-rolls.jpg        Cinnamon Rolls
multigrain-buns.jpg       Multigrain Buns

chicken-whole.jpg         Whole Chicken
chicken-breast.jpg        Chicken Breast
mutton-boneless.jpg       Mutton Boneless
beef-mince.jpg            Beef Mince
chicken-drumsticks.jpg    Chicken Drumsticks
mutton-chops.jpg          Mutton Chops

raw-honey.jpg             Raw Honey
organic-honey.jpg         Organic Multiflora Honey
honeycomb.jpg             Honeycomb
mustard-oil.jpg           Cold-Pressed Mustard Oil
olive-oil.jpg             Extra Virgin Olive Oil
dried-dates.jpg           Dried Dates
```

That's it — no database edits, no controller changes. The `image`/`avatar`
columns already point at these exact relative paths; dropping a correctly
named file into the folder is enough for it to appear on the site immediately.
