{{-- Faktenkasten zur Buchung. Achtung Markdown: Zeilen nicht einrücken, sonst entsteht ein Codeblock. $extra: zusätzliche Zeilen [Bezeichnung => Wert] --}}
@php
$start = \Carbon\Carbon::parse($event->start_date)->locale('de_CH');
$end = \Carbon\Carbon::parse($event->end_date)->locale('de_CH');
$nights = $event->total_days.' '.($event->total_days == 1 ? 'Nacht' : 'Nächte');
@endphp
@component('mail::table')
| Deine Buchung | {{ $event->number() }} |
| :------------ | :------------ |
| Zeitraum | {{ $start->isoFormat('dd, DD.MM.YYYY') }} – {{ $end->isoFormat('dd, DD.MM.YYYY') }} ({{ $nights }}) |
@if ($event->total_people)
| Personen | {{ $event->total_people }} |
@endif
@foreach ($extra ?? [] as $label => $value)
| {{ $label }} | {{ $value }} |
@endforeach
@endcomponent
