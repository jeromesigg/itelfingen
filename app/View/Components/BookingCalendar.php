<?php

namespace App\View\Components;

use App\Models\Event;
use App\Services\BookingDays;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Kalender rund um eine Buchung: ganze Wochen von einer Woche vor bis eine Woche nach der Buchung.
 *
 * Die Zustände der Tage entsprechen dem Kalender der Buchungsübersicht (siehe BookingDays):
 * je ein Buchstabe für Vormittag und Nachmittag, F = frei, P/B = Farbe des Buchungsstatus, S = diese Buchung.
 */
class BookingCalendar extends Component
{
    public const MARGIN_DAYS = 7;

    /**
     * @var list<list<array{date: CarbonImmutable, state: string, event: ?Event}>>
     */
    public array $weeks;

    public function __construct(public Event $event)
    {
        $start = CarbonImmutable::parse($event->start_date);
        $end = CarbonImmutable::parse($event->end_date);
        $from = $start->subDays(self::MARGIN_DAYS)->startOfWeek();
        $to = $end->addDays(self::MARGIN_DAYS)->endOfWeek()->startOfDay();

        $others = Event::with('event_status')
            ->whereKeyNot($event->id)
            ->where('start_date', '<=', $to->toDateString())
            ->where('end_date', '>=', $from->toDateString())
            ->orderBy('start_date')
            ->get();
        $halves = (new BookingDays)->markAll($others)->mark($event, 'S');

        $this->weeks = [];
        for ($day = $from; $day <= $to; $day = $day->addDay()) {
            $half = $halves->on($day->toDateString());
            $am = $half['am'] ?? 'F';
            $pm = $half['pm'] ?? 'F';
            // Neben dieser Buchung zeigt der Übersichtskalender keine halben Tage (es gibt keine Klassen wie BDS)
            if ($am[0] === 'S' || $pm === 'S') {
                $am = $am[0];
            }
            $this->weeks[intdiv((int) $from->diffInDays($day), 7)][] = [
                'date' => $day,
                'state' => $am.$pm,
                'event' => $half['pmEvent'] ?? $half['amEvent'],
            ];
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.booking-calendar');
    }
}
