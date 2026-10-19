# Tests

Übersicht über die automatisierten Tests des Buchungstools Itelfingen: wie man sie ausführt, wie sie aufgebaut sind und was sie absichern.

> **Beim Hinzufügen eines Tests bitte diese Datei ergänzen**, siehe [Neuen Test schreiben](#neuen-test-schreiben).

Stand 13.10.2026: **427 Testfälle in 39 Dateien** (67 Unit, 360 Feature), Laufzeit rund 12 Sekunden.
Aktuelle Zahlen: `vendor/bin/phpunit --list-tests`

## Inhalt

- [Tests ausführen](#tests-ausführen)
- [Aufbau](#aufbau)
- [Übersicht aller Tests](#übersicht-aller-tests)
- [Fachliche Regeln, die die Tests festhalten](#fachliche-regeln-die-die-tests-festhalten)
- [Neuen Test schreiben](#neuen-test-schreiben)

---

## Tests ausführen

```bash
vendor/bin/phpunit                                        # alle Tests
vendor/bin/phpunit --testsuite Unit                       # nur Unit-Tests (ohne Datenbank, unter 1 Sekunde)
vendor/bin/phpunit tests/Feature/ContactTest.php          # eine Datei
vendor/bin/phpunit --filter test_contact_form             # einzelne Tests
```

Mit Sail: `./vendor/bin/sail test` bzw. `./vendor/bin/sail php vendor/bin/phpunit`.

- **Datenbank:** immer SQLite im Arbeitsspeicher, nie die Datenbank aus der `.env`. `tests/TestCase.php` bricht ab, falls die Config gecacht ist und auf MySQL zeigt.
- **Externe Dienste:** `Http::preventStrayRequests()` in `TestCase`. Bexio, Glutz und Healthchecks werden nie wirklich aufgerufen; Tests, die sie brauchen, setzen `Http::fake()`. Der Google-Kalender läuft nicht über `Http`: `TestCase` ersetzt `GoogleCalendarService` durch `Tests\Support\FakeGoogleCalendar`. Google Sheets und Slack (DailyTask) per Facade-Mock (`Sheets::shouldReceive`, `Slack::shouldReceive`), Slack-Benachrichtigungen entfallen mangels Webhook.
- **CI:** `.github/workflows/tests.yml` mit den Jobs `pint` und `phpunit` (PHP 8.3), bei Push auf `develop` und bei jedem Pull Request. Braucht keine `.env`-Werte und keine Datenbank.
- **Code-Stil:** Laravel Pint (`pint.json`, Preset `laravel`): `vendor/bin/pint --test` prüft, `vendor/bin/pint` korrigiert. Der Git-Hook `.githooks/pre-commit` formatiert gestagte PHP-Dateien beim Commit; `composer install` aktiviert ihn (`git config core.hooksPath .githooks`). Teilweise gestagte PHP-Dateien blockieren den Commit. VS Code formatiert beim Speichern (`.vscode/settings.json`, Erweiterung "Laravel Pint").
- **git blame:** Der Formatierungs-Commit steht in `.git-blame-ignore-revs`. GitHub berücksichtigt das automatisch, lokal einmal `git config blame.ignoreRevsFile .git-blame-ignore-revs`.

---

## Aufbau

### Konfiguration

| Datei | Zweck |
|---|---|
| `phpunit.xml` | SQLite in-memory, feste Werte statt `.env` (auch `SPREADSHEET_ID`, `GOOGLE_CALENDAR_ID`), Mail/Cache/Session/Queue im Speicher, alle Werte mit `force="true"`, feste Test-Werte für `ADMIN_PASSWORD`, `API_TOKEN` |
| `tests/UnitTestCase.php` | Basis für Unit-Tests, die Laravel brauchen (config, Validator), aber keine Datenbank; eine DB-Abfrage bricht den Test ab |
| `tests/TestCase.php` | `RefreshDatabase` mit `TestingSeeder`, Schutz vor echter DB, `withoutVite()`, keine echten HTTP-Aufrufe, Google-Kalender gefakt, temporäres `storage/` mit Platzhalter-PDFs (die Vorlagen liegen nicht im Repo; Parkkarten landen so nie im echten storage) |
| `database/seeders/TestingSeeder.php` | `BasisdatenSeeder` (Rollen, Status, Homepage 1, Admin mit ID 1) plus Preisliste mit den Codes 20, 30, 40, 50, 210, 220, 250, 300 |

### Testdaten: Szenario

`tests/Support/BookingScenario.php`: `$s = BookingScenario::create();`

| Eigenschaft | Inhalt |
|---|---|
| `$s->admin`, `$s->verwalter`, `$s->team`, `$s->gast` | je ein aktiver Benutzer mit dieser Rolle |
| `$s->registered` | selbst registriert: ohne Rolle, inaktiv |
| `$s->event` | neue Buchung in der Zukunft mit Positionen |
| `$s->confirmedEvent` | bestätigt, Rechnung erstellt, Verwalter zugeteilt, mit Checkliste |
| `$s->cancelledEvent`, `$s->pastEvent` | storniert bzw. vergangen |
| `$s->room`, `$s->checkpoint`, `$s->eventRoom`, `$s->eventCheckpoint` | Checkliste der bestätigten Buchung |
| `$s->contact`, `$s->application` | offene Kontaktanfrage und Bewerbung |

`$s->user('team')` liefert den Benutzer frisch aus der DB (für `actingAs` pro Request). `BookingScenario::roles()` ist für Data Provider gedacht.

### Factories

| Factory | Standard | States / Hilfen |
|---|---|---|
| `User::factory()` | aktiv, ohne Rolle, Passwort `password` | `admin()`, `verwalter()`, `team()`, `gast()`, `inactive()` |
| `Event::factory()` | neue Buchung (Status neu / offen) in der Zukunft, mit UUID | `confirmed()`, `cancelled()`, `past()` |
| `Contact::factory()` | offene Anfrage | |
| `Application::factory()` | Bewerbung | |
| `Room::factory()`, `Checkpoint::factory()` | aktiv | |
| `EventRoom::factory()`, `EventCheckpoint::factory()` | offen | |

---

## Übersicht aller Tests

| Bereich | Datei | Fälle | Prüft |
|---|---|---:|---|
| Unit | `Unit/OfferAmountTest` | 11 | Mengenregel der Offerte (`EventOfferCreateListener::offerAmount`) |
| Unit | `Unit/ExternalBookingParserTest` | 19 | Parser Anfrage-Mail und Buchungs-PDF: Zeilenenden, Namen, Anrede, Adressen, fehlende Felder |
| Unit | `Unit/UserRoleTest` | 10 | Rollenstufen pro Rolle und aktiv/inaktiv, unbekannte Stufe |
| Unit | `Unit/ModelHelpersTest` | 7 | Buchungsnummer, Ablauf des Bexio-Tokens (60 s Puffer) |
| Unit | `Unit/EnumsTest` | 9 | Regeln der Status-Enums (Reihenfolge, belegt, Rechte der Rollen) |
| Unit | `Unit/ImageUploadTest` | 11 | Bildendung aus dem Inhalt, PHP/SVG/HTML abgelehnt |
| Smoke | `Smoke/AllRoutesSmokeTest` | 6 | Jede GET-Route pro Rolle ohne Serverfehler |
| Smoke | `Smoke/PagesRenderTest` | 4 | Jeder Menü-Link pro Rolle lädt (200), öffentliche Seiten |
| Grundlagen | `EnumTablesTest` | 2 | Enum-Werte = Zeilen der Status-Tabellen, Rollen-Flags = Enum-Rechte |
| Grundlagen | `FormPreselectionTest` | 10 | Auswahlfelder wählen den gespeicherten Wert vor (Status, Rolle, Archiv) |
| Grundlagen | `StatusDisplayTest` | 17 | Statusspalte, Schritte im Kundenbereich, Monatsmail, leere Status |
| Grundlagen | `TestInfrastructureTest` | 11 | Test-DB, Schutz vor echter DB, App-Start ohne DB, Seeder, Szenario, Startseite, Dashboard |
| Öffentlich | `CreateMultiDayEventTest` | 13 | Buchungsformular |
| Öffentlich | `ContactTest` | 5 | Kontaktformular |
| Öffentlich | `ApplicationTest` | 5 | Bewerbung Genossenschaft |
| Öffentlich | `PublicApiTest` | 9 | Ortssuche, API-Antworten (Kalender, Verfügbarkeit, Telefonnummer) |
| Login | `AuthTest` | 6 | Login; Registrierung, Passwort-Reset und -Bestätigung abgeschaltet |
| Admin | `ChecklistAdminTest` | 3 | Räume und Checkpunkte bearbeiten |
| Admin | `AdminApplicationTest` | 4 | Bewerbung anzeigen (Kontakt, Status, Verlauf), ablehnen, Escaping |
| Admin | `AdminEventFormLayoutTest` | 3 | Buchung erstellen/bearbeiten: keine Bootstrap-Klassen, alle Felder/IDs für das JavaScript vorhanden |
| Admin | `BookingCalendarTest` | 12 | Kalender auf "Buchung erstellen/bearbeiten": ±1 Woche, halbe Tage, Nachbarbuchungen verlinkt, Stornierte ausgeblendet, Vorschau mit Formulardaten (nicht gespeichert, validiert, nur Verwalter) |
| Admin | `NewsletterTest` | 3 | Newsletter-Empfänger bearbeiten und löschen |
| Admin | `MaintenanceTest` | 10 | Wartungsseite: Migrationen, Cache, nur Admin, nur POST |
| Kundenbereich | `CustomerBookingPageTest` | 4 | Buchungsübersicht mit Bexio-Links, auch wenn Bexio fehlt |
| Kundenbereich | `CustomerLoginTest` | 5 | Login mit Buchungsnummer und PLZ, Fehlermeldung, Rate-Limit |
| Fachlogik | `EventWorkflowTest` | 15 | Buttons 1-6: Offerte, Rechnung, Versand über Bexio; Putzfirma-Mail |
| Fachlogik | `DoorCodeTest` | 6 | Türcode in Glutz (JSON-RPC) |
| Fachlogik | `ScheduledTasksTest` | 15 | Einplanung und Inhalt der täglichen, wöchentlichen und monatlichen Tasks |
| Fachlogik | `NotificationMailsTest` | 17 | Jede Mail wird gebaut und versendet: Betreff, Empfänger, Anhänge |
| Fachlogik | `ExternalBookingImportTest` | 7 | Externe Buchung aus Anfrage-Mail oder Buchungs-PDF übernehmen |
| Fachlogik | `ExportsTest` | 3 | CSV-Exporte Buchungen und Newsletter, Newsletter-Import |
| Sicherheit | `Security/RoleAccessTest` | 97 | Jede Admin-Route pro Rolle (inkl. kein Serverfehler), Navigation |
| Sicherheit | `Security/UserManagementTest` | 28 | Benutzer: Admin alle, sonst nur eigenes Profil; Validierung Benutzername/Passwort |
| Sicherheit | `Security/PublicFormRateLimitTest` | 5 | Öffentliche Formulare: 5 pro Minute, 20 pro Stunde je IP |
| Sicherheit | `Security/SecurityHeadersTest` | 5 | X-Frame-Options, nosniff, Referrer-/Permissions-Policy, HSTS nur Prod/HTTPS |
| Fachlogik | `BexioTokenTest` | 5 | Tokens verschlüsselt, Migration für Altbestand, Erneuerung mit Sperre |
| Sicherheit | `Security/XssTest` | 5 | Eingaben aus öffentlichen Formularen in Admin-Tabellen |
| Sicherheit | `Security/DeployRouteTest` | 2 | Alte Wartungs-URLs `/deploy` und `/bexio/connect` sind weg |
| Sicherheit | `Security/ApiTokenTest` | 14 | API-Token |
| Sicherheit | `Security/CustomerChecklistTest` | 5 | Checkliste im Kundenbereich nur für die eigene Buchung |
| Sicherheit | `Security/BexioOAuthTest` | 6 | Bexio verbinden (nur Admin, Wartungsseite) mit `state` |
| Sicherheit | `Security/ImageUploadTest` | 9 | Bild-Uploads nach `public/images` |

### Grundlagen

**[`TestInfrastructureTest`](Feature/TestInfrastructureTest.php)**: die Test-Infrastruktur selbst.

| Test | Prüft |
|---|---|
| `test_runs_against_sqlite_in_memory` | Tests laufen gegen SQLite in-memory |
| `test_refuses_to_refresh_a_real_database` | `TestCase` bricht ab, wenn die Verbindung nicht SQLite in-memory ist |
| `test_booting_the_app_does_not_connect_to_the_database` | App-Start ohne Datenbank (composer install, Docker-Build, CI) |
| `test_mysql_connection_gets_german_month_names` | `lc_time_names = 'de_CH'` beim Aufbau einer MySQL-Verbindung (Monatsnamen in den Graphen) |
| `test_seeder_creates_master_data` | Rollen, Homepage 1, Admin, Preisliste |
| `test_scenario_creates_users_with_expected_rights` | Rollen im Szenario, Positionen der Buchung |
| `test_database_is_reset_between_tests` | DB wird zwischen Tests zurückgesetzt |
| `test_home_page_renders` | Startseite über den ganzen Stack |
| `test_admin_dashboard_renders_for_admin` | Dashboard für Admin |

### Smoke

**[`AllRoutesSmokeTest`](Feature/Smoke/AllRoutesSmokeTest.php)**: liest alle GET-Routen aus dem Router und ruft jede als anonym, registriert (ohne Rolle), Gast, Team, Verwalter und Admin auf; erwartet keinen Status >= 500. Jede Route zusätzlich mit Randfällen (FAQ und Person ohne Foto, stornierte Buchung ohne Positionen, Benutzer ohne Rolle). Bexio ist verbunden, aber gefakt.
- **Neue Route mit Parameter:** Wert in `routeParameters()` ergänzen (der Test sagt welcher fehlt).
- `EXCLUDED`: bewusst nicht aufgerufene Routen mit Grund. `KNOWN_ISSUES`: bekannte Serverfehler; der Test wird rot, wenn ein neuer dazukommt oder ein bekannter behoben ist.
- Findet nur Abstürze. Berechtigungen prüft `Security/RoleAccessTest`.

**[`PagesRenderTest`](Feature/Smoke/PagesRenderTest.php)**: liest die Links aus der Admin-Navigation pro Rolle (Team 5, Verwalter 10, Admin 20) und erwartet für jede 200; dazu die öffentlichen Seiten.

### Öffentliche Formulare

**[`CreateMultiDayEventTest`](Feature/CreateMultiDayEventTest.php)**: Buchungsformular auf der Startseite (`POST event/create`).

| Test | Prüft |
|---|---|
| `test_multi_day_booking_creates_event_with_positions` | Buchung mit Status neu/offen, Telefonnummer für die Suche, Positionen (Hausmiete, Personen, Reinigung), Benachrichtigung |
| `test_marketing_comment_is_stored_and_shown_to_manager` | "Wie sind Sie auf uns aufmerksam geworden?" wird gespeichert und in der Buchung angezeigt |
| `test_booking_form_cannot_set_internal_fields` | Türcode, interne Notiz, Bexio-IDs, Status usw. lassen sich nicht von aussen setzen |
| `test_one_day_booking_uses_day_rate` | Tagesbuchung: Start = Ende, 1 Tag, Positionen 20/50/210 |
| `test_invalid_zipcode_is_rejected` | PLZ ausserhalb 1000-9999 |
| `test_more_than_19_people_is_rejected` | maximal 19 Personen |

**[`ContactTest`](Feature/ContactTest.php)**: Kontaktformular (`POST contacts`).

| Test | Prüft |
|---|---|
| `test_contact_form_stores_message_and_notifies` | Nachricht gespeichert, offen, Benachrichtigung |
| `test_contact_form_cannot_set_internal_fields` | `done` und `user_id` lassen sich nicht von aussen setzen |
| `test_message_without_whitespace_is_rejected_as_spam` | Nachricht ohne Leerzeichen gilt als Spam (422) |
| `test_invalid_email_is_rejected` | ungültige E-Mail |

**[`ApplicationTest`](Feature/ApplicationTest.php)**: Bewerbung für die Genossenschaft (`POST applications/store`).

| Test | Prüft |
|---|---|
| `test_application_page_renders` | Formularseite |
| `test_application_is_stored_and_notified` | Bewerbung gespeichert, Benachrichtigung |
| `test_application_form_cannot_set_internal_fields` | Ablehnung, Rechnung, Bexio-IDs lassen sich nicht von aussen setzen |
| `test_reason_without_whitespace_is_rejected_as_spam` | Begründung ohne Leerzeichen gilt als Spam (422) |

**[`PublicApiTest`](Feature/PublicApiTest.php)**: fachliche Antworten von Ortssuche und API.

| Test | Prüft |
|---|---|
| `test_city_search_returns_matches` | Ortssuche im Buchungsformular |
| `test_city_search_without_matches_returns_empty_entry` | ohne Treffer leerer Eintrag (war ein 500) |
| `test_events_api_lists_upcoming_own_bookings_only` | Kalender-API: künftige, nicht stornierte, nicht externe Buchungen |
| `test_events_api_marks_confirmed_bookings_as_occupied` | bestätigt = `occupied`, sonst `tentative` |
| `test_is_free_ignores_new_and_cancelled_bookings` | neue und stornierte Buchungen blockieren nicht |
| `test_is_free_is_false_with_confirmed_booking` | bestätigte Buchung blockiert |
| `test_phone_number_lookup_finds_booking_by_local_and_international_number` | 079…, +4179…, 79… |
| `test_phone_number_lookup_does_not_treat_input_as_pattern` | `%` und `_` sind keine Platzhalter |
| `test_phone_number_lookup_requires_enough_digits` | mindestens 6 Ziffern |

**[`AuthTest`](Feature/AuthTest.php)**: Login mit Benutzername, falsches Passwort, Eingeloggte werden von Login/Registrierung weggeleitet, Selbst-Registrierung wartet auf Freischaltung (ohne Rolle, inaktiv).

**[`ChecklistAdminTest`](Feature/ChecklistAdminTest.php)**: Checkpunkt bearbeiten (Ansicht war eine Kopie der FAQ-Ansicht), Raum bearbeiten.

**[`NewsletterTest`](Feature/NewsletterTest.php)**: Empfänger bearbeiten (Ansicht nutzte noch `Form::` aus Laravel Collective), Checkboxen Buchungen/Genossenschaft, löschen.

**[`MaintenanceTest`](Feature/MaintenanceTest.php)**: Wartungsseite `admin/maintenance` zeigt offene Migrationen; Migrationen ausführen und Cache leeren nur per POST und nur als Admin; Ausgabe wird angezeigt. Artisan ist gefakt.

**[`CustomerBookingPageTest`](Feature/CustomerBookingPageTest.php)**: Kundenbereich `bookings/{uuid}` fragt Bexio nur, wenn Offerte/Rechnung existieren, zeigt deren Links und lädt auch, wenn Bexio nicht erreichbar oder nicht verbunden ist.

### Fachlogik

**[`EventWorkflowTest`](Feature/EventWorkflowTest.php)**: Formular "Buchung bearbeiten" mit den echten Button-Werten. 1 speichert (nur Positionen dieser Buchung), 2 legt Kontakt und Offerte in Bexio an (Mengenregeln siehe unten), 3 versendet die Offerte, 4 erstellt die Rechnung (Buchung bestätigt, Google-Kalender, Checkliste, Newsletter), 5 versendet die Rechnung, 6 Info Genossenschaft. Ein Test liest alle Buttons aus der View in jedem Vertragsstatus und prüft, dass der Controller sie kennt.

**[`DoorCodeTest`](Feature/DoorCodeTest.php)**: Besucher in Glutz anlegen bzw. wiederverwenden, Code setzen, Hauseingang vom Anreisetag 00:00 bis Abreisetag 23:59 freigeben; vergebener Code, ungültiger Code, Glutz nicht erreichbar (JSON-Fehler 502).

**[`ScheduledTasksTest`](Feature/ScheduledTasksTest.php)**: Einplanung (Cron-Ausdrücke) und Lauf ohne Daten. Täglich: letzte Infos 2 Wochen vor Anreise (nur bestätigt mit Türcode, einmal), Rechnung für Bewerbungen nach 2 Wochen (Bexio, Google Sheet, Newsletter), Slack-Hinweis 2 Tage vorher, Glutz-Besucher 3 Tage nach Abreise löschen. Abends: Feedback-Mail einmal. Wöchentlich/monatlich: Inhalt der Übersichtsmails. Slack und Google Sheets sind per Facade-Mock ersetzt.

**[`NotificationMailsTest`](Feature/NotificationMailsTest.php)**: alle 11 Benachrichtigungen über den array-Mailer, mit Anhängen (Hausordnung, Rechnung, Kalender-Einladung, Infos, Parkkarte); Türcode in den letzten Infos; externe Buchungsnummer im Betreff. Eigenes Storage-Verzeichnis mit Platzhalter-PDFs (die Vorlagen liegen nicht im Repo).

**[`ExternalBookingImportTest`](Feature/ExternalBookingImportTest.php)**: `admin/events/parse` mit anonymisierten Beispielen beider Formate (Anfrage-Mail mit Tabs, Buchungs-PDF mit typografischen Bindestrichen). Leere Felder bleiben leer (statt die nächste Zeile zu nehmen), fehlende Mietdauer führt nicht zu einem Fehler, Feldnamen zählen nur am Zeilenanfang, der Kommentar reicht bis zum Ende. Der Name aus der Anfrage-Mail wird geteilt (letztes Wort = Nachname). Ablauf: zuerst Anfrage-Mail, später Buchungs-PDF; das Formular übernimmt den Kommentar in die Bemerkungen (angehängt, nicht überschrieben).

**[`ExportsTest`](Feature/ExportsTest.php)**: `Excel::fake()`; Buchungsexport ohne Stornos und ohne Personendaten, Newsletter getrennt nach Buchungen und Mitgliedern, Import ohne Duplikate.

**[`CustomerLoginTest`](Feature/CustomerLoginTest.php)**: Buchungsnummer (auch mit führenden Nullen) und PLZ führen zur Buchung; Fehlermeldung erscheint; höchstens 3 Versuche pro Buchung und Stunde.

### Sicherheit

**[`RoleAccessTest`](Feature/Security/RoleAccessTest.php)**: setzt die Berechtigungstabelle unten durch. Jede Route steht mit ihrer Mindeststufe (`team` < `verwalter` < `admin`) in `pages()` bzw. `actions()`; **neue Admin-Routen dort eintragen**.

| Test | Prüft |
|---|---|
| `test_page_access_follows_role_table` | Seiten pro Rolle: erlaubt (kein 403/Login, kein Serverfehler) oder 403 |
| `test_actions_are_forbidden_below_required_role` | Schreibende Routen geben unterhalb der Stufe 403 und verändern nichts |
| `test_guests_are_redirected_to_login` | Nicht eingeloggt: Weiterleitung zum Login |
| `test_navigation_shows_only_allowed_entries` | Menü zeigt pro Rolle nur erlaubte Einträge |
| `test_team_sees_bookings_without_edit_controls` | Buchungsseite für Team ohne "Buchung erstellen" und Bemerkungsfelder |
| `test_registered_user_without_role_is_forbidden` | Selbst registriert, ohne Rolle: 403 statt Absturz |
| `test_inactive_team_member_is_forbidden` | Deaktivierte Benutzer haben keinen Zugang |

**[`UserManagementTest`](Feature/Security/UserManagementTest.php)**: Benutzerverwaltung.

| Test | Prüft |
|---|---|
| `test_non_admin_can_edit_own_profile` | Team/Verwalter ändern Name, Telefon, Passwort |
| `test_non_admin_cannot_change_own_role_or_status` | Rolle und Status bleiben |
| `test_non_admin_cannot_change_admin_password` | Fremde Profile: 403 |
| `test_non_admin_cannot_create_or_delete_users` | Anlegen und Löschen: 403 |
| `test_non_admin_cannot_download_foreign_signature` | Fremde Unterschrift: 403 |
| `test_admin_creates_user_with_role` | Admin legt Benutzer mit Rolle an |
| `test_admin_create_form_posts_to_store_route` | Formular schickt an `users.store` (war ein 405) |
| `test_admin_creates_user_with_signature` | Anlegen mit Unterschrift (war ein 500) |
| `test_admin_deactivates_user` | Archiv-Status setzt `is_active` (wirkte vorher nicht) |
| `test_admin_changes_role_of_other_user` | Rolle ändern |
| `test_admin_deletes_user` | Löschen |
| `test_empty_password_keeps_old_password` | Leeres Passwort ändert nichts |

**[`XssTest`](Feature/Security/XssTest.php)**: Namen, Gruppe, Fremdschlüssel aus Buchung, Kontakt und Bewerbung werden in den Admin-Tabellen escaped; die Buchungstabelle liefert nur ihre Spalten (kein Türcode, keine Bexio-IDs, keine UUID); Team bekommt keine Bearbeiten-Links.

**[`PublicFormRateLimitTest`](Feature/Security/PublicFormRateLimitTest.php)**: Buchung, Kontakt und Bewerbung teilen sich das Limit "public-forms" (AppServiceProvider) pro IP. Die Formulare validieren vor dem Zugriff auf Felder; leere Anfragen ergeben Fehlermeldungen statt 500.

**[`SecurityHeadersTest`](Feature/Security/SecurityHeadersTest.php)**: Middleware `SecurityHeaders` (global). Bewusst ohne Content-Security-Policy wegen Inline-JavaScript.

**[`BexioTokenTest`](Feature/BexioTokenTest.php)**: `access_token`/`refresh_token` mit Cast `encrypted`; die Migration `encrypt_bexio_tokens` verschlüsselt Klartext-Zeilen (mehrfach ausführbar). Erneuerung unter `Cache::lock('bexio-token-refresh')` mit erneuter Prüfung, weil Bexio den Refresh-Token rotiert; simuliert wird ein zweiter Prozess, der inzwischen erneuert hat.

**[`DeployRouteTest`](Feature/Security/DeployRouteTest.php)**: die früheren Wartungs-URLs `/deploy` (migrierte per GET) und `/bexio/connect` geben 404; ersetzt durch die Wartungsseite. Artisan ist gefakt.

**[`ApiTokenTest`](Feature/Security/ApiTokenTest.php)**: alle API-Routen mit gültigem Token erreichbar, mit falschem 401 ohne den Token zurückzugeben; ohne `API_TOKEN` gesperrt (früher Standardwert `1234`); Telefonnummer-Suche.

**[`CustomerChecklistTest`](Feature/Security/CustomerChecklistTest.php)**: Gäste haken nur Checkpoints ihrer eigenen Buchung ab (`POST bookings/{uuid}/checkpoint/done`).

**[`BexioOAuthTest`](Feature/Security/BexioOAuthTest.php)**: Bexio verbinden nur als Admin über `admin/bexio/connect` (Knopf und Verbindungsstatus auf der Wartungsseite); der Callback nimmt nur einen Code mit dem `state` der eigenen Weiterleitung an, einmalig, und leitet zurück zur Wartungsseite. Bexio ist mit `Http::fake()` ersetzt.

**[`ImageUploadTest`](Feature/Security/ImageUploadTest.php)**: FAQ, Geschichte und Bilder speichern nur echte Bilder unter einem Namen des Servers (`App\Helper\ImageUpload`). Läuft in einem Temp-Verzeichnis statt `public/`.

---

## Status und Rollen: Enums

Die festen IDs aus dem früheren `config/status.php` sind PHP-Enums in `app/Enums` und als Casts an den Models:

| Enum | Spalte | Werte |
|---|---|---|
| `ArchiveState` | `archive_status_id` (Inhalte, Räume, Checkpunkte, Preisliste) | Aktiv 1, Archiviert 2 |
| `UserRole` | `users.role_id` | Administrator 1, Team 2, Gast 3, Verwalter 5 |
| `BookingState` | `events.event_status_id` | Neu 5, Bestaetigt 10, Eigene 20, Storniert 50 |
| `ContractState` | `events.contract_status_id` | Offen 5, AngebotErstellt 7, AngebotVersendet 10, RechnungErstellt 20, RechnungVersendet 30, Storniert 50 |

- Reihenfolge über Methoden: `ContractState::isAtLeast()`, `isBefore()`, `before()`; `BookingState::isOccupied()`, `notCancelled()`. Nie `<`/`>`/`==` mit einem Enum und einer Zahl.
- Leere Spalten: `$event->bookingState()` und `$event->contractState()` fallen auf Neu bzw. Offen zurück (wie früher `null < x`).
- In Request-Daten von Tests `->value`, in Models, Abfragen und Assertions das Enum.
- Die Select-Komponente wandelt Enums für die Vorauswahl um (`FormPreselectionTest`).
- Rechte der Rollen kommen aus `UserRole` (keine Abfrage der Tabelle `roles`); `EnumTablesTest` prüft, dass sie zu den Flags `is_admin`/`is_team` passen.

## Fachliche Regeln, die die Tests festhalten

### Berechtigungen (abgestimmt am 10.10.2026, festgehalten in `RoleAccessTest`)

| Bereich | Admin | Verwalter | Team |
|---|---|---|---|
| Dashboard, Graphen | ✅ | ✅ | ✅ |
| Buchungen ansehen (Kalender, Liste) | ✅ | ✅ | ✅ |
| Buchungen bearbeiten, erstellen, löschen, Mails, Türcode | ✅ | ✅ | ❌ |
| Kontakte, Bewerbungen | ✅ | ✅ | ❌ |
| Inhalte (Homepage, FAQ, Bilder, Preise, Personen, Testimonials) | ✅ | ❌ | ❌ |
| Zimmer und Checkpoints | ✅ | ✅ | ❌ |
| Newsletter, Exporte | ✅ | ✅ | ❌ |
| Eigenes Profil | ✅ | ✅ | ✅ |
| Alle Benutzer verwalten, Rollen vergeben | ✅ | ❌ | ❌ |

Umsetzung: Middleware `role:team|verwalter|admin` (`App\Http\Middleware\Role`) auf den Routengruppen in `routes/web.php`; `User::hasRoleLevel()`. Eingeloggte ohne Recht bekommen 403, Gäste die Login-Seite.

Wer diese Regeln ändert, muss auch die Tests anpassen.

### Offerte: Mengen pro Position (EventOfferCreateListener, festgehalten in EventWorkflowTest)

Bestätigt am 10.10.2026.

| Position (bexio_code) | Menge in der Offerte |
|---|---|
| Hausmiete (20) | wie in der Buchung erfasst |
| Personen/Nutzung (21-200) | erfasste Menge × Anzahl Nächte |
| Parkplätze (210) | (erfasste Menge − 3 inbegriffene, mindestens 0) × Anzahl Nächte |
| Frühbezug, Spätabgabe (220) | fix, je 1 für Early Check-In und Late Check-Out (setzt "Buchung updaten" aus den Checkboxen) |
| übrige Zusatzkosten (> 200) | fix, wie erfasst |
| Menge 0 | Position entfällt |
| Rabatt | nur auf Positionen ab Code 100, nicht auf Frühbezug/Spätabgabe (220, bestätigt am 19.10.2026) |

Die Anzeige in den Formularen rechnet gleich (`resources/js/libs/pricing.js`).

---

## Neuen Test schreiben

1. Ort wählen: `tests/Unit` für reine Logik ohne Datenbank und HTTP (Regeln, Parser, Hilfsmethoden), `tests/Feature/Security` für Berechtigungen, `tests/Feature/Smoke` für seitenweite Prüfungen, sonst `tests/Feature`.
2. Unit: von `PHPUnit\Framework\TestCase` erben, oder von `Tests\UnitTestCase`, wenn `config()` oder der Validator gebraucht wird. Feature: von `Tests\TestCase` erben, Testdaten mit `BookingScenario` oder den Factories.
3. Bei Fehlern zuerst den Test (rot), dann den Fix.
4. Methoden `test_<englisch_snake_case>`, Erklärungen auf Deutsch.
5. Externe Dienste nie wirklich aufrufen (`Http::fake()`, `Notification::fake()`, `Mail::fake()`).
6. Öffentliche Formulare: Spam-Schutz mit `config(['honeypot.enabled' => false])` ausschalten und separat testen.
7. Neue Admin-Route: in `RoleAccessTest::pages()` bzw. `actions()` mit Mindeststufe eintragen.
8. Uploads: `UploadedFile::fake()` leitet den MIME-Typ aus dem Dateinamen ab. Für Inhaltsprüfungen eine echte Temp-Datei verwenden (siehe `ImageUploadTest::realUpload()`).
9. Diese Datei ergänzen.

### Vorlage für einen neuen Eintrag

```markdown
**[`NeuerTest`](Feature/NeuerTest.php)**: wofür der Test zuständig ist.

| Test | Prüft |
|---|---|
| `test_...` | ... |
```
