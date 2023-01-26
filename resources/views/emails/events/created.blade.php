@component('mail::message')
<h1>Ihre Buchung für das Ferienhaus Itelfingen</h1>
@component('mail::table')
    |        |          |
    | :------------- | :------------- |
    | Datum | {{Carbon\Carbon::parse($event->start_date)->format('d.m.Y')}} bis {{Carbon\Carbon::parse($event->end_date)->format('d.m.Y')}}      |
    | Anzahl Übernachtungen | {{$event->total_days}} |
    | Name  | {{$event->firstname}} {{$event->name}} |
    | E-Mail | {{$event->email}} |
    | Anlass / Gruppe | {{$event->group_name}} |
    | Strasse |  {{$event->street}} |
    | PlZ / Ort | {{$event->plz}} {{$event->city}} |
    | Telefon | {{$event->telephone}} |
    @foreach ($event->positions as $position)
        @if($position->pricelist_position['bexio_code'] >= 50 && $position['amount'] >= 1)
            | {{$position->pricelist_position['name']}} | {{$position['amount']}} |
        @endif
    @endforeach
    | Voraussichtliches Total | {{$event->total_amount}} |
    | Bemerkung | {{$event->comment}} |
@endcomponent

Sobald die Buchung überprüft wurde, erhalten Sie von unserem Hausverwalter eine E-Mail mit dem Vertrag und allen weiteren Dokumenten.
@endcomponent
