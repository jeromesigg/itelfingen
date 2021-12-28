<!DOCTYPE html>
<html lang="de_CH">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$data['title']}}</title>
</head>    
<body>
    <p>Guten Tag,<p>

    @if (count($contacts_new)>0)
    <h3>Folgende Anfragen sind noch offen:</h3>
        <table class="table">
            <thead>
                <tr>
                    <th scope="row" style="text-align:left">Name</th>
                    <th scope="row" style="text-align:left">E-Mail</th>
                    <th scope="row" style="text-align:left">Betreff</th>
                    <th scope="row" style="text-align:left">Text</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contacts_new as $contact)
                    <tr>
                        <td>{{$contact->name}}</td>
                        <td>{{$contact->email}}</td>
                        <td>{{$contact->subject}}</td>
                        <td>{{$contact->content}}</td>
                    </tr>
                @endforeach
            </tbody>   
        </table>
    @endif

    @if (count($events_new)>0)
    <h3>Folgende Buchungen wurden noch nicht bearbeitet:</h3>
        <table class="table">
            <thead>
                <tr>
                    <th scope="row" style="text-align:left">Datum</th>
                    <th scope="row" style="text-align:left">Name</th>
                    <th scope="row" style="text-align:left">E-Mail</th>
                    <th scope="row" style="text-align:left">Anlass / Gruppe</th>
                    <th scope="row" style="text-align:left">Strasse</th>
                    <th scope="row" style="text-align:left">PlZ / Ort</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($events_new as $event)
                    <tr>
                        <td>{{Carbon\Carbon::parse($event->start_date)->format('d.m.Y')}} bis {{Carbon\Carbon::parse($event->end_date)->format('d.m.Y')}}</td>
                        <td><a href="{{route('events.edit', $event->id)}}">{{$event->firstname}} {{$event->name}}</a></td>
                        <td>{{$event->email}}</td>
                        <td>{{$event->group_name}}</td>
                        <td>{{$event->street}}</td>
                        <td>{{$event->plz}} {{$event->city}}</td>
                    </tr>
                @endforeach
            </tbody>   
        </table>
    @endif

    @if (count($events_open_offers)>0)
    <h3>Folgende Offerten wurden noch nicht angenommen:</h3>
        <table class="table">
            <thead>
                <tr>
                    <th scope="row" style="text-align:left">Datum</th>
                    <th scope="row" style="text-align:left">Name</th>
                    <th scope="row" style="text-align:left">E-Mail</th>
                    <th scope="row" style="text-align:left">Anlass / Gruppe</th>
                    <th scope="row" style="text-align:left">Strasse</th>
                    <th scope="row" style="text-align:left">PlZ / Ort</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($events_open_offers as $event)
                    <tr>
                        <td>{{Carbon\Carbon::parse($event->start_date)->format('d.m.Y')}} bis {{Carbon\Carbon::parse($event->end_date)->format('d.m.Y')}}</td>
                        <td><a href="{{route('events.edit', $event->id)}}">{{$event->firstname}} {{$event->name}}</a></td>
                        <td>{{$event->email}}</td>
                        <td>{{$event->group_name}}</td>
                        <td>{{$event->street}}</td>
                        <td>{{$event->plz}} {{$event->city}}</td>
                    </tr>
                @endforeach
            </tbody>   
        </table>
    @endif

    @if (count($events_no_cleaning_mail)>0)
    <h3>Folgende Buchungen haben noch kein Reiningungs-Mail versendet:</h3>
        <table class="table">
            <thead>
                <tr>
                    <th scope="row" style="text-align:left">Datum</th>
                    <th scope="row" style="text-align:left">Name</th>
                    <th scope="row" style="text-align:left">E-Mail</th>
                    <th scope="row" style="text-align:left">Anlass / Gruppe</th>
                    <th scope="row" style="text-align:left">Strasse</th>
                    <th scope="row" style="text-align:left">PlZ / Ort</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($events_no_cleaning_mail as $event)
                    <tr>
                        <td>{{Carbon\Carbon::parse($event->start_date)->format('d.m.Y')}} bis {{Carbon\Carbon::parse($event->end_date)->format('d.m.Y')}}</td>
                        <td><a href="{{route('events.edit', $event->id)}}">{{$event->firstname}} {{$event->name}}</a></td>
                        <td>{{$event->email}}</td>
                        <td>{{$event->group_name}}</td>
                        <td>{{$event->street}}</td>
                        <td>{{$event->plz}} {{$event->city}}</td>
                    </tr>
                @endforeach
            </tbody>   
        </table>
    @endif

    <p>Freundliche Grüsse,<p>
    <p>Das Ferienhaus Itelfingen<p>
</body>
</html>