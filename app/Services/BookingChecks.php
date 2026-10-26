<?php

namespace App\Services;

use App\Enums\BookingState;
use App\Enums\ContractState;
use App\Models\Contact;
use App\Models\Event;
use App\Notifications\EventOfferSendNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Offene Arbeiten an Buchungen und Anfragen: Grundlage für das wöchentliche Erinnerungsmail
 * und die Aufgabenliste auf dem Dashboard.
 */
class BookingChecks
{
    /** Ab so vielen Tagen vor der Anreise wird ein fehlender Türcode oder ein fehlendes Putzmail dringend. */
    public const URGENT_DAYS = 14;

    /**
     * Noch kein Angebot erstellt (ohne eigene Termine).
     *
     * @return Collection<int, Event>
     */
    public function unprocessed(): Collection
    {
        return Event::whereIn('contract_status_id', ContractState::before(ContractState::AngebotErstellt))
            ->where('event_status_id', '<>', BookingState::Eigene)
            ->orderBy('start_date')->get();
    }

    /**
     * Angebot erstellt, aber noch nicht versendet.
     *
     * @return Collection<int, Event>
     */
    public function offersNotSent(): Collection
    {
        return Event::where('contract_status_id', ContractState::AngebotErstellt)
            ->whereIn('event_status_id', [BookingState::Neu, BookingState::Bestaetigt])
            ->orderBy('start_date')->get();
    }

    /**
     * Angebot vor mehr als zwei Monaten versendet und noch nicht angenommen (ohne eigene Termine, die haben kein Angebot).
     *
     * @return Collection<int, Event>
     */
    public function staleOffers(): Collection
    {
        return Event::with('notifications')
            ->where('start_date', '>', Carbon::today())
            ->where('contract_status_id', ContractState::AngebotVersendet)
            ->whereIn('event_status_id', [BookingState::Neu, BookingState::Bestaetigt])
            ->orderBy('start_date')->get()
            ->filter(fn (Event $event) => $this->staleOfferSentAt($event) !== null)
            ->values();
    }

    /**
     * Versanddatum des Angebots, falls es älter als zwei Monate ist.
     */
    public function staleOfferSentAt(Event $event): ?Carbon
    {
        $limit = Carbon::today()->subMonths(2);

        return $event->notifications
            ->first(fn ($notification) => $notification->type === EventOfferSendNotification::class && $notification->created_at < $limit)
            ?->created_at;
    }

    /**
     * Bestätigte Buchungen und eigene Termine der nächsten vier Monate ohne Reinigungs-Mail.
     *
     * @return Collection<int, Event>
     */
    public function withoutCleaningMail(): Collection
    {
        return $this->upcoming([BookingState::Bestaetigt, BookingState::Eigene])->where('cleaning_mail', false)->orderBy('start_date')->get();
    }

    /**
     * Bestätigte Buchungen der nächsten vier Monate ohne Türcode.
     *
     * @return Collection<int, Event>
     */
    public function withoutDoorCode(): Collection
    {
        return $this->upcoming([BookingState::Bestaetigt])->whereNull('code')->orderBy('start_date')->get();
    }

    /**
     * Vergangene bestätigte Buchungen, deren Rechnung erstellt, aber nicht versendet ist.
     *
     * @return Collection<int, Event>
     */
    public function withoutInvoice(): Collection
    {
        return Event::where('end_date', '<', Carbon::today())
            ->where('event_status_id', BookingState::Bestaetigt)
            ->where('contract_status_id', ContractState::RechnungErstellt)
            ->orderBy('start_date')->get();
    }

    /**
     * Bestätigte, noch nicht beendete Buchungen ohne verantwortliche Person.
     *
     * @return Collection<int, Event>
     */
    public function withoutResponsibleUser(): Collection
    {
        return Event::where('event_status_id', BookingState::Bestaetigt)
            ->whereNull('user_id')
            ->where('end_date', '>=', Carbon::today())
            ->orderBy('start_date')->get();
    }

    /**
     * @return Collection<int, Contact>
     */
    public function openContacts(): Collection
    {
        return Contact::where('done', false)->orderBy('created_at', 'DESC')->get();
    }

