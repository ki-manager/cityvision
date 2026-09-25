-- =====================================================================
-- Seed-Daten: Kinderarztpraxis Königslutter am Elm
-- Nur Inhalte aus der bestehenden Website (docs/content-inventory.md).
-- Fehlende Informationen: "TODO: Inhalt durch Praxis bestätigen"
-- Admin-Passwort wird NICHT hier gesetzt -> Installer /install oder
-- scripts/create-admin.php verwenden (password_hash).
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- settings
-- ---------------------------------------------------------------------
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('practice_name', 'Kinderarztpraxis Königslutter am Elm'),
('practice_owner', 'Andreas Clare'),
('street', 'Westernstraße 31'),
('zip_city', '38154 Königslutter am Elm'),
('phone', '05353 5511'),
('phone_intl', '+4953535511'),
('fax', '05353 8038'),
('email', 'TODO: Inhalt durch Praxis bestätigen'),
('doctolib_url', 'https://www.doctolib.de/praxis/koenigslutter/kinderarztpraxis-koenigslutter-am-elm'),
('languages_spoken', 'Deutsch, Englisch, Polnisch, Arabisch, Russisch'),
('emergency_note', 'Bei bedrohlichen Notfällen rufen Sie bitte den Notarzt unter Tel.: 112!'),
('footer_note', 'Copyright © 2026 Andreas Clare. Alle Rechte vorbehalten.'),
('og_image', 'assets/images/og-default.svg')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);

-- ---------------------------------------------------------------------
-- opening_hours (1=Mo ... 7=So) — wörtlich aus der Altsite
-- ---------------------------------------------------------------------
INSERT INTO `opening_hours`
(`weekday`, `morning_from`, `morning_to`, `afternoon_from`, `afternoon_to`, `closed`, `note`, `sort_order`) VALUES
(1, '08:00:00', '12:30:00', '15:00:00', '18:00:00', 0, '', 1),
(2, '08:00:00', '12:30:00', '15:00:00', '18:00:00', 0, '', 2),
(3, '08:00:00', '12:30:00', NULL, NULL, 0, 'nach Vereinbarung', 3),
(4, '08:00:00', '14:00:00', NULL, NULL, 0, 'nach Vereinbarung', 4),
(5, '08:00:00', '14:00:00', NULL, NULL, 0, '', 5),
(6, NULL, NULL, NULL, NULL, 1, '', 6),
(7, NULL, NULL, NULL, NULL, 1, '', 7)
ON DUPLICATE KEY UPDATE
  `morning_from` = VALUES(`morning_from`), `morning_to` = VALUES(`morning_to`),
  `afternoon_from` = VALUES(`afternoon_from`), `afternoon_to` = VALUES(`afternoon_to`),
  `closed` = VALUES(`closed`), `note` = VALUES(`note`), `sort_order` = VALUES(`sort_order`);

-- ---------------------------------------------------------------------
-- team (wörtlich aus der Altsite)
-- ---------------------------------------------------------------------
INSERT INTO `team` (`slug`, `name`, `role`, `role_type`, `specialty`, `qualifications`, `bio`, `sort_order`, `active`) VALUES
('andreas-clare', 'Andreas Clare',
 'Facharzt für Kinderheilkunde und Jugendmedizin', 'arzt',
 'Zusatzbezeichnung Neonatologie',
 'Studium der Humanmedizin an der Medizinischen Hochschule Hannover und dem Trinity College Dublin (1989–1995)\nAssistenzarzt – insgesamt acht Jahre: Kinderklinik, Städtisches Klinikum Braunschweig, bei Prof. Dr. G. Mau; Kinderklinik, Medizinische Hochschule Hannover, Abt. III, Kardiologie und Pädiatrische Intensivmedizin, bei Prof. Dr. A. Wessel\nOberarzt – insgesamt acht Jahre: Kinderklinik, Klinikum Wolfsburg, bei Prof. Dr. Sinnecker (OA für Neonatologie, päd. Intensivmedizin, Infektiologie und Diagnostik); Kinderklinik, Städtisches Klinikum Braunschweig, bei Prof. Dr. H. G. Koch (OA für Neonatologie, päd. Intensivmedizin und Kardiologie)\nMitgliedschaften: Deutsche Gesellschaft für Kinderheilkunde und Jugendmedizin (DGKJ); Gesellschaft für Neonatologie und Pädiatrische Intensivmedizin (GNPI); Gründungsmitglied Deutscher Rat für Wiederbelebung im Kindesalter\nProvider ALS-, NLS- und EPLS-Kurse; ehemaliger Ausbilder und Kursdirektor (European Resuscitation Council) für NLS- und EPLS-Kurse',
 'Zwei Kinder, eine Tochter (*2000) und ein Sohn (*2005).', 1, 1),
