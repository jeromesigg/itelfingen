@component('mail::message')
    <h1>Deine Rechnung zum Genossenschaftsschein der Genossenschaft Ferienhaus Itelfingen</h1>
    Guten Tag {{$application['firstname'] . ' ' . $application['name']}},
    <br><br>
    Vielen Dank für dein Interesse an der Genossenschaft Ferienhaus Itelfingen und deiner Bewerbung als Genossenschafter:in.
    Unter folgendem Link kannst du deine Rechnung für  Deinen Genossenschaftsschein über CHF 100.- ansehen:
    @component('mail::button', ['url' => $link, 'color' => 'success'])
        Zur Rechnung
    @endcomponent
    Wir bitten um Bezahlung über einer der zur Verfügung stehenden Zahlungsmöglichkeiten.
    <br>
    Für Rückfragen zu dieser Rechnung stehen wir jederzeit gerne zur Verfügung.
    <br><br>
    Freundliche Grüsse, <br>
    Das Ferienhaus Itelfingen
@endcomponent
