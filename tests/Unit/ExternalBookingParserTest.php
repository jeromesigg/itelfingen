<?php

namespace Tests\Unit;

use App\Helper\Helper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Parser für externe Buchungen: zuerst Anfrage-Mail (parseFromMailText), später Buchungs-PDF (parseFromText).
 * Randfälle der Texte; der Ablauf über die Route steht in Feature/ExternalBookingImportTest.
 */
class ExternalBookingParserTest extends TestCase
{
    private const MAIL = "Anfragenummer   \t440713\n"
        ."Datum\tDo 12.11.2026 - Fr 13.11.2026      \n"
        ."Name\tVorname Nachname\n"
        ."Gruppenart\tSeminar/Firmenanlass\n"
        ."Anzahl\tca. 12 Personen\n"
        ."Verpflegung\tSelbstkocher\n"
        ."Sprache\tfranzösisch\n"
        ."Kommentar der Gruppe    \tIst das Haus frei?";

    private const PDF = "Name : Frau Vorname Nachname\n"
        ."Dorfstrasse 12\n"
        ."CH‐8003 Zürich\n"
        ."Gruppe : Gruppe test\n"
        ."Telefon :\n"
        ."Mobil : 079 123 45 56\n"
        ."E‐Mail : mail@mail.com\n"
        ."Mietdauer : So 04.10.2026 ‐ Sa 10.10.2026 = 6 Nächte\n"
        ."Anzahl Personen : 10\n"
        .'Verpflegung : Selbstkocher';

    // Anfrage-Mail

    public function test_mail_with_windows_line_endings(): void
    {
        $data = Helper::parseFromMailText(str_replace("\n", "\r\n", self::MAIL));

        $this->assertSame('Nachname', $data['name']);
        $this->assertSame('Seminar/Firmenanlass', $data['gruppe']);
        $this->assertSame('2026-11-13', $data['mietdauer_bis']);
        $this->assertSame('Ist das Haus frei?', $data['kommentar']);
    }

    public static function mailNames(): array
    {
        return [
            'Vor- und Nachname' => ['Vorname Nachname', 'Vorname', 'Nachname'],
            'Doppelter Vorname' => ['Anna Maria Muster', 'Anna Maria', 'Muster'],
            'Nur ein Wort' => ['Muster', null, 'Muster'],
            'Mehrere Leerzeichen' => ['Anna   Muster  ', 'Anna', 'Muster'],
        ];
    }

    #[DataProvider('mailNames')]
    public function test_mail_name_is_split_at_last_word(string $raw, ?string $firstname, string $name): void
    {
        $data = Helper::parseFromMailText(str_replace('Vorname Nachname', $raw, self::MAIL));

        $this->assertSame($firstname, $data['vorname']);
        $this->assertSame($name, $data['name']);
    }

    public function test_mail_without_number_or_dates(): void
    {
        $text = preg_replace('/^(Anfragenummer|Datum).*\n/m', '', self::MAIL);

        $data = Helper::parseFromMailText($text);

        // Ohne Anfragenummer kein "A-" ohne Nummer im Fremdschlüssel
        $this->assertNull($data['buchungsnummer']);
        $this->assertNull($data['mietdauer_von']);
        $this->assertSame('Nachname', $data['name']);
    }

    public static function lineEndings(): array
    {
        return [
            'Unix' => ["\n"],
            'Windows' => ["\r\n"],
        ];
    }

    #[DataProvider('lineEndings')]
    public function test_mail_with_empty_comment_keeps_language_clean(string $lineEnding): void
    {
        $text = str_replace("    \tIst das Haus frei?", '', self::MAIL)."\n";

        $data = Helper::parseFromMailText(str_replace("\n", $lineEnding, $text));

        $this->assertSame('französisch', $data['sprache']);
        $this->assertNull($data['kommentar']);
    }

