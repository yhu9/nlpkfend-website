#!/usr/bin/env bash
# ---------------------------------------------------------------------------
# PHP 8 interaction sweep for the NLPK app.
#
# Drives the LIVE app with curl and reports any page that throws a PHP fatal,
# so PHP-5/7 -> PHP-8 regressions (removed funcs, count()/->format() on null,
# ArgumentCountError, mysqli-throws-on-error, reserved-word columns) surface
# without clicking through a browser.
#
# Runs WITHOUT root. It uses the app DB credentials from db.ini (nlpkuser),
# which has ALL PRIVILEGES ON NLPKDB.* -- enough for every SELECT/INSERT/
# UPDATE/DELETE the pages issue AND for reloading the mysqldump snapshot
# (the dump's per-table DROP TABLE/CREATE TABLE needs only DB-level privs,
# not the superuser DROP DATABASE/GRANT the old version used).
#
#     bash tests/php8_sweep.sh
#
# One host prerequisite that is NOT in this repo: the running user must be
# able to READ the Apache error log (default /var/log/apache2/error.log, mode
# 640 root:adm) for fatal detection. Being in the `adm` group is enough
# (`sudo usermod -aG adm "$USER"` once, then re-login). If you can't, point
# APACHE_LOG at a log you can read.
#
# Why the auth works: sessions are keyed by client IP (the `session` table),
# so we insert a loopback session row for a level-5 admin and drive from
# 127.0.0.1.
# ---------------------------------------------------------------------------
set -u
BASE="http://127.0.0.1"
HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"   # repo root (docroot)
LOG="${APACHE_LOG:-/var/log/apache2/error.log}"
ADMIN="${ADMIN:-masahu}"          # MUST be a real admin row with level >= 5
MODULES="employee student account scheduler income expenditure receipt log"

# --- DB credentials come from db.ini (same file the app reads) -------------
INI="${DB_INI:-$HERE/db.ini}"
ini(){ sed -n "s/^$1=//p" "$INI" | tr -d ' "'\''' | head -1; }
DBHOST="$(ini dbhost)"; DBHOST="${DBHOST:-localhost}"
DBUSER="$(ini dbuser)"
DBPASS="$(ini dbpass)"
DB="${DB:-$(ini dbname)}"; DB="${DB:-NLPKDB}"

if [ -z "$DBUSER" ]; then echo "!! could not read dbuser from $INI" >&2; exit 1; fi
if [ ! -r "$LOG" ]; then
  echo "!! cannot read Apache log $LOG -- add yourself to the adm group or set APACHE_LOG" >&2
  exit 1
fi

# mysql / mysqldump wrappers that carry the app creds (no root, no prompt)
MYSQL(){ command mysql -h"$DBHOST" -u"$DBUSER" ${DBPASS:+-p"$DBPASS"} "$@" 2>/dev/null; }
MYSQLDUMP(){ command mysqldump -h"$DBHOST" -u"$DBUSER" ${DBPASS:+-p"$DBPASS"} "$@" 2>/dev/null; }
session(){ MYSQL "$DB" -e "REPLACE INTO session (username, ipaddress) VALUES ('$ADMIN','127.0.0.1');"; }

# hit METHOD PATH [POSTDATA] -> "ok" or "FATAL <message>"
# Uses the error-log byte offset before/after the request to attribute fatals.
hit(){
  local before err; before=$(wc -c < "$LOG")
  if [ "$1" = GET ]; then curl -s -o /dev/null "$BASE/$2"
  else curl -s -o /dev/null -X POST -d "$3" "$BASE/$2"; fi
  err=$(tail -c +$((before+1)) "$LOG" | grep -iE "php fatal|uncaught" | head -1)
  [ -n "$err" ] && echo "FATAL ${err##*] }" || echo ok
}

echo "## session as $ADMIN (level>=5) on $DB via $DBUSER@$DBHOST"; session

echo "## Phase 1 - GET every non-mutating page"
p1ok=0; p1bad=0
while IFS= read -r pg; do
  r=$(hit GET "$pg")
  if [ "$r" = ok ]; then p1ok=$((p1ok+1)); else p1bad=$((p1bad+1)); echo "  $pg -> $r"; fi
done < <(find $MODULES -name '*.php' | grep -viE 'execute|queries\.php' | sort)
echo "Phase 1: $p1ok ok, $p1bad fatals"

echo "## Phase 2 - POST detail pages with REAL ids"
AID=$(MYSQL "$DB" -N -e "SELECT accountID FROM Account LIMIT 1;")
SID=$(MYSQL "$DB" -N -e "SELECT studentID FROM Student WHERE status='active' LIMIT 1;")
for spec in "account/viewDetails.php|id=$AID" "account/viewDetails.php|id=$SID&newstudent=1"; do
  echo "  ${spec%%|*} (${spec#*|}) -> $(hit POST "${spec%%|*}" "${spec#*|}")"
done

echo "## Phase 3 - mutation pages under a DB snapshot (NONEXISTENT ids; restore after)"
# fresh user-owned snapshot file (never collide with a root-owned /tmp leftover)
SNAP="$(mktemp "${TMPDIR:-/tmp}/nlpk_sweep_snap.XXXXXX.sql")" || { echo "!! mktemp failed" >&2; exit 1; }
if ! MYSQLDUMP --triggers --routines --single-transaction "$DB" > "$SNAP" || [ ! -s "$SNAP" ]; then
  echo "!! snapshot failed; aborting BEFORE mutations (no restore would be possible)" >&2
  rm -f "$SNAP"; exit 1
fi
# nonexistent record ids => UPDATE/DELETE touch 0 real rows; still runs full PHP path
BODY="id=999999&studentID=999999&accountID=999999&employeeID=999999&fid=999999&newstudent=1&last_name=ZZSWEEP&first_name=ZZSWEEP&text_last_name=ZZSWEEP&amount=1&date%5B%5D=01&date%5B%5D=01&date%5B%5D=2020"
p3ok=0; p3bad=0
while IFS= read -r pg; do
  r=$(hit POST "$pg" "$BODY")
  if [ "$r" = ok ]; then p3ok=$((p3ok+1)); else p3bad=$((p3bad+1)); echo "  $pg -> $r"; fi
done < <(find $MODULES -name 'execute*.php' | sort)
echo "Phase 3: $p3ok ok, $p3bad fatals"
# restore: reload the snapshot into the SAME database. mysqldump emits
# DROP TABLE IF EXISTS + CREATE TABLE per table, so INSERT-page test rows are
# wiped without needing DROP DATABASE/GRANT (which nlpkuser lacks).
MYSQL "$DB" < "$SNAP"
echo "restored $DB ($(MYSQL "$DB" -N -e 'SELECT COUNT(*) FROM Student;') students); snapshot kept at $SNAP"
