@extends('layouts.admin')
@section('content')
@php
$card = 'p-6 bg-white border border-gray-200 rounded-lg shadow-xs dark:bg-gray-800 dark:border-gray-700';
$heading = 'mb-4 text-xl font-semibold dark:text-white';
$btn = 'inline-flex items-center justify-center text-white font-medium rounded-lg text-sm px-5 py-2.5
focus:outline-hidden focus:ring-4';
$grannysmith = $btn.' bg-grannysmith hover:opacity-90 focus:ring-grannysmith/50';
$gladegreen = $btn.' bg-gladegreen hover:opacity-90 focus:ring-gladegreen/50';
$norway = $btn.' bg-norway hover:opacity-90 focus:ring-norway/50';
$ebonyclay = $btn.' bg-ebonyclay hover:opacity-90 focus:ring-ebonyclay/50 dark:border dark:border-gray-600';
$outline = 'inline-flex items-center gap-2 font-medium rounded-lg text-sm px-4 py-2 text-gray-900 bg-white border
border-gray-300 hover:bg-gray-100 focus:outline-hidden focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-white
dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700';
$badgeBase = 'text-xs font-medium px-2.5 py-0.5 rounded-full';
$badge = $badgeBase.' bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
$badgeGreen = $badgeBase.' bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
$offerUrl = 'https://office.bexio.com/index.php/kb_offer/show/id/'.$event['bexio_offer_id'];
$invoiceUrl = 'https://office.bexio.com/index.php/kb_invoice/show/id/'.$event['bexio_invoice_id'];
$contract = $event->contractState();
@endphp

<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <a href="{{ route('admin.events.index') }}"
            class="inline-flex items-center gap-1 mb-2 text-sm text-orientalpink hover:underline">
            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 12h14M5 12l4-4m-4 4 4 4" />
            </svg>
            Zurück zur Übersicht
        </a>
        <h3 class="text-3xl font-bold dark:text-white">Buchung {{ str_pad($event['id'], 5, '0', STR_PAD_LEFT) }}
            bearbeiten</h3>
        <div class="flex flex-wrap items-center gap-3 mt-2">
            @if ($event->event_status)
            <span class="{{ $event->bookingState() === \App\Enums\BookingState::Bestaetigt ? $badgeGreen : $badge }}">{{
                $event->event_status->name }}</span>
            @endif
            @if ($event->contract_status)
            <span
                class="{{ in_array($contract, [\App\Enums\ContractState::RechnungErstellt, \App\Enums\ContractState::RechnungVersendet], true) ? $badgeGreen : $badge }}">{{
                $event->contract_status->name }}</span>
            @endif
            @if ($event->cleaning_mail)
            <span class="{{ $badgeGreen }}">Reinigungsmail versendet</span>
            @else
            <span class="{{ $badge }}">Reinigungsmail noch nicht versendet</span>
            @endif
            @if ($event->glutz_user_id)
            <span class="{{ $badgeGreen }}">Code übertragen</span>
            @else
            <span class="{{ $badge }}">Code nicht übertragen</span>
            @endif
            @if ($event->external)
            <span class="{{ $badge }}">Extern</span>
            @endif
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ $event['firstname'] }} {{ $event['name'] }}@if ($event['group_name']) · {{ $event['group_name']
                }}@endif
            </span>
        </div>
    </div>
    <div class="flex flex-wrap gap-2">
        <a class="{{ $outline }}" href="{{ route('bookings.uuid', $event['uuid']) }}" target="_blank">Zum
            Kundenlogin</a>
        <a class="{{ $outline }}" href="{{ route('events.downloadParking', $event['uuid']) }}">Parkplatz-Karte
            herunterladen</a>
    </div>
</div>

<div class="mb-6">
    @include('includes.form_error')
</div>

