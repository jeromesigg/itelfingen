<?php

namespace App\View\Components\Bookings;

use App\Enums\BookingState;
use App\Enums\ContractState;
use App\Models\Event;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Status-Banner im Kundenbereich: wo steht die Buchung und was ist der nächste Schritt.
 */
class Status extends Component
{
    public string $tone;

    public string $title;

    public string $description;

    /** @var array{link: string, text: string}|null */
    public ?array $action = null;

    /**
     * Create a new component instance.
     */
    public function __construct(public Event $event, ?string $offerLink = null)
    {
        $today = Carbon::today();
        $start = Carbon::parse($event['start_date']);
        $end = Carbon::parse($event['end_date']);

        if ($event->bookingState() === BookingState::Storniert || $event->contractState() === ContractState::Storniert) {
            $this->tone = 'danger';
            $this->title = 'Buchung storniert';
            $this->description = 'Diese Buchung wurde storniert. Bei Fragen melde dich gerne bei uns.';
        } elseif ($end->lt($today)) {
            $this->tone = 'success';
            $this->title = 'Danke für deinen Aufenthalt';
            $this->description = 'Wir hoffen, es hat dir gefallen. Wir freuen uns über deine Rückmeldung.';
            $this->action = ['link' => 'https://forms.gle/RMWyPzs8wauakQam9', 'text' => 'Feedback abgeben'];
        } elseif ($event['last_info'] && $start->lte($today)) {
            $this->tone = 'success';
            $this->title = 'Schön, bist du im Ferienhaus';
            $this->description = 'Vor der Abreise hilft dir die Checkliste bei der Hausabgabe.';
            $this->action = ['link' => route('bookings.checklist', $event['uuid']), 'text' => 'Checkliste Hausabgabe'];
        } elseif ($event['last_info']) {
            $this->tone = 'success';
            $this->title = 'Alles bereit für deine Anreise';
            $this->description = 'In den letzten Infos findest du alles Wichtige zu Anreise und Haus.';
            $this->action = ['link' => route('bookings.downloadLastInfos', $event['uuid']), 'text' => 'Letzte Infos ansehen'];
        } elseif ($event->contractState()->isAtLeast(ContractState::RechnungErstellt)) {
            $this->tone = 'success';
            $this->title = 'Deine Buchung ist definitiv';
            $this->description = 'Vor der Anreise erhältst du die letzten Infos mit dem Türcode per E-Mail.';
        } elseif ($event->contractState()->isAtLeast(ContractState::AngebotVersendet)) {
            $this->tone = 'warning';
            $this->title = 'Angebot versendet – bitte prüfen';
            $this->description = 'Mit der Annahme des Angebots wird deine Buchung definitiv.';
            if ($offerLink) {
                $this->action = ['link' => $offerLink, 'text' => 'Angebot ansehen'];
            }
        } else {
            $this->tone = 'info';
            $this->title = 'Buchung eingegangen';
            $this->description = 'Wir prüfen deine Anfrage und senden dir anschliessend ein Angebot.';
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.bookings.status');
    }
}
