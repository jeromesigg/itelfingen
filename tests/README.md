# Tests

Übersicht über die automatisierten Tests des Buchungstools Itelfingen: wie man sie ausführt, wie sie aufgebaut sind und was sie absichern.

> **Beim Hinzufügen eines Tests bitte diese Datei ergänzen**, siehe [Neuen Test schreiben](#neuen-test-schreiben).

Stand 10.10.2026: **14 Testfälle in 3 Dateien**, Laufzeit unter 1 Sekunde.
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
| Grundlagen | `TestInfrastructureTest` | 7 | Test-DB, Schutz vor echter DB, Seeder, Szenario, Startseite, Dashboard |
| Öffentlich | `CreateMultiDayEventTest` | 4 | Buchungsformular |
| Öffentlich | `ContactTest` | 3 | Kontaktformular |

### Grundlagen

**[`TestInfrastructureTest`](Feature/TestInfrastructureTest.php)**: die Test-Infrastruktur selbst.

| Test | Prüft |
|---|---|
| `test_runs_against_sqlite_in_memory` | Tests laufen gegen SQLite in-memory |
| `test_refuses_to_refresh_a_real_database` | `TestCase` bricht ab, wenn die Verbindung nicht SQLite in-memory ist |
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
| `test_one_day_booking_uses_day_rate` | Tagesbuchung: Start = Ende, 1 Tag, Positionen 20/50/210 |
| `test_invalid_zipcode_is_rejected` | PLZ ausserhalb 1000-9999 |
| `test_more_than_19_people_is_rejected` | maximal 19 Personen |

**[`ContactTest`](Feature/ContactTest.php)**: Kontaktformular (`POST contacts`).

| Test | Prüft |
|---|---|
| `test_contact_form_stores_message_and_notifies` | Nachricht gespeichert, offen, Benachrichtigung |
| `test_message_without_whitespace_is_rejected_as_spam` | Nachricht ohne Leerzeichen gilt als Spam (422) |
| `test_invalid_email_is_rejected` | ungültige E-Mail |

---

## Fachliche Regeln, die die Tests festhalten

### Berechtigungen (abgestimmt am 10.10.2026, Tests folgen)

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

Wer diese Regeln ändert, muss auch die Tests anpassen.

---

## Neuen Test schreiben

1. Ort wählen: `tests/Feature/Security` für Berechtigungen, `tests/Feature/Smoke` für seitenweite Prüfungen, sonst `tests/Feature`.
2. Von `Tests\TestCase` erben, Testdaten mit `BookingScenario` oder den Factories.
3. Bei Fehlern zuerst den Test (rot), dann den Fix.
4. Methoden `test_<englisch_snake_case>`, Erklärungen auf Deutsch.
5. Externe Dienste nie wirklich aufrufen (`Http::fake()`, `Notification::fake()`, `Mail::fake()`).
6. Öffentliche Formulare: Spam-Schutz mit `config(['honeypot.enabled' => false])` ausschalten und separat testen.
7. Diese Datei ergänzen.

### Vorlage für einen neuen Eintrag

```markdown
**[`NeuerTest`](Feature/NeuerTest.php)**: wofür der Test zuständig ist.

| Test | Prüft |
|---|---|
| `test_...` | ... |
```
