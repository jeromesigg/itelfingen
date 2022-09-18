@component('mail::message')
    <h1>Deine Rechnung zum Genossenschaftsschein der Genossenschaft Ferienhaus Itelfingen</h1>
    Guten Tag {{$application['firstname'] . ' ' . $application['name']}},
    <br><br>
    Vielen Dank für deine Bewerbung für die Genossenschaft Ferienhaus Itelfingen. Der Vorstand hat deine Bewerbung geprüft und wir freuen uns,
    dich in der Genossenschaft willkommen zu heissen. Mit Bezahlung der Rechnung für deinen Genossenschafts-Anteil wird der Genossenschafter:innen-Status vergeben.
    Unter folgendem Link kannst du die Rechnung für deinen Genossenschaftsschein über CHF 100 ansehen und bezahlen:
    @component('mail::button', ['url' => $link, 'color' => 'success'])
        Zur Rechnung
    @endcomponent
    Wir bitten um Bezahlung über eine der zur Verfügung stehenden Zahlungsmöglichkeiten innerhalb von 30 Tagen.
    <br>
    Für Rückfragen zu dieser Rechnung stehen wir jederzeit gerne zur Verfügung.
    <br><br>
    Freundliche Grüsse, <br>
    Der Vorstand <br>
    Genossenschaft Ferienhaus Itelfingen
@endcomponent
