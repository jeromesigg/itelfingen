@component('mail::message')
    <h1>Dein Feedback für das Ferienhaus Itelfingen</h1>
    Guten Tag {{$event->firstname}} {{$event->name}},
    <br><br>
    Heute ist dein Abreisetag in unserem Ferienhaus Itelfingen. Bitte beachte die Hausrückgabe-Checkliste in der Hausordnung und bei der Haustüre.
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
    <div class="header">
        <div class="breadcrumb flat"  >
            <a href="#" class="past">Buchungsanfrage</a>
            <a href="#" class="past">Angebot</a>
            <a href="#" class="past">Definitive Buchung</a>
            <a href="#" class="past">Letzte Infos</a>
            <a href="#" class="active">Aufenthalt</a>
            <a href="#" class="">Rechnung</a>
        </div>
    </div>
@endcomponent
