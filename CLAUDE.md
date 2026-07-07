# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

**NLPK** — the internal database/admin web app for **Northern Lights Preschool & Childcare** (Anchorage, AK). It manages employees, students, student accounts (charges/payments/contracts), attendance, scheduling, and simple accounting (income/expenditure/receipts).

Stack: **plain PHP + MySQL (mysqli) served by Apache**. There is **no framework, no build system, no package manager, no test suite, and no JS bundler**. `.php` files are served directly. The production deploy path is `/var/www/html` on a Linux/Apache host (several files hardcode that absolute path).

## Running / working on it

- **No build, lint config, or tests exist.** Don't look for `package.json`, `composer.json`, or CI.
- **Database:** credentials live in `db.ini` (host/user/pass/name), read by `connect()` in `config.php` via `parse_ini_file`. A running MySQL with the expected schema is required for any page to work.
- **Apache requirement:** redirects use the `SITE_HTMLROOT` server env var (see `config.php`, `session.php`), so it must be set (`SetEnv`/`.htaccess`). `.htaccess` currently only sets `Options +FollowSymLinks`.
- **Entry flow:** `index.html` → `login.php` → `homepage.php` (the module launcher).
- **Useful CLI checks** (the only tooling that applies): `php -l <file>.php` to syntax-check a file. `php -S localhost:8000` can serve locally, but pages that `include("/var/www/html/...")` absolutely (e.g. `test.php`, `attendance/all/*`) will only resolve on the real deploy path.
- `info.php` is `phpinfo()` and `test.php` is a dev scratch page — not part of the app.

## Architecture

### `config.php` is the shared library (~1600 lines)

Every page `include`s it. It provides three kinds of things:

1. **Infrastructure:** `connect()`, `getUserIP()`, `checkSession()`, `checkAdvancedSession($clearance)`.
2. **Generic, metadata-driven HTML renderers** — the heart of the UI. They take `$data` (array of rows) + `$fields` (mysqli `fetch_fields()` metadata) and emit HTML tables/forms, special-casing columns *by name*: dates/`DOB`, `*time*`, `*phone*`, foreign keys (`fk_studentID`, `fk_employeeID`, `fk_accountID`), `age`, `sex`, `status`, `authorization`. Key ones: `showAddForm`/`showAddForm2`, `showSearchForm`, `showData`/`showData2`, `showAdvancedData`/`showAdvancedData2` (rows are clickable and POST an id to a detail page), `showEditableData2`, `showDataWithLimit`.
3. **Data accessors:** generic `getRowByID`, `getLastInsert`, `getFieldValue`, plus per-entity getters (`getStudentByID`, `getEmployeeByID`, `getAccountByID`, `getChargeByID`, `getPaymentByID`, `getExpenditureByID`, `getCCAByID`, `getAttendanceByID`, `getPunchByID`, `queryActiveStudents`, …). By convention these return `['data' => rows, 'fields' => fieldInfo]`.

### The module CRUD pattern (the thing to internalize)

Each top-level domain directory is a **self-contained module with an identical layout**. Learn it once and every module reads the same:

```
<module>/
  <module>.php              landing/list page — nav menu + calls queries.php getters + config.php renderers
  queries.php               module-specific SELECT/getX() functions returning ['data'=>…, 'fields'=>…]
  viewDetails.php           per-record detail page (row click / detail button lands here)
  print.css                 print styles for this module's tables
  add/    <X>_page.php       add form  → executeAdd<X>.php    (builds & runs INSERT, shows confirmation)
  search/ search<X>_page.php form      → search<X>.php        (builds dynamic WHERE query, shows results)
  update/ search_update.php  find rec  → execute_update<X>.php (runs UPDATE)
  delete/ search_delete.php  find rec  → execute_delete<X>.php (runs DELETE)
```

Modules following this: `employee`, `student` (with nested `student/cca` = Child Care Authorization contracts), `account` (with nested `account/charge`, `account/payment`, `account/forms`), `scheduler`, `income`, `expenditure`, `receipt`, `log`. `attendance` is a labeled **prototype** (the Red/Blue/… room pages linked from `attendance/attendance_home.php` exist only under `attendance/archive/`, not at top level). `admin_attendance` is WIP/gated. `archive` and `history` hold archived-record and cross-entity update/delete pages.

