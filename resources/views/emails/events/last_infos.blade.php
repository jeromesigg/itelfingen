@component('mail::message')
    <h1>Ihre Buchung für das Ferienhaus Itelfingen</h1>
    Guten Tag {{$event->firstname}} {{$event->name}},
    <br><br>
    Im angehängten PDF findest du die wichtigsten Informationen zu deinem baldigen Aufenthalt im Ferienhaus Itelfingen und die Parkkarte für das Ferienhaus Itelfingen.
    <br>
    Es hat ein Hornissen Nest im Hausgiebel an der Seite, welche zur Wiese zeigt. Gemäss dem Landwirtschafts- und Walddepartement von Luzern sollen wir sie sein lassen, da Hornissen geschützt und friedlich sind.
    <br>
    @component('mail::button', ['url' => route('faq'), 'color' => 'success'])
        Besuche auch unsere FAQ-Seite
    @endcomponent
    @component('mail::panel')
    Der Code für die Türe ist {{$event->code}}.
    @endcomponent
Bei Notfällen erreichst du uns direkt: <br>
    - Lukas Affolter (079 810 30 05), <br>
    - Jérôme Sigg (079 587 56 51) oder <br>
    - Matthias Bächler (079 386 73 20)
    <br><br>
    Freundliche Grüsse,<br>
    Das Ferienhaus Itelfingen
@endcomponent
