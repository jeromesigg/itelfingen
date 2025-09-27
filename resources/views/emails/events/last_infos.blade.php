@component('mail::message')
<h1>Die letzten Informationen zu deiner Buchung für das Ferienhaus Itelfingen</h1>
Guten Tag {{$event->firstname}} {{$event->name}},
<br><br>
Im angehängten PDF findest du die wichtigsten Informationen zu deinem baldigen Aufenthalt im Ferienhaus Itelfingen und die Parkkarte für das Ferienhaus Itelfingen. 
Hinterlege in jedem Auto eine Parkkarte, da sonst die Gefahr einer Busse in der Höhe von 240.- droht. Und beachte die Nachtruhe um 22:00 Uhr, Danke.
<br>
@component('mail::button', ['url' => route('faq'), 'color' => 'success'])
    Besuche auch unsere FAQ-Seite
@endcomponent
@component('mail::panel')
    Der Code für die Türe ist {{$event->code}}.
@endcomponent
<br>
{{$additional_text}}
<br>
Bei Notfällen erreichst du uns direkt: <br>
- Lukas Affolter (079 810 30 05), <br>
- Jérôme Sigg (079 587 56 51) oder <br>
- Matthias Bächler (079 386 73 20)
<br><br>
Freundliche Grüsse,<br>
Das Ferienhaus Itelfingen
<br>
<br>
In Deinem <a href="{{ config('app.url') }}/bookings/{{$event->uuid}}">Kundenkonto</a> findest Du alle wichtigen Informationen und eine Übersicht Deiner Buchung.
<div class="breadcrumbs">
    <a href="{{ config('app.url') }}/bookings/{{$event->uuid}}">
        <img src="https://itelfingen.ch/img/mail/4.png" class="logo img-header" style="display: block; margin: auto" width="60%" alt="Letzte Infos"/>
    </a>
</div>
@endcomponent
