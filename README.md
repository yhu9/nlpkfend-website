# NLPK — Deploying to a Local Ubuntu Server

The internal database/admin web app for Northern Lights Preschool & Childcare.

**Stack:** plain PHP + MySQL (mysqli) served by Apache from `/var/www/html`. No build
system, framework, or package manager — `.php` files are served directly.

This guide deploys the whole app (website + database) onto a **single Ubuntu 22.04
machine on your own network** — a spare desktop, a mini-PC, or a local VM — running
Apache + PHP + MySQL. A single box is used because the app assumes a `localhost`
database (`db.ini`) and hardcodes the `/var/www/html` deploy path, so almost nothing
in the code needs to change.

> ⚠️ **Security baseline is weak:** `admin` passwords are stored in plaintext, SQL is
> built by string concatenation, and sessions are keyed by client IP. Keep this box on a
> trusted LAN, put the site behind HTTPS (Step 9), and never port-forward it to the
> public internet without hardening it first.

## Prerequisites

- A machine (physical or VM) with **Ubuntu Server 22.04 LTS** installed and reachable on
  your LAN. During install, enabling "OpenSSH server" makes the rest easier.
- The machine's LAN IP address — find it on the box with `ip -4 addr` (e.g. `192.168.1.50`).
  This is referred to as `SERVER_IP` below. A **static/reserved IP** is strongly
  recommended (set it in your router's DHCP reservations, or configure netplan) so the
  address doesn't change on reboot.
- Access to the database dump (`dumps/2026-07-06.sql`).

Commands are shown for bash on the server; the one local-machine command (copying the
dump) is shown for PowerShell.

## Step 1 — Log into the server

Sit at the machine directly, or SSH in from your local machine:

```powershell
ssh youruser@SERVER_IP
```

**Everything below runs on the server** unless it says otherwise.

## Step 2 — Install Apache, PHP, MySQL

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y apache2 mysql-server php libapache2-mod-php php-mysqli php-mbstring php-gd unzip git
sudo systemctl enable --now apache2 mysql
```

Visiting `http://SERVER_IP` from another machine on the LAN now should show the Apache
default page.

## Step 3 — Open the local firewall (if ufw is enabled)

Many Ubuntu Server installs run `ufw`. If it's active, allow web + SSH traffic:

```bash
sudo ufw allow OpenSSH
sudo ufw allow "Apache Full"     # opens ports 80 and 443
sudo ufw status
```

(If `ufw status` reports `inactive`, there's nothing to open — skip this step.)

## Step 4 — Deploy the code to `/var/www/html`

```bash
sudo rm -f /var/www/html/index.html          # remove Apache's placeholder
sudo git clone https://github.com/yhu9/nlpkfend-website.git /tmp/nlpk
sudo cp -r /tmp/nlpk/. /var/www/html/
sudo chown -R www-data:www-data /var/www/html
```

> `db.ini` and the `resources/nlp_data/account/**` uploads are gitignored/not in the
> repo — `db.ini` is set in Step 7, and any existing uploads can be `scp`'d over separately.

## Step 5 — Configure Apache (`SITE_HTMLROOT` env var + AllowOverride)

The app redirects using `$_SERVER["SITE_HTMLROOT"]` (in `config.php` and `session.php`)
and its `.htaccess` must be honored. Edit the default vhost:

```bash
sudo nano /etc/apache2/sites-available/000-default.conf
```

Inside the `<VirtualHost *:80>` block, add:

```apache
    SetEnv SITE_HTMLROOT "http://SERVER_IP"

    <Directory /var/www/html>
        AllowOverride All
        Require all granted
    </Directory>
```

(If you give the box a local hostname or add HTTPS in Step 9, change this to match —
e.g. `http://nlpk.local` or `https://nlpk.yourdomain.lan`.)

Enable modules and reload:

```bash
sudo a2enmod rewrite env
sudo systemctl restart apache2
```

## Step 6 — Create the database and import the dump

Copy the dump from your local machine (run **locally**, in a new PowerShell window):

```powershell
scp C:\Users\ynshe\Projects\dumps\2026-07-06.sql youruser@SERVER_IP:/tmp/nlpk.sql
```

Back **on the server**, create the DB + the app's user and import. Choose your own
values for `your_db_user` / `your_db_password` — they must match `db.ini` (Step 7) exactly:

```bash
sudo mysql <<'SQL'
CREATE DATABASE IF NOT EXISTS NLPKDB CHARACTER SET utf8;
CREATE USER IF NOT EXISTS 'your_db_user'@'localhost' IDENTIFIED BY 'your_db_password';
GRANT ALL PRIVILEGES ON NLPKDB.* TO 'your_db_user'@'localhost';
FLUSH PRIVILEGES;
SQL

sudo mysql NLPKDB < /tmp/nlpk.sql
```

Verify tables loaded, and confirm a known admin account exists:

```bash
sudo mysql NLPKDB -e "SHOW TABLES;"
sudo mysql NLPKDB -e "SELECT username, level FROM admin;"
```

> The dump is from MySQL 5.7; it imports cleanly into the 8.0 that ships with Ubuntu 22.04.

## Step 7 — Create `db.ini`

The app reads `db.ini` from the web root:

```bash
sudo tee /var/www/html/db.ini >/dev/null <<'INI'
dbhost=localhost
dbuser=your_db_user
dbpass=your_db_password
dbname=NLPKDB
INI
sudo chown www-data:www-data /var/www/html/db.ini
sudo chmod 640 /var/www/html/db.ini
```

> Use the same `your_db_user` / `your_db_password` you chose in Step 6. These are the
> **database** credentials only — never commit real values (that's why `db.ini` is gitignored).

## Step 8 — Test

From another machine on the LAN, open `http://SERVER_IP` → it should hit `index.html` →
`login.php`. Log in with an admin username/password from the `admin` table.

If a page errors, check the log:

```bash
sudo tail -n 50 /var/log/apache2/error.log
```

Remove the dev/diagnostic pages, which should not be public:

```bash
sudo rm -f /var/www/html/info.php /var/www/html/test.php
```

## Step 9 — (Recommended) HTTPS on the LAN

There's no public domain to validate against, so use a **self-signed certificate**.
Browsers will show a one-time "not trusted" warning that you accept (or import the cert
into trusted roots on each client machine).

```bash
sudo a2enmod ssl
sudo mkdir -p /etc/apache2/ssl
sudo openssl req -x509 -nodes -days 825 -newkey rsa:2048 \
  -keyout /etc/apache2/ssl/nlpk.key \
  -out /etc/apache2/ssl/nlpk.crt \
  -subj "/CN=SERVER_IP"
```

Edit the SSL vhost (`sudo nano /etc/apache2/sites-available/default-ssl.conf`), point it
at the cert, and add the same `SetEnv` + `<Directory>` block as Step 5 but with an
`https://` URL:

```apache
    SSLCertificateFile      /etc/apache2/ssl/nlpk.crt
    SSLCertificateKeyFile   /etc/apache2/ssl/nlpk.key
    SetEnv SITE_HTMLROOT "https://SERVER_IP"

    <Directory /var/www/html>
        AllowOverride All
        Require all granted
    </Directory>
```

Enable and reload:

```bash
sudo a2ensite default-ssl
sudo systemctl restart apache2
```

> If your LAN has an internal DNS name for the box, put that in `/CN=` and
> `SITE_HTMLROOT` instead of the raw IP — the cert warning is cleaner with a hostname.

## Step 10 — (Optional) Access the MySQL database from another LAN machine

Only do this if you need to connect from a desktop tool like MySQL Workbench on another
computer on the same network.

On the server, let MySQL listen on all interfaces:

```bash
sudo sed -i 's/^bind-address.*/bind-address = 0.0.0.0/' /etc/mysql/mysql.conf.d/mysqld.cnf
```

Create a separate remote user (keep the `localhost` one for the app), scoped to your LAN
subnet rather than the whole world:

```bash
sudo mysql <<'SQL'
CREATE USER 'NLPKadmin'@'192.168.1.%' IDENTIFIED WITH mysql_native_password BY 'CHOOSE_A_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON NLPKDB.* TO 'NLPKadmin'@'192.168.1.%';
FLUSH PRIVILEGES;
SQL
sudo systemctl restart mysql
```

If `ufw` is active, allow 3306 from the LAN subnet only:

```bash
sudo ufw allow from 192.168.1.0/24 to any port 3306
```

(Adjust `192.168.1.%` / `192.168.1.0/24` to match your actual LAN range.) Connect from
Workbench: host `SERVER_IP`, port `3306`, user `NLPKadmin`.

## Step 11 — (Optional) Start on boot / keep it running

Apache and MySQL were enabled with `systemctl enable` in Step 2, so they restart
automatically after a reboot or power loss. Confirm with:

```bash
systemctl is-enabled apache2 mysql
```

## Quick reference

| Item | Value |
|---|---|
| Server | Ubuntu 22.04, local machine at `SERVER_IP` |
| Web root | `/var/www/html` |
| Database | `NLPKDB` on `localhost`, user `your_db_user` / `your_db_password` |
| Dump | `dumps/2026-07-06.sql` → imported into `NLPKDB` |
| Must-set Apache env | `SITE_HTMLROOT` |
| Entry flow | `index.html → login.php → homepage.php` |
