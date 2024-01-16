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
<div class="header">
    <div class="breadcrumb flat"  >
        <a href="#" class="past">Buchungsanfrage</a>
        <a href="#" class="past">Angebot</a>
        <a href="#" class="active">Definitive Buchung</a>
        <a href="#" class="">Letzte Infos</a>
        <a href="#" class="">Aufenthalt</a>
        <a href="#" class="">Rechnung</a>
    </div>
</div>
@endcomponent
