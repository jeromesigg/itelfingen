<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

/**
 * Externe Buchung aus Text übernehmen (POST admin/events/parse, Feld "Import Buchung" bei externen Buchungen).
 * Zwei Formate: Anfrage-Mail ("Kommentar der Gruppe") und Buchungs-PDF. Beispiele anonymisiert.
 */
class ExternalBookingImportTest extends TestCase
{
    private const MAIL_TEXT = "Anfragenummer   \t440713\n"
        ."Datum\tDo 12.11.2026 - Fr 13.11.2026                                               \n"
        ."Name\tVorname Nachname\n"
        ."Gruppenart\tSeminar/Firmenanlass\n"
        ."Anzahl\tca. 12 Personen\n"
        ."Verpflegung\tSelbstkocher\n"
        ."Sprache\tfranzösisch\n"
        ."Kommentar der Gruppe                \tIch würde gerne wissen, ob das Haus von Donnerstag, dem 12.11.26 ab 08:30 Uhr bis Freitag, dem 13.11.26, um 12:00 Uhr verfügbar ist.";

    // Aus dem PDF kopiert: mit typografischen Bindestrichen (U+2010)
    private const PDF_TEXT = "Name : Frau Vorname Nachname\n"
        ."Dorfstrasse 12\n"
        ."CH‐8003 Zürich\n"
        ."Gruppe : Gruppe test\n"
        ."Telefon :\n"
        ."Mobil : 079 123 45 56\n"
        ."E‐Mail : mail@mail.com\n"
        ."Mietdauer : So 04.10.2026 ‐ Sa 10.10.2026 = 6 Nächte\n"
        ."Anzahl Personen : 10\n"
        .'Verpflegung : Selbstkocher';

    private function parse(string $text)
    {
        return $this->actingAs(User::find(1))->postJson('/admin/events/parse', ['text' => $text]);
    }

    public function test_parses_inquiry_mail(): void
    {
        $this->parse(self::MAIL_TEXT)->assertOk()->assertJson([
            'buchungsnummer' => 'A-440713',
            'mietdauer_von' => '2026-11-12',
            'mietdauer_bis' => '2026-11-13',
            'vorname' => 'Vorname',
            'name' => 'Nachname',
            'gruppe' => 'Seminar/Firmenanlass',
            'anzahl_personen' => '12',
            'verpflegung' => 'Selbstkocher',
            'sprache' => 'französisch',
            'kommentar' => 'Ich würde gerne wissen, ob das Haus von Donnerstag, dem 12.11.26 ab 08:30 Uhr bis Freitag, dem 13.11.26, um 12:00 Uhr verfügbar ist.',
        ]);
    }

    public function test_mail_splits_name_at_last_word(): void
    {
        $double = str_replace("Name\tVorname Nachname", "Name\tAnna Maria Muster", self::MAIL_TEXT);
        $single = str_replace("Name\tVorname Nachname", "Name\tMuster", self::MAIL_TEXT);

        $this->parse($double)->assertJsonPath('vorname', 'Anna Maria')->assertJsonPath('name', 'Muster');
        $this->parse($single)->assertJsonPath('vorname', null)->assertJsonPath('name', 'Muster');
    }

    public function test_comment_may_contain_field_names(): void
    {
        // Feldnamen zählen nur am Zeilenanfang; im Kommentar dürfen sie vorkommen.
        $text = str_replace('verfügbar ist.', "verfügbar ist.\nAnzahl Parkplätze: 4, Name der Firma: Muster AG", self::MAIL_TEXT);

        $this->parse($text)->assertOk()
            ->assertJsonPath('kommentar', fn ($k) => str_ends_with($k, 'Anzahl Parkplätze: 4, Name der Firma: Muster AG'))
            ->assertJsonPath('anzahl_personen', '12');
    }

    public function test_parses_booking_pdf(): void
    {
        $this->parse(self::PDF_TEXT)->assertOk()->assertJson([
            'anrede' => 'Frau',
            'vorname' => 'Vorname',
            'name' => 'Nachname',
            'strasse' => 'Dorfstrasse',
            'hausnummer' => '12',
            'plz' => '8003',
            'stadt' => 'Zürich',
            'gruppe' => 'Gruppe test',
            'mobil' => '079 123 45 56',
            'email' => 'mail@mail.com',
            'mietdauer_von' => '2026-10-04',
            'mietdauer_bis' => '2026-10-10',
            'naechte' => '6',
            'anzahl_personen' => '10',
            'verpflegung' => 'Selbstkocher',
        ]);
    }

    public function test_empty_field_does_not_take_the_next_line(): void
    {
        // "Telefon :" ist leer. Bisher landete "Mobil : 079 …" im Telefonfeld; das Formular nimmt Telefon vor Mobil.
        $this->parse(self::PDF_TEXT)->assertOk()->assertJsonPath('telefon', null);
    }

    public function test_text_without_dates_does_not_fail(): void
    {
        // Ohne Mietdauer stürzte convertDate(null) ab (500); das Formular bleibt dann einfach ohne Daten.
        $text = str_replace("Mietdauer : So 04.10.2026 ‐ Sa 10.10.2026 = 6 Nächte\n", '', self::PDF_TEXT);

        $this->parse($text)->assertOk()->assertJsonPath('mietdauer_von', null)->assertJsonPath('name', 'Nachname');
    }

    public function test_requires_text(): void
    {
        $this->parse('')->assertUnprocessable();
    }
}
