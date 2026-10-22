<?php

namespace Tests\Feature;

use App\Mail\SendEventConfirmation;
use App\Models\Event;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Kalendereinträge (.ics): Abo-Kalender der API und Einladung in der Bestätigungsmail.
 * Das Enddatum ist laut iCalendar exklusiv, also der Tag nach dem letzten Buchungstag.
 */
class CalendarExportTest extends TestCase
{
    public function test_ical_api_lists_bookings_with_day_after_last_day_as_end(): void
    {
        BookingScenario::create();
        Event::factory()->confirmed()->create(['start_date' => '2099-03-06', 'end_date' => '2099-03-08']);

        $this->get('/api/events_ical/test-api-token')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/calendar; charset=utf-8')
            ->assertSee('DTSTART:20990306T000000')
            ->assertSee('DTEND:20990309T000000')
            ->assertSee('STATUS:CONFIRMED');
    }

    public function test_confirmation_mail_invite_is_full_day_until_day_after_departure(): void
    {
        $event = Event::factory()->confirmed()->create(['start_date' => '2099-03-06', 'end_date' => '2099-03-08']);

        $mail = (new SendEventConfirmation($event, ''))->build();
        $ics = $mail->rawAttachments[0]['data'];

        $this->assertSame('invite.ics', $mail->rawAttachments[0]['name']);
        $this->assertMatchesRegularExpression('/^DTSTART;(TZID=[^;]+;)?VALUE=DATE:20990306\r$/m', $ics);
        $this->assertMatchesRegularExpression('/^DTEND;(TZID=[^;]+;)?VALUE=DATE:20990309\r$/m', $ics);
        $this->assertStringContainsString('METHOD:REQUEST', $ics);
    }
}
