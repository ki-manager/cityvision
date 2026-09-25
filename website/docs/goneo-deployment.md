# goneo-Deployment-Anleitung

## Voraussetzungen

- goneo Webhosting-Paket mit PHP 8.2+ und MySQL/MariaDB
- FTP/SFTP-Zugang (z. B. FileZilla) oder goneo-WebFTP
- Domain: `kinderarzt-koenigslutter.de`

## 1. Domain verbinden

1. Im goneo-Kundencenter: Domain dem Hosting-Paket zuordnen.
2. Als Ziel das Projektverzeichnis (z. B. `/htdocs/kinderarzt`) einstellen – **kein** separates `/public`-Ziel nötig (die Root-`.htaccess` routet automatisch nach `public/`).
   - Alternativ kann der Docroot direkt auf `/htdocs/kinderarzt/public` zeigen; auch das wird unterstützt.

## 2. PHP-Version wählen

Kundencenter → Hosting-Paket → PHP-Version: **8.2 oder höher** wählen.

## 3. SSL aktivieren

1. Kundencenter → Domain → SSL: Let's-Encrypt-Zertifikat aktivieren (goneo macht das automatisch).
2. HTTPS-Erzwingung ist in der `.htaccess` und in PHP eingebaut – nichts weiter nötig.

## 4. Datenbank anlegen

1. Kundencenter → Datenbanken → MySQL-Datenbank anlegen.
2. Notieren: **Host** (meist `localhost` oder `mysql5.goneo.de`), **Datenbankname**, **Benutzername**, **Passwort**.

## 5. Datenbank importieren

1. phpMyAdmin im goneo-Kundencenter öffnen.
2. `database/schema.sql` importieren (Tabellen).
3. `database/seed.sql` importieren (Inhalte der bisherigen Website).
   - Alternativ beides später über `/install` erledigen.

## 6. Dateien hochladen

Alle Projektdateien per FTP/SFTP in das Webverzeichnis hochladen (inkl. `vendor/`):

```
/htdocs/kinderarzt/
├── app/  config/  database/  public/  storage/  vendor/  scripts/  docs/
├── .htaccess  (Root-Variante, aktiv)
├── .env  (siehe Schritt 7 – erst nach Upload anlegen)
└── composer.json (optional; Composer wird auf dem Server NICHT benötigt)
```

## 7. `.env` erstellen

Im Webverzeichnis (nicht in `public/`) die Datei `.env` anlegen (Vorlage: `.env.example`):

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://www.kinderarzt-koenigslutter.de

DB_HOST=<goneo-DB-Host>
DB_PORT=3306
DB_DATABASE=<Datenbankname>
DB_USERNAME=<DB-Benutzer>
DB_PASSWORD=<DB-Passwort>
DB_CHARSET=utf8mb4

SMTP_HOST=smtp.goneo.de
SMTP_PORT=587
SMTP_USERNAME=<goneo-Postfach, z. B. praxis@kinderarzt-koenigslutter.de>
SMTP_PASSWORD=<Postfach-Passwort>
SMTP_ENCRYPTION=tls

MAIL_FROM=<Absenderadresse, identisch mit SMTP_USERNAME empfohlen>
MAIL_FROM_NAME="Kinderarztpraxis Königslutter"
MAIL_TO=<Empfängeradresse der Praxis>
```

## 8. SMTP eintragen

Siehe oben. Danach im Adminbereich unter **System-Check → SMTP-Test** (bzw. `admin/smtp-test`) die Verbindung testen: Verbindung, STARTTLS, Authentifizierung und optionaler Testversand werden geprüft und protokolliert – Passwörter werden nie angezeigt.

## 9. Schreibrechte prüfen

Beschreibbar sein müssen (per FTP auf 755 bzw. Dateien 644; **niemals 777**):

```
/storage/logs/
/storage/cache/
/storage/temp/
/public/uploads/
```

## 10. Admin-Konto erstellen

Entweder:

- **Installer:** `https://www.kinderarzt-koenigslutter.de/install` aufrufen → Tabellen/Seed prüfen (falls Schritt 5 übersprungen: dort importieren) → Admin-Konto anlegen. Der Installer sperrt sich danach selbst (`storage/installed.lock`).
- **oder** falls SSH/CLI verfügbar: `php scripts/create-admin.php <benutzer> <email> <passwort>`.

## 11. Website testen

Checkliste (Details: `docs/final-check.md`):

- Startseite, Navigation, alle Unterseiten
- Kontaktformular (Testnachricht + SMTP-Versand)
- Doctolib-Link
- Admin-Login, News/Team/Leistungen/Öffnungszeiten pflegen
- HTTPS (Schloss), www-Weiterleitung
- `robots.txt`, `sitemap.xml`
- Aufruf von `/.env` muss gesperrt sein (404/403)

## Staging

Für eine Testinstallation (z. B. `neu.kinderarzt-koenigslutter.de`): komplettes Projekt in ein eigenes Verzeichnis hochladen, separate Datenbank anlegen, `.env` anpassen:

```env
APP_ENV=staging
APP_DEBUG=false
APP_URL=https://neu.kinderarzt-koenigslutter.de
```

## Troubleshooting

| Problem | Lösung |
|---|---|
| Weiße Seite / 500 | `storage/logs/` prüfen; `APP_DEBUG=true` nur kurzzeitig zum Debuggen |
| DB-Verbindungsfehler | `.env`-Werte mit goneo-Kundencenter abgleichen (Host oft nicht `localhost`) |
| Mails kommen nicht an | SMTP-Test im Admin; Absender muss meist dem goneo-Postfach entsprechen |
| Bilder-Upload scheitert | Schreibrechte `public/uploads/` prüfen |
| Weiterleitung endlos | Prüfen, ob SSL-Proxy aktiv (Seite lädt nur mit https); `.env` `APP_URL` korrekt? |
| Installer sagt „bereits abgeschlossen" | `storage/installed.lock` löschen, um erneut zu installieren |