    /**
     * Listen für das wöchentliche Erinnerungsmail.
     *
     * @return Collection<int, array{text: string, events: Collection<int, Event>}>
     */
    public function forWeeklyMail(): Collection
    {
        $staleOffers = $this->staleOffers()->each(function (Event $event) {
            $event['name'] = $event['name'].' ('.$this->staleOfferSentAt($event)->isoFormat('L').')';
        });

        return collect([
            ['text' => 'Folgende Buchungen wurden noch nicht bearbeitet', 'events' => $this->unprocessed()],
            ['text' => 'Folgende Angebote sind älter als zwei Monate', 'events' => $staleOffers],
            ['text' => 'Folgende Buchungen haben noch kein Reinigungs-Mail versendet', 'events' => $this->withoutCleaningMail()],
            ['text' => 'Folgende Buchungen haben noch keinen Tür-Code.', 'events' => $this->withoutDoorCode()],
            ['text' => 'Folgende Buchungen haben noch keine Rechnung erhalten.', 'events' => $this->withoutInvoice()],
        ]);
    }

    /**
     * Alle offenen Arbeiten als eine Liste für das Dashboard: dringende zuerst, dann nach Datum.
     *
     * @return Collection<int, array{title: string, subtitle: string, urgent: bool, link: string, date: Carbon}>
     */
    public function dashboardTasks(): Collection
    {
        $startsSoon = fn (Event $event) => Carbon::parse($event->start_date)->lte(Carbon::today()->addDays(self::URGENT_DAYS));

        return collect()
            ->concat($this->unprocessed()->map(fn (Event $event) => $this->eventTask(
                $event, $event->bookingState() === BookingState::Neu ? 'Neue Buchung bearbeiten' : 'Angebot erstellen', $event->bookingState() === BookingState::Neu)))
            ->concat($this->offersNotSent()->map(fn (Event $event) => $this->eventTask($event, 'Angebot versenden', false)))
            ->concat($this->staleOffers()->map(fn (Event $event) => $this->eventTask(
                $event, 'Angebot nachfassen', false, 'versendet '.$this->staleOfferSentAt($event)->format('d.m.Y'))))
            ->concat($this->withoutDoorCode()->map(fn (Event $event) => $this->eventTask($event, 'Türcode fehlt', $startsSoon($event), $this->untilStart($event))))
            ->concat($this->withoutCleaningMail()->map(fn (Event $event) => $this->eventTask($event, 'Putzmail senden', $startsSoon($event), $this->untilStart($event))))
            ->concat($this->withoutInvoice()->map(fn (Event $event) => $this->eventTask($event, 'Rechnung fehlt', true)))
            ->concat($this->withoutResponsibleUser()->map(fn (Event $event) => $this->eventTask($event, 'Person zuweisen', false)))
            ->concat($this->openContacts()->map(fn (Contact $contact) => [
                'title' => 'Anfrage beantworten',
                'subtitle' => $contact->name.' · '.Carbon::parse($contact->created_at)->diffForHumans(),
                'urgent' => false,
                'link' => route('admin.contacts.index'),
                'date' => Carbon::parse($contact->created_at),
            ]))
            ->sortBy([['urgent', 'desc'], fn (array $a, array $b) => $a['date'] <=> $b['date']])
            ->values();
    }

    /**
     * Buchungen mit Anreise in den nächsten vier Monaten.
     *
     * @param  list<BookingState>  $states
     */
    private function upcoming(array $states): Builder
    {
        return Event::where('start_date', '<=', Carbon::today()->addMonths(4))
            ->where('start_date', '>', Carbon::today())
            ->whereIn('event_status_id', $states);
    }

    /**
     * @return array{title: string, subtitle: string, urgent: bool, link: string, date: Carbon}
     */
    private function eventTask(Event $event, string $title, bool $urgent, ?string $detail = null): array
    {
        $start = Carbon::parse($event->start_date);
        $end = Carbon::parse($event->end_date);

        return [
            'title' => $title,
            'subtitle' => implode(' · ', array_filter([$event->number().' '.$event->displayName(), $start->format('d.m.').'–'.$end->format('d.m.Y'), $detail])),
            'urgent' => $urgent,
            'link' => route('admin.events.edit', $event->id),
            'date' => $start,
        ];
    }

    private function untilStart(Event $event): string
    {
        $days = (int) Carbon::today()->diffInDays(Carbon::parse($event->start_date));

        return $days === 1 ? 'morgen' : "in $days Tagen";
    }
}