('salim-shammas', 'Salim Shammas',
 'Facharzt für Kinderheilkunde und Jugendmedizin (angestellter Facharzt)', 'arzt',
 'Kinderheilkunde und Jugendmedizin',
 'Studium der Humanmedizin an der Staatlichen Universität Belarus, Minsk (2002–2009)\nAssistenzarzt: Gesundheitsministerium Jordanien (2011–2014); Kinderklinik Klinikum Gifhorn (2014–2019 und 2021–2022); Kinderarztpraxis Königslutter (2020)\nMitgliedschaften: Deutsche Gesellschaft für Kinderheilkunde und Jugendmedizin (DGKJ); Jordan Medical Association',
 'Verheiratet, zwei Söhne (*2016, *2022).', 2, 1),
('k-klin', 'Frau K. Klin',
 'Medizinische Fachangestellte', 'mitarbeitende',
 'Impf- und Abrechnungsassistentin, Praxismanagement',
 NULL, NULL, 3, 1),
('n-schnalke', 'Frau N. Schnalke',
 'Medizinische Fachangestellte', 'mitarbeitende',
 'Impfassistentin, Qualitätsmanagement',
 NULL, NULL, 4, 1),
('t-dieling', 'Frau T. Dieling',
 'Pharmazeutisch-technische Assistentin', 'mitarbeitende',
 'Impfassistentin',
 NULL, NULL, 5, 1),
