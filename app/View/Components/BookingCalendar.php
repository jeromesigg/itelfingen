<?php

namespace App\View\Components;

use App\Models\Event;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Kalender rund um eine Buchung: ganze Wochen von einer Woche vor bis eine Woche nach der Buchung.
 *
 * Die Zustände der Tage entsprechen dem Kalender der Buchungsübersicht (resources/views/contents/event_js.blade.php):
 * je ein Buchstabe für Vormittag und Nachmittag, F = frei, P/B = Farbe des Buchungsstatus, S = diese Buchung,
 * ein angehängtes D markiert die Abreise am Vormittag. Die CSS-Klassen stehen in resources/css/libs/events.css.
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

        /** @var array<string, array{am: ?string, pm: ?string, amEvent: ?Event, pmEvent: ?Event}> $halves */
        $halves = [];
        $others = Event::with('event_status')
            ->whereKeyNot($event->id)
            ->where('start_date', '<=', $to->toDateString())
            ->where('end_date', '>=', $from->toDateString())
            ->orderBy('start_date')
            ->get();

        foreach ($others as $other) {
            if ($other->event_status?->color) {
                $this->mark($halves, $other, $other->event_status->color);
            }
        }
        $this->mark($halves, $event, 'S');

        $this->weeks = [];
        for ($day = $from; $day <= $to; $day = $day->addDay()) {
            $half = $halves[$day->toDateString()] ?? ['am' => null, 'pm' => null, 'amEvent' => null, 'pmEvent' => null];
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

    /**
     * Trägt die Halbtage einer Buchung ein. Ohne Early Check-In beginnt sie am Nachmittag,
     * ohne Late Check-Out endet sie am Vormittag.
     *
     * @param  array<string, array{am: ?string, pm: ?string, amEvent: ?Event, pmEvent: ?Event}>  $halves
     */
    private function mark(array &$halves, Event $event, string $color): void
    {
        $start = CarbonImmutable::parse($event->start_date);
        $end = CarbonImmutable::parse($event->end_date);

        for ($day = $start; $day <= $end; $day = $day->addDay()) {
            $key = $day->toDateString();
            $halves[$key] ??= ['am' => null, 'pm' => null, 'amEvent' => null, 'pmEvent' => null];
            $morning = ! ($day->equalTo($start) && ! $start->equalTo($end) && ! $event->early_checkin);
            $afternoon = ! ($day->equalTo($end) && ! $start->equalTo($end) && ! $event->late_checkout);

            if ($morning) {
                $halves[$key]['am'] = $afternoon ? $color : $color.'D';
                $halves[$key]['amEvent'] = $event;
            }
            if ($afternoon) {
                $halves[$key]['pm'] = $color;
                $halves[$key]['pmEvent'] = $event;
            }
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.booking-calendar');
    }
}
