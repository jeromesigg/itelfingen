<?php

namespace Tests\Feature;

use App\Mail\FeedbackMail;
use App\Mail\SendOffersMail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Mails an Buchende: Inhalte und die Vorschau im Admin (admin/mail-preview).
 */
class BookingMailsTest extends TestCase
{
    /** @return array<string, array{0: string, 1: list<string>}> Vorlage => Texte, die vorkommen müssen */
    public static function mails(): array
    {
        return [
            'Anfrage' => ['created', ['Deine Buchungsanfrage ist eingegangen', 'Wie geht es weiter?']],
            'Angebot' => ['offers', ['Bitte bestätige das Angebot bis', 'Zum Angebot']],
            'Definitiv' => ['confirmation', ['Deine Buchung ist definitiv', 'Kalendereintrag']],
            'Letzte Infos' => ['last_infos', ['Bei Notfällen', 'Jérôme Sigg', 'Nachtruhe']],
            'Abreise' => ['feedback', ['Zur Checkliste für die Hausrückgabe', '4315']],
            'Rechnung' => ['invoices', ['Zur Rechnung']],
            'Genossenschaft' => ['application', ['Mehr erfahren und bewerben']],
        ];
    }

    /**
     * @param  list<string>  $texts
     */
    #[DataProvider('mails')]
    public function test_preview_renders_mail_with_du_and_link_to_customer_area(string $mail, array $texts): void
    {
        $s = BookingScenario::create();

        $response = $this->actingAs($s->user('admin'))
            ->get(route('admin.mail-preview.show', ['mail' => $mail, 'event' => $s->confirmedEvent->id]))
            ->assertOk()
            ->assertSee('Deine Buchung ansehen')
            ->assertSee(route('bookings.uuid', $s->confirmedEvent->uuid), false)
            ->assertDontSee('Ihre')
            ->assertDontSee('Sie ');

        foreach ($texts as $text) {
            $response->assertSee($text);
        }
    }

    public function test_preview_is_only_for_admins(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('verwalter'))->get(route('admin.mail-preview'))->assertForbidden();
        $this->actingAs($s->user('admin'))->get(route('admin.mail-preview'))->assertOk()->assertSee('Angebot');
        $this->actingAs($s->user('admin'))->get(route('admin.mail-preview.show', ['mail' => 'unbekannt']))->assertNotFound();
    }

    public function test_offer_mail_shows_deadline_from_bexio_and_swiss_amount(): void
    {
        $s = BookingScenario::create();

        $mail = new SendOffersMail($s->confirmedEvent, 'https://bexio.test/offerte', '1234.5', '', '2026-11-30');

        $mail->assertSeeInHtml('30.11.2026');
        $mail->assertSeeInHtml('CHF 1’234.50', false);
        $mail->assertHasSubject('Dein Angebot zur Buchung '.$s->confirmedEvent->number().' für das Ferienhaus Itelfingen');
    }

    public function test_departure_mail_heading_matches_end_date(): void
    {
        $s = BookingScenario::create();

        $s->confirmedEvent->update(['end_date' => today()->addDay()->format('Y-m-d')]);
        (new FeedbackMail($s->confirmedEvent->fresh()))->assertSeeInHtml('Morgen ist dein Abreisetag');

        $s->confirmedEvent->update(['end_date' => today()->format('Y-m-d')]);
        (new FeedbackMail($s->confirmedEvent->fresh()))->assertSeeInHtml('Heute ist dein Abreisetag');
    }
}
