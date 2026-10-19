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
                <button type="button" class="{{ $grannysmith }}" onclick="createExtern()">Import übernehmen</button>
                <button type="button" class="{{ $grannysmith }}" onclick="createIntern()">Als interne Buchung
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
                    <x-forms.text label="Start:" name="start_date" type="date" required=true
                        onchange="Total_Change()" />
                </div>
                <div class="sm:col-span-3">
                    <x-forms.text label="Ende:" name="end_date" type="date" required=true onchange="Total_Change()" />
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
                        id="{{'position_'.$position['id']}}" onChange="Total_Change()" value="1" />
                    @else
                    <div>
                        <x-forms.text label="{{ $position['name'] . ' ('. $position['price'] . ' CHF)'}}"
                            name="{{'positions['.$position['bexio_code'].']'}}" type="number"
                            id="{{'position_'.$position['id']}}" onChange="Total_Change()" />
                    </div>
                    @endif
                    @endforeach
                    <x-forms.hidden name="total_people" />
                </div>
                <div
                    class="grid grid-cols-2 md:grid-cols-3 gap-4 pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                    <div>
                        <x-forms.text label="Tage:" name="total_days" type="number" required=true
                            onChange="Total_Change()" />
                    </div>
                    <div>
                        <x-forms.text label="Rabatt [%]:" name="discount" type="number" onChange="Total_Change()" />
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
<script type="module">
    function createIntern() {
        $('#name').val("Interne Buchung");
        $('#street').val("Itelfingen");
        $('#house_number').val("3");
        $('#plz').val("6344");
        $('#city').val("Meierskappel");
        $('#email').val("verwalter@itelfingen.ch");
        $('#total_people').val("0");
        $('#total_days').val("0");
        $('#total_amount').val("0");
        $('#code').val("290393");
        $('#event_status_id').val(@json(\App\Enums\BookingState::Eigene->value));
    }
    function createExtern() {
        const text = document.getElementById('import-text').value;

        fetch('{{ route("events.parse") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ text }),
        })
        .then(res => res.json())
        .then(data => {
            // Felder befüllen
            document.getElementById('foreign_key').value        = data.buchungsnummer ?? '';
            document.getElementById('name').value        = data.name ?? '';
            document.getElementById('firstname').value        = data.vorname ?? '';
            document.getElementById('group_name').value        = data.gruppe ?? '';
            document.getElementById('street').value        = data.strasse ?? 'Itelfingen';
            document.getElementById('house_number').value        = data.hausnummer ?? '3';
            document.getElementById('plz').value        = data.plz ?? '6344';
            document.getElementById('city').value        = data.stadt ?? 'Meierskappel';
            document.getElementById('email').value        = data.email ?? 'verwalter@itelfingen.ch';
            document.getElementById('start_date').value = data.mietdauer_von ?? '';
            document.getElementById('end_date').value = data.mietdauer_bis ?? '';
            document.getElementById('position_2').value = data.anzahl_personen ?? '';
            $('#event_status_id').val(@json(\App\Enums\BookingState::Neu->value));
            Total_Change();
            UpdateCalendar()
        })
        .catch(err => console.error('Fehler:', err));
    }
    function Total_Change() {
        var start_date = new Date(document.getElementById('start_date').value);
        var end_date = new Date(document.getElementById('end_date').value);
        var days = (end_date - start_date)/(24*3600*1000);
        var positions = @json($positions);
        var total_amount = 0, id = 0, total_person = 0;
        var discount = (100 - (parseInt(document.getElementById("discount").value) || 0)) / 100 ;
        positions.forEach(position => {
            id = 'position_' + position['id'];
            var person = position['bexio_code'] < 100 ? 0 : parseInt(document.getElementById(id).value);
            person = person || 0;
            var subtotal = 0
            if(days === 0){
                if(position['bexio_code'] < 50){
                    subtotal = position['price'] / 2;
                }
                else if(position['bexio_code'] < 100) {
                    subtotal =position['price']
                }
            }
            else {
                if(position['bexio_code'] < 50){
                    subtotal = position['price'];
                }
                else if(position['bexio_code'] > 100) {
                    subtotal = parseInt(document.getElementById(id).value) * position['price'] * days * discount || 0;
                }
            }
            if( position['bexio_code']>200){
                subtotal =  Math.max(parseInt(document.getElementById(id).value) -3,0) * position['price'] * Math.max(days,1) || 0;
            }
            total_amount += subtotal;
            total_person += person;
        });
        $("#total").text(total_amount);
        $("#total_days").val(days);
        $("#total_amount").val(total_amount);
        $("#total_people").val(total_person);
    }
    window.createExtern = createExtern;
    window.createIntern = createIntern;
    window.Total_Change = Total_Change;

    // Kalender mit den Daten aus dem Formular laden (nur Anzeige)
    let calendarRequest = null;
    function UpdateCalendar() {
        const start = document.getElementById('start_date').value;
        const end = document.getElementById('end_date').value;
        if (!start || !end || end < start) {
            return;
        }
        calendarRequest?.abort();
        calendarRequest = new AbortController();
        fetch('{{ route('events.calendar') }}?' + new URLSearchParams({ start_date: start, end_date: end }), {
            headers: { 'Accept': 'application/json' },
            signal: calendarRequest.signal,
        })
            .then(response => response.ok ? response.text() : null)
            .then(html => {
                if (html) {
                    document.getElementById('booking-calendar').innerHTML = html;
                }
            })
            .catch(() => {});
    }
    ['start_date', 'end_date'].forEach(id => {
        document.getElementById(id).addEventListener('change', UpdateCalendar);
    });
    // Nach einem Validierungsfehler sind die Daten schon ausgefüllt
    UpdateCalendar();
</script>
@endpush
