@component('mail::message')
# Deine Buchungsanfrage ist eingegangen

@include('emails.events.partials.greeting')

Vielen Dank für deine Anfrage für das Ferienhaus Itelfingen. Hier nochmals deine Angaben:

@php
$extra = [];
foreach ($event->positions as $position) {
    if ($position->pricelist_position['bexio_code'] >= 50 && $position['amount'] >= 1) {
        $extra[$position->pricelist_position['name']] = $position['amount'];
    }
}
$extra['Voraussichtliches Total'] = 'CHF '.number_format((float) $event->total_amount, 2, '.', '’');
@endphp
@include('emails.events.partials.facts', ['extra' => $extra])

{{-- Zeilen hier nicht auf @endif enden lassen: Blade verschluckt den Umbruch danach, der folgende Absatz rutschte sonst in die Zeile --}}
**Deine Kontaktdaten**<br>
{{ $event->firstname }} {{ $event->name }}@if ($event->group_name), {{ $event->group_name }}@endif<br>
{{ $event->street }} {{ $event->house_number }}, {{ $event->plz }} {{ $event->city }}<br>
{{ $event->email }}{{ $event->telephone ? ' · '.$event->telephone : '' }}

@if ($event->comment)
**Deine Bemerkung**<br>
{!! nl2br(e($event->comment)) !!}
@endif

**Wie geht es weiter?**<br>
Wir prüfen deine Anfrage und senden dir anschliessend ein Angebot per E-Mail. Falls nichts ankommt, schau bitte auch im Spam-Ordner nach.

@include('emails.events.partials.closing', ['step' => 1])
@endcomponent