<div class="grid grid-cols-1 xl:grid-cols-6 gap-6">
    <div class="xl:col-span-5 flex flex-col gap-6">
        <x-forms.form :action="route('admin.events.update', $event)" method="PATCH" :model="$event"
            class="flex flex-col gap-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="{{ $card }}">
                    <h4 class="{{ $heading }}">Kalender</h4>
                    <div id="booking-calendar">
                        <x-booking-calendar :event="$event" />
                    </div>
                </div>
                <div class="{{ $card }} col-auto md:col-span-4">
                    <h4 class="{{ $heading }}">Kontakt &amp; Zeitraum</h4>
                    <div class="grid grid-cols-2 md:grid-cols-12 gap-4">
                        <div class="md:col-span-2">
                            <x-forms.text label="Start:" name="start_date" type="date" required=true />
                        </div>
                        <div class="md:col-span-2">
                            <x-forms.text label="Ende:" name="end_date" type="date" required=true />
                        </div>
                        <div class="md:col-span-3">
                            <x-forms.text label="Name:" name="name" required=true />
                        </div>
                        <div class="md:col-span-3">
                            <x-forms.text label="Vorname:" name="firstname" />
                        </div>
                        <div class="col-span-2">
                            <x-forms.text label="Gruppe/Anlass:" name="group_name" />
                        </div>
                        <div class="md:col-span-3">
                            <x-forms.text label="Email:" name="email" required=true type="email" />
                        </div>
                        <div class="md:col-span-2">
                            <x-forms.text label="Telefon:" name="telephone" />
                        </div>
                        <div class="md:col-span-2">
                            <x-forms.text label="Strasse:" name="street" required=true />
                        </div>
                        <div class="md:col-span-1">
                            <x-forms.text label="Nr.:" name="house_number" />
                        </div>
                        <div class="md:col-span-1">
                            <x-forms.text label="PLZ:" name="plz" required=true type="number" />
                        </div>
                        <div class="md:col-span-3">
                            <x-forms.text label="Ortschaft:" name="city" required=true />
                        </div>
                    </div>
                </div>
            </div>

            <div class="{{ $card }}">
                <h4 class="{{ $heading }}">Positionen &amp; Preis</h4>
                <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                    @foreach ($positions as $index => $position)
                    @if ($position->pricelist_position['bexio_code']
                    <50) <x-forms.hidden name="{{'positions['.$position['id'].']'}}"
                        id="{{'position_'.$position['id']}}" type="number"
                        value="{{$position['amount']}}" />
                    @else
                    <div>
                        <x-forms.text
                            label="{{$position->pricelist_position['name'] . ' ('. $position->pricelist_position['price'] . ' CHF)'}}"
                            name="{{'positions['.$position['id'].']'}}" type="number"
                            id="{{'position_'.$position['id']}}"
                            value="{{$position['amount']}}" />
                    </div>
                    @endif
                    @endforeach
                    <x-forms.hidden name="total_people" />
                </div>
                <div
                    class="grid grid-cols-2 md:grid-cols-3 gap-4 pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                    <div>
                        <x-forms.text label="Tage:" name="total_days" type="number" required=true />
                    </div>
                    <div>
                        <x-forms.text label="Rabatt [%]:" name="discount" type="number" />
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <span class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Total</span>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">CHF <span id="total"></span>.-</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-6 mt-4">
                    <div class="flex items-center gap-2">
                        <x-forms.text type="checkbox" name="early_checkin" value="{{$event['early_checkin']}}" />
                        <label for="early_checkin" class="text-sm font-medium text-gray-900 dark:text-white">Early
                            Check-In ({{ $checkin_checkout_price }} CHF)</label>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-forms.text type="checkbox" name="late_checkout" value="{{$event['late_checkout']}}" />
                        <label for="late_checkout" class="text-sm font-medium text-gray-900 dark:text-white">Late
                            Check-Out ({{ $checkin_checkout_price }} CHF)</label>
                    </div>
                </div>
            </div>

            <div class="{{ $card }}">
                <h4 class="{{ $heading }}">Verwaltung</h4>
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                    <div>
                        <x-forms.select label="Angebot / Rechnung:" name="contract_status_id"
                            :collection="$contract_statuses" required=true />
                    </div>
                    <div>
                        <x-forms.text label="Externe Buchungs-Nr.:" name="foreign_key" />
                    </div>
                    <div>
                        <x-forms.select label="Verantwortlicher:" name="user_id" required=true :collection="$users" />
                    </div>
                    <div class="flex items-end gap-2">
                        <div class="grow">
                            <x-forms.text label="Tür-Code:" name="code" type="number" />
                        </div>
                        <button type="button" class="{{ $grannysmith }} shrink-0" data-action="create-code">Code
                            erstellen</button>
                    </div>
                    <div>
                        <x-forms.text-area label="Bemerkungen:" name="comment" rows=5 />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <x-forms.text-area label="Bemerkung (intern):" name="comment_intern" rows=5 />
                    </div>
                    <div class="col-span-1">
                        <x-forms.text-area label="Aufmerksam geworden durch:" name="marketing_comment" rows=5 />
                    </div>
                </div>
            </div>

            <div class="{{ $card }}">
                <h4 class="{{ $heading }}">Angebot &amp; Rechnung</h4>
                <x-forms.text-area label="Zusatztext für Mail:" name="additional_text" rows=3 />
                <button type="button" class="mt-2 text-sm text-orientalpink hover:underline"
                    data-action="reminder-text">Erinnerungstext einfügen</button>

                @if ($event['bexio_offer_id'] && $contract->isAtLeast(\App\Enums\ContractState::AngebotErstellt) ||
                $event['bexio_invoice_id'] && $contract->isAtLeast(\App\Enums\ContractState::RechnungErstellt))
                <div class="flex flex-wrap gap-4 mt-4 text-sm">
                    @if ($event['bexio_offer_id'] && $contract->isAtLeast(\App\Enums\ContractState::AngebotErstellt))
                    <a target="_blank" class="text-orientalpink hover:underline" href="{{ $offerUrl }}">Angebot in Bexio
                        anzeigen</a>
                    @endif
                    @if ($event['bexio_invoice_id'] && $contract->isAtLeast(\App\Enums\ContractState::RechnungErstellt))
                    <a target="_blank" class="text-orientalpink hover:underline" href="{{ $invoiceUrl }}">Rechnung in
                        Bexio anzeigen</a>
                    @endif
                </div>
                @endif

                {{-- Die Werte der Buttons wertet AdminEventController::update aus (erstes Zeichen) --}}
                <div class="flex flex-wrap gap-3 pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit" class="{{ $gladegreen }}" name="submit" value="1 Buchung updaten">1 Buchung
                        updaten</button>
                    @switch($event['contract_status_id'])
                    @case(\App\Enums\ContractState::Offen)
                    <button type="submit" class="{{ $norway }}" name="submit" value="2 Angebot erstellen">2 Angebot
                        erstellen</button>
                    @break
                    @case(\App\Enums\ContractState::AngebotErstellt)
                    <button type="submit" class="{{ $ebonyclay }}" name="submit" value=" 3 Angebot versenden">3 Angebot
                        versenden</button>
                    @break
                    @case(\App\Enums\ContractState::AngebotVersendet)
                    <button type="submit" class="{{ $grannysmith }}" name="submit" value="3 Erinnerung versenden">3
                        Erinnerung versenden</button>
                    <button type="submit" class="{{ $ebonyclay }}" name="submit" value="4 Rechnung erstellen">4 Rechnung
                        erstellen</button>
                    @break
                    @case(\App\Enums\ContractState::RechnungErstellt)
                    <button type="submit" class="{{ $norway }}" name="submit" value="5 Rechnung versenden">5 Rechnung
                        versenden</button>
                    @break
                    @case(\App\Enums\ContractState::RechnungVersendet)
                    <button type="submit" class="{{ $ebonyclay }}" name="submit"
                        value="6 Genossenschafts Info versenden">6 Genossenschafts Info versenden</button>
                    @break
                    @default
                    @endswitch
                </div>
            </div>
        </x-forms.form>

        <x-forms.form :action="route('admin.events.destroy', $event)" method="DELETE" :model="$event" id="DeleteForm"
            class="flex justify-end">
            <button type="submit" name="submit-delete"
                class="confirm-delete text-sm font-medium text-red-700 hover:underline dark:text-red-500">
                Buchung löschen
            </button>
        </x-forms.form>
    </div>

    <div class="flex flex-col gap-6">
        <div class="{{ $card }}">
            <h4 class="{{ $heading }}">Reinigung</h4>
            @if ($event->cleaning_mail)
            <p class="text-sm text-gray-500 dark:text-gray-400">Die Putzfirma wurde bereits informiert.</p>
            @else
            <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">Die Putzfirma wurde noch nicht informiert.</p>
            <button type="button" class="{{ $grannysmith }}" data-action="cleaning-mail">Mail an Putzfirma</button>
            @endif
            <div id="cleaning_mail" class="mt-4" hidden>
                <x-forms.form :action="route('events.sendCleaningMail', $event)" method="POST" :model="$event"
                    class="flex flex-col gap-4">
                    <x-forms.text label="Mail Adresse:" name="cleaning_mail_address" type="email" />
                    <x-forms.text-area label="Mail Text:" name="cleaning_mail_text" rows=9 />
                    <div>
                        <button type="submit" name="submit" class="{{ $gladegreen }}">Mail versenden</button>
                    </div>
                </x-forms.form>
            </div>
        </div>

        @if($event->external && $contract->isBefore(\App\Enums\ContractState::AngebotErstellt))
        <div class="{{ $card }}">
            <h4 class="{{ $heading }}">Externe Buchung importieren</h4>
            <x-forms.text-area label="Import Buchung:" name="import-text" rows=9
                placeholder="Buchungsinformationen aus PDF 'Name' bis 'Anzahl Personen' hier einfügen..." />
            <button type="button" class="{{ $grannysmith }} mt-4" data-action="import">Externe Buchung
                importieren</button>
        </div>
        @endif

        <div class="{{ $card }}">
            <h4 class="{{ $heading }}">Verlauf</h4>
            @if ($event->notifications->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">Noch keine Einträge.</p>
            @else
            <ol class="relative border-s border-gray-200 dark:border-gray-700 ms-2">
                @foreach ($event->notifications->sortBy('created_at') as $notification)
                <li class="ms-4 {{ $loop->last ? '' : 'mb-4' }}">
                    <div
                        class="absolute w-3 h-3 bg-gray-300 rounded-full mt-1.5 -start-1.5 border border-white dark:border-gray-900 dark:bg-gray-600">
                    </div>
                    <p class="text-gray-900 dark:text-white">{{ $notification->data['action'] ?? 'Buchung erstellt' }}
                    </p>
                    <time class="text-sm text-gray-500 dark:text-gray-400">{{
                        \Carbon\Carbon::parse($notification->created_at)->isoFormat('DD.MM.YY') }}</time>
                </li>
                @endforeach
            </ol>
            @endif
        </div>
    </div>
</div>
@endsection
@push('scripts')
@php
    $eventFormConfig = [
        'positions' => $positions->map(fn ($position) => [
            'input' => 'position_'.$position->id,
            'bexio_code' => (int) $position->pricelist_position->bexio_code,
            'price' => (float) $position->pricelist_position->price,
        ])->values(),
        // Ein Tag gilt wie bisher als eine Nacht
        'minNights' => 1,
        'checkinCheckoutPrice' => $checkin_checkout_price,
        'calendarUrl' => route('events.calendar', $event),
        'parseUrl' => route('events.parse'),
        'createCodeUrl' => route('events.createAPICode'),
        'eventId' => $event->id,
        'cleaningMailAddress' => config('mail.cleaning_mail'),
        'cleaningMailSent' => (bool) $event->cleaning_mail,
        'cancelledContractState' => \App\Enums\ContractState::Storniert->value,
    ];
@endphp
<script type="application/json" id="event-form-config">@json($eventFormConfig)</script>
@vite('resources/js/admin/event-form.js')
@endpush
