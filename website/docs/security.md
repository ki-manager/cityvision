# Sicherheitskonzept

## Transport & Hosting

- HTTPS-Erzwingung per `.htaccess` (301) und zusätzlich in PHP (erkennt SSL-Proxies über `X-Forwarded-Proto`, daher keine Redirect-Schleifen).
- Kanonische Domain (www) per `APP_URL`; keine Parallel-Indexierung.
- `Options -Indexes` verhindert Directory Listing.

## Zugriffsschutz (`.htaccess`)

Gesperrt: `.env`, `composer.json/lock`, `*.sql`, `*.log`, `*.lock`, `*.md`, `*.example` sowie die Verzeichnisse `app/`, `config/`, `database/`, `storage/`, `vendor/`, `docs/`, `scripts/` (404 statt 403, keine Informationspreisgabe).

In `public/uploads/`: keine PHP-Ausführung (`php_flag engine off`, FilesMatch-Deny), kein ExecCGI, kein Listing.

## Eingaben & Ausgaben

- **SQL:** ausschließlich PDO-Prepared-Statements (`EMULATE_PREPARES = false`); keine dynamisch konkatenierten Queries mit Nutzereingaben.
- **XSS:** jede Ausgabe escapen (`Security::e()` = htmlspecialchars mit ENT_QUOTES/ENT_SUBSTITUTE, UTF-8); DB-Texte für Seiten/News durch `render_safe_text()` (Whitelist-Mini-Markdown, kein Roh-HTML aus der DB).
- **CSRF:** Token pro Session (`random_bytes(32)`), `hash_equals`-Vergleich, Pflicht bei jedem POST-Formular (Frontend + Admin).
- **Serverseitige Validierung:** Kontaktformular und Admin-Formulare prüfen Länge, Format (E-Mail, Datum, Zeit) serverseitig; zusätzlich HTML5-Validierung clientseitig.
- **Honeypot:** unsichtbares Feld `website` + Zeit-Mindestabstand (`ts` ≥ 3 s) im Kontaktformular.
- **Rate Limiting:** Login: max. 5 Fehlversuche pro 15 Minuten (pro Benutzer+IP-Hash, Tabelle `login_attempts`); Kontaktformular: Obergrenze pro Stunde.

## Sessions & Auth

- Session-Cookies: `HttpOnly`, `SameSite=Lax`, `Secure` (bei HTTPS), eigener Session-Name (`SESSION_NAME`).
- Session-ID-Regeneration bei Login und alle 30 Minuten.
- Passwörter nur als Hash (`password_hash`, PASSWORD_DEFAULT); nie im Klartext, nie in SQL-Dateien.
- Admin-Bereich: `AdminAuth::requireLogin()` vor jeder Aktion; Logout zerstört Session.
- Audit-Log (`audit_log`) für Login, CRUD-Aktionen, Einstellungsänderungen.

## Uploads

- Nur jpg/jpeg/png/webp/avif; MIME-Typ via `finfo`, echte Bildprüfung via `getimagesize`, Größenlimit (4 MB).
- Zufallsdateinamen (`bin2hex(random_bytes(16))`), Ablage in `public/uploads/` (ohne PHP-Ausführung).
- Schreibrechte minimal: nur `storage/logs`, `storage/cache`, `storage/temp`, `public/uploads` beschreibbar; kein 777.

## Fehlerbehandlung & Logging

- `APP_DEBUG=false` in Produktion: keine Stacktraces/technischen Details in der Ausgabe; stattdessen 500-Designseite.
- Fehler und sicherheitsrelevante Ereignisse in `storage/logs/` (Monatsdateien), nicht öffentlich abrufbar.
- Passwörter/Secrets erscheinen nie in Logs oder Admin-Anzeigen (SMTP-Test zeigt nur Host/Port).

## Datensparsamkeit

- Kontaktformular fragt keine sensiblen Gesundheitsdaten ab; deutlicher Hinweis „nicht für Notfälle".
- Keine externen Dienste automatisch geladen (Doctolib/OpenStreetMap nur als verlinkte Angebote); keine Tracker, keine Marketing-Cookies → kein Cookiebanner nötig (nur technisch notwendige Session-Cookies).

## TYPO3/WordPress-Übernahme

Nicht relevant – eigenständiger Code, keine Drittskripte im Frontend (vanilla JS ~1 KB, CSS ohne Framework).
