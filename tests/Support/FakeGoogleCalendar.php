<?php

namespace Tests\Support;

use App\Models\Event;
use App\Services\GoogleCalendarService;

/**
 * Ersetzt den Google-Kalender in allen Tests (siehe TestCase) und merkt sich die eingetragenen Buchungen.
 */
class FakeGoogleCalendar extends GoogleCalendarService
{
    /** @var list<Event> */
    public array $bookings = [];

    public function addBooking(Event $event): void
    {
        $this->bookings[] = $event;
    }
}
