# SAQR Motors

**Multi-brand motorcycle dealership platform — technical assessment**

SAQR Motors is a bilingual (English/Arabic), responsive motorcycle dealership website with a role-based administration panel. Customers can explore motorcycle models and promotions, request a test ride, and submit enquiries. Staff can manage the catalog and operational content through a Filament CMS.

The product is conceived for the **Middle East**, with **Egypt selected as the initial MVP market** for its sample locations, booking flow, and EGP pricing. It is an assessment/demo, not a live motorcycle dealership or a production-ready commercial offering.

## Submission links

| Deliverable | Link / detail |
| --- | --- |
| Source repository | [GitHub — saqr-motors](https://github.com/Gamalsobhy585/saqr-motors) |
| Deployed website | [SAQR Motors — Laravel Cloud](https://saqr-motors-production-cnfjti.laravel.cloud/) |
| Admin / CMS | [Open `/admin`](https://saqr-motors-production-cnfjti.laravel.cloud/admin) |
| Database ERD (image) | [erd.png](erd.png) |
| Database ERD (PDF) | [SAQR_Motors_ERD.pdf](SAQR_Motors_ERD.pdf) |
| Walkthrough video  | [Google Drive — assessment materials](https://drive.google.com/drive/folders/150DTp4rYrs9pPBWFPoFm9oredbuj1s5V?usp=sharing) |
| Figma | **Not used.** The interface was implemented directly using Blade, Tailwind CSS, Alpine.js, and Filament. |

> **Demo availability:** The deployment runs on a resource-limited Laravel Cloud assessment environment, so an initial cold start may take a little longer. If possible, please review the hosted demo **within 30 days of submission**; continued hosting is not guaranteed indefinitely. The repository and setup instructions remain available independently of the live demo.

## Demo CMS accounts

Visit **[Admin login](https://saqr-motors-production-cnfjti.laravel.cloud/admin)**.

| Role | Email | Password |
| --- | --- | --- |
| Administrator | `admin@saqr.test` | `saqradmin123@` |
| Data Entry | `dataentry@saqr.test` | `saqrdataentry123@` |

These are **assessment-only credentials**, deliberately provided for evaluation. Do not reuse them in a real deployment. The data-entry account is assigned to the Honda, Yamaha, Kawasaki, and Suzuki brands in the demo seeder; an administrator has broader access.

## Main functionality

- **Public catalog:** Browse published motorcycles; filter by brand, category, price, and promotions; view model specifications, pricing, availability, and image galleries.
- **Homepage:** Featured and published motorcycles, active brands, financing plans, services, and dealer locations.
- **Promotions:** Brand-wide or model-specific percentage discounts with an active date range; applicable prices are calculated for public listings.
- **Test rides:** Select an available motorcycle, Egyptian dealership location, and future booking slot; duplicate bookings for the same motorcycle, location, and time are rejected.
- **Contact enquiries:** Public enquiries associated with a motorcycle and brand; staff can review enquiries in the CMS.
- **CMS:** Brand, motorcycle, specifications, galleries, services, promotions, locations, finance-related data, users, and enquiry/booking management, subject to permissions.
- **Localization and responsiveness:** English/Arabic interfaces, mobile-friendly layouts, and a light/dark interface preference.

## Technology stack

| Layer | Choice | Why |
| --- | --- | --- |
| Server application | **PHP 8.4 in Sail**; Laravel **13** (`composer.json` requires PHP `^8.3`) | Mature MVC framework, migrations, validation, policies, and Eloquent ORM. |
| Admin / CMS | **Filament 5**, built on Livewire | Resource-driven forms, tables, relation management, and authenticated administration without building a separate SPA. |
| Public UI | **Blade**, **Tailwind CSS 4**, **Alpine.js 3** | Server-rendered, lightweight interactivity, responsive styling, and minimal JavaScript complexity. |
| Asset pipeline | **Vite 7** | Development hot reload and optimized production assets. |
| Database | **PostgreSQL 18** in Sail and Laravel Cloud | Relational integrity, indexes, foreign keys, and transactional operations. |
| Local services | **Laravel Sail**, Docker Compose, PostgreSQL, **Redis** | Reproducible development environment. |
| Cloud deployment | **Laravel Cloud** | Deployment from the GitHub repository; PostgreSQL-backed persistent application data. |
| Testing | **PHPUnit**, Laravel feature tests | Authentication, authorization, catalog and management-flow coverage. |

The project also uses Lucide icons and the Laravel/Filament component ecosystem. The authoritative package versions are in `composer.lock` and `package-lock.json`.

## Run locally — Option A: Laravel Sail (recommended)

### Requirements

- Git
- Docker Desktop with WSL2 support (Windows), or Docker Engine with Docker Compose (Linux/macOS)
- PHP **8.3+** and Composer **2.x** on the host for initial dependency installation (or an equivalent Composer container)
- Node.js compatible with Vite 7 (**20.19+** or **22.12+**) and npm
- Available host ports: **8080** (application), **5173** (Vite), and **5434** (PostgreSQL port mapping); change them in `.env` if occupied

### Install and start

```bash
# Clone the assessment source
git clone https://github.com/Gamalsobhy585/saqr-motors.git
cd saqr-motors

# Install PHP dependencies on the host
composer install

# Create your local environment file
cp .env.example .env
```

In `.env`, keep the Docker service names defined in `compose.yaml`:

```dotenv
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8080
APP_PORT=8080

DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=saqr_motors
DB_USERNAME=sail
DB_PASSWORD=password
FORWARD_DB_PORT=5434

REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PORT=6379

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
FILESYSTEM_DISK=public

SAQR_ADMIN_EMAIL=admin@saqr.test
SAQR_ADMIN_PASSWORD=saqradmin123@
SAQR_DATA_ENTRY_EMAIL=dataentry@saqr.test
SAQR_DATA_ENTRY_PASSWORD=saqrdataentry123@
```

Then execute:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed

npm ci
npm run dev
```

Open **http://localhost:8080** and **http://localhost:8080/admin**. Leave `npm run dev` running for the Vite development server.

For a production-style frontend build, run `npm run build` instead of `npm run dev`.

If you use the filesystem-backed public disk for custom or legacy local files, you can also run:

```bash
./vendor/bin/sail artisan storage:link
```

**Note:** The current CMS image-persistence workflow saves new permanent images in PostgreSQL (`media_assets`). It does **not** require `storage:link` for those database-backed media URLs. The initial Livewire upload is still temporary.

To stop containers:

```bash
./vendor/bin/sail down
```

Do not add `-v` unless you intentionally want to remove local database volumes.

## Run locally — Option B: Native PHP / PostgreSQL (without Sail)

### Requirements

- PHP **8.3+**, Composer **2.x**
- PostgreSQL (the project is developed and deployed against **PostgreSQL 18**)
- Node.js **20.19+** or **22.12+**, and npm
- PHP extensions normally required by Laravel plus `pdo_pgsql`, `pgsql`, `mbstring`, `openssl`, `fileinfo`, `intl`, `xml`, `ctype`, and `tokenizer`
- A local PostgreSQL database and a user with permissions to run migrations

### Install

```bash
git clone https://github.com/Gamalsobhy585/saqr-motors.git
cd saqr-motors
composer install
cp .env.example .env
```

Create the PostgreSQL database (e.g. `saqr_motors`) and configure `.env` for your local machine:

```dotenv
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=saqr_motors
DB_USERNAME=YOUR_POSTGRES_USER
DB_PASSWORD=YOUR_POSTGRES_PASSWORD

# No Redis service is required for this option.
CACHE_STORE=database
QUEUE_CONNECTION=database
SESSION_DRIVER=database
FILESYSTEM_DISK=public

SAQR_ADMIN_EMAIL=admin@saqr.test
SAQR_ADMIN_PASSWORD=saqradmin123@
SAQR_DATA_ENTRY_EMAIL=dataentry@saqr.test
SAQR_DATA_ENTRY_PASSWORD=saqrdataentry123@
```

Then:

```bash
php artisan key:generate
php artisan migrate --seed
npm ci
```

Run the application and frontend compiler in **separate terminals**:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

```bash
npm run dev
```

Open **http://127.0.0.1:8000** or **http://127.0.0.1:8000/admin**.

Optional: run `php artisan storage:link` for local filesystem-backed public assets; run `php artisan queue:work` if testing queued jobs with the database queue driver.

### Seeding and environment safety

- `DatabaseSeeder` populates demo users, brand assignments, motorcycles, cities/locations, financial plans, services, promotions, enquiries, and test-ride bookings.
- The user seeder reads the four `SAQR_*_EMAIL` / `SAQR_*_PASSWORD` environment variables; populate them **before** running `migrate --seed`.
- Demo account seeding is allowed automatically in `local` and `testing`. In other environments it is **disabled by default** and requires the explicit `SAQR_ALLOW_DEMO_SEEDING=true` setting during controlled initialization only. Turn it off afterward.
- Never run `migrate:fresh`, `db:wipe`, or destructive reseeding on an existing deployment with useful data.
- Keep `.env`, application keys, and real credentials out of version control.

## Tests and quality checks

Using Sail:

```bash
./vendor/bin/sail artisan test
./vendor/bin/sail artisan route:list
```

Without Sail:

```bash
php artisan test
php artisan route:list
```

The repository contains Laravel feature tests for authorization, brands, motorcycles, promotions, services, users, public navigation, enquiries, and financial-plan operations. **This README does not claim an all-green CI run**; run the tests in the environment being evaluated and address any environment-specific failures.

## Architecture and engineering approach

### Pragmatic MVC with a small service layer

The implementation follows Laravel's MVC conventions rather than adding a large custom abstraction stack:

```text
Public HTTP request
  -> Route -> Controller -> FormRequest validation
  -> Service (catalog / lead submission)
  -> Eloquent models -> PostgreSQL
  -> Blade + Tailwind + Alpine.js

Admin HTTP request
  -> Filament authentication -> Resources / Pages / Forms / Tables
  -> Policies + scoped queries -> Eloquent -> PostgreSQL
```

- **MVC / separation of concerns:** Controllers handle the HTTP boundary; Eloquent models express relationships and data access; Blade renders the storefront.
- **Service layer:** `PublicCatalogService` owns catalog queries, filtering and promotional-price selection; `LeadSubmissionService` handles public leads and booking persistence.
- **Policy pattern:** Laravel policies define resource permissions and brand-scoped access; database queries and write paths are also scoped and validated rather than relying only on hidden form fields.
- **Framework-driven resource pattern:** Filament resources compose pages, schemas/forms, tables, and relation inputs. Livewire handles server-backed CMS interactions, avoiding the complexity of a separate administration SPA.
- **Dependency injection and conventions:** Laravel's container resolves controller/service dependencies. ORM relationships, validation rules, migrations, and enums keep the business rules explicit.
- **Atomicity and data integrity:** Motorcycle create/edit flows use database transactions for related data; the test-ride slot has a **database unique constraint** on `(motorcycle_id, location_id, scheduled_at)` with a handled duplicate-booking response.

This deliberately avoids unnecessary repository abstractions, microservices, a separate public API, or a dedicated JavaScript SPA where Laravel's existing primitives are sufficient.

### Authentication, authorization, and security

- **Session-based authentication** through Filament/Laravel; no separate token-authentication layer was necessary for the server-rendered assessment.
- Two intentionally simple roles: **Administrator** and **Data Entry**. Laravel **Policies + role enum + brand assignments** were sufficient; **Spatie Permission** was not introduced because dynamic permission/role management was outside the assessment's needs.
- More than one administrator and more than one data-entry user are supported by the data model. A data-entry user may be assigned to **multiple brands**, via the `brand_user` pivot relationship.
- An administrator can manage application-wide records; data-entry permissions are restricted to their assigned brands and resource-specific policy rules.
- A **User Management** resource was added beyond the minimum catalog scope so account creation, activation, and brand assignment could be administered correctly.
- **Audit trail columns** (`created_by` / `updated_by`, as appropriate) record who created or last changed relevant records. This is **not** a full append-only activity/history log: no runtime activity-log package is claimed.
- Laravel **FormRequest/input validation**, Eloquent mass-assignment protections, policy checks, CSRF/session protection, and database constraints cover the relevant trust boundaries.
- Public test-ride and contact endpoints have **per-IP rate limits**. The configured test-ride limiter is **3 requests/minute and 10/hour**; contact uses the same thresholds.
- Public booking is allowed only for **published motorcycles** belonging to active brands and with `Available` inventory status; contact requests require a published motorcycle/active brand.

### Image storage and deployment trade-off

- Seeded/sample artwork remains in `public/images/...` in the repository.
- New CMS uploads use validated JPEG/PNG/WebP files and are saved by `DatabaseImages` to PostgreSQL's `media_assets` table, with a short `db-media/{uuid}` reference in the related model and a `/media/{uuid}` display route.
- PostgreSQL storage was selected for **small volumes of assessment media** because the Laravel Cloud application filesystem is ephemeral and an external object-storage bucket was not required for the submission. This is **not a recommendation for a high-volume media library**: base64 increases database size and media traffic is served through PHP.
- Livewire's **temporary upload stage** still uses its configured temporary filesystem, even though final CMS image content is persisted in PostgreSQL after saving.

### Deployment

The demonstration is deployed using **Laravel Cloud**, connected to the GitHub source repository. The environment uses PostgreSQL and database-backed sessions/cache where configured, rather than relying on a durable local application filesystem.

For a fresh production environment, configure `APP_KEY`, `APP_URL`, the attached database, the session/cache drivers and any required runtime secrets through **Laravel Cloud environment variables**; build assets and execute migrations as part of the deployment process:

```bash
php artisan migrate --force
```

Do not copy the development `.env` or demo password configuration into a new production system without reviewing security and intended access. This README documents an existing assessment deployment rather than a general-purpose production runbook.

## Functional assumptions and decisions

1. **Geography:** The target audience is the Middle East, but the MVP uses Egyptian cities, branches/locations, and EGP sample prices. Regional expansion would require additional location/currency configuration.
2. **User accounts:** Multiple admin and data-entry accounts can exist; a data-entry account can manage several assigned brands. Permissions are enforced by role **and** brand scope.
3. **Motorcycle ownership:** Each motorcycle belongs to **one brand** and has **one category**; the available categories are a controlled enumeration, not free-form tags.
4. **Publishing:** Draft/unpublished motorcycles can exist in the CMS. They are hidden from public discovery; homepage motorcycle cards are also limited to published **featured** records.
5. **Galleries:** Each motorcycle requires **at least one image and allows at most three** via the CMS form; the earliest ordered image becomes its primary image.
6. **Availability:** Publishing and inventory availability are separate concepts. A motorcycle can be published but unavailable for test rides.
7. **Promotions:** A promotion can target an entire brand or a single model. **Overlapping brand-wide promotions are permitted**. An **active, motorcycle-specific** promotion cannot have an overlapping date period with another active promotion for the same model, as validated by the CMS. Model-specific offers take precedence over brand-wide offers when selecting the displayed discount.
8. **Draft promotions:** Promotions may be associated with **unpublished motorcycles** so campaigns can be prepared in advance; public visibility still depends on motorcycle publication and the active offer period.
9. **Booking uniqueness:** Only one test ride can be booked for a given combination of **motorcycle + location + scheduled date/time**. Different motorcycles or locations may use the same time slot.
10. **Enquiries:** Public contact requests become operational enquiry records, rather than automatically sending an external email or creating a CRM integration.
11. **Demo data:** Example catalog entries, prices, finance plans, promotions, booking data, and locations are illustrative assessment content, not verified commercial inventory or guaranteed real-world offers.
12. **Auditability:** Creator/updater fields provide **last-editor accountability**, not a chronological per-change activity log.
13. **Media scope:** Persistent database image storage is a deliberate small-demo compromise; large-scale production use should move to a durable object-storage/CDN solution.
14. **Simplicity:** The implementation prioritizes relational correctness, explicit authorization, and comprehensible code over adding packages or infrastructure that the assessment does not need.

## Third-party materials, AI imagery, and attribution

**Source transparency:** The following describes where the visual material came from. It is **not a claim that every externally sourced image has been cleared for unrestricted reuse**.

| Asset category | Location / usage | Provenance | Attribution and use notes |
| --- | --- | --- | --- |
| SAQR brand identity and related logo artwork | `public/images/logos/`, site header and CMS | **AI-generated imagery** | Created for this assessment; explicitly identified as AI-generated. |
| Service illustrations | `public/images/services/`, homepage service section | **AI-generated imagery** | Created for this assessment; explicitly identified as AI-generated. |
| Motorcycle photographs | `public/images/motorcycles/`, model cards and galleries | **Third-party images found via Google/web searches** | Original rights holders, source-page URLs and licenses have not been individually documented. No ownership or permissive license is claimed. |
| Motorcycle manufacturer / brand logos | `public/images/brands/`, manufacturer cards | **Third-party manufacturer branding found via Google/web searches** | Trademarks/logos belong to their respective owners. Used for identification in a non-commercial technical demo; this does **not** establish a license or imply endorsement. |
| Frameworks, libraries, icons | Laravel, Filament, Tailwind, Alpine.js, Vite, Lucide, etc. | Third-party software | Their respective project licenses and attribution requirements apply; see the dependency manifests and lockfiles. |

**Important licensing limitation:** A Google image-search result is a discovery method, **not a usage license**. The current repository does not include a per-image URL, author, and license record for motorcycle photos or manufacturer images; therefore those files should be considered **unverified third-party demonstration assets**. Before public/commercial reuse, replace them with original or properly licensed images, or obtain permission and add source-specific attribution where required. No affiliation with, sponsorship by, or endorsement from any motorcycle manufacturer is intended.

**AI usage disclosure:** AI was used to generate the SAQR logo/identity imagery and the service images identified above. Other third-party photo/brand materials were sourced through web search; they are not represented as AI-generated or owned by the project author.

## Assessment artifacts and handover

- **Repository:** https://github.com/Gamalsobhy585/saqr-motors
- **Hosted demo:** https://saqr-motors-production-cnfjti.laravel.cloud/
- **Admin:** https://saqr-motors-production-cnfjti.laravel.cloud/admin
- **Video / ERD folder:** https://drive.google.com/drive/folders/150DTp4rYrs9pPBWFPoFm9oredbuj1s5V?usp=sharing
- **ERD in repository:** [`erd.png`](erd.png) · [`SAQR_Motors_ERD.pdf`](SAQR_Motors_ERD.pdf)
- **Figma prototype:** Not used.

The repository contains the Laravel source, schema migrations, seeders, public and CMS interfaces, and tests. This README is intended to be the single handover document for running and evaluating the assessment.
