@component('mail::message')
# Dein Interesse an der Genossenschaft

@include('emails.events.partials.greeting')

Du möchtest mehr über die Genossenschaft Ferienhaus Itelfingen und einen Beitritt erfahren – das freut uns! Alle Infos findest du auf unserer Webseite. Dort kannst du dich auch direkt bewerben.

@component('mail::button', ['url' => route('applications'), 'color' => 'success'])
Mehr erfahren und bewerben
@endcomponent

@if ($additional_text)
{{ $additional_text }}

@endif
Bei Fragen sind wir gerne für dich da.

@include('emails.events.partials.closing')
@endcomponent
