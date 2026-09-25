# Architektur

## Übersicht

Moderne, schlanke PHP-Anwendung ohne Framework-Ballast, optimiert für goneo Shared Hosting (Apache, PHP 8.2+, MySQL/MariaDB, `.htaccess`, PDO, PHPMailer).

```
website/
├── app/
│   ├── Controllers/         PageController (Frontend), ContactController, Sitemap/Robots,
│   │   └── Admin/           Auth, Dashboard, News, Pages, Team, Services, OpeningHours,
│   │                        Settings, ContactRequests, SmtpTest, SystemCheck
│   ├── Models/              Database (PDO-Singleton), Setting, News, TeamMember, Service,
│   │                        OpeningHours, Page
│   ├── Services/            Mailer (PHPMailer), Upload, Schema (JSON-LD)
│   ├── Helpers/             Env, App, Security, Session, Response, View, Url
│   ├── Middleware/          AdminAuth (Login-Check, Audit)
│   ├── Views/               layouts/ (default, admin), partials/ (header, footer),
│   │                        pages/ (alle öffentlichen Seiten), admin/, errors/, install/
│   ├── bootstrap.php        Autoloader, Fehlerbehandlung, PHPMailer-Einbindung
│   ├── compat.php          globale Helferfunktionen (e(), url(), t(), render_safe_text())
│   └── Router.php           RegExp-Router mit Locale-Präfix (/, /en/, /ar/)
├── config/                  app.php, database.php, mail.php, redirects.php (301-Map)
├── database/                schema.sql, seed.sql
├── docs/                    Dokumentation
├── public/                  Document Root (index.php, assets/, uploads/, robots.txt, Favicons)
├── scripts/                 cron.php (optional), create-admin.php (CLI)
├── storage/                 logs/, cache/, temp/ (nicht öffentlich)
└── vendor/                  phpmailer/phpmailer (für FTP-Upload ohne Composer)
```

## Routing

- Front-Controller: alle Requests laufen über `public/index.php`.
- `.htaccess` (Root-Variante) leitet bei Webroot = Projektverzeichnis alles nach `public/` um; liegt der Docroot auf `public/`, greift `public/.htaccess`.
- Sprechende URLs: `/`, `/praxis`, `/team`, `/team/{slug}`, `/leistungen`, `/vorsorge`, `/impfungen`, `/informationen`, `/aktuelles`, `/kontakt`, `/impressum`, `/datenschutz`.
- Sprachpräfixe: `/en/...`, `/ar/...` (Arabisch mit `dir="rtl"`); Deutsch ohne Präfix.
- Alte URLs werden per `config/redirects.php` mit 301 weitergeleitet.
- HTTPS und www-Kanonisierung erfolgen doppelt abgesichert (`.htaccess` + PHP), ohne Redirect-Schleifen (SSL-Proxy via `X-Forwarded-Proto` erkannt).

## Request-Ablauf

1. `.htaccess` → `public/index.php`
2. `app/bootstrap.php`: Autoloading, `.env`-Ladung, Fehlerhandler (Details nur ins Log, 500-Seite im Design)
3. HTTPS/Host-Kanonisierung (301)
4. `Router::dispatch()` → Controller
5. Controller → Models (PDO, Prepared Statements) → View (`app/Views/`)
6. `View::renderWithLayout()` bettet Inhalt in `layouts/default.php` ein (Admin: `layouts/admin.php`)

## Besonderheiten

- **Kein Composer auf dem Server nötig:** PHPMailer liegt in `vendor/phpmailer/phpmailer/` und wird in `bootstrap.php` eingebunden; ein vorhandener `vendor/autoload.php` würde bevorzugt.
- **Caching:** Settings werden pro Request gecacht (Datei-Cache `storage/cache`, TTL 300 s) → wenige DB-Queries pro Seitenaufruf.
- **Mehrsprachigkeit:** UI-Strings aus `translations`-Tabelle; medizinische Inhalte werden nicht automatisch übersetzt.
- **Installer:** `/install` (DB-Test, schema.sql, seed.sql, Admin-Konto) sperrt sich nach Abschluss über `storage/installed.lock` selbst.
- **Fehlerseiten:** 403/404/500 im Websitedesign (404 mit freundlichem Teddy-Bär).