('j-leyder', 'J. Leyder',
 'Auszubildende*r zur Medizinischen Fachangestellten', 'mitarbeitende',
 'Auszubildende*r zur MFA',
 NULL, NULL, 6, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- ---------------------------------------------------------------------
-- services (Gruppen + Einzelleistungen, wörtlich aus der Altsite)
-- ---------------------------------------------------------------------
INSERT INTO `services` (`slug`, `group_key`, `title`, `description`, `icon`, `homepage`, `sort_order`) VALUES
('vorsorge-u2-bis-j2', 'vorsorge', 'Vorsorgeuntersuchungen U2 bis J2', 'Alle Vorsorgeuntersuchungen vom Neugeborenen (U2) bis zum Jugendlichen (J2).', 'baby', 1, 1),
('sprachstandsueberpruefung', 'vorsorge', 'Sprachstands-Überprüfung', 'Sprachstands-Überprüfung im Rahmen der Vorsorge.', 'chat', 1, 2),
('grob-feinmotorik-testung', 'vorsorge', 'Testung der Grob- und Feinmotorik', 'Testung der Grob- und Feinmotorik.', 'child', 0, 3),
('jugendarbeitsschutz', 'vorsorge', 'Jugendarbeitsschutzuntersuchungen', 'Jugendarbeitsschutzuntersuchungen.', 'clipboard', 0, 4),
('reisemedizinische-beratung', 'vorsorge', 'Reisemedizinische Beratung', 'Reisemedizinische Beratung.', 'globe', 0, 5),
('sporttauglichkeit', 'vorsorge', 'Untersuchungen der Sporttauglichkeit', 'Untersuchungen der Sporttauglichkeit.', 'sport', 0, 6),
('ultraschall', 'diagnostik', 'Ultraschall', 'Ultraschalluntersuchungen des Schädels inkl. der Dopplersonografie, der Bauchorgane inkl. Niere und ableitende Harnwege und der Säuglingshüfte.', 'scan', 1, 10),
('sehtest-hoertest', 'diagnostik', 'Sehtest, Hörtest, Farbprüfungen', 'Sehtest, Hörtest, Farbprüfungen und apparative Sehtestungen.', 'eye', 0, 11),
('labordiagnostik', 'diagnostik', 'Labordiagnostik', 'Labordiagnostik inkl. Stuhl und Urin.', 'lab', 0, 12),
('allergiediagnostik', 'diagnostik', 'Allergiediagnostik', 'Allergiediagnostik sowie Beratung und Durchführung von Desensibilisierungen.', 'allergy', 0, 13),
('lungenfunktion', 'diagnostik', 'Lungenfunktionsuntersuchung', 'Lungenfunktionsuntersuchung (Spirometrie) und Provokationstestungen.', 'lungs', 0, 14),
('asthma-neurodermitis', 'chronisch', 'Asthma- und Neurodermitis-Diagnostik und Therapie', 'Asthma- und Neurodermitis-Diagnostik und Therapie.', 'lungs', 1, 20),
('sozialpaediatric', 'diagnostik', 'Sozialpädiatrische Versorgung', 'Sozialpädiatrische Versorgung.', 'heart', 0, 15),
('stiko-impfungen', 'impfungen', 'Alle Impfungen gemäß der STIKO', 'Alle Impfungen gemäß der STIKO (auch bei den Eltern).', 'syringe', 1, 30),
('reiseimpfungen', 'impfungen', 'Reiseimpfungen', 'Reiseimpfungen (außer Gelbfieber).', 'globe', 0, 31),
('beratung-ausnahmesituationen', 'beratung', 'Beratung in besonderen Situationen', 'Beratung bei Ausnahmesituationen, Trauer und bei Mobbingerfahrungen, Schulproblemen.', 'chat', 1, 40),
('beratung-fruehgeborene', 'beratung', 'Beratung für Eltern frühgeborener Kinder', 'Spezielle Beratung für Eltern frühgeborener Kinder.', 'baby', 0, 41),
('sachverstaendigkeit-neonatologie', 'beratung', 'Sachverständigen-Tätigkeit Neonatologie', 'Sachverständigen-Tätigkeit im Bereich Neonatologie.', 'clipboard', 0, 42),
('ernaehrungsberatung', 'beratung', 'Ernährungsberatungen', 'Ernährungsberatungen.', 'food', 0, 43)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`), `description` = VALUES(`description`), `homepage` = VALUES(`homepage`), `sort_order` = VALUES(`sort_order`), `icon` = VALUES(`icon`), `group_key` = VALUES(`group_key`);

-- ---------------------------------------------------------------------
-- news (aus „Aktuelles" und „Corona-Pandemie" der Altsite)
-- ---------------------------------------------------------------------
INSERT INTO `news` (`title`, `slug`, `summary`, `content`, `publish_date`, `start_date`, `end_date`, `important`, `active`) VALUES
('Termine online buchen', 'termine-online-buchen',
 'Sie können ab sofort online Termine bei uns buchen. Wenn Sie keine Bestandspatienten sind, rufen Sie uns bitte beim ersten Mal an!',
 'Sie können ab sofort online Termine bei uns buchen (>Home / >Kontakt).\n\nWenn Sie keine Bestandspatienten sind, rufen Sie uns bitte beim ersten Mal an!',
 CURDATE(), NULL, NULL, 1, 1),
('Infektionen – Besuchsregelungen – Termine', 'infektionen-besuchsregelungen-termine',
 'Bitte begleiten Sie Ihr Kind nur mit einem Erwachsenen und rufen Sie bei Infektzeichen oder Fieber vorher an.',
 'Nach Wegfall der allgemeinen Maskenpflicht, besteht in der Praxis keine durchgehende Maskenpflicht mehr. Wir freuen uns über das freiwillige Tragen Ihrer Maske.\n\nWir bitten Sie, in jedem Fall vorher in der Praxis anzurufen, wenn Ihr Kind Infektzeichen oder Fieber hat.\n\nBitte begleiten Sie Ihr Kind mit nur einem Erwachsenen! Zur U2-U4 können Sie Ihr Kind mit zwei Erwachsenen begleiten – danach bitte, insbesondere bei Infekten, nur mit einer Begleitperson! Diese Regelung gilt seit Jahren, aufgrund der räumlichen Situation und des Infektionsrisikos!\n\nWir möchten Sie bitten, ihre Termine, insbesondere Vorsorge- und Impftermine einzuhalten. Wenn Terminverschiebungen notwendig sein sollten, werden wir Sie vorab informieren!\n\nSollten Sie einen Termin nicht einhalten können, sagen Sie uns bitte rechtzeitig Bescheid!\n\nBitte schicken Sie die Kinder nicht mit anderen Verwandten in die Praxis!',
 CURDATE(), NULL, NULL, 1, 1)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- ---------------------------------------------------------------------
-- pages (Basis-Seitentexte; inhaltlich aus dem Bestand)
-- ---------------------------------------------------------------------
INSERT INTO `pages` (`slug`, `locale`, `title`, `meta_description`, `content`) VALUES
('praxis', 'de', 'Unsere Praxis', 'Kinderarztpraxis Königslutter am Elm – Kinder- und Jugendmedizin mit Erfahrung, Zeit und Herz.',
 '## Kinder- und Jugendmedizin in Königslutter am Elm\n\nLiebe Patientinnen und Patienten, liebe Eltern, herzlich willkommen in der Kinderarztpraxis Königslutter am Elm!\n\nUnser Team unter der Leitung von Andreas Clare, Facharzt für Kinderheilkunde und Jugendmedizin mit Zusatzbezeichnung Neonatologie, und unserem angestellten Facharzt Salim Shammas betreut Kinder und Jugendliche vom Neugeborenen bis zum jungen Erwachsenen.\n\n## Schwerpunkte\n\n- Kinder- und Jugendmedizin\n- Neonatologie\n- Alle Vorsorgeuntersuchungen (U2 bis J2)\n- Impfungen gemäß STIKO\n\n## Gesprochene Sprachen\n\nWir sprechen in der Praxis: Deutsch, Englisch, Polnisch, Arabisch und Russisch.\n\n## Weiterbildung und Famulatur\n\nSie suchen eine Weiterbildungsstelle im Fach Pädiatrie? Wir freuen uns auf Ihren Anruf oder Ihre Bewerbung! Wir bilden das gesamte Spektrum der modernen ambulanten Kinderheilkunde ab.\n\nSie interessieren sich für eine interessante Famulatur? Dann nichts wie los zu uns! Sie lernen alle Basics vom Untersuchen eines Neugeborenen bis hin zur Desensibilisierung.\n\n## Praktika und Schnuppertage\n\nSie möchten gern bei uns ein Schülerpraktikum machen, einen Schnuppertag oder einen Girls-Day? Oder Sie überlegen, ob der Beruf der MFA etwas für Sie ist? Dann nichts, wie los – rufen Sie an oder bewerben Sie sich schriftlich!'),
('informationen', 'de', 'Informationen für Eltern', 'Alles Wichtige rund um den Praxisbesuch: Terminvorbereitung, Unterlagen, Notfälle und Notdienste.',
 '## Terminvorbereitung\n\nBitte bringen Sie zum Termin mit:\n\n- Impfpass\n- Versichertenkarte\n- Vorsorgeheft (gelbes Heft)\n- Medikamentenplan, falls vorhanden\n- Vorbefunde, falls vorhanden\n\nTODO: Inhalt durch Praxis bestätigen (Liste der Altsite nicht vollständig erfasst).\n\n## Akuter Krankheitsfall\n\nWir bitten Sie, in jedem Fall vorher in der Praxis anzurufen, wenn Ihr Kind Infektzeichen oder Fieber hat.\n\n## Besuchsregelungen\n\nBitte begleiten Sie Ihr Kind mit nur einem Erwachsenen! Zur U2-U4 können Sie Ihr Kind mit zwei Erwachsenen begleiten – danach bitte, insbesondere bei Infekten, nur mit einer Begleitperson!\n\n## Terminabsage\n\nSollten Sie einen Termin nicht einhalten können, sagen Sie uns bitte rechtzeitig Bescheid!\n\n## Rezepte und Überweisungen\n\nTODO: Inhalt durch Praxis bestätigen (Ablauf auf der Altsite nicht beschrieben).\n\n## Selbstzahler-Leistungen\n\nEinige der Leistungen werden nicht von den Krankenkassen ersetzt, wie: Schul- und Kindergartenbescheinigungen, Atteste z. B. für Gerichtsverfahren etc., längere schriftliche Stellungnahmen, spezielle Impfungen.\n\n## Notfälle und Notdienste\n\nBei bedrohlichen Notfällen rufen Sie bitte den Notarzt unter Tel.: 112!\n\nKinderärztlicher Notdienst:\n\n- Braunschweig – Städtisches Klinikum, Salzdahlumer Str. 90, 38126 Braunschweig, Tel.: 116 117. Mi: 16:00–22:00 Uhr, Sa+So+Feiertage: 10:00–20:00 Uhr.\n- Wolfsburg – Notdienst am Klinikum Wolfsburg, MVZ. Sa+So+Feiertage: 10–20 Uhr, Mi und Fr: 16–22 Uhr, Mo, Di, Do: 20–22 Uhr.\n- Magdeburg – Das Medico-Soziale Zentrum, Leipziger Straße 17a, 39112 Magdeburg wurde geschlossen! Bitte wenden Sie sich an die Kinderklinik, Tel.: 0391 6724190, oder die Zentrale Notaufnahme, Tel.: 0391 6721202.\n\nBedrohliche Notfälle:\n\n- Städtisches Klinikum Braunschweig, Tel.: 0531-5951325\n- Kinderklinik Klinikum Wolfsburg, Sauerbruchstr. 7, 38440 Wolfsburg, Tel.: 05361/801382\n\nZusätzliche Notdienste:\n\n- Apotheken-Notdienste: aktueller Dienstplan unter www.apothekerkammer-niedersachsen.de\n- Augenklinik Braunschweig: Tel. 0531 5952244\n- Giftnotrufzentrale Göttingen: Tel. 0551-19240\n- Hals-Nasen-Ohren-Klinik Braunschweig: Tel. 0531-5951215\n- Mund-Kiefer-Gesichtschirurgie Braunschweig: Tel. 0531 5953422\n- Psychiatrische Klinik BS: Tel. 0531-5952284\n- Psychiatrische Klinik für Kinder, Königslutter: Tel. 05353-900\n- Telefonseelsorge: Tel. 0800 1110111 (24h)\n- Unfallchirurgie: Tel. 0531-5951450')
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- ---------------------------------------------------------------------
-- translations (UI-Texte, de vollständig; en/ar Basis)
-- ---------------------------------------------------------------------
INSERT INTO `translations` (`locale`, `key`, `value`) VALUES
('de', 'nav.home', 'Startseite'),
('de', 'nav.praxis', 'Praxis'),
('de', 'nav.team', 'Team'),
('de', 'nav.leistungen', 'Leistungen'),
('de', 'nav.vorsorge', 'Vorsorge'),
('de', 'nav.impfungen', 'Impfungen'),
('de', 'nav.informationen', 'Informationen'),
('de', 'nav.kontakt', 'Kontakt'),
('de', 'cta.termin', 'Termin buchen'),
('de', 'cta.call', 'Praxis anrufen'),
('de', 'cta.route', 'Route planen'),
('de', 'hero.title', 'Gesund groß werden.'),
('de', 'hero.subtitle', 'Kinder- und Jugendmedizin mit Erfahrung, Zeit und Herz in Königslutter am Elm.'),
('de', 'quick.termin', 'Termin buchen'),
('de', 'quick.zeiten', 'Öffnungszeiten'),
('de', 'quick.akut', 'Akuter Krankheitsfall'),
('de', 'quick.rezepte', 'Rezepte & Überweisungen'),
('de', 'quick.anfahrt', 'Anfahrt'),
('de', 'quick.anruf', 'Praxis anrufen'),
('de', 'section.leistungen', 'Unsere Leistungen'),
('de', 'section.team', 'Unser Team'),
('de', 'section.aktuelles', 'Aktuelles aus der Praxis'),
('de', 'section.eltern', 'Informationen für Eltern'),
('de', 'footer.contact', 'Kontakt'),
('de', 'footer.times', 'Öffnungszeiten'),
('de', 'form.notfall', 'Das Kontaktformular ist nicht für medizinische Notfälle geeignet.'),
('en', 'nav.home', 'Home'),
('en', 'nav.praxis', 'Practice'),
('en', 'nav.team', 'Team'),
('en', 'nav.leistungen', 'Services'),
('en', 'nav.vorsorge', 'Check-ups'),
('en', 'nav.impfungen', 'Vaccinations'),
('en', 'nav.informationen', 'Information'),
('en', 'nav.kontakt', 'Contact'),
('en', 'cta.termin', 'Book appointment'),
('en', 'cta.call', 'Call practice'),
('en', 'cta.route', 'Directions'),
('en', 'hero.title', 'Growing up healthy.'),
('en', 'hero.subtitle', 'Pediatrics and adolescent medicine with experience, time and heart in Königslutter am Elm.'),
('ar', 'nav.home', 'الرئيسية'),
('ar', 'nav.praxis', 'العيادة'),
('ar', 'nav.team', 'الفريق'),
('ar', 'nav.leistungen', 'الخدمات'),
('ar', 'nav.vorsorge', 'الفحوصات الوقائية'),
('ar', 'nav.impfungen', 'التطعيمات'),
('ar', 'nav.informationen', 'معلومات'),
('ar', 'nav.kontakt', 'اتصل بنا'),
('ar', 'cta.termin', 'حجز موعد'),
('ar', 'cta.call', 'اتصل بالعيادة'),
('ar', 'cta.route', 'الاتجاهات'),
('ar', 'hero.title', 'النمو الصحي.'),
('ar', 'hero.subtitle', 'طب الأطفال والمراهقين بخبرة ووقت وقلب في كونيغسلوتر آم إلم.')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);
