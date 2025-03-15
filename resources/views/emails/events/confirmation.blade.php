@component('mail::message')
<h1>Deine Buchung für das Ferienhaus Itelfingen</h1>
Guten Tag {{$event->firstname}} {{$event->name}},
<br><br>
Deine Buchung für das Ferienhaus Itelfingen vom {{Carbon\Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y')}} bis {{Carbon\Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y')}} ist definitiv.
<br>
Du wirst zwei Wochen vor deinem Aufenthalt noch die letzten Informationen per E-Mail erhalten.
<br>
@component('mail::button', ['url' => route('faq'), 'color' => 'success'])
    Besuche auch unsere FAQ-Seite
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
<div class="breadcrumbs">
    <a href="{{ config('app.url') }}/bookings/{{$event->uuid}}">
        <img src="https://itelfingen.ch/img/mail/3.png" class="logo img-header" style="display: block; margin: auto" width="60%" alt="Bestätigung"/>
    </a>
</div>
@endcomponent
