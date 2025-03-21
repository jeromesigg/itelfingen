@component('mail::message')
<h1>Deine Interesse für die Genossenschaft Ferienhaus Itelfingen</h1>
Guten Tag {{$event->firstname}} {{$event->name}},
<br><br>
Gemäss deiner Rückmeldung wünscht du noch nähere Informationen zu unserer Genossenschaft bzw. zum Beitritt dieser.
Du findest alle Informationen dazu auf unserer Homepage, dort hast du auch die Möglichkeit, direkt das Formular auszufüllen, falls du beitreten möchtest.
<br>
@component('mail::button', ['url' => route('applications'), 'color' => 'success'])
    Zur Bewerbung
@endcomponent
<br>
{{$additional_text}}
<br>
Für Rückfragen und weitere Informationen stehen wir gerne jederzeit zur Verfügung.
<br><br>
Freundliche Grüsse,<br>
Das Ferienhaus Itelfingen
<br>
<br>
In Deinem <a href="{{ config('app.url') }}/bookings/{{$event->uuid}}">Kundenkonto</a> findest Du alle wichtigen Informationen und eine Übersicht Deiner Buchung.
@endcomponent
