# NLPK — Deploying to AWS Lightsail

The internal database/admin web app for Northern Lights Preschool & Childcare.

**Stack:** plain PHP + MySQL (mysqli) served by Apache from `/var/www/html`. No build
system, framework, or package manager — `.php` files are served directly.

This guide deploys the whole app (website + database) onto a **single Ubuntu 22.04
Lightsail instance** running Apache + PHP + MySQL. A single box is used because the app
assumes a `localhost` database (`db.ini`) and hardcodes the `/var/www/html` deploy path,
so almost nothing in the code needs to change.

> ⚠️ **Security baseline is weak:** `admin` passwords are stored in plaintext, SQL is
> built by string concatenation, and sessions are keyed by client IP. Always put the
> site behind HTTPS (Step 10), and think carefully before exposing MySQL to the public
> internet (Step 11) — prefer locking it to your own IP.
>
> Do **not** use the Bitnami "LAMP" blueprint: it serves from `/opt/bitnami/...` and
> fights the hardcoded `/var/www/html` paths. Use **OS-only Ubuntu 22.04**.

## Prerequisites (on your local machine)

- An AWS account.
- AWS CLI installed and configured: `aws configure` (access key, secret, region e.g. `us-west-2`).
- Access to the database dump (`dumps/2026-07-06.sql`).

Everything below can be done in the **Lightsail web console** or via CLI. Instance
creation shows both; the rest runs over SSH. Commands are shown for PowerShell locally
and bash on the server.

## Step 1 — Create the instance

**Console:** Lightsail → Create instance → Linux/Unix → **OS Only → Ubuntu 22.04 LTS** →
pick the **$10/mo (2 GB RAM)** plan (1 GB works but MySQL+Apache is tight) → name it
`nlpk-web` → Create.

**Or CLI:**

```powershell
aws lightsail create-instances `
  --instance-names nlpk-web `
  --availability-zone us-west-2a `
  --blueprint-id ubuntu_22_04 `
  --bundle-id small_3_0
```

(`small_3_0` = 2 GB; `micro_3_0` = 1 GB. Match the AZ to your configured region.)

## Step 2 — Give it a static public IP

```powershell
aws lightsail allocate-static-ip --static-ip-name nlpk-ip
aws lightsail attach-static-ip --static-ip-name nlpk-ip --instance-name nlpk-web
aws lightsail get-static-ip --static-ip-name nlpk-ip --query "staticIp.ipAddress" --output text
```

Note the IP it prints — referred to as `YOUR_IP` below.

## Step 3 — Open firewall ports

Ports 22 (SSH) and 80 (HTTP) are open by default. Add 443 (HTTPS):

```powershell
aws lightsail open-instance-public-ports `
  --instance-name nlpk-web `
  --port-info fromPort=443,toPort=443,protocol=TCP
```

## Step 4 — SSH into the instance

Easiest: Lightsail console → click the instance → **Connect using SSH** (browser terminal).

Or with the default key (console → Account → SSH keys):

```powershell
ssh -i C:\path\to\LightsailDefaultKey.pem ubuntu@YOUR_IP
```

**Everything from here runs on the server.**

## Step 5 — Install Apache, PHP, MySQL

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y apache2 mysql-server php libapache2-mod-php php-mysqli php-mbstring php-gd unzip git
sudo systemctl enable --now apache2 mysql
```

Visiting `http://YOUR_IP` now should show the Apache default page.

## Step 6 — Deploy the code to `/var/www/html`

```bash
sudo rm -f /var/www/html/index.html          # remove Apache's placeholder
sudo git clone https://github.com/yhu9/nlpkfend-website.git /tmp/nlpk
sudo cp -r /tmp/nlpk/. /var/www/html/
sudo chown -R www-data:www-data /var/www/html
```

> `db.ini` and the `resources/nlp_data/account/**` uploads are gitignored/not in the
> repo — `db.ini` is set in Step 9, and any existing uploads can be `scp`'d over separately.

## Step 7 — Configure Apache (`SITE_HTMLROOT` env var + AllowOverride)

The app redirects using `$_SERVER["SITE_HTMLROOT"]` (in `config.php` and `session.php`)
and its `.htaccess` must be honored. Edit the default vhost:

