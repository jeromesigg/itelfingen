@component('mail::message')
<h1>Dein Feedback für das Ferienhaus Itelfingen</h1>
Guten Tag {{$event->firstname}} {{$event->name}},
<br><br>
Heute ist dein Abreisetag in unserem Ferienhaus Itelfingen. Bitte beachte die Hausrückgabe-Checkliste in der Hausordnung und bei der Haustüre. Der Code für den Abfalleimer ist 4315. Bitte entsorge den Abfall in den Containern gemäss Hausordnung.
Wir hoffen, dass alles funktioniert hat und du wunderbare Tage verbringen konntest.
<br>
Sollte sich etwas an den Anzahl Übernachtung gegenüber der Offerte geändert haben, kannst du und dies gerne noch melden.
<br>
Bitte fülle noch die Rückmeldung aus, damit wir uns in Zukunft noch weiter verbessern können.
@component('mail::button', ['url' => 'https://forms.gle/RMWyPzs8wauakQam9', 'color' => 'success'])
    Zum Feedback-Formular
@endcomponent
Wir würden uns freuen, dich wieder einmal im Ferienhaus Itelfingen begrüssen zu dürfen.
<br><br>
Freundliche Grüsse,<br>
Das Ferienhaus Itelfingen
<br>
<br>
In Deinem <a href="{{ config('app.url') }}/bookings/{{$event->uuid}}">Kundenkonto</a> findest Du alle wichtigen Informationen und eine Übersicht Deiner Buchung.
<div class="breadcrumbs">
    <a href="{{ config('app.url') }}/bookings/{{$event->uuid}}">
        <img src="https://itelfingen.ch/img/mail/5.png" class="logo img-header" style="display: block; margin: auto" width="60%" alt="Feedback"/>
    </a>
</div>
@endcomponent