### How a request actually flows

A typical page: open `<html>` and duplicated nav menu → `include("…/config.php")` → `$db = connect()` → `checkSession()` or `checkAdvancedSession(N)` → include the module `queries.php` → call a getter → hand `['data','fields']` to a `config.php` renderer that emits the table.

**The add/search/execute pages are schema-driven, not hardcoded.** They `SELECT * FROM <Table>`, read `$result->fetch_fields()`, and build the INSERT column list / WHERE clause dynamically from `$_POST` keyed by the actual DB column name. Numeric MySQL field types (1,2,3,4,5,8,9,16,246) are emitted unquoted; everything else is single-quoted. **Consequence:** forms and queries automatically track the table schema — to add a field to a form you usually add the column to the table, not edit the PHP.

### Auth & session model

- `login.php`: checks the **`admin`** table for a matching `username`/`password` (**plaintext**, direct `SELECT`), then `REPLACE INTO session (username, login_time, ipaddress)` — **sessions are keyed by client IP**, not a PHP session cookie.
- `checkSession()` (config.php): requires an active `session` row for the request IP or redirects to `login.php`; refreshes `login_time`.
- `checkAdvancedSession($clearance)`: role gate — reads `admin.level` for the IP's session and redirects to `badaccess.php` if `level < clearance`. Modules call e.g. `checkAdvancedSession(3)`.
- `logout.php`: `DELETE FROM session WHERE ipaddress = <ip>`.

### Client side

`js/js_main.js` is the only script (jQuery is loaded from the Google CDN per-page):
- `post(path, params)` — builds and submits a hidden `<form>`; used for clickable rows that carry a record `id` to a detail/update page.
- `contentChanger()` / `contentChanger2()` — the "Sort By" dropdowns don't re-query; every variant (e.g. Active / All / Inactive) is rendered server-side into hidden `<div>`s and swapped into `#content` client-side.

### Styling

`mystyle.css` (desktop, `min-device-width:1281px`) and `mystyle_small.css` (mobile) are selected via `<link media=…>` queries; `css/homepage.css`, `css/logout.css`, and per-module `print.css` layer on top. Recurring classes: `.menu`/`.button` (nav bar), `.data`/`.form` (tables), `.selectpicker`, `.circularsmall`, `.rectpretty`.

### File uploads

`upload.php` exposes `uploader($name, $target_dir)` — accepts jpg/jpeg/png/gif/pdf under 500 KB and stores per-entity documents under `resources/nlp_data/<entity>/<id>/`. Detail forms POST to `/upload.php`. `resources/nlp_data/account/**` is gitignored (only `.gitkeep` tracked).

### Database naming conventions (inferred from queries)

Tables are PascalCase (`Employee`, `Student`, `Account`, `Charge`, `Payment`, `CCA`, `Expenditure`, `Income`, `Receipt`), plus lowercase infra tables `admin` and `session`. Primary keys are `<entity>ID` (e.g. `studentID`); foreign keys are `fk_<entity>ID`. Records are typically soft-scoped by a `status` column of `'active'`/`'inactive'`.

## Conventions & gotchas when editing

- **Massive copy-paste UI.** The nav menu, `<head>`, and footer are duplicated verbatim in nearly every `.php` file. Changing shared chrome means editing many files. Some copied headers are stale (e.g. `history/search.php` still shows a CCA/student header) — verify the nav you're touching matches the page.
- **Relative includes depend on directory depth:** top level uses `include("config.php")`, `add/`/`search/` use `../../config.php`, deeper nesting uses `../../../config.php`. A few files instead hardcode `/var/www/html/...` absolute includes.
- **SQL is string-interpolated.** Only some inputs pass through `mysqli_real_escape_string`; queries and INSERTs are assembled by concatenation. Passwords are stored and compared in plaintext. This is the existing baseline — match surrounding code, and be aware new query code sits in an injection-prone context.
- **`['data','fields']` is the universal contract.** New query functions should return that shape so the `config.php` renderers can consume them; new list/detail views should reuse the existing renderers rather than hand-rolling tables.
- To add a new CRUD entity, **copy an existing module directory** (`employee/` is the cleanest reference) and rename the entity throughout — that is how every current module was made.