    public function test_mail_comment_on_next_line(): void
    {
        $data = Helper::parseFromMailText(str_replace("    \tIst", "\nIst", self::MAIL));

        $this->assertSame('französisch', $data['sprache']);
        $this->assertSame('Ist das Haus frei?', $data['kommentar']);
    }

    public function test_mail_people_count_takes_first_number(): void
    {
        $data = Helper::parseFromMailText(str_replace('ca. 12 Personen', '12-15 Personen', self::MAIL));

        $this->assertSame('12', $data['anzahl_personen']);
    }

    // Buchungs-PDF

    public function test_pdf_with_windows_line_endings(): void
    {
        $data = Helper::parseFromText(str_replace("\n", "\r\n", self::PDF));

        $this->assertSame('Nachname', $data['name']);
        $this->assertSame('Dorfstrasse', $data['strasse']);
        $this->assertSame('8003', $data['plz']);
        $this->assertSame('Zürich', $data['stadt']);
        $this->assertNull($data['telefon']);
        $this->assertSame('079 123 45 56', $data['mobil']);
        $this->assertSame('mail@mail.com', $data['email']);
    }

    public static function pdfNames(): array
    {
        return [
            'mit Anrede' => ['Frau Vorname Nachname', 'Frau', 'Vorname', 'Nachname'],
            'Herr' => ['Herr Hans Muster', 'Herr', 'Hans', 'Muster'],
            'ohne Anrede' => ['Vorname Nachname', '', 'Vorname', 'Nachname'],
            'doppelter Vorname' => ['Frau Anna Maria Muster', 'Frau', 'Anna Maria', 'Muster'],
        ];
    }

    #[DataProvider('pdfNames')]
    public function test_pdf_name_with_and_without_salutation(string $raw, string $salutation, string $firstname, string $name): void
    {
        $data = Helper::parseFromText(str_replace('Frau Vorname Nachname', $raw, self::PDF));

        $this->assertSame([$salutation, $firstname, $name], [$data['anrede'], $data['vorname'], $data['name']]);
    }

    public static function pdfAddresses(): array
    {
        return [
            'Hausnummer mit Buchstabe' => ["Dorfstrasse 12a\nCH‐8003 Zürich", 'Dorfstrasse', '12a', '8003', 'Zürich'],
            'Strasse aus mehreren Wörtern' => ["Obere Dorfstrasse 7\nCH‐8003 Zürich", 'Obere Dorfstrasse', '7', '8003', 'Zürich'],
            'PLZ ohne CH' => ["Dorfstrasse 12\n8003 Zürich", 'Dorfstrasse', '12', '8003', 'Zürich'],
            'normaler Bindestrich' => ["Dorfstrasse 12\nCH-6344 Meierskappel", 'Dorfstrasse', '12', '6344', 'Meierskappel'],
            'Ort aus mehreren Wörtern' => ["Dorfstrasse 12\nCH‐8400 Winterthur Seen", 'Dorfstrasse', '12', '8400', 'Winterthur Seen'],
        ];
    }

    #[DataProvider('pdfAddresses')]
    public function test_pdf_address(string $raw, string $street, string $number, string $zip, string $city): void
    {
        $data = Helper::parseFromText(str_replace("Dorfstrasse 12\nCH‐8003 Zürich", $raw, self::PDF));

        $this->assertSame([$street, $number, $zip, $city], [$data['strasse'], $data['hausnummer'], $data['plz'], $data['stadt']]);
    }

    public function test_pdf_dates_with_normal_hyphen(): void
    {
        $data = Helper::parseFromText(str_replace('So 04.10.2026 ‐ Sa 10.10.2026', 'So 04.10.2026 - Sa 10.10.2026', self::PDF));

        $this->assertSame('2026-10-04', $data['mietdauer_von']);
        $this->assertSame('2026-10-10', $data['mietdauer_bis']);
    }

    public function test_pdf_single_night(): void
    {
        $data = Helper::parseFromText(str_replace('= 6 Nächte', '= 1 Nacht', self::PDF));

        $this->assertSame('1', $data['naechte']);
    }
}
