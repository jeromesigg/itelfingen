@component('mail::message')
    <h1>Wöchentliches Erinnerungsmail</h1>
    Guten Tag,<br><br>
    @if (count($contacts)>0)
        <h3>Folgende Anfragen sind noch offen:</h3>
        @component('mail::table')
            | Name | E-Mail | Betreff | Text |
            | :------------- | :------------- |  :------------- |  :------------- |
            @foreach ($contacts as $contact)
                | {{$contact->name}} | {{$contact->email}} | {{$contact->subject}} | {{$contact->content}} |
            @endforeach
        @endcomponent
    @endif
    @foreach ($events as $event_element)
        @if (count($event_element['events'])>0)
            <h3>{{$event_element['text']}}:</h3>
            @component('mail::table')
                | Datum | Name | E-Mail | Anlass / Gruppe | Kommentar Intern |
                | :------------- | :------------- |  :------------- |  :------------- |  :------------- |
                @foreach ($event_element['events'] as $event)
                    | {{Carbon\Carbon::parse($event->start_date)->format('d.m.Y')}} bis {{Carbon\Carbon::parse($event->end_date)->format('d.m.Y')}} | <a href="{{route('admin.events.edit', $event->id)}}">{{$event->firstname}} {{$event->name}}</a> | {{$event->email}} | {{$event->group_name}} | {{$event->comment_intern}} |
                @endforeach
            @endcomponent
        @endif
    @endforeach
    Freundliche Grüsse,<br>
    Das Ferienhaus Itelfingen
@endcomponent
