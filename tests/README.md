# Smoke tests

`smoke.sh` fetches key pages of a **running** NLPK instance over HTTP and asserts status
codes / body content. It turns the manual verification for several of the filed issues into
one repeatable command. Each check is tagged with its GitHub issue number.

The script asserts the **post-fix** (desired) state, so running it *before* the fixes land
will report failures on purpose — that is how you track which issues are still open.

## Usage

```sh
# minimal — public/unauthenticated checks only
BASE_URL=http://localhost:8000 tests/smoke.sh

# add the authenticated checks (login redirect, admin nav, logout)
SMOKE_USER=someadmin SMOKE_PASS=secret BASE_URL=https://staging.example tests/smoke.sh

# on a real Apache host, also test the .htaccess db.ini rule
SMOKE_APACHE=1 SMOKE_USER=... SMOKE_PASS=... BASE_URL=https://staging.example tests/smoke.sh
```

Exit code: `0` all checks passed, `1` some failed, `2` server unreachable.

## Environment variables

| Var            | Purpose                                                                    |
| -------------- | -------------------------------------------------------------------------- |
| `BASE_URL`     | Base URL of the running site (default `http://localhost:8000`)             |
| `SMOKE_USER`   | Valid admin username — enables the authenticated checks (optional)         |
| `SMOKE_PASS`   | Valid admin password (optional)                                            |
| `SMOKE_APACHE` | Set to `1` on real Apache so the `db.ini` 403 check runs (see caveat below) |

## What it covers

| Issue | Check |
| ----- | ----- |
| #3  | Failed login surfaces the "invalid" error message |
| #4  | Valid login redirects to `homepage.php` (authenticated run only) |
| #7  | Every button on `attendance_home.php` resolves (no 404) — extracted from the file, so it works whether the fix repointed or removed the links |
| #8  | `info.php` returns 404 (removed) |
| #9  | `test.php`, `attendance/test.php`, `attendance/all/test.php` return 404 |
| #11 | `db.ini` returns 403 (Apache only) |
| #12 | `homepage.php` redirects to login when logged out |

## Caveats

- **`.htaccess` (#11) is Apache-only.** `php -S` ignores `.htaccess`, so the `db.ini` check is
  skipped unless `SMOKE_APACHE=1`. Run it against a staging Apache box for a real result.
- **Sessions are keyed by client IP** (a row in the `session` DB table), not a cookie. The
  authenticated run logs in (creating a session row for the test host), runs its checks, then
  hits `logout.php` to tear the session down. Run it from a host that is not already logged in.
- The remaining filed issues (#1, #2, #5, #6, #10, #13–#17) are verified by the static/manual
  methods described in the issues themselves (`php -l`, grep-for-string-gone, an HTML validator,
  a file-upload harness, `git ls-files`) — they don't have a stable HTTP signature to assert here.
