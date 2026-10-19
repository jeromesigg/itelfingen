<?php

namespace App\Services;

use App\Models\Event;
use Carbon\CarbonImmutable;

/**
 * Belegung der Halbtage für die Buchungskalender.
 *
 * Je Tag ein Zustand für Vormittag (am) und Nachmittag (pm): P/B = Farbe des Buchungsstatus,
 * S = hervorgehobene Buchung, ein angehängtes D markiert die Abreise am Vormittag, null = frei.
 * Die CSS-Klassen der Kombinationen stehen in resources/css/libs/events.css.
 */
class BookingDays
{
    /**
     * @var array<string, array{am: ?string, pm: ?string, amEvent: ?Event, pmEvent: ?Event}>
     */
    private array $halves = [];

    /**
     * @param  bool  $sundayIsFullDay  Im Kalender der Startseite gilt eine An- oder Abreise am Sonntag als ganzer Tag.
     */
    public function __construct(private bool $sundayIsFullDay = false) {}

    /**
     * Trägt alle Buchungen mit der Farbe ihres Status ein; Buchungen ohne Farbe (storniert) fehlen.
     *
     * @param  iterable<Event>  $events
     */
    public function markAll(iterable $events): static
    {
        foreach ($events as $event) {
            if ($event->event_status?->color) {
                $this->mark($event, $event->event_status->color);
            }
        }

        return $this;
    }

    /**
     * Trägt die Halbtage einer Buchung ein. Ohne Early Check-In beginnt sie am Nachmittag,
     * ohne Late Check-Out endet sie am Vormittag.
     */
    public function mark(Event $event, string $color): static
    {
        $start = CarbonImmutable::parse($event->start_date);
        $end = CarbonImmutable::parse($event->end_date);
        $earlyCheckin = $event->early_checkin || ($this->sundayIsFullDay && $start->isSunday());
        $lateCheckout = $event->late_checkout || ($this->sundayIsFullDay && $end->isSunday());

        for ($day = $start; $day <= $end; $day = $day->addDay()) {
            $key = $day->toDateString();
            $this->halves[$key] ??= ['am' => null, 'pm' => null, 'amEvent' => null, 'pmEvent' => null];
            $morning = ! ($day->equalTo($start) && ! $start->equalTo($end) && ! $earlyCheckin);
            $afternoon = ! ($day->equalTo($end) && ! $start->equalTo($end) && ! $lateCheckout);

            if ($morning) {
                $this->halves[$key]['am'] = $afternoon ? $color : $color.'D';
                $this->halves[$key]['amEvent'] = $event;
            }
            if ($afternoon) {
                $this->halves[$key]['pm'] = $color;
                $this->halves[$key]['pmEvent'] = $event;
            }
        }

        return $this;
    }

    /**
     * @return array{am: ?string, pm: ?string, amEvent: ?Event, pmEvent: ?Event}
     */
    public function on(string $date): array
    {
        return $this->halves[$date] ?? ['am' => null, 'pm' => null, 'amEvent' => null, 'pmEvent' => null];
    }

    /**
     * Belegte Tage für den Vue-Kalender. Die Buchungs-ID braucht nur der Admin-Kalender (Link zur Buchung).
     *
     * @return array<string, array{am: ?string, pm: ?string, id?: int}>
     */
    public function toArray(bool $withIds = false): array
    {
        $days = [];
        foreach ($this->halves as $date => $half) {
            $days[$date] = ['am' => $half['am'], 'pm' => $half['pm']];
            if ($withIds) {
                $days[$date]['id'] = ($half['pmEvent'] ?? $half['amEvent'])->id;
            }
        }

        return $days;
    }
}
