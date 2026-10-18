@component('mail::message')
# Danke für deine Nachricht

Hallo {{ $contact->name }}

Vielen Dank für deine Nachricht an das Ferienhaus Itelfingen. Wir melden uns so schnell wie möglich bei dir. Hier nochmals eine Kopie:

@component('mail::panel')
**{{ $contact->subject }}**

{!! nl2br(e($contact->content)) !!}
@endcomponent

Vielleicht findest du die Antwort auch schon in unseren häufigen Fragen:

@component('mail::button', ['url' => route('faq'), 'color' => 'success'])
Häufige Fragen ansehen
@endcomponent

Freundliche Grüsse<br>
Das Ferienhaus Itelfingen
@endcomponent
