# Datenbank

## Allgemein

- MySQL 5.7+ / MariaDB 10.3+ (goneo-kompatibel), Zeichensatz `utf8mb4` / `utf8mb4_unicode_ci`.
- Installation: `database/schema.sql` (Tabellen), danach `database/seed.sql` (Inhalte).
- PDO mit `ERRMODE_EXCEPTION`, `FETCH_ASSOC`, `EMULATE_PREPARES = false` (echte Prepared Statements).

## Tabellen

| Tabelle | Zweck | Wichtige Felder |
|---|---|---|
| `users` | Admin-Benutzer | username, email, password_hash (password_hash/PASSWORD_DEFAULT), role (admin/editor), active, last_login_at |
| `login_attempts` | Brute-Force-Schutz | username, ip_hash (SHA-256), success, created_at |
| `settings` | Praxis-Konfiguration | setting_key, setting_value (Telefon, Fax, E-Mail, Doctolib, Sprachen, Notfall-Hinweis, Footer, og_image) |
| `pages` | editierbare Seitentexte | slug, locale, title, meta_description, content (Mini-Markdown: `##`, `- `) |
| `translations` | UI-Texte je Sprache | locale (de/en/ar, erweiterbar), key, value |
| `team` | Ärztinnen/Ärzte + Mitarbeitende | slug, name, role, role_type (arzt/mitarbeitende), specialty, qualifications, bio, photo, sort_order, active |
| `services` | Leistungen | slug, group_key (vorsorge/diagnostik/chronisch/impfungen/beratung), title, description, icon, homepage, sort_order, active |
| `opening_hours` | Sprechzeiten | weekday (1=Mo … 7=So), morning_from/to, afternoon_from/to, closed, note, sort_order |
| `news` | Aktuelles | title, slug, summary, content, publish_date, start_date, end_date (Anzeigezeitraum), important (Banner), active |
| `contact_requests` | Kontaktformular | firstname, lastname, email, phone, subject, message, status (neu/in_bearbeitung/erledigt/geloescht) |
| `media` | Upload-Registry | filename (Zufallsname), original_name, mime_type, size |
| `audit_log` | Admin-Aktionen | user_id, action, details, ip_hash |

## Sichtbarkeit von News

Aktiv sichtbar ist eine Meldung, wenn:
- `active = 1`
- `publish_date <= CURDATE()`
- (`start_date` ist NULL oder `start_date <= CURDATE()`)
- (`end_date` ist NULL oder `end_date >= CURDATE()`)

`important = 1` hebt die Meldung zusätzlich als farbiges Banner auf der Startseite hervor (max. 3).

## Seed-Daten

Ausschließlich gesichert übernommene Inhalte der bisherigen Website (siehe `docs/content-inventory.md`): Ärzte, Team, Leistungen, Öffnungszeiten, News, Basis-Seitentexte, UI-Übersetzungen. Fehlende Angaben sind als `TODO: Inhalt durch Praxis bestätigen` markiert. Kein Admin-Passwort in SQL – Admin-Konto nur über Installer oder `scripts/create-admin.php` (password_hash).
