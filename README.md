# StructBrew

A high-performance, multi-store e-commerce platform built on a custom PHP framework. Designed for international scale with 45+ domains, 163 store views, geo-routing across 99 countries, and full i18n support.

Built for **Scooter Dynamics** — a global scooter parts marketplace.

---

## Table of Contents

- [Features](#features)
- [Architecture](#architecture)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [CLI Tool](#cli-tool)
- [Routing](#routing)
- [Multi-Store System](#multi-store-system)
- [Geo-Routing](#geo-routing)
- [Internationalization](#internationalization)
- [SEO](#seo)
- [Admin Panel](#admin-panel)
- [Storefront API](#storefront-api)
- [Security](#security)
- [Directory Structure](#directory-structure)
- [Nginx Configuration](#nginx-configuration)

---

## Features

### Storefront
- Product catalog with categories, brands, and attributes
- Vehicle/fitment compatibility system (year/make/model matching)
- Shopping cart with coupon and price rule support
- Multi-currency pricing with tax zone calculations
- Customer accounts with order history, address book, and vehicle garage
- Loyalty wallet with points and store credits
- Wishlist and product search
- CMS pages with translations

### International
- **45 domains** across 6 continents (country-code TLDs + shared regional domains)
- **163 store views** with locale-specific content, currency, and language
- **99 countries** mapped with automatic geo-routing via Cloudflare
- **12 multi-language domains** with path-prefix routing (e.g., `.be/fr/` for French-speaking Belgium)
- **9 fully translated locales** with folder-based translation system
- Automatic browser language detection with Accept-Language parsing

### SEO
- hreflang tags across all 163 store views with x-default
- XML sitemaps with hreflang annotations (pages, products, categories)
- Canonical URLs, Open Graph, and Twitter Card meta tags
- JSON-LD structured data (Organization, Product, BreadcrumbList)
- Dynamic robots.txt per store view
- Search engine bot detection (prevents geo-redirect for crawlers)

### Admin Panel
- Dashboard with store analytics
- Full product management (CRUD, images, pricing, translations, vehicle fitment)
- Category and brand management
- Order processing with status history
- Customer management with account details
- Marketing tools: price rules, coupons, and ad campaigns with UTM tracking
- Content management: CMS pages and translation editor
- Store management: websites, store views, and domain configuration
- Support ticket system with departments, SLA policies, mailboxes, and canned responses
- System settings: admin users, roles, permissions, activity log, and configuration

### Support System
- Multi-department ticket system
- SLA policy enforcement
- Email mailbox integration per department
- Canned responses and ticket categories
- File attachments and ticket tagging
- Watcher notifications

---

## Architecture

StructBrew is a custom MVC framework — no external PHP dependencies. The entire stack runs on native PHP with PDO for database access.

```
Request → public/index.php → Bootstrap → StoreResolver → GeoRouter → Router → Controller → View → Response
```

**Core components:**

| Component | Description |
|---|---|
| `Bootstrap` | Application initialization, autoloading, environment loading |
| `App` | Route registration and request dispatching |
| `Router` | Pattern-based regex routing with parameter extraction |
| `Request` | HTTP request abstraction (input, headers, IP, method) |
| `Response` | HTTP response builder (HTML, JSON, redirects) |
| `Controller` | Base controller with view rendering, JSON responses, CSRF |
| `Database` | Singleton PDO wrapper with fluent query builder |
| `Model` | Base model class for database entities |
| `Auth` | Customer and admin authentication with brute-force protection |
| `Session` | Secure session management with flash messages and CSRF tokens |
| `Config` | Dot-notation configuration with .env support |
| `Cache` | File-based cache with TTL |
| `Validator` | Input validation (required, email, min, max, unique, confirmed) |
| `View` | PHP template rendering with shared data |
| `StoreResolver` | Multi-store domain and path-prefix resolution |
| `GeoRouter` | Country-based visitor routing across domains |
| `Translator` | Folder-based i18n with page-specific translation groups |
| `Seo` | Centralized SEO tag generation (hreflang, OG, JSON-LD) |
| `Migration` | Database schema migrations with batch tracking |
| `EventDispatcher` | Event system for decoupled components |

---

## Requirements

- PHP 8.1+ (strict types, fibers, enums)
- MySQL 5.7+ or MariaDB 10.3+ (utf8mb4, JSON column support)
- Nginx or Apache with URL rewriting
- Cloudflare (recommended, for geo-routing via CF-IPCountry header)

---

## Installation

### 1. Clone and configure

```bash
git clone https://github.com/yarpii/structbrew.git
cd structbrew
cp .env.example .env
```

Edit `.env` with your database credentials and application settings.

### 2. Run the setup wizard

Point your web server to the `public/` directory and open the site in your browser. The setup wizard will guide you through:

1. **Database** — connection test and configuration
2. **Application** — store name, URL, timezone
3. **Admin user** — create your first admin account
4. **Installation** — run all migrations and seed the database

### 3. Or use the CLI

```bash
php brew migrate        # Run all migrations
php brew db:seed        # Seed with sample data
php brew admin:create   # Create admin user (interactive)
```

---

## Configuration

All configuration lives in `config/config.php` with values pulled from `.env`:

```ini
# Application
APP_NAME=StructBrew
APP_URL=https://yourdomain.com
APP_DEBUG=false
APP_TIMEZONE=UTC

# Database
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=structbrew
DB_USER=root
DB_PASS=
DB_PREFIX=

# Session
SESSION_SECURE=true

# Mail (SMTP)
MAIL_DRIVER=smtp
MAIL_HOST=localhost
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@structbrew.com
MAIL_FROM_NAME=StructBrew

# Cloudflare Turnstile (optional bot protection)
TURNSTILE_ENABLED=true
TURNSTILE_SITE_KEY=
TURNSTILE_SECRET_KEY=

# Customer security
CUSTOMER_TWO_FACTOR_ENABLED=true
```

Additional store-level configuration (currency, locale, tax rates, etc.) is managed through the admin panel's **Configuration** section and stored in the database with scoped overrides per website, store, or store view.

---

## CLI Tool

The `brew` CLI provides database and system management commands:

```bash
php brew migrate              # Run pending migrations
php brew migrate:rollback     # Rollback last migration batch
php brew migrate:reset        # Rollback all migrations
php brew migrate:fresh        # Drop all tables and re-migrate
php brew migrate:status       # Show migration status
php brew db:seed              # Seed the database with sample data
php brew cache:clear          # Clear file-based cache
php brew admin:create         # Create an admin user (interactive)
```

---

## Routing

Routes are defined in `App/Routes/` and loaded alphabetically. The framework supports `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, and `ANY` methods with named parameters.

| File | Routes | Scope |
|---|---|---|
| `admin.php` | 114 | Admin panel (products, orders, customers, marketing, tickets, config) |
| `shop.php` | 39 | Storefront (catalog, cart, auth, account, garage, tickets) |
| `api.php` | 18 | REST API v1 (products, categories, cart, checkout, auth, vehicles) |
| `home.php` | 8 | Homepage, about, contact, support, geo-routing controls |
| `seo.php` | 5 | Sitemaps (XML index + per-type) and dynamic robots.txt |
| `setup.php` | 8 | Installation wizard (only active when `.env` is missing) |
| `zzz-pages.php` | 1 | CMS page catch-all (loaded last) |

**Total: ~193 routes**

Route parameters use `{name}` syntax and are automatically injected into controller methods:

```php
$app->get('/shop/product/{slug}', [ShopController::class, 'show']);
// ShopController::show(Request $req, string $slug)
```

---

## Multi-Store System

StructBrew uses a three-level store hierarchy:

```
Website → Store → Store View
                     ↓
              Store Domain (host + path_prefix)
```

- **Websites** — top-level business units (e.g., "Scooter Dynamics Global")
- **Stores** — groups of store views (e.g., "Europe", "Americas")
- **Store Views** — locale-specific implementations with their own language, currency, and content
- **Store Domains** — map hostnames and path prefixes to store views

The `StoreResolver` resolves the current store view on every request by matching `HTTP_HOST` and the URI path prefix against the `store_domains` table. Configuration cascades from global → website → store → store view, so you can set defaults at any level and override where needed.

### Path-prefix routing

Multi-language domains use path prefixes to serve different locales from the same hostname:

| Domain | Path | Language | Store View |
|---|---|---|---|
| `scooterdynamics.be` | `/` | Dutch | Belgium (NL) |
| `scooterdynamics.be` | `/fr/` | French | Belgium (FR) |
| `scooterdynamics.ch` | `/` | German | Switzerland (DE) |
| `scooterdynamics.ch` | `/fr/` | French | Switzerland (FR) |
| `scooterdynamics.ch` | `/it/` | Italian | Switzerland (IT) |

Resolution matches the longest path prefix first, so `/fr/shop/product/brake-pads` correctly resolves to the French store view on `.be`.

---

## Geo-Routing

The `GeoRouter` automatically redirects visitors to their country-specific domain based on their geographic location.

### How it works

1. **Country detection** — reads Cloudflare's `CF-IPCountry` header (or `X-Geo-Country` fallback)
2. **Domain lookup** — maps the ISO 3166-1 country code to the target domain from a hardcoded map of 99 countries
3. **Language matching** — for multi-language domains, parses `Accept-Language` to route to the correct path prefix
4. **Redirect** — issues a 302 redirect to the target domain

### Smart skip conditions

Geo-routing is skipped for:
- Visitors with a `geo_store_override` cookie (manual store choice)
- Non-GET requests (POST, PUT, etc.)
- AJAX requests
- Admin, API, and asset routes
- **Search engine bots** (25+ crawler patterns detected — critical for indexing all domains)

### Override controls

- `/store/stay?redirect=/` — sets the override cookie, keeping the visitor on their current domain
- `/store/reset-geo` — clears the override cookie, re-enabling geo-routing

### Domain coverage

| Region | Domains | Countries |
|---|---|---|
| Europe | 25 TLDs + `.eu` shared | AT, BE, CH, CZ, DE, DK, EE, ES, FI, FR, GB, GR, HR, HU, IT, LI, LT, LV, ME, NL, PL, PT, RO, SE, SI, SK, TR + 11 via `.eu` |
| Americas | 4 TLDs + `.com` shared | US, MX, CL + 14 via `.com` |
| Asia | 4 TLDs + `.asia` shared | IN, MY, TW + 26 via `.asia` |
| Middle East | via `.com` | AE, SA, IL, JO, LB |
| Africa | via `.com` | ZA, NG, EG, KE, MA, ET, GH |
| Oceania | 1 TLD + `.com` | NZ, AU |

---

## Internationalization

### Folder-based translation system

Translations are organized by locale and page group:

```
App/Locale/
├── en_US/
│   ├── common.php      # Shared strings (nav, footer, buttons)
│   ├── shop.php         # Product catalog
│   ├── cart.php         # Shopping cart
│   ├── checkout.php     # Checkout flow
│   ├── account.php      # Customer account
│   ├── auth.php         # Login & registration
│   └── vehicles.php     # Vehicle fitment
├── nl_NL/
│   ├── common.php
│   ├── shop.php
│   └── ...
├── de_DE/
├── fr_FR/
├── it_IT/
├── es_ES/
├── pt_PT/
├── pl_PL/
└── en_GB/
```

### How it works

1. **Common group** — `common.php` is loaded automatically for every page
2. **Page groups** — controllers call `Translator::page('shop')` to load page-specific translations on demand
3. **Fallback chain** — missing keys fall back to `en_US`, then to the key name itself
4. **Database overlay** — store-view-specific overrides from the `translations` table take priority over file-based strings

### Usage in views

```php
// Simple key (from common.php)
<?= __('add_to_cart') ?>

// Dot-notation (explicitly loads group)
<?= __('shop.in_stock') ?>

// With parameters
<?= __('items_count', ['count' => 5]) ?>
```

### Adding a new language

1. Create a folder: `App/Locale/ja_JP/`
2. Copy `common.php` from `en_US/` and translate
3. Add page groups as needed (`shop.php`, `cart.php`, etc.)
4. Create the store view and domain in the admin panel

The system auto-discovers available locales and groups.

---

## SEO

The `Seo` class generates all SEO tags in a single `Seo::head()` call placed in the layout's `<head>`:

### hreflang tags

Every page renders `<link rel="alternate" hreflang="..." href="...">` for all 163 store views, plus an `x-default` tag. This tells search engines which version of a page to show in each country/language.

### XML Sitemaps

| URL | Content |
|---|---|
| `/sitemap.xml` | Sitemap index linking to all sub-sitemaps |
| `/sitemap-pages.xml` | Static pages and CMS pages |
| `/sitemap-products.xml` | All active products |
| `/sitemap-categories.xml` | All active categories |

Each URL entry includes `<xhtml:link rel="alternate">` annotations for all store views — search engines can discover every localized version of every page.

### Structured data

- **Organization** — JSON-LD on all pages
- **Product** — JSON-LD on product detail pages (name, price, currency, availability, image, brand)
- **BreadcrumbList** — JSON-LD for navigation trails

### Additional tags

- Canonical URL per page
- Open Graph tags (title, description, image, type, locale)
- Twitter Card tags (summary_large_image)
- Dynamic `<html lang="...">` attribute (BCP 47 format)
- Per-store-view `robots.txt` with configurable rules

---

## Admin Panel

Access the admin panel at `/admin` with role-based permissions.

### Modules

| Module | Features |
|---|---|
| **Dashboard** | Store overview and analytics |
| **Products** | CRUD, images, translations, pricing, vehicle fitment, attributes |
| **Categories** | Tree structure, translations, attribute assignments |
| **Brands** | Brand management for product association |
| **Vehicles** | Year/make/model definitions for fitment matching |
| **Attributes** | Product attributes and filterable properties |
| **Orders** | Order list, detail view, status history |
| **Customers** | Account management, addresses, order history |
| **Marketing** | Price rules, coupons, ad campaigns (with UTM + click tracking) |
| **Content** | CMS pages, translation management |
| **Stores** | Websites, store views, domain mapping |
| **Configuration** | Scoped settings (global, website, store, store view) |
| **Support** | Tickets, departments, mailboxes, SLA policies, canned responses, categories |
| **System** | Admin users, roles, permissions, activity log |

---

## Storefront API

REST API at `/api/v1/` for headless or mobile integrations:

```
GET    /api/v1/products             # List products
GET    /api/v1/products/{id}        # Product detail
GET    /api/v1/categories           # List categories
GET    /api/v1/cart                 # Get cart
POST   /api/v1/cart/add             # Add to cart
POST   /api/v1/cart/update          # Update cart item
POST   /api/v1/cart/remove          # Remove from cart
POST   /api/v1/checkout             # Place order
POST   /api/v1/auth/login           # Customer login
POST   /api/v1/auth/register        # Customer registration
GET    /api/v1/account              # Account details
GET    /api/v1/store/config         # Store configuration
GET    /api/v1/vehicles             # Vehicle fitment data
```

---

## Security

### Authentication
- **Password hashing** — bcrypt via `password_hash()` / `password_verify()`
- **Brute-force protection** — 5 failed attempts triggers a 15-minute lockout per email
- **Timing-attack prevention** — constant-time password verification with dummy hash on unknown emails
- **Two-factor authentication** — optional TOTP-based 2FA for customer accounts
- **Session fixation prevention** — session ID regenerated on login and every 30 minutes

### Input & output
- **CSRF protection** — 32-byte random hex tokens verified on all state-changing requests
- **SQL injection prevention** — PDO prepared statements (emulation disabled)
- **XSS prevention** — `htmlspecialchars()` in views, `Content-Type` headers on responses
- **Header injection prevention** — CRLF stripping on all redirect URLs
- **File upload security** — MIME type validation, SVG explicitly blocked (JavaScript vector)

### Session & cookies
- **`__Host-` prefix** — session cookie bound to the origin
- **HttpOnly** — cookies inaccessible to JavaScript
- **SameSite=Lax** — CSRF protection at the browser level
- **Secure flag** — auto-enabled on HTTPS

### Infrastructure
- **Cloudflare Turnstile** — optional bot protection on login and registration forms
- **Open redirect prevention** — redirect parameters validated as relative paths
- **`.env` security** — no `putenv()` calls (prevents LD_PRELOAD exploits)
- **Safe config parsing** — alphanumeric key validation in `.env` parser

---

## Directory Structure

```
structbrew/
├── App/
│   ├── Core/                    # Framework core (21 classes)
│   │   ├── App.php              # Application container
│   │   ├── Auth.php             # Authentication
│   │   ├── Bootstrap.php        # Application bootstrap
│   │   ├── Cache.php            # File-based caching
│   │   ├── Config.php           # Configuration management
│   │   ├── Controller.php       # Base controller
│   │   ├── Database.php         # Query builder & PDO wrapper
│   │   ├── EventDispatcher.php  # Event system
│   │   ├── GeoRouter.php        # Geo-routing (99 countries)
│   │   ├── Middleware.php       # Middleware base
│   │   ├── Migration.php        # Database migrations
│   │   ├── Model.php            # Base model
│   │   ├── Request.php          # HTTP request
│   │   ├── Response.php         # HTTP response
│   │   ├── Router.php           # URL routing
│   │   ├── Seo.php              # SEO tag generation
│   │   ├── Session.php          # Session management
│   │   ├── StoreResolver.php    # Multi-store resolution
│   │   ├── Translator.php       # i18n system
│   │   ├── Validator.php        # Input validation
│   │   └── View.php             # Template rendering
│   │
│   ├── Controllers/
│   │   ├── Admin/               # 16 admin controllers
│   │   ├── Storefront/          # API controller
│   │   ├── AccountController.php
│   │   ├── AuthController.php
│   │   ├── CartController.php
│   │   ├── HomeController.php
│   │   ├── PagesController.php
│   │   ├── SetupController.php
│   │   ├── ShopController.php
│   │   └── SitemapController.php
│   │
│   ├── Models/                  # 32 database models
│   ├── Middleware/               # 6 middleware classes
│   │   ├── AdminAuthMiddleware.php
│   │   ├── AdminPermissionMiddleware.php
│   │   ├── AuthMiddleware.php
│   │   ├── CsrfMiddleware.php
│   │   ├── LocaleMiddleware.php
│   │   └── StoreResolverMiddleware.php
│   │
│   ├── Services/                # Business logic
│   │   ├── PaymentMethodService.php
│   │   ├── SecurityConfigService.php
│   │   ├── TurnstileService.php
│   │   ├── TwoFactorService.php
│   │   └── WalletService.php
│   │
│   ├── Data/                    # Seeders & data helpers
│   ├── Routes/                  # 7 route files (~193 routes)
│   ├── Migrations/              # 58 database migrations
│   ├── Locale/                  # 9 locales × 7 groups
│   └── Views/                   # 11 view directories
│       ├── admin/               # Admin panel views
│       ├── shop/                # Storefront views
│       ├── cart/                # Cart views
│       ├── account/             # Account views
│       ├── auth/                # Login/register views
│       ├── home/                # Homepage views
│       ├── support/             # Support center views
│       ├── setup/               # Installation wizard
│       ├── pages/               # CMS page views
│       ├── layout/              # Layouts (app, header, footer)
│       └── partials/            # Reusable components
│
├── config/
│   └── config.php               # Application configuration
│
├── public/
│   ├── index.php                # Front controller
│   └── assets/                  # CSS, JS, images
│
├── storage/
│   ├── cache/                   # File cache
│   └── sessions/                # Session files
│
├── docs/
│   └── nginx.conf               # Nginx configuration example
│
├── brew                         # CLI tool
├── .env.example                 # Environment template
└── README.md
```

**347 PHP files** across the application.

---

## Nginx Configuration

An example Nginx configuration is provided in `docs/nginx.conf`. Key points:

- Document root points to `public/`
- All requests routed through `index.php` (front controller pattern)
- Sensitive files blocked (`.env`, `.git`, `.sql`, etc.)
- Static assets cached for 1 month
- Gzip compression enabled
- PHP-FPM via Unix socket

For multi-domain setups, create a server block per domain (or use a wildcard) all pointing to the same `public/` directory. The `StoreResolver` handles domain differentiation in PHP.

---

## Database

58 migrations define the full schema:

**Store infrastructure** — websites, stores, store_views, store_domains, configurations

**Catalog** — products, product_translations, product_pricing, product_images, product_categories, product_vehicles, product_attributes, categories, category_translations, category_attributes, brands, attributes, vehicles

**Sales** — carts, cart_items, orders, order_items, order_status_history

**Customers** — customers, addresses, customer_vehicles

**Finance** — currencies, currency_rates, tax_zones, tax_rates, shipping_zones, shipping_methods, shipping_rates, price_rules, coupons

**Loyalty** — wallet points and credits tables

**Content** — cms_pages, cms_page_translations, translations

**Marketing** — marketing_ads (with UTM and click tracking)

**Support** — tickets, ticket_replies, ticket_attachments, ticket_departments, ticket_categories, ticket_sla_policies, ticket_canned_responses, ticket_tags, ticket_watchers, ticket_mailboxes

**System** — admin_roles, admin_users, activity_log, migrations

---

## License

Proprietary. All rights reserved.
