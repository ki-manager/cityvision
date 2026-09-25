# Final Check / Deployment-Checkliste

Automatisch und manuell geprüfte Punkte (lokale Testinstallation: PHP 8.4, MariaDB 11.8, PHP-eigener Webserver).

## Technik

- [x] PHP funktioniert (8.4 lokal getestet; Code kompatibel mit 8.2+)
- [x] MySQL funktioniert (Schema + Seed fehlerfrei importiert)
- [x] PDO mit Prepared Statements (`EMULATE_PREPARES=false`)
- [x] Routing über `.htaccess`-Muster (Front-Controller) – lokal via PHP-Server getestet; `.htaccess`-Regeln für Apache in Root- und public-Variante vorhanden
- [x] HTTPS-Erzwingung (301, SSL-Proxy-sicher)
- [x] www-Kanonisierung über `APP_URL`
- [x] 404-Seite im Design (Teddy) – getestet (`/nichtda` → 404)
- [x] 403 (CSRF) und 500-Seiten vorhanden
- [x] Datei-Schutz: `.env`, `*.sql`, `*.log`, `config/`, `storage/`, `vendor/` per `.htaccess` gesperrt
- [x] Directory Listing deaktiviert (`Options -Indexes`)
- [x] `robots.txt` vorhanden (sperrt `/admin`, `/install`), `sitemap.xml` dynamisch mit hreflang
- [x] Admin-Bereich `noindex,nofollow`

## Frontend (getestet)

- [x] Startseite: Hero („Gesund groß werden."), Schnellzugriff (6 Karten), Leistungen (6 Karten aus DB), Team (2 Arztkarten), Aktuelles (aus DB, wichtige Meldung als Banner), Informationen für Eltern (Chips), Footer
- [x] Header mit Sprachen-Hinweis, Locale-Switch, Hamburger-Menü (Mobil), Termin-Button (Doctolib)
- [x] Navigation: alle Hauptseiten erreichbar (`/praxis`, `/team`, `/leistungen`, `/vorsorge`, `/impfungen`, `/informationen`, `/kontakt`, `/aktuelles`, `/impressum`, `/datenschutz`)
- [x] Team-Detailseite (`/team/andreas-clare`) mit vollständiger Biografie
- [x] Sprachversionen: `/en`, `/ar` (RTL), `/en/kontakt`, `/ar/kontakt`, `/en/team`, `/ar/leistungen`
- [x] Alte URLs: `/unser-team` → 301 `/team` (u. a., siehe redirect-map)
- [x] Mobile Sticky Action Bar (Anrufen | Termin | Route) per CSS auf Mobilgeräten
- [x] Druckansicht (`@media print`)

## Backend (getestet)

- [x] Installer `/install`: DB-Test, Tabellen anlegen, Seed, Admin-Konto → sperrt sich selbst
- [x] Admin-Login: falsches Passwort abgewiesen, Rate-Limit aktiv; korrekter Login → Dashboard
- [x] Logout; danach Admin geschützt (Redirect auf Login)
- [x] News anlegen (wichtig + aktiv), Liste, Bearbeiten, Toggle, Löschen
- [x] Team-Liste, Leistungen-Liste, Öffnungszeiten speichern (fix: Checkbox-Behandlung), Einstellungen speichern
- [x] System-Check (`/admin/system-check`): PHP-Version, DB, Schreibrechte, SMTP-Konfiguration, Extensions, HTTPS
- [x] Kontaktanfragen: Liste/Status/Löschen (leer getestet, Anfrage aus Formular sichtbar)
- [x] Audit-Log schreibt Login-Aktionen

## Formulare & Mail

- [x] Kontaktformular: Validierung (server + client), CSRF, Honeypot (Bot abgewiesen), Zeitsperre, Rate-Limit
- [x] Anfrage wird in DB gespeichert; SMTP-Versand vorbereitet (PHPMailer, `smtp.goneo.de:587` TLS)
- [ ] SMTP-Versand live mit goneo-Konto testen (vor Ort, da Zugangsdaten fehlen) → **Admin → SMTP-Test**
- [x] SMTP-Test-Funktion implementiert (Verbindung, STARTTLS, Auth, optionaler Testversand; keine Passwortanzeige)

## SEO

- [x] Individuelle Titel/Meta-Descriptions pro Seite
- [x] Canonical, OpenGraph (og:title, og:description, og:image, og:url), Twitter Card
- [x] Schema.org: MedicalClinic, Physician, PostalAddress, OpeningHoursSpecification (JSON-LD aus DB)
- [x] Sitemap mit hreflang (de/en/ar/x-default)
- [x] robots.txt (statisch + dynamische Variante)
- [x] Alt-Texte, semantisches HTML, Skip-Link, Fokuszustände

## Offene Punkte (durch Praxis zu klären)

- [ ] **E-Mail-Adresse:** `TODO: Inhalt durch Praxis bestätigen` (Altsite: „folgt zeitlang")
- [ ] **Praxisfotos/Portraits:** Rechte klären (Foto Teamseite: Uwe Brodmann); Platzhalter im Einsatz, Upload über Admin möglich
- [ ] **SMTP-Zugangsdaten** im goneo-Kundencenter anlegen und in `.env` eintragen
- [ ] **Rezepte & Überweisungen:** Ablauf (`TODO` auf der Informationen-Seite)
- [ ] **Terminvorbereitung-Liste** prüfen (TODO-Marker auf der Informationen-Seite)
- [ ] **Datenschutzerklärung:** rechtliche Prüfung der übernommenen Texte + technischen Ergänzungen
- [ ] **Doctolib-Link** auf Aktualität prüfen (Link aus Altsite/Doctolib-Profil übernommen)
- [ ] Live-Test auf goneo: `.htaccess`-Verhalten (Root-Variante), echte HTTPS-Zertifikatskette, Browser-Tests (Chrome, Edge, Firefox; Safari soweit möglich)
- [ ] Lighthouse-Messung auf Produktionsserver (Performance > 90, Accessibility > 95, Best Practices > 95, SEO > 95)
