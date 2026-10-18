@component('mail::message')
# Deine Rechnung

@include('emails.events.partials.greeting')

Vielen Dank für deinen Aufenthalt im Ferienhaus Itelfingen – wir hoffen, ihr hattet eine schöne Zeit.

@include('emails.events.partials.facts')

Die Rechnung findest du im Anhang und online:

@component('mail::button', ['url' => $link, 'color' => 'success'])
Zur Rechnung
@endcomponent

Bitte bezahle sie über eine der angegebenen Zahlungsmöglichkeiten.

@if ($additional_text)
{{ $additional_text }}

@endif
Bei Fragen zur Rechnung sind wir gerne für dich da.

@include('emails.events.partials.closing', ['step' => 6])
@endcomponent
