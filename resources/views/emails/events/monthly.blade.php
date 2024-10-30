@component('mail::message')
    <h1>Reinigungen im {{$month}} für das Ferienhaus Itelfingen</h1>
    Guten Tag,
    Hier noch die Liste der Reinigungen für den Monat {{$month}}:<br><br>
    @foreach ($events as $event)
        - am {{Carbon\Carbon::parse($event->end_date)->addDay()->locale('de')->isoFormat('dd DD.MM')}} (Gruppe vom {{Carbon\Carbon::parse($event->start_date)->locale('de')->format('d.m')}} - {{Carbon\Carbon::parse($event->end_date)->locale('de')->format('d.m')}}) {{$event['event_status_id'] < config('status.event_bestaetigt') ? 'provisorisch' : ''}}{{$event['event_status_id'] === config('status.event_storniert') ? 'storniert' : ''}}<br>
    @endforeach
    <br>
    Freundliche Grüsse,<br>
    Das Ferienhaus Itelfingen
@endcomponent
