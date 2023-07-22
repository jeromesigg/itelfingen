@extends('layouts.admin')
@section('content')
    <section>
        <div class="container-fluid">
            <header>
                <h3 class="display">Buchung {{str_pad($event['id'],5,'0', STR_PAD_LEFT)}} bearbeiten</h3>
            </header>
            <div class="form-row">
                <div class="form-group col-xl-10">
                    {!! Form::model($event, ['method' => 'PATCH', 'action'=>['AdminEventController@update', $event]]) !!}
                        <div class="form-row">
                            <div class="form-group col-xl-2 col-6">
                                {!! Form::label('start_date', 'Start:') !!}
                                {!! Form::date('start_date', null, ['class' => 'form-control', 'required', 'id' => 'start_date', 'onchange' => "Total_Change()"]) !!}
                            </div>
                            <div class="form-group col-xl-2 col-6">
                                {!! Form::label('end_date', 'Ende:') !!}
                                {!! Form::date('end_date', null, ['class' => 'form-control', 'id' => 'end_date', 'onchange' => "Total_Change()"]) !!}
                            </div>
                            <div class="form-group col-xl-3 col-6">
                                {!! Form::label('name', 'Name:') !!}
                                {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                            </div>
                            <div class="form-group col-xl-3 col-6">
                                {!! Form::label('firstname', 'Vorname:') !!}
                                {!! Form::text('firstname', null, ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-xl-2">
                                {!! Form::label('group_name', 'Gruppe/Anlass:') !!}
                                {!! Form::text('group_name', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-xl-3 col-6">
                                {!! Form::label('email', 'Email:') !!}
                                {!! Form::email('email', null, ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-xl-2 col-6">
                                {!! Form::label('telephone', 'Telefon:') !!}
                                {!! Form::text('telephone', null, ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-xl-2">
                                {!! Form::label('street', 'Strasse:') !!}
                                {!! Form::text('street', null, ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-xl-2 col-3">
                                {!! Form::label('plz', 'PLZ:') !!}
                                {!! Form::text('plz', null, ['class' => 'form-control', 'required']) !!}
                            </div>
                            <div class="form-group col-xl-3 col-9">
                                {!! Form::label('city', 'Ortschaft:') !!}
                                {!! Form::text('city', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    <hr>
                        <div class="form-row">
                            @foreach ($positions as $index => $position)
                                @if ($position->pricelist_position['bexio_code']<50)
                                    {!! Form::hidden('positions['.$position['id'].']', $position['amount'], ['class' => 'form-control', 'id' =>'position_'.$position['id']]) !!}
                                @else
                                    <div class="col-xl-2 col-4 form-group">
                                        {!! Form::label('positions['.$position['id'].']', $position->pricelist_position['name'] . ' ('. $position->pricelist_position['price'] . ' CHF)') !!}
                                        {!! Form::number('positions['.$position['id'].']', $position['amount'], ['class' => 'form-control', 'id' =>'position_'.$position['id'], 'onchange' => "Total_Change()"]) !!}
                                    </div>
                                @endif
                            @endforeach
                            {!! Form::hidden('total_people', null, ['class' => 'form-control', 'id' => 'total_people']) !!}
                        </div>
                        <div class="form-row">
                            <div class="col-xl-2 col-4 form-group">
                                {!! Form::label('total_days', 'Tage:') !!}
                                {!! Form::number('total_days', null, ['class' => 'form-control', 'required', 'id' => 'total_days']) !!}
                            </div>
                            <div class="col-xl-2 col-4 form-group">
                                {!! Form::label('discount', 'Rabatt [%]:') !!}
                                {!! Form::number('discount', null, ['class' => 'form-control', 'id' => 'discount', 'onchange' => "Total_Change()"]) !!}
                            </div>
                            <div class="col-xl-2 col-4 form-group">
                                {!! Form::label('', 'Total [CHF]:') !!}<br>
                                {!! Form::hidden('total_amount', null, ['class' => 'form-control', 'id' => 'total_amount']) !!}
                                <span id="total"></span>.-
                            </div>
                            <div class="col-xl-2 col-4 form-group">
                                <br>
                                {!! Form::label('early_checkin', 'Early Check-In:') !!}
                                {!! Form::checkbox('early_checkin', '1', $event['early_checkin']) !!}
                                {!! Form::label('late_checkout', 'Late Check-Out:') !!}
                                {!! Form::checkbox('late_checkout', '1', $event['late_checkout']) !!}
                            </div>
                            <div class="col-xl-2 col-4 form-group">
                                <br>
                                {!! Form::label('external', 'Externe Buchung:') !!}
                                {!! Form::checkbox('external', '1', $event['external']) !!}
                            </div>
                            <div class="col-xl-2 col-6 form-group">
                                {!! Form::label('contract_status_id', 'Angebot / Rechnung:') !!}
                                {!! Form::select('contract_status_id', $contract_statuses, null, ['class' => 'form-control', 'required']) !!}
                            </div>
                        </div>
                    <hr>
                        <div class="form-row">
                            <div class="col-xl-2 col-12">
                                <div class="form-row">
                                    <div class="col-xl-12 col-6 form-group">
                                        {!! Form::label('user_id', 'Verantwortlicher:') !!}
                                        {!! Form::select('user_id', $users, null, ['class' => 'form-control', 'required']) !!}
                                    </div>
                                    <div class="col-xl-12 col-6 form-group">
                                        {!! Form::label('code', 'Tür-Code:') !!}
                                        {!! Form::number('code', null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-xl-6">
                                {!! Form::label('comment', 'Bemerkung:') !!}
                                {!! Form::textarea('comment', null, ['class' => 'form-control', 'rows' =>5]) !!}
                            </div>
                            <div class="form-group col-xl-4">
                                {!! Form::label('comment_intern', 'Bemerkung (intern):') !!}
                                {!! Form::textarea('comment_intern', null, ['class' => 'form-control', 'rows' =>5]) !!}
                            </div>
                        </div>
                        <hr>
                        <div class="form-row">
                            <div class="col-xl-2 col-6 form-group">
                                {!! Form::submit('1 Buchung updaten', ['class' => 'btn btn-primary', 'name' => 'submit'])!!}
                            </div>
                            @switch($event['contract_status_id'])
                                @case(config('status.contract_offen'))
                                    <div class="col-xl-2 col-6 form-group">
                                        {!! Form::submit(config('mail.direct_send') ? '2 Angebot erstellen & versenden' : '2 Angebot erstellen',
                                            ['class' => 'btn btn-info', 'name' => 'submit'])!!}
                                    </div>
                                    <div class="form-group col-xl-2 col-6 ">
                                    </div>
                                    <div class="form-group col-xl-2 col-6 ">
                                    </div>
                                    @break
                                @case(config('status.contract_angebot_erstellt'))
                                    <div class="form-group col-xl-2 col-6 ">
                                        <a target="_blank" class = 'btn btn-secondary' href="https://office.bexio.com/index.php/kb_offer/show/id/{{$event['bexio_offer_id']}}">Angebot anzeigen</a>
                                    </div>
                                    <div class="form-group col-xl-2 col-6 ">
                                        {!! Form::submit('3 Angebot versenden', ['class' => 'btn btn-info', 'name' => 'submit'])!!}
                                    </div>
                                    <div class="form-group col-xl-2 col-6 ">
                                    </div>
                                    @break
                                @case(config('status.contract_angebot_versendet'))
                                    <div class="form-group col-xl-2 col-6 ">
                                        <a target="_blank" class = 'btn btn-secondary' href="https://office.bexio.com/index.php/kb_offer/show/id/{{$event['bexio_offer_id']}}">Angebot anzeigen</a>
                                    </div>
                                    <div class="form-group col-xl-2 col-6 ">
                                        {!! Form::submit('4 Rechnung erstellen', ['class' => 'btn btn-info', 'name' => 'submit'])!!}
                                    </div>
                                    <div class="form-group col-xl-2 col-6 ">
                                    </div>
                                    @break
                                @case(config('status.contract_rechnung_erstellt'))
                                    <div class="form-group col-xl-2 col-6 ">
                                        <a target="_blank" class = 'btn btn-secondary' href="https://office.bexio.com/index.php/kb_offer/show/id/{{$event['bexio_offer_id']}}">Angebot anzeigen</a>
                                    </div>
                                    <div class="form-group col-xl-2 col-6 ">
                                        <a target="_blank" class = 'btn btn-secondary' href="https://office.bexio.com/index.php/kb_invoice/show/id/{{$event['bexio_invoice_id']}}">Rechnung anzeigen</a>
                                    </div>
                                    <div class="form-group col-xl-2 col-6 ">
                                        {!! Form::submit('5 Rechnung versenden', ['class' => 'btn btn-info', 'name' => 'submit'])!!}
                                    </div>
                                    @break
                                @case(config('status.contract_rechnung_versendet'))
                                    <div class="form-group col-xl-2 col-6 ">
                                        <a target="_blank" class = 'btn btn-secondary' href="https://office.bexio.com/index.php/kb_offer/show/id/{{$event['bexio_offer_id']}}">Angebot anzeigen</a>
                                    </div>
                                    <div class="form-group col-xl-2 col-6 ">
                                        <a target="_blank" class = 'btn btn-secondary' href="https://office.bexio.com/index.php/kb_invoice/show/id/{{$event['bexio_invoice_id']}}">Rechnung anzeigen</a>
                                    </div>
                                    <div class="form-group col-xl-2 col-6 ">
                                        {!! Form::submit('6 Genossenschafts Info versenden', ['class' => 'btn btn-info', 'name' => 'submit'])!!}
                                    </div>
                                    @break
                                @default
                            @endswitch
                            <div class="form-group col-xl-4">
                                {!! Form::label('additional_text', 'Zusatztext für Mail:') !!}
                                {!! Form::textarea('additional_text', null, ['class' => 'form-control', 'rows' =>3]) !!}
                            </div>
                        </div>
                    {!! Form::close()!!}
                    {!! Form::open(['method' => 'DELETE', 'action'=>['AdminEventController@destroy', $event->id]]) !!}
                    <div class="form-group">
                        {!! Form::submit('Buchung löschen', ['class' => 'btn btn-danger'])!!}
                    </div>
                    {!! Form::close()!!}
                </div>
                <div class="col-xl-2">

                    <br>
                    @if(!$event->cleaning_mail)
                        <div class="form-group">
                            <button class="btn btn-secondary" onclick="PrepareMail()">Mail an Putzfirma</button>
                        </div>
                        <br>
                    @endif
                    <div id="cleaning_mail" style="display: none">
                        {!! Form::open(['method' => 'POST', 'action'=>['AdminEventController@SendCleaningMail', $event]]) !!}
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
            <div class="row">
                @include('includes.form_error')
            </div>
        </div>
    </section>
@endsection
@section('scripts')
<script>
    window.addEventListener("load", function() {
        Total_Change();
    });
    $("#external").change(function() {
        Total_Change();
    });
    function PrepareMail() {
  		document.getElementById("cleaning_mail").style.display = "block";

        start_date = new Date(document.getElementById('start_date').value).toLocaleDateString();
        end_date = new Date(document.getElementById('end_date').value).toLocaleDateString();
        var total = 0, subtotal = 0, id = 0;
	    var positions = @json($positions);
        positions.forEach(position => {
            id = 'position_' + position['id'];
            if(position.pricelist_position['bexio_code']>100 && position.pricelist_position['bexio_code']<200){
                subtotal =  parseInt(document.getElementById(id).value) || 0;
                total += subtotal;
            }
        });
        text = "Sehr geehrte Damen und Herren,\n" + "Wir haben eine neue Buchung für unser Ferienhaus vom " + start_date + " bis "
        + end_date + " (" + document.getElementById("total_days").value + " " + (document.getElementById("total_days").value ==1 ? "Nacht" : "Nächte") + ") für "
        + total + " Personen. Für einige nachfolgende Reinigung wären wir sehr dankbar.\n\n" + "Vielen Dank und freundliche Grüsse,\n" + "Verwaltung Ferienhaus Itelfingen";
	    $('#cleaning_mail_address').val(@json(config('mail.cleaning_mail')));
	    $('#cleaning_mail_text').val(text);
    }

    function Total_Change() {
        var start_date = new Date(document.getElementById('start_date').value);
        var end_date = new Date(document.getElementById('end_date').value);
        var days = (end_date - start_date)/(24*3600*1000);
	    var positions = @json($positions);
        var total_amount = 0, id = 0, total_person = 0;
        var discount = (100 - (parseInt(document.getElementById("discount").value) || 0)) / 100 ;
        positions.forEach(position => {
            id = 'position_' + position['id'];
            person = position.pricelist_position['bexio_code'] < 100 ? 0 : parseInt(document.getElementById(id).value);
            person = person || 0;
            var subtotal = 0
            if(days === 0){
                if(position.pricelist_position['bexio_code'] < 50){
                    subtotal = position.pricelist_position['price'] / 2;
                    person = 1;
                }
                else if(position.pricelist_position['bexio_code'] < 100) {
                    subtotal =position.pricelist_position['price']
                }
            }
            else {
                if(position.pricelist_position['bexio_code'] < 50){
                    subtotal = position.pricelist_position['price'];
                }
                else if(position.pricelist_position['bexio_code'] > 100) {
                    subtotal = parseInt(document.getElementById(id).value) * position.pricelist_position['price'] * days * discount || 0;
                }
            }
            if( position.pricelist_position['bexio_code']>200){
                subtotal =  Math.max(parseInt(document.getElementById(id).value) -3,0) * position.pricelist_position['price'] * Math.max(days,1) || 0;
                person = 0;
            }
            total_amount += subtotal;
            total_person += person;
        });
        $("#total").text(total_amount);
        $("#total_days").val(days);
        $("#total_amount").val(total_amount);
        $("#total_people").val(total_person);
    }
</script>
@endsection
