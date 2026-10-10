# Tests

Übersicht über die automatisierten Tests des Buchungstools Itelfingen: wie man sie ausführt, wie sie aufgebaut sind und was sie absichern.

> **Beim Hinzufügen eines Tests bitte diese Datei ergänzen**, siehe [Neuen Test schreiben](#neuen-test-schreiben).

Stand 10.10.2026: **192 Testfälle in 15 Dateien**, Laufzeit rund 5 Sekunden.
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
vendor/bin/phpunit tests/Feature/ContactTest.php          # eine Datei
vendor/bin/phpunit --filter test_contact_form             # einzelne Tests
```

Mit Sail: `./vendor/bin/sail test` bzw. `./vendor/bin/sail php vendor/bin/phpunit`.

- **Datenbank:** immer SQLite im Arbeitsspeicher, nie die Datenbank aus der `.env`. `tests/TestCase.php` bricht ab, falls die Config gecacht ist und auf MySQL zeigt.
- **Externe Dienste:** `Http::preventStrayRequests()` in `TestCase`. Bexio, Glutz, Slack und Healthchecks werden nie wirklich aufgerufen; Tests, die sie brauchen, setzen `Http::fake()`.
- **CI:** `.github/workflows/tests.yml` mit den Jobs `pint` und `phpunit` (PHP 8.3), bei Push auf `develop` und bei jedem Pull Request. Braucht keine `.env`-Werte und keine Datenbank.
- **Code-Stil:** Laravel Pint (`pint.json`, Preset `laravel`): `vendor/bin/pint --test` prüft, `vendor/bin/pint` korrigiert. Der Git-Hook `.githooks/pre-commit` formatiert gestagte PHP-Dateien beim Commit; `composer install` aktiviert ihn (`git config core.hooksPath .githooks`). Teilweise gestagte PHP-Dateien blockieren den Commit. VS Code formatiert beim Speichern (`.vscode/settings.json`, Erweiterung "Laravel Pint").
- **git blame:** Der Formatierungs-Commit steht in `.git-blame-ignore-revs`. GitHub berücksichtigt das automatisch, lokal einmal `git config blame.ignoreRevsFile .git-blame-ignore-revs`.

---

## Aufbau

### Konfiguration

| Datei | Zweck |
|---|---|
| `phpunit.xml` | SQLite in-memory, Mail/Cache/Session/Queue im Speicher, alle Werte mit `force="true"`, feste Test-Werte für `ADMIN_PASSWORD`, `API_TOKEN`, `DEPLOY_*` |
| `tests/TestCase.php` | `RefreshDatabase` mit `TestingSeeder`, Schutz vor echter DB, `withoutVite()`, keine echten HTTP-Aufrufe |
| `database/seeders/TestingSeeder.php` | `BasisdatenSeeder` (Rollen, Status, Homepage 1, Admin mit ID 1) plus Preisliste mit den Codes 20, 30, 40, 50, 210 |

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
| Grundlagen | `TestInfrastructureTest` | 9 | Test-DB, Schutz vor echter DB, App-Start ohne DB, Seeder, Szenario, Startseite, Dashboard |
| Öffentlich | `CreateMultiDayEventTest` | 6 | Buchungsformular |
| Öffentlich | `ContactTest` | 4 | Kontaktformular |
| Öffentlich | `ApplicationTest` | 4 | Bewerbung Genossenschaft |
| Öffentlich | `PublicApiTest` | 9 | Ortssuche, API-Antworten (Kalender, Verfügbarkeit, Telefonnummer) |
| Login | `AuthTest` | 4 | Login, Registrierung |
| Admin | `ChecklistAdminTest` | 3 | Räume und Checkpunkte bearbeiten |
| Sicherheit | `Security/RoleAccessTest` | 93 | Jede Admin-Route pro Rolle (inkl. kein Serverfehler), Navigation |
| Sicherheit | `Security/UserManagementTest` | 17 | Benutzer: Admin alle, sonst nur eigenes Profil |
| Sicherheit | `Security/XssTest` | 5 | Eingaben aus öffentlichen Formularen in Admin-Tabellen |
| Sicherheit | `Security/DeployRouteTest` | 6 | Basic-Auth für `/deploy` und `/bexio/connect` |
| Sicherheit | `Security/ApiTokenTest` | 14 | API-Token |
| Sicherheit | `Security/CustomerChecklistTest` | 5 | Checkliste im Kundenbereich nur für die eigene Buchung |
| Sicherheit | `Security/BexioOAuthTest` | 4 | Bexio verbinden mit `state` |
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

**[`DeployRouteTest`](Feature/Security/DeployRouteTest.php)**: `/deploy` und `/bexio/connect` nur mit `DEPLOY_USER`/`DEPLOY_PASS`; ohne konfigurierte Werte gesperrt. Artisan ist gefakt, es wird nie migriert.

**[`ApiTokenTest`](Feature/Security/ApiTokenTest.php)**: alle API-Routen mit gültigem Token erreichbar, mit falschem 401 ohne den Token zurückzugeben; ohne `API_TOKEN` gesperrt (früher Standardwert `1234`); Telefonnummer-Suche.

**[`CustomerChecklistTest`](Feature/Security/CustomerChecklistTest.php)**: Gäste haken nur Checkpoints ihrer eigenen Buchung ab (`POST bookings/{uuid}/checkpoint/done`).

**[`BexioOAuthTest`](Feature/Security/BexioOAuthTest.php)**: der Callback nimmt nur einen Code mit dem `state` der eigenen Weiterleitung an, einmalig. Bexio ist mit `Http::fake()` ersetzt.

**[`ImageUploadTest`](Feature/Security/ImageUploadTest.php)**: FAQ, Geschichte und Bilder speichern nur echte Bilder unter einem Namen des Servers (`App\Helper\ImageUpload`). Läuft in einem Temp-Verzeichnis statt `public/`.

---

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

---

## Neuen Test schreiben

1. Ort wählen: `tests/Feature/Security` für Berechtigungen, `tests/Feature/Smoke` für seitenweite Prüfungen, sonst `tests/Feature`.
2. Von `Tests\TestCase` erben, Testdaten mit `BookingScenario` oder den Factories.
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