```bash
sudo nano /etc/apache2/sites-available/000-default.conf
```

Inside the `<VirtualHost *:80>` block, add:

```apache
    SetEnv SITE_HTMLROOT "http://YOUR_IP"

    <Directory /var/www/html>
        AllowOverride All
        Require all granted
    </Directory>
```

(When you add a domain + HTTPS in Step 10, change this to `https://your-domain.com`.)

Enable modules and reload:

```bash
sudo a2enmod rewrite env
sudo systemctl restart apache2
```

## Step 8 — Create the database and import the dump

Copy the dump from your local machine (run **locally**, in a new PowerShell window):

```powershell
scp -i C:\path\to\LightsailDefaultKey.pem `
  C:\Users\ynshe\Projects\dumps\2026-07-06.sql `
  ubuntu@YOUR_IP:/tmp/nlpk.sql
```

Back **on the server**, create the DB + the app's user (matching `db.ini` exactly) and import:

```bash
sudo mysql <<'SQL'
CREATE DATABASE IF NOT EXISTS NLPKDB CHARACTER SET utf8;
CREATE USER IF NOT EXISTS 'NLPKfend'@'localhost' IDENTIFIED BY 'NLPK6565';
GRANT ALL PRIVILEGES ON NLPKDB.* TO 'NLPKfend'@'localhost';
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

## Step 9 — Create `db.ini`

The app reads `db.ini` from the web root:

```bash
sudo tee /var/www/html/db.ini >/dev/null <<'INI'
dbhost=localhost
dbuser=NLPKfend
dbpass=NLPK6565
dbname=NLPKDB
INI
sudo chown www-data:www-data /var/www/html/db.ini
sudo chmod 640 /var/www/html/db.ini
```

## Step 10 — Test

Open `http://YOUR_IP` → it should hit `index.html` → `login.php`. Log in with an admin
username/password from the `admin` table.

If a page errors, check the log:

```bash
sudo tail -n 50 /var/log/apache2/error.log
```

Remove the dev/diagnostic pages, which should not be public:

```bash
sudo rm -f /var/www/html/info.php /var/www/html/test.php
```

## Step 11 — (Recommended) Domain + free HTTPS

Point a domain's A record at `YOUR_IP` (via a Lightsail DNS zone or your registrar), then:

```bash
sudo apt install -y certbot python3-certbot-apache
sudo certbot --apache -d your-domain.com
```

Then update the vhost's `SetEnv SITE_HTMLROOT "https://your-domain.com"` and
`sudo systemctl restart apache2`. Certbot auto-renews.

## Step 12 — (Optional) Access the MySQL database over the internet

Only do this if you need to connect from a desktop tool like MySQL Workbench, and
**restrict it to your own IP** — do not open 3306 to the world.

On the server, let MySQL listen on all interfaces:

```bash
sudo sed -i 's/^bind-address.*/bind-address = 0.0.0.0/' /etc/mysql/mysql.conf.d/mysqld.cnf
```

Create a separate remote user (keep the `localhost` one for the app):

```bash
sudo mysql <<'SQL'
CREATE USER 'NLPKadmin'@'%' IDENTIFIED WITH mysql_native_password BY 'CHOOSE_A_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON NLPKDB.* TO 'NLPKadmin'@'%';
FLUSH PRIVILEGES;
SQL
sudo systemctl restart mysql
```

Open port 3306 **only to your IP** (find yours at whatismyip.com):

```powershell
aws lightsail open-instance-public-ports `
  --instance-name nlpk-web `
  --port-info fromPort=3306,toPort=3306,protocol=TCP,cidrs=YOUR_HOME_IP/32
```

Connect from Workbench: host `YOUR_IP`, port `3306`, user `NLPKadmin`. To revoke access
later, use `close-instance-public-ports` with the same port info.

## Quick reference

| Item | Value |
|---|---|
| Instance | Ubuntu 22.04, Lightsail `nlpk-web` |
| Web root | `/var/www/html` |
| Database | `NLPKDB` on `localhost`, user `NLPKfend` / `NLPK6565` |
| Dump | `dumps/2026-07-06.sql` → imported into `NLPKDB` |
| Must-set Apache env | `SITE_HTMLROOT` |
| Entry flow | `index.html → login.php → homepage.php` |
