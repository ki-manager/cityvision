# Redirect-Map (301)

Alle relevanten alten URLs der bisherigen Website werden per `config/redirects.php` mit HTTP-301 auf die neue Struktur weitergeleitet. Sprachvarianten der alten Website (`/en/...`, `/ar/...`, `/ru/...`) werden auf die jeweilige neue Sprachstartseite bzw. die deutsche Zielseite geleitet.

| Alte URL (www.kinderarzt-koenigslutter.de) | Neue URL | Hinweis |
|---|---|---|
| `/` | `/` | Startseite |
| `/unser-team` | `/team` | Team |
| `/unsere-leistungen` | `/leistungen` | Leistungen |
| `/kontakt` | `/kontakt` | Kontakt (identisch) |
| `/aktuelles` | `/aktuelles` | Aktuelles (identisch) |
| `/notdienste` | `/informationen` | Notdienste jetzt Bereich „Informationen" |
| `/galerie` | `/praxis` | Galerie entfällt; Praxisbilder künftig via Admin-Uploads (Rechte previously ungeklärt) |
| `/impressum` | `/impressum` | identisch |
| `/datenschutz` | `/datenschutz` | identisch, mit technischen Ergänzungen |
| `/corona-pandemie` | `/informationen` | Besuchsregelungen als wichtige News + Informationen-Seite |
| `/impfkalender-international` | `/impfungen` | Impfkalender-Angebot im Impfbereich (Hinweis der Altsite: „Änderungen werden derzeit eingepflegt!") |
| `/links` | `/informationen` | Restinhalte (Schatten-und-Licht-Link) → Informationen |
| `/wb-assistenten-/-famula` | `/praxis` | Weiterbildung/Famulatur als Praxis-Abschnitt |
| `/praktika-/-schnuppertage` | `/praxis` | Praktika als Praxis-Abschnitt |
| `/en` | `/en` | Englische Startseite (neu) |
| `/en/*` | `/en/*` | Entsprechende neue Sprachseite |
| `/ar` | `/ar` | Arabische Startseite (neu, RTL) |
| `/ar/*` | `/ar/*` | Entsprechende neue Sprachseite |
| `/ru/*` | `/` | Russisch in neuer Version zunächst nicht gepflegt (keine ungeprüften automatischen medizinischen Übersetzungen); Vorbereitung vorhanden |

## Umsetzung

- Die Map liegt in `config/redirects.php` und wird vom Router ausgewertet (`public/index.php`).
- Redirects sind 301 (permanent), damit Google die neuen URLs übernimmt.
- Die alten URL-Sonderzeichen (`/-`) werden URL-kodiert korrekt gematcht (`rawurldecode` im Router).
