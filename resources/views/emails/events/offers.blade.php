@component('mail::message')
<h1>Deine Buchung vom {{Carbon\Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y')}} bis {{Carbon\Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y')}}</h1>
Guten Tag {{$event['firstname'] . ' ' . $event['name']}},
<br><br>
Vielen Dank für Dein Interesse an das Ferienhaus Itelfingen und Buchung vom {{Carbon\Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y')}} bis {{Carbon\Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y')}}

Unter folgendem Link kannst Du deine Buchung für Deinen Aufenthalt vom {{Carbon\Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y')}} über {{$total}} ansehen:
@component('mail::button', ['url' => $link, 'color' => 'success'])
Zum Angebot
@endcomponent
{{$link}}

Wir hoffen, dass Die Buchung Deinen Wünschen entspricht und würden uns über Deine Bestätigung freuen. Die Bestätigung beinhaltet ebenfalls ein Akzeptieren der Hausordnung im angehängten PDF.
<br>
Für Rückfragen und weitere Informationen stehen wir gerne jederzeit zur Verfügung.
<br><br>
Freundliche Grüsse, <br>
Das Ferienhaus Itelfingen
@endcomponent
