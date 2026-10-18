@component('mail::message')
# Dein Angebot für das Ferienhaus Itelfingen

@include('emails.events.partials.greeting')

Vielen Dank für dein Interesse am Ferienhaus Itelfingen. Gerne bieten wir dir folgende Buchung an:

@include('emails.events.partials.facts', ['extra' => ['Total' => $total]])

@component('mail::panel')
**Bitte bestätige das Angebot bis {{ $valid_until }}.**<br>
Öffne dazu das Angebot über den Button unten und nimm es dort mit einem Klick an. Ohne Bestätigung müssen wir die Reservation danach leider stornieren.
@endcomponent

@component('mail::button', ['url' => $link, 'color' => 'success'])
Zum Angebot
@endcomponent

Kontrolliere bitte vor allem das Datum und die Anzahl Personen. Mit der Bestätigung akzeptierst du auch die Hausordnung (im Anhang).

Die Angebots-Rechnung musst du nicht bezahlen: Die Schlussrechnung erhältst du nach deinem Aufenthalt, so können wir Änderungen bei den Übernachtungen noch berücksichtigen. Bei grösseren Abweichungen melde dich bitte vorher bei uns.

@if ($additional_text)
{{ $additional_text }}

@endif
Bei Fragen sind wir gerne für dich da.

@include('emails.events.partials.closing', ['step' => 2])
@endcomponent
