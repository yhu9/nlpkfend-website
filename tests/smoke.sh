#!/usr/bin/env bash
#
# NLPK website smoke tests.
#
# Fetches key pages over HTTP and asserts status codes / body content, turning the
# manual checks for several filed issues into one repeatable command. Each check is
# tagged with its GitHub issue number.
#
# NOTE: the script encodes the DESIRED (post-fix) state, so before the fixes land
# some checks FAIL on purpose — that is how you see which issues remain.
#
# Usage:
#   BASE_URL=http://localhost:8000 tests/smoke.sh
#
# Environment:
#   BASE_URL      Base URL of a running instance (default: http://localhost:8000)
#   SMOKE_USER    Valid admin username — enables the authenticated checks (optional)
#   SMOKE_PASS    Valid admin password (optional)
#   SMOKE_APACHE  Set to 1 when testing against real Apache, so the .htaccess-dependent
#                 db.ini check runs (php -S ignores .htaccess and would fail it).
#
# Exit code: 0 if every check that ran passed, 1 if any failed, 2 if the server is unreachable.

set -u

BASE_URL="${BASE_URL:-http://localhost:8000}"
BASE_URL="${BASE_URL%/}"          # strip trailing slash
CURL="curl -s -S -m 10"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(dirname "$SCRIPT_DIR")"

pass=0; fail=0; skip=0

# Colors only when stdout is a terminal.
if [ -t 1 ]; then
  G=$'\033[32m'; R=$'\033[31m'; Y=$'\033[33m'; B=$'\033[1m'; N=$'\033[0m'
else
  G=; R=; Y=; B=; N=
fi

ok()  { printf '  %sPASS%s  %s\n' "$G" "$N" "$1"; pass=$((pass+1)); }
no()  { printf '  %sFAIL%s  %s\n' "$R" "$N" "$1"; fail=$((fail+1)); }
skp() { printf '  %sSKIP%s  %s\n' "$Y" "$N" "$1"; skip=$((skip+1)); }

status_of()   { $CURL -o /dev/null -w '%{http_code}'   "$1"; }   # prints numeric HTTP code
redirect_of() { $CURL -o /dev/null -w '%{redirect_url}' "$1"; }   # prints Location target, or empty

expect_status() {           # URL CODE DESC
  local got; got="$(status_of "$1")"
  if [ "$got" = "$2" ]; then ok "$3 ($2)"; else no "$3 — expected $2, got $got"; fi
}
expect_not_status() {       # URL CODE DESC
  local got; got="$(status_of "$1")"
  if [ "$got" != "$2" ]; then ok "$3 (got $got)"; else no "$3 — got $2"; fi
}
expect_body_contains() {    # URL REGEX DESC
  if $CURL "$1" | grep -qiE "$2"; then ok "$3"; else no "$3 — pattern /$2/ not found"; fi
}
expect_redirect_contains() { # URL SUBSTR DESC
  local loc; loc="$(redirect_of "$1")"
  if printf '%s' "$loc" | grep -qi "$2"; then ok "$3 (-> $loc)"; else no "$3 — redirect was '${loc:-<none>}'"; fi
}

printf '%sNLPK smoke tests%s -> %s\n\n' "$B" "$N" "$BASE_URL"

if ! $CURL -o /dev/null "$BASE_URL/index.html"; then
  printf '%sCannot reach %s — is the server running?%s\n' "$R" "$BASE_URL" "$N"
  exit 2
fi

printf '%s[Public / unauthenticated]%s\n' "$B" "$N"
expect_status        "$BASE_URL/index.html"    200 "index.html loads"
expect_body_contains "$BASE_URL/index.html"    "login.php"   "index.html links to login"
expect_status        "$BASE_URL/login.php"     200 "login page loads"
expect_body_contains "$BASE_URL/badaccess.php" "access level" "badaccess page loads"

# #3 — a failed login must surface the "invalid" message.
if $CURL --data-urlencode "username=__nope__" --data-urlencode "password=__nope__" "$BASE_URL/login.php" \
     | grep -qi "invalid"; then
  ok "#3 failed login shows an error message"
else
  no "#3 failed login shows no error message"
fi

# #8 / #9 — dev/scratch pages removed.
expect_status "$BASE_URL/info.php"                404 "#8 info.php removed"
expect_status "$BASE_URL/test.php"                404 "#9 root test.php removed"
expect_status "$BASE_URL/attendance/test.php"     404 "#9 attendance/test.php removed"
expect_status "$BASE_URL/attendance/all/test.php" 404 "#9 attendance/all/test.php removed"

# #12 — homepage requires auth: logged out, it must redirect to login.
expect_redirect_contains "$BASE_URL/homepage.php" "login.php" "#12 homepage.php redirects when logged out"

# #11 — db.ini must not be served (relies on .htaccess, so Apache only).
if [ "${SMOKE_APACHE:-}" = "1" ]; then
  expect_status "$BASE_URL/db.ini" 403 "#11 db.ini blocked by .htaccess"
else
  skp "#11 db.ini access — set SMOKE_APACHE=1 to test on real Apache (php -S ignores .htaccess)"
fi

# #7 — every button on the attendance home page must resolve (not 404). Fix-agnostic:
#      works whether the fix repointed the links or removed them.
printf '\n%s[#7 attendance room links]%s\n' "$B" "$N"
home="$REPO_ROOT/attendance/attendance_home.php"
if [ -f "$home" ]; then
  actions="$(grep -oE 'action="[^"]+"' "$home" | sed -E 's/action="([^"]+)"/\1/' | sort -u)"
  if [ -z "$actions" ]; then
    skp "no form actions found in attendance_home.php"
  else
    while IFS= read -r a; do
      [ -z "$a" ] && continue
      expect_not_status "$BASE_URL/attendance/$a" 404 "button '$a' resolves"
    done <<< "$actions"
  fi
else
  skp "attendance_home.php not found at $home"
fi

# Authenticated flow — sessions are keyed by client IP in the DB, so a successful
# POST creates a session row for this host and later requests are authenticated
# without a cookie. This also exercises #4 (login redirect) and logout.
printf '\n%s[Authenticated]%s\n' "$B" "$N"
if [ -n "${SMOKE_USER:-}" ] && [ -n "${SMOKE_PASS:-}" ]; then
  loginloc="$($CURL -o /dev/null -w '%{redirect_url}' \
      --data-urlencode "username=$SMOKE_USER" --data-urlencode "password=$SMOKE_PASS" \
      "$BASE_URL/login.php")"
  if printf '%s' "$loginloc" | grep -qi "homepage.php"; then
    ok "#4 valid login redirects to homepage.php (-> $loginloc)"
    expect_body_contains "$BASE_URL/homepage.php" "Database Tools" "homepage renders admin nav when logged in"
    $CURL -o /dev/null "$BASE_URL/logout.php"   # tear down the session
    expect_redirect_contains "$BASE_URL/homepage.php" "login.php" "logout clears session (homepage redirects again)"
  else
    skp "login did not redirect to homepage — wrong SMOKE_USER/SMOKE_PASS, or #4 unfixed (redirect was '${loginloc:-<none>}')"
  fi
else
  skp "authenticated checks — set SMOKE_USER and SMOKE_PASS to enable"
fi

printf '\n%sSummary:%s %s%d passed%s, %s%d failed%s, %s%d skipped%s\n' \
  "$B" "$N" "$G" "$pass" "$N" "$R" "$fail" "$N" "$Y" "$skip" "$N"

[ "$fail" -eq 0 ]
