<?php

namespace Tests\Feature;

use App\Models\BexioToken;
use App\Notifications\ApplicationCreatedNotification;
use App\Notifications\ApplicationInvoiceNotification;
use App\Notifications\ContactCreatedNotification;
use App\Notifications\EventApplicationWantedNotification;
use App\Notifications\EventCleaningSentNotification;
use App\Notifications\EventCreatedNotification;
use App\Notifications\EventFeedbackNotification;
use App\Notifications\EventInvoiceCreatedNotification;
use App\Notifications\EventInvoiceSendNotification;
use App\Notifications\EventLastInfosNotification;
use App\Notifications\EventOfferSendNotification;
use Closure;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Mime\Email;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Alle Mails werden wirklich gebaut und über den array-Mailer "versendet": Views, Betreff, Empfänger, Anhänge.
 * Storage mit Platzhalter-PDFs stellt TestCase bereit (die Vorlagen liegen nicht im Repo).
 */
class NotificationMailsTest extends TestCase
{
    private BookingScenario $s;

    protected function setUp(): void
    {
        parent::setUp();

        BexioToken::create(['access_token' => 'token', 'refresh_token' => 'refresh', 'expires_at' => now()->addHour()]);
        Http::fake(['api.bexio.com/*' => Http::response([
            'id' => 22,
            'network_link' => 'https://bexio.test/dokument',
            'total' => '1200.00',
            'content' => base64_encode('%PDF-1.4 Rechnung'),
        ])]);

        $this->s = BookingScenario::create();
        $this->s->confirmedEvent->update(['bexio_offer_id' => 11, 'bexio_invoice_id' => 22, 'code' => 4711]);
        $this->s->application->update(['bexio_invoice_id' => 22]);
    }

    /**
     * @return list<Email>
     */
    private function sentMails(): array
    {
        return collect(app('mailer')->getSymfonyTransport()->messages())->map->getOriginalMessage()->all();
    }

    private function attachmentNames(Email $mail): array
    {
        return array_map(fn ($a) => $a->getFilename(), $mail->getAttachments());
    }

    public static function notifications(): array
    {
        return [
            'Buchung erstellt' => [fn (BookingScenario $s) => [$s->event, new EventCreatedNotification($s->event)], []],
            'Angebot versendet' => [fn (BookingScenario $s) => [$s->confirmedEvent, new EventOfferSendNotification($s->confirmedEvent, 'Zusatz')], ['Hausordnung.pdf']],
            'Rechnung erstellt' => [fn (BookingScenario $s) => [$s->confirmedEvent, new EventInvoiceCreatedNotification($s->confirmedEvent, 'Zusatz')], ['invite.ics']],
            'Rechnung versendet' => [fn (BookingScenario $s) => [$s->confirmedEvent, new EventInvoiceSendNotification($s->confirmedEvent, 'Zusatz')], ['Rechnung.pdf']],
            'Letzte Infos' => [fn (BookingScenario $s) => [$s->confirmedEvent, new EventLastInfosNotification($s->confirmedEvent, 'Zusatz')], ['Infos_vor_Buchung.pdf', 'Parkkarte.pdf']],
            'Feedback' => [fn (BookingScenario $s) => [$s->confirmedEvent, new EventFeedbackNotification($s->confirmedEvent)], ['Infos_vor_Buchung.pdf']],
            'Genossenschaft' => [fn (BookingScenario $s) => [$s->confirmedEvent, new EventApplicationWantedNotification($s->confirmedEvent, 'Zusatz')], []],
            'Putzfirma' => [fn (BookingScenario $s) => [$s->confirmedEvent, new EventCleaningSentNotification($s->confirmedEvent, 'putz@example.com', 'Bitte reinigen')], []],
            'Bewerbung erhalten' => [fn (BookingScenario $s) => [$s->application, new ApplicationCreatedNotification($s->application)], []],
            'Bewerbung Rechnung' => [fn (BookingScenario $s) => [$s->application, new ApplicationInvoiceNotification($s->application, ['id' => 22, 'network_link' => 'https://bexio.test/dokument', 'total' => '500.00'])], ['Rechnung.pdf']],
            'Kontaktanfrage' => [fn (BookingScenario $s) => [$s->contact, new ContactCreatedNotification($s->contact)], []],
        ];
    }

    #[DataProvider('notifications')]
    public function test_mail_is_built_and_sent(Closure $make, array $attachments): void
    {
        [$notifiable, $notification] = $make($this->s);

        Notification::sendNow($notifiable, $notification);

        $mails = $this->sentMails();
        $this->assertNotEmpty($mails, 'Keine Mail versendet');
        $mail = $mails[0];
        $this->assertNotEmpty($mail->getSubject());
        $this->assertNotEmpty($mail->getTo());
        $this->assertNotEmpty($mail->getHtmlBody() ?? $mail->getTextBody());
        $this->assertEqualsCanonicalizing($attachments, $this->attachmentNames($mail));
    }

    public function test_cleaning_mail_goes_to_given_address(): void
    {
        Notification::sendNow($this->s->confirmedEvent, new EventCleaningSentNotification($this->s->confirmedEvent, 'putz@example.com', 'Bitte reinigen'));

        $this->assertSame('putz@example.com', $this->sentMails()[0]->getTo()[0]->getAddress());
    }

    public function test_last_infos_mail_contains_door_code(): void
    {
        Notification::sendNow($this->s->confirmedEvent, new EventLastInfosNotification($this->s->confirmedEvent));

        $this->assertStringContainsString('4711', $this->sentMails()[0]->getHtmlBody());
        $this->assertSame($this->s->confirmedEvent->email, $this->sentMails()[0]->getTo()[0]->getAddress());
    }

    public static function mailsWithBookingNumber(): array
    {
        return [
            'Letzte Infos' => [fn ($event) => new EventLastInfosNotification($event)],
            'Feedback' => [fn ($event) => new EventFeedbackNotification($event)],
            'Angebot versendet' => [fn ($event) => new EventOfferSendNotification($event, '')],
            'Rechnung versendet' => [fn ($event) => new EventInvoiceSendNotification($event, '')],
        ];
    }

    #[DataProvider('mailsWithBookingNumber')]
    public function test_subject_shows_external_booking_number(Closure $make): void
    {
        // Externe Buchungen (z. B. über eine Plattform) tragen deren Nummer zusätzlich im Betreff.
        $event = $this->s->confirmedEvent;
        $event->update(['external' => true, 'foreign_key' => 'EXT-123']);

        Notification::sendNow($event, $make($event->fresh()));

        $this->assertStringContainsString($event->number().' (EXT-123)', $this->sentMails()[0]->getSubject());
    }
}
