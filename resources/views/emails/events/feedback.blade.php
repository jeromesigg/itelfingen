@component('mail::message')
    <h1>Dein Feedback für das Ferienhaus Itelfingen</h1>
    Guten Tag {{$event->firstname}} {{$event->name}},
    <br><br>
    Du konntest letzte Woche einen Aufenthalt in unserem Ferienhaus Itelfingen geniessen. Wir hoffen, dass alles funktioniert hat und du wunderbare Tage verbringen konntest.
    <br>
    Bitte fülle noch die Rückmeldung aus, damit wir uns in Zukunft noch weiter verbessern können.
    @component('mail::button', ['url' => 'https://forms.gle/RMWyPzs8wauakQam9', 'color' => 'success'])
        Zum Feedback-Formular
    @endcomponent
    Wir würden uns freuen, dich wieder einmal im Ferienhaus Itelfingen begrüssen zu dürfen.
    <br><br>
    Freundliche Grüsse,<br>
    Das Ferienhaus Itelfingen
@endcomponent
