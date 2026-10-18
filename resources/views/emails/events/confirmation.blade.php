@component('mail::message')
# Deine Buchung ist definitiv

@include('emails.events.partials.greeting')

Deine Buchung für das Ferienhaus Itelfingen ist definitiv – wir freuen uns auf dich!

@include('emails.events.partials.facts')

Etwa zwei Wochen vor deinem Aufenthalt erhältst du die letzten Infos mit Türcode und Parkkarte per E-Mail. Im Anhang findest du einen Kalendereintrag für deinen Aufenthalt.

@component('mail::button', ['url' => route('faq'), 'color' => 'success'])
Häufige Fragen ansehen
@endcomponent

@if ($additional_text)
{{ $additional_text }}

@endif
Bei Fragen sind wir gerne für dich da.

@include('emails.events.partials.closing', ['step' => 3])
@endcomponent
