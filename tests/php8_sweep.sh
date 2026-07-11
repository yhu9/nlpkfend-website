#!/usr/bin/env bash
# ---------------------------------------------------------------------------
# PHP 8 interaction sweep for the NLPK app.
#
# Drives the LIVE app with curl and reports any page that throws a PHP fatal,
# so PHP-5/7 -> PHP-8 regressions (removed funcs, count()/->format() on null,
# ArgumentCountError, mysqli-throws-on-error, reserved-word columns) surface
# without clicking through a browser.
#
# Run on the Apache/MySQL host (WSL) as a user that can read the Apache error
# log and reach MySQL over the socket -- i.e. root:
#     sudo bash tests/php8_sweep.sh
#
# Why it works: sessions are keyed by client IP (the `session` table), so we
# insert a loopback session row for a level-5 admin and drive from 127.0.0.1.
# ---------------------------------------------------------------------------
set -u
BASE="http://127.0.0.1"
LOG="${APACHE_LOG:-/var/log/apache2/error.log}"
DB="${DB:-NLPKDB}"
ADMIN="${ADMIN:-masahu}"          # MUST be a real admin row with level >= 5
DBUSER="${DBUSER:-nlpkuser}"      # app DB user (for the restore GRANT)
MODULES="employee student account scheduler income expenditure receipt log"

mysql_do(){ mysql "$DB" -e "$1"; }
session(){ mysql_do "REPLACE INTO session (username, ipaddress) VALUES ('$ADMIN','127.0.0.1');"; }

# hit METHOD PATH [POSTDATA] -> "ok" or "FATAL <message>"
# Uses the error-log byte offset before/after the request to attribute fatals.
hit(){
  local before err; before=$(wc -c < "$LOG")
  if [ "$1" = GET ]; then curl -s -o /dev/null "$BASE/$2"
  else curl -s -o /dev/null -X POST -d "$3" "$BASE/$2"; fi
  err=$(tail -c +$((before+1)) "$LOG" | grep -iE "php fatal|uncaught" | head -1)
  [ -n "$err" ] && echo "FATAL ${err##*] }" || echo ok
}

echo "## session as $ADMIN (level>=5)"; session

echo "## Phase 1 - GET every non-mutating page"
p1ok=0; p1bad=0
while IFS= read -r pg; do
  r=$(hit GET "$pg")
  if [ "$r" = ok ]; then p1ok=$((p1ok+1)); else p1bad=$((p1bad+1)); echo "  $pg -> $r"; fi
done < <(find $MODULES -name '*.php' | grep -viE 'execute|queries\.php' | sort)
echo "Phase 1: $p1ok ok, $p1bad fatals"

echo "## Phase 2 - POST detail pages with REAL ids"
AID=$(mysql "$DB" -N -e "SELECT accountID FROM Account LIMIT 1;")
SID=$(mysql "$DB" -N -e "SELECT studentID FROM Student WHERE status='active' LIMIT 1;")
for spec in "account/viewDetails.php|id=$AID" "account/viewDetails.php|id=$SID&newstudent=1"; do
  echo "  ${spec%%|*} (${spec#*|}) -> $(hit POST "${spec%%|*}" "${spec#*|}")"
done

echo "## Phase 3 - mutation pages under a DB snapshot (NONEXISTENT ids; restore after)"
SNAP=/tmp/nlpk_sweep_snap.sql
mysqldump --triggers --routines --single-transaction "$DB" > "$SNAP"
# nonexistent record ids => UPDATE/DELETE touch 0 real rows; still runs full PHP path
BODY="id=999999&studentID=999999&accountID=999999&employeeID=999999&fid=999999&newstudent=1&last_name=ZZSWEEP&first_name=ZZSWEEP&text_last_name=ZZSWEEP&amount=1&date%5B%5D=01&date%5B%5D=01&date%5B%5D=2020"
p3ok=0; p3bad=0
while IFS= read -r pg; do
  r=$(hit POST "$pg" "$BODY")
  if [ "$r" = ok ]; then p3ok=$((p3ok+1)); else p3bad=$((p3bad+1)); echo "  $pg -> $r"; fi
done < <(find $MODULES -name 'execute*.php' | sort)
echo "Phase 3: $p3ok ok, $p3bad fatals"
# restore from snapshot (backstop)
mysql -e "DROP DATABASE $DB; CREATE DATABASE $DB CHARACTER SET utf8; GRANT ALL PRIVILEGES ON $DB.* TO '$DBUSER'@'localhost';"
mysql "$DB" < "$SNAP"
echo "restored $DB ($(mysql "$DB" -N -e 'SELECT COUNT(*) FROM Student;') students); snapshot kept at $SNAP"
