@component('mail::message')
<h1>Ihre Buchungs-Anfrage für das Ferienhaus Itelfingen</h1>
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
@endcomponent
Bemerkung <br>
{{$event->comment}}
<br>
<br>
Sobald die Buchung überprüft wurde, erhalten Sie von unserem Hausverwalter eine E-Mail mit einem Angebot und allen weiteren Dokumenten (Überprüfen Sie auch ihren Spam-Ordner).
<br>
<div class="header">
    <div class="breadcrumb flat"  >
        <a href="#" class="active">Buchungsanfrage</a>
        <a href="#" class="">Angebot</a>
        <a href="#" class="">Definitive Buchung</a>
        <a href="#" class="">Letzte Infos</a>
        <a href="#" class="">Aufenthalt</a>
        <a href="#" class="">Rechnung</a>
    </div>
</div>
@endcomponent
