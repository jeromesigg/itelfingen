@component('mail::message')
<h1>Deine Buchung vom {{Carbon\Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y')}} bis {{Carbon\Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y')}}</h1>
Guten Tag {{$event['firstname'] . ' ' . $event['name']}},
<br><br>
Vielen Dank für Dein Interesse an das Ferienhaus Itelfingen und die Buchung vom {{Carbon\Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y')}} bis {{Carbon\Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y')}}

Unter folgendem Link kannst Du deine Buchung und die Hausordnung für Deinen Aufenthalt vom {{Carbon\Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y')}} über {{$total}} ansehen:
@component('mail::button', ['url' => $link, 'color' => 'success'])
Zum Angebot
@endcomponent
<br>
{{$additional_text}}
<br>
Die Schlussrechnung erhälst Du nach der Buchung, somit können allfällige akzeptierte Anpassungen an den Übernachtungen noch berücksichtigt werden, Du musst die Angebots-Rechnung also nicht bezahlen. Bei grossen Abweichung melde Dich bitte vorher bei der Verwaltung, damit diese akzeptiert wird.
<br>
Wir hoffen, dass die Buchung Deinen Wünschen entspricht und würden uns über Deine Bestätigung freuen. Die Bestätigung beinhaltet ebenfalls ein Akzeptieren der Hausordnung im angehängten PDF. Kontrolliere vor allem das Datum und die Anzahl Personen.
<br>
Für Rückfragen und weitere Informationen stehen wir gerne jederzeit zur Verfügung.
<br><br>
Freundliche Grüsse, <br>
Das Ferienhaus Itelfingen
<br>
<div class="breadcrumbs">
    <img src="https://itelfingen.ch/img/mail/2.png" class="logo img-header" style="display: block; margin: auto" width="60%" alt="Angebot"/>
</div>
@endcomponent
