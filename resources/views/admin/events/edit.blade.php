@extends('layouts.admin')
@section('content')
    <section>
        <div class="container-fluid">
            <header> 
                <h3 class="display">Buchung bearbeiten</h3>
            </header>
            <div class="row">
                <div class="form-row">
                    <div class="form-group col-md-9">
                        {!! Form::model($event, ['method' => 'PATCH', 'action'=>['AdminEventController@update', $event->id], 'files' => true]) !!}
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            {!! Form::label('start_date', 'Start:') !!}
                                            {!! Form::date('start_date', null, ['class' => 'form-control', 'required', 'id' => 'start_date', 'onchange' => "Total_Change()"]) !!}
                                        </div>
                                        <div class="form-group col-md-6">
                                            {!! Form::label('end_date', 'Ende:') !!}
                                            {!! Form::date('end_date', null, ['class' => 'form-control', 'id' => 'end_date', 'onchange' => "Total_Change()"]) !!}
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            {!! Form::label('name', 'Name:') !!}
                                            {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                                        </div>
                                        <div class="form-group col-md-6">
                                            {!! Form::label('firstname', 'Vorname:') !!}
                                            {!! Form::text('firstname', null, ['class' => 'form-control']) !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('groupname', 'Gruppe/Anlass:') !!}
                                        {!! Form::text('groupname', null, ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            {!! Form::label('email', 'Email:') !!}
                                            {!! Form::email('email', null, ['class' => 'form-control']) !!}
                                        </div>
                                        <div class="form-group col-md-6">
                                            {!! Form::label('telephone', 'Telefon:') !!}
                                            {!! Form::text('telephone', null, ['class' => 'form-control']) !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('street', 'Strasse:') !!}
                                        {!! Form::text('street', null, ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            {!! Form::label('plz', 'PLZ:') !!}
                                            {!! Form::text('plz', null, ['class' => 'form-control', 'required']) !!}
                                        </div>
                                        <div class="form-group col-md-9">
                                            {!! Form::label('city', 'Ortschaft:') !!}
                                            {!! Form::text('city', null, ['class' => 'form-control']) !!}
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            {!! Form::submit('Buchung updaten', ['class' => 'btn btn-primary'])!!}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6" style="padding-left:30px">
                                    <div class="form-group">
                                        {!! Form::label('comment', 'Bemerkung:') !!}
                                        {!! Form::textarea('comment', null, ['class' => 'form-control', 'rows' =>3]) !!}
                                    </div>
                                    <div class="form-group">
                                            {!! Form::label('comment_intern', 'Bemerkung (intern):') !!}
                                            {!! Form::textarea('comment_intern', null, ['class' => 'form-control', 'rows' =>3]) !!}
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-3 form-group">
                                            {!! Form::label('other_adults', 'Erw:') !!}
                                            {!! Form::number('other_adults', null, ['class' => 'form-control', 'id' => 'other_adults', 'onchange' => "Total_Change()"]) !!}
                                        </div>
                                        <div class="col-md-3 form-group">
                                            {!! Form::label('member_adults', 'Erw (G):') !!}
                                            {!! Form::number('member_adults', null, ['class' => 'form-control', 'id' => 'member_adults', 'onchange' => "Total_Change()"]) !!}
                                        </div>
                                        <div class="col-md-3 form-group">
                                            {!! Form::label('other_kids', 'Kinder:') !!}
                                            {!! Form::number('other_kids', null, ['class' => 'form-control', 'id' => 'other_kids', 'onchange' => "Total_Change()"]) !!}
                                        </div>
                                        <div class="col-md-3 form-group">
                                            {!! Form::label('member_kids', 'Kinder (G):') !!}
                                            {!! Form::number('member_kids', null, ['class' => 'form-control', 'id' => 'member_kids', 'onchange' => "Total_Change()"]) !!}
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-3 form-group">
                                            {!! Form::label('total_days', 'Tage:') !!}
                                            {!! Form::number('total_days', null, ['class' => 'form-control', 'required', 'id' => 'total_days']) !!}
                                        </div>
                                        <div class="col-md-3 form-group">
                                            {!! Form::label('discount', 'Rabatt [%]:') !!}
                                            {!! Form::number('discount', null, ['class' => 'form-control', 'id' => 'discount', 'onchange' => "Total_Change()"]) !!}
                                        </div>
                                        <div class="col-md-3 form-group">
                                            {!! Form::label('parking', 'Anz. Parkplätze:') !!}
                                            {!! Form::number('parking', null, ['class' => 'form-control', 'id' => 'parking', 'onchange' => "Total_Change()"]) !!}
                                        </div>
                                        <div class="col-md-3 form-group">
                                            {!! Form::label('', 'Total [CHF]:') !!}<br>
                                            <span id="total"></span>.-
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-6 form-group">
                                            {!! Form::label('event_status_id', 'Status:') !!}
                                            {!! Form::select('event_status_id', $event_statuses, null, ['class' => 'form-control', 'required']) !!}
                                        </div>
                                        <div class="col-md-6 form-group">
                                            {!! Form::label('contract_status_id', 'Angebot / Rechnung:') !!}
                                            {!! Form::select('contract_status_id', $contract_statuses, null, ['class' => 'form-control', 'required']) !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('user_id', 'Verantwortlicher:') !!}
                                        {!! Form::select('user_id', $users, null, ['class' => 'form-control', 'required']) !!}
                                    </div>
                                </div>
                            </div>
                        {!! Form::close()!!}
                    </div>
                    <div class="col-md-3">
                        @switch($event['contract_status_id'])
                            @case(config('status.contract_offen'))
                                <div class="form-group">
                                    <a type='submit' class = 'btn btn-primary' href="{{ route('events.createoffer', $event->id) }}">
                                        @if(config('mail.direct_send'))
                                            Angebot erstellen & versenden
                                        @else
                                            Angebot erstellen    
                                        @endif
                                    </a> 
                                </div>
                                @break
                            @case(config('status.contract_angebot_erstellt'))
                                <div class="form-group">
                                    <a target="_blank" class = 'btn btn-primary' href="https://office.bexio.com/index.php/kb_offer/show/id/{{$event['bexio_offer_id']}}">Angebot anzeigen</a>
                                </div>
                                <div class="form-group">
                                    <a type='submit' class = 'btn btn-primary' href="{{ route('events.sendoffer', $event->id) }}">Angebot versenden</a>
                                </div>
                                @break
                            @case(config('status.contract_angebot_versendet'))
                                <div class="form-group">
                                    <a target="_blank" class = 'btn btn-primary' href="https://office.bexio.com/index.php/kb_offer/show/id/{{$event['bexio_offer_id']}}">Angebot anzeigen</a>
                                </div>
                                <div class="form-group">
                                    <a type='submit' class = 'btn btn-primary' href="{{ route('events.createinvoice', $event->id) }}">Rechnung versenden</a>
                                </div>
                                @break
                            @case(config('status.contract_rechnung_gestellt'))
                                <div class="form-group">
                                    <a target="_blank" class = 'btn btn-primary' href="https://office.bexio.com/index.php/kb_offer/show/id/{{$event['bexio_offer_id']}}">Angebot anzeigen</a>
                                </div>
                                <div class="form-group">
                                    <a target="_blank" class = 'btn btn-primary' href="https://office.bexio.com/index.php/kb_invoice/show/id/{{$event['bexio_invoice_id']}}">Rechnung anzeigen</a>
                                </div>
                                @break
                            @default
                        @endswitch
                        <br>
                        @if(!$event->cleaning_mail)
                            <div class="form-group">
                                <button class="btn btn-secondary" onclick="PrepareMail()">Mail an Putzfirma</button>
                            </div>
                            <br>
                        @endif
                        {!! Form::open(['method' => 'DELETE', 'action'=>['AdminEventController@destroy', $event->id]]) !!}
                            <div class="form-group">
                                {!! Form::submit('Buchung löschen', ['class' => 'btn btn-danger'])!!}
                            </div>
                        {!! Form::close()!!}
                        <div id="cleaning_mail" style="display: none">
                            {!! Form::open(['method' => 'POST', 'action'=>['AdminEventController@SendCleaningMail', $event->id]]) !!}
                            <div class="form-group">
                                    {!! Form::label('cleaning_mail_address', 'Mail Adresse:') !!}
                                    {!! Form::text('cleaning_mail_address', null, ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group">
                                    {!! Form::label('cleaning_mail_text', 'Mail Text:') !!}
                                    {!! Form::textarea('cleaning_mail_text', null, ['class' => 'form-control', 'rows' =>9]) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::submit('Mail versenden', ['class' => 'btn btn-secondary'])!!}
                            </div>
                            {!! Form::close()!!}
                        </div>
                        <br>
                    </div>
                </div>
            </div>
            <div class="row"> 
                @include('includes.form_error')
            </div>   
        </div>
    </section>
@endsection
@section('scripts')
<script>
    window.addEventListener("load", function(event) {
        Total_Change();
    });
    function PrepareMail() {
  		document.getElementById("cleaning_mail").style.display = "block";
          
        start_date = new Date(document.getElementById('start_date').value).toLocaleDateString();
        end_date = new Date(document.getElementById('end_date').value).toLocaleDateString();
        other_adults_total = parseInt(document.getElementById("other_adults").value) || 0;
        member_adult_total = parseInt(document.getElementById("member_adults").value) || 0;
        other_kids_total = parseInt(document.getElementById("other_kids").value) || 0;
        member_kids_total = parseInt(document.getElementById("member_kids").value) || 0;
        total = other_adults_total + member_adult_total + other_kids_total + member_kids_total;
        text = "Sehr geehrte Damen und Herren,\n" + "Wir haben eine neue Buchung für unser Ferienhaus vom " + start_date + " bis " 
        + end_date + " (" + document.getElementById("total_days").value + " " + (document.getElementById("total_days").value ==1 ? "Nacht" : "Nächte") + ") für " 
        + total + " Personen. Für einige nachfolgende Reinigung wären wir sehr dankbar.\n\n" + "Vielen Dank und freundliche Grüsse,\n" + "Verwaltung Ferienhaus Itelfingen";
	    $('#cleaning_mail_address').val(@json(config('mail.cleaning_mail')));
	    $('#cleaning_mail_text').val(text);
    }

    function Total_Change() {
        start_date = new Date(document.getElementById('start_date').value);
        end_date = new Date(document.getElementById('end_date').value);
        days = (end_date - start_date)/(24*3600*1000);
        other_adults = @json(config('pricelist.other_adults'));
        member_adults = @json(config('pricelist.member_adults'));
        other_kids = @json(config('pricelist.other_kids'));
        member_kids = @json(config('pricelist.member_kids'));
        booking = @json(config('pricelist.booking'));
        cleaning = @json(config('pricelist.cleaning'));
        parking = @json(config('pricelist.parking'));
        parking_amount = Math.max(parseInt(document.getElementById("parking").value) - 3, 0);
        parking_total = parking_amount * parking * days || 0;
        other_adults_total = parseInt(document.getElementById("other_adults").value) * other_adults * days || 0;
        member_adult_total = parseInt(document.getElementById("member_adults").value)* member_adults * days || 0;
        other_kids_total = parseInt(document.getElementById("other_kids").value) * other_kids * days || 0;
        member_kids_total = parseInt(document.getElementById("member_kids").value) * member_kids * days || 0;
        discount = (100 - (parseInt(document.getElementById("discount").value) || 0)) / 100 ;
        total_amount = (booking + cleaning + other_adults_total + member_adult_total + other_kids_total + member_kids_total + parking_total)* discount;
        $("#total_days").val(days);
        $("#total").text(total_amount);
    }
</script>
@endsection