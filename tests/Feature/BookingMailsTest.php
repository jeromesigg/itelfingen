<?php

namespace Tests\Feature;

use App\Mail\ApplicationInvoiceMail;
use App\Mail\CleaningSent;
use App\Mail\EventCreated;
use App\Mail\FeedbackMail;
use App\Mail\SendOffersMail;
use App\Models\BexioToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Mails an Gäste (Buchung, Kontakt, Genossenschaft): Inhalte und die Vorschau im Admin (admin/mail-preview).
 */
class BookingMailsTest extends TestCase
{
    /** @return array<string, array{0: string, 1: list<string>}> Vorlage => Texte, die vorkommen müssen */
    public static function mails(): array
    {
        return [
            'Anfrage' => ['events.created', ['Deine Buchungsanfrage ist eingegangen', 'Wie geht es weiter?']],
            'Angebot' => ['events.offers', ['Bitte bestätige das Angebot bis', 'Zum Angebot']],
            'Definitiv' => ['events.confirmation', ['Deine Buchung ist definitiv', 'Kalendereintrag']],
            'Letzte Infos' => ['events.last_infos', ['Bei Notfällen', 'Jérôme Sigg', 'Nachtruhe']],
            'Abreise' => ['events.feedback', ['Zur Checkliste für die Hausrückgabe', '4315']],
            'Rechnung' => ['events.invoices', ['Zur Rechnung']],
            'Genossenschaft' => ['events.application', ['Mehr erfahren und bewerben']],
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

    public function test_booking_request_mail_separates_contact_data_and_comment(): void
    {
        $s = BookingScenario::create();
        $s->event->update(['telephone' => '079 123 45 67', 'comment' => "Evtl. kommen noch Personen dazu.\nWir melden uns."]);

        $html = (new EventCreated($s->event->fresh()))->render();

        // Telefon und Überschrift "Deine Bemerkung" nicht im selben Absatz (Blade verschluckt den Umbruch nach @endif)
        $this->assertMatchesRegularExpression('/<br>\s*'.preg_quote(e($s->event->email), '/').' · 079 123 45 67\s*<\/p>/', $html);
        $this->assertMatchesRegularExpression('/<p[^>]*><strong[^>]*>Deine Bemerkung<\/strong>/', $html);
        $this->assertStringContainsString('Evtl. kommen noch Personen dazu.<br', $html);
    }

    public function test_booking_request_mail_without_phone_and_comment(): void
    {
        $s = BookingScenario::create();
        $s->event->update(['telephone' => null, 'comment' => null]);

        $html = (new EventCreated($s->event->fresh()))->render();

        $this->assertMatchesRegularExpression('/<br>\s*'.preg_quote(e($s->event->email), '/').'\s*<\/p>/', $html);
        $this->assertStringNotContainsString('Deine Bemerkung', $html);
        $this->assertStringNotContainsString(' · ', $html);
    }

    public function test_contact_mail_thanks_first_and_keeps_line_breaks(): void
    {
        $s = BookingScenario::create();
        $s->contact->update(['content' => "Erste Zeile\nZweite Zeile"]);

        $this->actingAs($s->user('admin'))
            ->get(route('admin.mail-preview.show', ['mail' => 'contacts.created']))
            ->assertOk()
            ->assertSeeInOrder(['Danke für deine Nachricht', 'Erste Zeile<br>', 'Zweite Zeile', 'Häufige Fragen ansehen'], false)
            ->assertDontSee('Ihre');
    }

    public function test_application_mail_hides_empty_fields_and_mentions_statutes(): void
    {
        $s = BookingScenario::create();
        $s->application->update(['organisation' => null, 'telephone' => null, 'comment' => null]);

        $this->actingAs($s->user('admin'))
            ->get(route('admin.mail-preview.show', ['mail' => 'applications.created']))
            ->assertOk()
            ->assertSee('Deine Bewerbung ist eingegangen')
            ->assertSee('Statuten')
            ->assertDontSee('Organisation')
            ->assertDontSee('Telefon')
            ->assertDontSee('Anlass / Gruppe');
    }

    public function test_membership_invoice_shows_due_date_from_bexio_and_member_price_tip(): void
    {
        BexioToken::create(['access_token' => 'token', 'refresh_token' => 'refresh', 'expires_at' => now()->addHour()]);
        Http::fake(['api.bexio.com/*' => Http::response(['content' => base64_encode('%PDF')])]);
        $s = BookingScenario::create();

        $mail = new ApplicationInvoiceMail($s->application, ['network_link' => 'https://bexio.test/rechnung', 'is_valid_to' => '2026-12-01']);

        $mail->assertSeeInHtml('01.12.2026');
        $mail->assertSeeInHtml('So buchst du zum Mitgliederpreis');
        $mail->assertSeeInHtml('https://bexio.test/rechnung');
    }

    public function test_cleaning_mail_renders_text_and_subject(): void
    {
        $s = BookingScenario::create();

        $mail = new CleaningSent($s->confirmedEvent, 'reinigung@example.com', 'Bitte Reinigung am Sonntag.');

        $mail->assertSeeInHtml('Bitte Reinigung am Sonntag.');
        $mail->assertHasSubject('Reinigungsanfrage '.$s->confirmedEvent->number().' für das Ferienhaus Itelfingen vom '.Carbon::parse($s->confirmedEvent->end_date)->format('d.m.Y'));
    }
}
