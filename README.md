# Savor 🍽️

**Recipe → Grocery List PWA** for Robinsons Retail (O!Save, Robinsons Supermarket, Easymart, Shopwise).

Browse Filipino recipes, add ingredients to your grocery list, and see real-time estimated costs based on your selected branch's pricing.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Framework** | Laravel 12 |
| **Frontend** | Blade + Alpine.js (no Vue/React/Livewire) |
| **Styling** | Tailwind CSS 3.x |
| **Database** | SQLite (development), PostgreSQL-ready |
| **PWA** | Service Worker + Web Manifest |
| **Build** | Vite |

## Features

- **50+ Filipino Recipes** — Adobo, Sinigang, Kare-Kare, and more
- **Branch Pricing** — Real-time cost estimates for O!Save, Robinsons, Easymart, Shopwise
- **Smart Unit Conversion** — Converts recipe units (cloves, tbsp, pcs) to purchase units (kg, bottle, pack) with context-aware per-item weights
- **Session-Free Branch** — Branch selection persists via Alpine persist
- **Session Cart** — Full grocery list with add/remove/clear, aggregated quantities
- **PWA** — Installable, works offline with fallback page
- **Admin Panel** — CRUD for recipes, ingredients, branches, tags
- **CSV Price Import** — Upload → Validate → Preview → Confirm flow
- **Print-Ready** — Print-friendly grocery list layout

## Quick Start

```bash
# 1. Clone & install dependencies
git clone git@github.com:moreishi/savor-app.git
cd savor-app
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Database (SQLite)
touch database/database.sqlite
php artisan migrate --seed --seeder=SavorDatabaseSeeder

# 4. Build frontend
npm run build

# 5. Serve
php artisan serve --port=8200
```

The app will be at **http://localhost:8200**.

### Admin Access

| Credential | Value |
|------------|-------|
| **URL** | `/login` |
| **Email** | `admin@savor.ph` |
| **Password** | `password` |

## Architecture

### Models & Relationships

```
Category ──1:N──> Recipe ──M:N──> Tag (via recipe_tag)
                     │
                     └────M:N──> Ingredient (via recipe_ingredient)
                                     │
                                     └────1:N──> BranchPrice
                                                     │
                                                     └────N:1──> Branch
```

- **recipe_ingredient pivot**: `quantity`, `unit`, `is_optional`, `notes`, `sort_order`
- **BranchPrice**: Links ingredient → branch with `price`, `purchase_quantity`, `purchase_unit`, `variant_label`

### Key Services

- **`UnitConverter`** (`app/Helpers/UnitConverter.php`) — 465-line core conversion engine with:
  - BFS graph traversal for multi-step conversions
  - Context-aware piece-to-weight mapping (e.g., 1 pcs Bay Leaf = 0.5g, 1 pcs Egg = 50g)
  - Density-based volume↔weight (e.g., 1 L White Rice = 850g)
  - Per-ingredient pack sizes (e.g., Spaghetti Pasta = 500g/pack)
  - Volume → pack (via grams intermediate)
  - 15 unit types: kg, g, mg, L, mL, tbsp, tsp, cup, bottle, can, clove, pack, bundle, dozen, pcs
- **`GroceryListService`** (`app/Services/GroceryListService.php`) — Session-based cart with aggregation, dedup, total calculation
- **`Recipe::getGroceryList($branchId)`** — Resolves ingredient prices per branch with variant fallback

### Routes

| Prefix | Description |
|--------|-------------|
| `/` | Public recipe listing & search |
| `/recipes/{slug}` | Recipe detail with serving adjuster |
| `/grocery-list/*` | Cart management (view, add, remove, clear, set branch) |
| `/admin/*` | Admin CRUD (auth required) |
| `/prices/import` | CSV price import wizard |
| `/offline` | PWA offline fallback |

## Sprint Roadmap

| Sprint | Focus | Status |
|--------|-------|--------|
| **Sprint 0** | Laravel scaffold, 50 recipes, 9 categories, 70 ingredients, CSV import queue | ✅ Done |
| **Sprint 1** | Public frontend (home, recipe detail, serving adjuster) | ✅ Done |
| **Sprint 2** | PWA (manifest, service worker, offline page) | ✅ Done |
| **Sprint 3** | Branch pricing (624 records, session cart, branch selector) | ✅ Done |
| **Sprint 4** | Admin CRUD (recipes, ingredients, branches, tags) | ✅ Done |
| **Sprint 5** | Unit conversion polish, nav price badge, error handling, docs | ✅ Done |
| **Sprint 6** | Promo engine (planned) | 🔜 |

## CSV Price Import Flow

1. **Download template** at `/prices/template`
2. **Upload CSV** — system validates headers & formats
3. **Queue validation** — background job checks data
4. **Preview** — see valid/invalid rows before committing
5. **Confirm** — queued batch import (idempotent)

## PWA Features

- **Web Manifest** — Installable on mobile/desktop home screens
- **Service Worker** — Caches assets on first visit, serves offline fallback page
- **Offline Page** — Graceful message when network unavailable

## Unit Conversion Reference

| Recipe Unit | Converts To | Method |
|-------------|-------------|--------|
| `cloves` | g, kg | 5g per clove |
| `tbsp` | mL, bottle | 15mL per tbsp |
| `tsp` | mL, g (via density), pack (via density) | 5mL per tsp |
| `pcs` | g, kg | Context-aware per-item weight |
| `pack` | g, kg | Context-aware pack size (default 50g) |
| `bundle` | g, kg | 250g per bundle |
| `dozen` | pcs | 12 pcs per dozen |
| `bottle` | mL, L | 500mL per bottle |
| `can` | mL, L | 370mL per can |

## License

MIT
