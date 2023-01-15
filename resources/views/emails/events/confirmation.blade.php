@component('mail::message')
    <h1>Deine Buchung für das Ferienhaus Itelfingen</h1>
    Guten Tag {{$event->firstname}} {{$event->name}},
    <br><br>
    Deine Buchung für das Ferienhaus Itelfingen vom {{Carbon\Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y')}} bis {{Carbon\Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y')}} ist definitiv.
    <br>
    Du wirst zwei Wochen vor deinem Aufenthalt noch die letzten Informationen per E-Mail erhalten.
    <br>
    Für Rückfragen und weitere Informationen stehen wir gerne jederzeit zur Verfügung.
    <br><br>
    Freundliche Grüsse,<br>
    Das Ferienhaus Itelfingen
@endcomponent
