@component('mail::message')
<h1>Deine Rechnung zur Buchung vom {{Carbon\Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y')}} bis {{Carbon\Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y')}}</h1>
Guten Tag {{$event['firstname'] . ' ' . $event['name']}},
<br><br>
Vielen Dank für deinen Aufenthalt im Ferienhaus Itelfingen, wir hoffen, dass ihr einen schönen Aufenthalt hattet.

Unter folgendem Link kannst du deine Rechnung für deinen Aufenthalt vom {{Carbon\Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y')}} ansehen:
@component('mail::button', ['url' => $link, 'color' => 'success'])
Zur Rechnung
@endcomponent
<br>
{{$additional_text}}
<br>
Wir bitten um Bezahlung über einer der zur Verfügung stehenden Zahlungsmöglichkeiten.
<br>
Für Rückfragen zu dieser Rechnung stehen wir jederzeit gerne zur Verfügung.
<br><br>
Freundliche Grüsse, <br>
Das Ferienhaus Itelfingen
<br>
<br>
In Deinem <a href="{{ config('app.url') }}/bookings/{{$event->uuid}}">Kundenkonto</a> findest Du alle wichtigen Informationen und eine Übersicht Deiner Buchung.
<div class="breadcrumbs">
    <a href="{{ config('app.url') }}/bookings/{{$event->uuid}}">
        <img src="https://itelfingen.ch/img/mail/6.png" class="logo img-header" style="display: block; margin: auto" width="60%" alt="Rechnung"/>
    </a>
</div>
@endcomponent

