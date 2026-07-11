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

## PHP 8 compatibility: how interaction pages are tested for SQL issues

This app was written for PHP 5/7 and now runs on PHP 8.3 (Ubuntu 24.04/WSL). PHP 8
turns several previously-tolerated things into fatal errors, and **mysqli throws
exceptions on SQL errors by default in PHP 8.1+** (PHP 7 returned `false`). The
landing pages are fixed and verified; the add/search/update/delete/detail pages are
tested with the method below. Everything runs headless via `curl` against Apache —
no browser required.

### 1. Drive the app as root inside WSL
```bash
wsl -d Ubuntu -u root -- bash -s   # root can query MySQL via socket and read logs
```
All requests go to `http://127.0.0.1/<path>`; PHP errors land in
`/var/log/apache2/error.log`.

### 2. Fake an authenticated session (sessions are keyed by client IP)
The app has no login cookie — `checkSession()`/`checkAdvancedSession($n)` look up the
`session` table by the request's IP and compare `admin.level`. To test as "logged in"
from loopback, insert a session row for 127.0.0.1 using a real level-5 admin so every
clearance gate passes:
```bash
mysql NLPKDB -e "REPLACE INTO session (username, ipaddress) VALUES ('masahu','127.0.0.1');"
```

### 3. Isolate each request's errors with a log marker
Before a request, append a unique marker to the error log; after it, read from the
marker to end-of-file and look for fatals. This attributes errors to the exact request:
```bash
M="MK$(date +%s%N)"; echo "$M" >> /var/log/apache2/error.log
curl -s -o /tmp/out.html -w "%{http_code} %{size_download}\n" http://127.0.0.1/<page>
awk -v m="$M" '$0 ~ m {f=1; next} f' /var/log/apache2/error.log | grep -i "php fatal\|uncaught"
```
HTTP 500 + a fatal in the slice = broken page. A 200 with a tiny byte size that should
be large = the query returned nothing (soft SQL failure worth investigating).

### 4. Landing pages (GET) vs interaction pages (POST)
Landing pages `<module>/<module>.php` need only a GET. The add/search/update/delete and
`viewDetails.php` pages are **schema-driven**: they read `$_POST` keyed by real DB
column names and build the SQL dynamically. To exercise them, replay a realistic POST:
```bash
# find real values to post with
mysql NLPKDB -e "DESCRIBE Student;"                    # column names/types
mysql NLPKDB -N -e "SELECT studentID FROM Student WHERE status='active' LIMIT 1;"
# detail page expects an 'id' (and sometimes 'newstudent'); search pages expect field=value
curl -s -X POST -d "id=<realID>" http://127.0.0.1/account/viewDetails.php
curl -s -X POST -d "last_name=Smith" http://127.0.0.1/student/search/searchStudent.php
```
Read the matching form page's HTML (`*_page.php`) to get the exact input `name`s, or read
the module's `queries.php` to see which columns the query references.

### 5. When a page renders but shows no data, get the real MySQL error
Because we set `mysqli_report(MYSQLI_REPORT_OFF)` in `connect()`, a bad query now returns
`false` (soft-fail) instead of throwing — the page renders empty or echoes "Error: ...".
To find the cause, copy the SQL string out of the module's `queries.php` and run it
directly so MySQL reports the exact problem:
```bash
mysql NLPKDB -e "SELECT ...the query from queries.php... ;"
```
Typical PHP-8-era culprits this surfaces:
- **Reserved words used as unquoted columns** (e.g. `function` in the `Log` table) — fix by
  backticking: `` `function` ``.
- **Column/table name drift** vs the imported dump.
- **`count()` / `mysqli_num_rows()` / `->free()` on the null** a failed query returns —
  guard these (see the existing fixes in `config.php` and module pages).

### 6. Fix → re-verify loop
After each fix: `php -l <file>` (syntax), re-run the marker probe (expect `200`, zero
fatals), and check the rendered byte size grew (data now present). Never submit real
`execute*Add/Update/Delete` writes against live records while testing — use a throwaway
record or omit the final write step, since these pages mutate the database.

### 7. Coverage checklist per module
For each of `employee student account(+charge/payment/forms) scheduler income
expenditure receipt log student/cca`, walk: landing → search form → search results →
add form → detail view → update form → delete form, applying steps 3–6 to each.
(`history/` has copy-pasted wrong include depths and is tracked as separate work.)

### Runnable: `tests/php8_sweep.sh`

The procedure above is automated end-to-end in `tests/php8_sweep.sh`. Run it on the
WSL host as root (it reads the Apache error log and reaches MySQL over the socket):

    sudo bash tests/php8_sweep.sh

It runs the three phases and prints only pages that throw a PHP fatal:
1. **GET** every non-mutating page under the active modules.
2. **POST** the account detail page with a real account id, and with a real student
   id + `newstudent=1`.
3. **POST** every `execute*` mutation page with NONEXISTENT record ids (so
   UPDATE/DELETE touch zero real rows), wrapped in a `mysqldump` snapshot that is
   restored afterward. INSERT pages still create rows, so the snapshot restore is
   what keeps data clean — the script prints the post-restore student count so you
   can confirm it returned to baseline (1222).

Env overrides: `ADMIN` (real admin with `level>=5`, default `masahu`), `DB`
(default `NLPKDB`), `DBUSER` (default `nlpkuser`), `APACHE_LOG`.

A clean run prints `0 fatals` for every phase. This is the exact harness used to fix
the interaction-page PHP-8 regressions (removed `money_format()`; `implode()`/
`mysqli_real_escape_string()` on array-valued date/time fields; `DateTime::
createFromFormat()->format()` on `false`; `ArgumentCountError`; empty-string
`mysqli_query()`). Re-run it before committing changes to query-building or
form-processing code.
