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

    @foreach ($event_array as $event_element)   
        @if (count($event_element['events'])>0)
        <h3>{{$event_element['text']}}:</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="row" style="text-align:left" width="17%">Datum</th>
                        <th scope="row" style="text-align:left" width="10%">Name</th>
                        <th scope="row" style="text-align:left" width="25%">E-Mail</th>
                        <th scope="row" style="text-align:left" width="22%">Anlass / Gruppe</th>
                        <th scope="row" style="text-align:left" width="10%">Strasse</th>
                        <th scope="row" style="text-align:left" width="15%">PlZ / Ort</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($event_element['events'] as $event)
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
    @endforeach
    <p>Freundliche Grüsse,<p>
    <p>Das Ferienhaus Itelfingen<p>
</body>
</html>