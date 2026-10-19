@extends('layouts.admin')
@section('pageTitle', 'Buchungen')
@section('content')
@php
$card = 'p-6 bg-white border border-gray-200 rounded-lg shadow-xs dark:bg-gray-800 dark:border-gray-700';
$heading = 'mb-4 text-xl font-semibold dark:text-white';
$btn = 'inline-flex items-center justify-center text-white font-medium rounded-lg text-sm px-5 py-2.5
focus:outline-hidden focus:ring-4';
$grannysmith = $btn.' bg-grannysmith hover:opacity-90 focus:ring-grannysmith/50';
$gladegreen = $btn.' bg-gladegreen hover:opacity-90 focus:ring-gladegreen/50';
@endphp

<div class="mb-6">
    <a href="{{ route('admin.events.index') }}"
        class="inline-flex items-center gap-1 mb-2 text-sm text-orientalpink hover:underline">
        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5 12h14M5 12l4-4m-4 4 4 4" />
        </svg>
        Zurück zur Übersicht
    </a>
    <h3 class="text-3xl font-bold dark:text-white">Buchung erstellen</h3>
</div>

<div class="mb-6">
    @include('includes.form_error')
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <div class="{{ $card }} mb-6">
        <h4 class="{{ $heading }}">Kalender</h4>
        <div id="booking-calendar">
            <p class="text-sm text-gray-500 dark:text-gray-400">Start und Ende wählen, um die Buchungen in diesem Zeitraum zu sehen.</p>
        </div>
    </div>

    <div class="{{ $card }} mb-6 lg:col-span-3">
        <h4 class="{{ $heading }}">Vorausfüllen</h4>
        <div class="flex flex-col lg:flex-row lg:items-end gap-4">
            <div class="grow">
                <x-forms.text-area label="Import Mail:" name="import-text" rows=4
                    placeholder="Anfrage-Mail von 'Anfragenummer' bis 'Kommentar der Gruppe' hier einfügen..." />
            </div>
            <div class="flex flex-wrap gap-2 shrink-0">
                <button type="button" class="{{ $grannysmith }}" data-action="import">Import übernehmen</button>
                <button type="button" class="{{ $grannysmith }}" data-action="intern">Als interne Buchung
                    ausfüllen</button>
            </div>
        </div>
    </div>
</div>

<x-forms.form :action="route('admin.events.store')" class="flex flex-col gap-6">
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="{{ $card }}">
            <h4 class="{{ $heading }}">Kontakt &amp; Zeitraum</h4>
            <div class="grid grid-cols-1 sm:grid-cols-6 gap-4">
                <div class="sm:col-span-3">
                    <x-forms.text label="Start:" name="start_date" type="date" required=true />
                </div>
                <div class="sm:col-span-3">
                    <x-forms.text label="Ende:" name="end_date" type="date" required=true />
                </div>
                <div class="sm:col-span-3">
                    <x-forms.text label="Name:" name="name" required=true />
                </div>
                <div class="sm:col-span-3">
                    <x-forms.text label="Vorname:" name="firstname" />
                </div>
                <div class="sm:col-span-6">
                    <x-forms.text label="Gruppe/Anlass:" name="group_name" />
                </div>
                <div class="sm:col-span-3">
                    <x-forms.text label="Email:" name="email" required=true type="email" />
                </div>
                <div class="sm:col-span-3">
                    <x-forms.text label="Telefon:" name="telephone" />
                </div>
                <div class="sm:col-span-5">
                    <x-forms.text label="Strasse:" name="street" required=true />
                </div>
                <div class="sm:col-span-1">
                    <x-forms.text label="Nr.:" name="house_number" />
                </div>
                <div class="sm:col-span-2">
                    <x-forms.text label="PLZ:" name="plz" required=true type="number" />
                </div>
                <div class="sm:col-span-4">
                    <x-forms.text label="Ortschaft:" name="city" required=true />
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-6">
            <div class="{{ $card }}">
                <h4 class="{{ $heading }}">Positionen &amp; Preis</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach ($positions as $index => $position)
                    @if ($position['bexio_code']
                    <50) <x-forms.hidden name="{{'positions['.$position['bexio_code'].']'}}"
                        id="{{'position_'.$position['id']}}" value="1" />
                    @else
                    <div>
                        <x-forms.text label="{{ $position['name'] . ' ('. $position['price'] . ' CHF)'}}"
                            name="{{'positions['.$position['bexio_code'].']'}}" type="number"
                            id="{{'position_'.$position['id']}}" />
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
            </div>

            <div class="{{ $card }}">
                <h4 class="{{ $heading }}">Verwaltung</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-forms.select label="Status:" name="event_status_id" :collection="$event_statuses" />
                    </div>
                    <div>
                        <x-forms.text label="Externe Buchungs-Nr.:" name="foreign_key" />
                    </div>
                    <div>
                        <x-forms.select label="Verantwortlicher:" name="user_id" required=true :collection="$users" />
                    </div>
                    <div>
                        <x-forms.text label="Tür-Code:" name="code" type="number" />
                    </div>
                    <div>
                        <x-forms.text-area label="Bemerkungen:" name="comment" rows=3 />
                    </div>
                    <div>
                        <x-forms.text-area label="Bemerkung (intern):" name="comment_intern" rows=3 />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="{{ $gladegreen }}">Buchung erstellen</button>
    </div>
</x-forms.form>
@endsection
@push('scripts')
@php
    $adults = $positions->firstWhere('bexio_code', 120);
    $eventFormConfig = [
        'positions' => $positions->map(fn ($position) => [
            'input' => 'position_'.$position->id,
            'bexio_code' => (int) $position->bexio_code,
            'price' => (float) $position->price,
        ])->values(),
        'minNights' => 0,
        'calendarUrl' => route('events.calendar'),
        'parseUrl' => route('events.parse'),
        // Anzahl Personen aus dem Import zählt als Erwachsene, Normaltarif
        'importPersonsInput' => $adults ? 'position_'.$adults->id : null,
        'importStatus' => \App\Enums\BookingState::Neu->value,
        'internStatus' => \App\Enums\BookingState::Eigene->value,
    ];
@endphp
<script type="application/json" id="event-form-config">@json($eventFormConfig)</script>
@vite('resources/js/admin/event-form.js')
@endpush
