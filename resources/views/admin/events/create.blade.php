@extends('layouts.admin')
@section('pageTitle', 'Buchungen')
@section('content')
<section>
    <div class="container-fluid">
        <header>
            <h3 class="display">Buchung erstellen</h3>
        </header>
        <div>
            <button class="btn btn-primary" onclick="createIntern()">Interne Buchung erstellen</button>
            <button class="btn btn-primary" onclick="createExtern()">Externe Buchung erstellen</button>
        </div>
        <br>
        {!! Form::open(['method' => 'POST', 'action'=>['AdminEventController@store']]) !!}
            <div class="form-row">
                <div class="form-group col-md-6">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            {!! Form::label('start_date', 'Start:') !!}
                            {!! Form::date('start_date', null, ['class' => 'form-control', 'required', 'onchange' => "Total_Change()"]) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('end_date', 'Ende:') !!}
                            {!! Form::date('end_date', null, ['class' => 'form-control', 'onchange' => "Total_Change()"]) !!}
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
                            {!! Form::email('email', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('telephone', 'Telefon:') !!}
                            {!! Form::text('telephone', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('street', 'Strasse:') !!}
                        {!! Form::text('street', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            {!! Form::label('plz', 'PLZ:') !!}
                            {!! Form::text('plz', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group col-md-9">
                            {!! Form::label('city', 'Ortschaft:') !!}
                            {!! Form::text('city', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6" style="padding-left:30px">
                    <div class="form-group">
                        {!! Form::label('comment', 'Bemerkung:') !!}
                        {!! Form::textarea('comment', null, ['class' => 'form-control', 'rows' =>3]) !!}
                    </div>
                    <div class="form-row">
                        <div class="col-md-3 form-group">
                            {!! Form::label('event_status_id', 'Status:') !!}
                            {!! Form::select('event_status_id', $event_statuses, null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="col-md-6 form-group">
                                {!! Form::label('comment_intern', 'Bemerkung (intern):') !!}
                                {!! Form::textarea('comment_intern', null, ['class' => 'form-control', 'rows' =>3]) !!}
                        </div>
                        <div class="col-md-3 form-group">
                            {!! Form::label('external', 'Externe Buchung:') !!}
                            {!! Form::checkbox('external', '1',false) !!}
                        </div>
                    </div>
                    <div class="form-row">
                        @foreach ($positions as $index => $position)
                            @if ($position['bexio_code']<50)
                                {!! Form::hidden('positions['.$position['bexio_code'].']', $position['amount'], ['class' => 'form-control', 'id' =>'position_'.$position['id'], 'onchange' => "Total_Change()"]) !!}
                            @else
                                <div class="col-md-3 form-group">
                                    {!! Form::label('positions['.$position['bexio_code'].']', $position['name'] . ' ('. $position['price'] . ' CHF)') !!}
                                    {!! Form::number('positions['.$position['bexio_code'].']', $position['amount'], ['class' => 'form-control', 'id' =>'position_'.$position['id'], 'onchange' => "Total_Change()"]) !!}
                                </div>
                            @endif
                        @endforeach
                        <div class="col-md-3 form-group">
                            {!! Form::label('total_days', 'Tage:') !!}
                            {!! Form::number('total_days', null, ['class' => 'form-control', 'required', 'id' => 'total_days', 'onchange' => "Total_Change()"]) !!}
                        </div>
                        <div class="col-md-3 form-group">
                            {!! Form::label('discount', 'Rabatt [%]:') !!}
                            {!! Form::number('discount', null, ['class' => 'form-control', 'id' => 'discount', 'onchange' => "Total_Change()"]) !!}
                        </div>
                        <div class="col-md-3 form-group">
                            {!! Form::label('', 'Total [CHF]:') !!}<br>
                            {!! Form::hidden('total_amount', null, ['class' => 'form-control', 'id' => 'total_amount']) !!}
                            <span id="total"></span>.-
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 form-group">
                            {!! Form::label('user_id', 'Verantwortlicher:') !!}
                            {!! Form::select('user_id', $users, null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="col-md-6 form-group">
                            {!! Form::label('code', 'Tür-Code:') !!}
                            {!! Form::number('code', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::submit('Buchung erstellen', ['class' => 'btn btn-primary'])!!}
        {!! Form::close()!!}
        <div class="row">
            @include('includes.form_error')
        </div>
    </div>
</section>
@endsection
@section('scripts')
<script>
    function createIntern() {
	    $('#name').val("Interne Buchung");
	    $('#street').val("Itelfingen 3");
	    $('#plz').val("6344");
	    $('#city').val("Meierskappel");
	    $('#email').val("verwalter@itelfingen.ch");
	    $('#total_people').val("0");
	    $('#total_days').val("0");
	    $('#total_amount').val("0");
        $('#code').val("290393");
	    $('#event_status_id').val(@json(config('status.event_eigene')));
        $('#external')[0].checked = false;
    }
    function createExtern() {
        $('#name').val("Externe Buchung");
        $('#street').val("Itelfingen 3");
        $('#plz').val("6344");
        $('#city').val("Meierskappel");
        $('#email').val("verwalter@itelfingen.ch");
        $('#total_people').val("0");
        $('#total_days').val("0");
        $('#total_amount').val("0");
        $('#code').val("");
        $('#event_status_id').val(@json(config('status.event_neu')));
        $('#external')[0].checked = true;
        $("#discount").val(10);
    }
    function Total_Change() {
        var start_date = new Date(document.getElementById('start_date').value);
        var end_date = new Date(document.getElementById('end_date').value);
        var days = (end_date - start_date)/(24*3600*1000);
        var positions = @json($positions);
        var total_amount = 0, id = 0;
        var discount = (100 - (parseInt(document.getElementById("discount").value) || 0)) / 100 ;
        positions.forEach(position => {
            id = 'position_' + position['id'];
            var subtotal = 0
            if(days === 0){
                if(position['bexio_code'] < 50){
                    subtotal = position['price'] / 2;
                }
                else if(position['bexio_code'] < 100) {
                    subtotal =position['price']
                }
            }
            else {
                if(position['bexio_code'] < 50){
                    subtotal = position['price'];
                }
                else if(position['bexio_code'] > 100) {
                    subtotal = parseInt(document.getElementById(id).value) * position['price'] * days * discount || 0;
                }
            }
            if( position['bexio_code']>200){
                subtotal =  Math.max(parseInt(document.getElementById(id).value) -3,0) * position['price'] * Math.max(days,1) || 0;
            }
            total_amount += subtotal;
        });
        $("#total").text(total_amount);
        $("#total_days").val(days);
        $("#total_amount").val(total_amount);
    }
</script>
@endsection
