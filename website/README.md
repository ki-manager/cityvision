# Kinderarztpraxis Königslutter am Elm – Website

Vollständig neue, moderne Website für die **Kinderarztpraxis Königslutter am Elm**, entwickelt als schlankes PHP-/MySQL-Projekt für den Betrieb auf **goneo.de Shared Hosting** (Apache, PHP 8.2+, MySQL/MariaDB, `.htaccess`, PDO, PHPMailer).

Die Inhalte basieren vollständig auf der Analyse der bisherigen Website (siehe `docs/site-audit.md` und `docs/content-inventory.md`). Nicht vorhandene Informationen sind als `TODO: Inhalt durch Praxis bestätigen` markiert – es wurde nichts erfunden.

## Features

- **Responsives, freundliches Design** (Petrol/Türkis/Gelb, weiche Karten, kindgerechte Illustrationen, viel Weißraum), mobile Sticky Action Bar, RTL-Support für Arabisch
- **Adminbereich** (`/admin`): Aktuelles, Seiten, Team (inkl. Bild-Upload), Leistungen, Öffnungszeiten, Einstellungen, Kontaktanfragen, SMTP-Test, System-Check
- **Sicherheit:** PDO-Prepared-Statements, CSRF, XSS-Schutz, Honeypot, Rate-Limiting, Brute-Force-Schutz, sichere Sessions, Upload-Prüfung, geschützte `.env`/`storage`/`config`
- **SMTP** ausschließlich über PHPMailer (goneo: `smtp.goneo.de:587`, STARTTLS), Testfunktion im Adminbereich
- **SEO:** sprechende URLs, Canonical, OpenGraph, Schema.org (MedicalClinic, Physician, OpeningHoursSpecification), `sitemap.xml`, `robots.txt`, 301-Redirects aller alten URLs
- **Mehrsprachigkeit:** DE (default), EN, AR vorbereitet (`/en/…`, `/ar/…`, `dir="rtl"`); UI-Texte datenbankbasiert
- **Installer** (`/install`): DB-Test, Tabellen, Inhalte, Admin-Konto; sperrt sich selbst
- **Datenschutzfreundlich:** keine Tracker, keine externen Fonts, keine externen Karten ohne Klick, kein Cookiebanner (nur technisch notwendige Cookies)

## Schnellstart (lokal)

```bash
# Voraussetzungen: PHP 8.2+, MySQL/MariaDB
mysql -u root -e "CREATE DATABASE kinderarzt CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root kinderarzt < database/schema.sql
mysql -u root kinderarzt < database/seed.sql
cp .env.example .env   # DB-Zugangsdaten eintragen
php -S localhost:8090 -t public
# Installer: http://localhost:8090/install (Admin-Konto anlegen)
```

## Installation auf goneo

Ausführliche Schritt-für-Schritt-Anleitung: **[docs/goneo-deployment.md](docs/goneo-deployment.md)**

Kurz:

1. Domain mit Projektverzeichnis verbinden (Docroot auf Projektverzeichnis **oder** auf `public/` – beides unterstützt)
2. PHP ≥ 8.2 wählen, SSL aktivieren
3. Datenbank anlegen, `schema.sql` + `seed.sql` importieren
4. Dateien inkl. `vendor/` hochladen (Composer auf dem Server **nicht nötig**)
5. `.env` anlegen (Vorlage `.env.example`)
6. SMTP-Daten (`smtp.goneo.de`, Port 587, TLS) eintragen
7. Schreibrechte: `storage/logs`, `storage/cache`, `storage/temp`, `public/uploads`
8. Admin-Konto über `/install` anlegen
9. Website testen (`docs/final-check.md`)

## Projektstruktur

```
app/         Controller, Models, Services, Helpers, Views
config/      app.php, database.php, mail.php, redirects.php (301-Map)
database/     schema.sql, seed.sql
docs/        site-audit, content-inventory, architecture, database, security,
             goneo-deployment, redirect-map, final-check
public/      Document Root (index.php, assets, uploads, robots.txt, Favicons)
scripts/     cron.php (optional), create-admin.php (CLI)
storage/     logs/, cache/, temp/ (nicht öffentlich)
vendor/      phpmailer/phpmailer (LGPL-2.1)
```

## Dokumentation

- [docs/site-audit.md](docs/site-audit.md) – Bestandsaufnahme der alten Website
- [docs/content-inventory.md](docs/content-inventory.md) – übernommene Inhalte
- [docs/architecture.md](docs/architecture.md) – Architektur & Routing
- [docs/database.md](docs/database.md) – Datenbankmodell
- [docs/security.md](docs/security.md) – Sicherheitskonzept
- [docs/goneo-deployment.md](docs/goneo-deployment.md) – Deployment auf goneo
- [docs/redirect-map.md](docs/redirect-map.md) – 301-Weiterleitungen alter URLs
- [docs/final-check.md](docs/final-check.md) – Checkliste & offene Punkte

## Lizenzen

- Projektcode: proprietär (Praxis)
- PHPMailer (in `vendor/`): LGPL-2.1 (siehe `vendor/phpmailer/phpmailer/LICENSE`)
