<?php

namespace App\Services;

use App\Models\Event;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event as GoogleCalendarEvent;

/**
 * Trägt Buchungen in den Google-Kalender des Hauses ein.
 * Eigener Service, damit Tests ihn ersetzen können: der Google-Client läuft nicht über Laravels Http
 * und würde sonst mit den lokalen Zugangsdaten echte Einträge anlegen.
 */
class GoogleCalendarService
{
    public function addBooking(Event $event): void
    {
        $calendarEvent = new GoogleCalendarEvent;
        $calendarEvent->name = $event['firstname'].' '.$event['name'].' - '.$event['group_name'].' - '.$event['telephone'];
        $calendarEvent->startDate = Carbon::parse($event->start_date);
        $calendarEvent->endDate = Carbon::parse($event->end_date)->addDay();
        $calendarEvent->save();
    }
}
