@extends('layouts.admin')
@section('content')
<section>
    <div class="container-fluid">
        <header> 
            <h3 class="display">Buchung erstellen</h3>
        </header>
        <div>
            <button class="btn btn-primary" onclick="createIntern()">Interne Buchung erstellen</button>
        </div>
        <br>
        {!! Form::open(['method' => 'POST', 'action'=>['AdminEventController@store']]) !!}
            <div class="form-row">
                <div class="form-group col-md-6">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            {!! Form::label('start_date', 'Start:') !!}
                            {!! Form::date('start_date', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('end_date', 'Ende:') !!}
                            {!! Form::date('end_date', null, ['class' => 'form-control']) !!}
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
                    <div class="form-group">
                        {!! Form::label('comment', 'Bemerkung:') !!}
                        {!! Form::textarea('comment', null, ['class' => 'form-control', 'rows' =>3]) !!}
                    </div>
                </div>
                <div class="form-group col-md-6" style="padding-left:30px">
                    <div class="form-group">
                            {!! Form::label('comment_intern', 'Bemerkung (intern):') !!}
                            {!! Form::textarea('comment_intern', null, ['class' => 'form-control', 'rows' =>3]) !!}
                    </div>
                    <div class="form-row">
                        <div class="col-md-3 form-group">
                            {!! Form::label('other_adults', 'Erw:') !!}
                            {!! Form::number('other_adults', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-3 form-group">
                            {!! Form::label('member_adults', 'Erw (G):') !!}
                            {!! Form::number('member_adults', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-3 form-group">
                            {!! Form::label('other_kids', 'Kinder:') !!}
                            {!! Form::number('other_kids', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-3 form-group">
                            {!! Form::label('member_kids', 'Kinder (G):') !!}
                            {!! Form::number('member_kids', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-4 form-group">
                            {!! Form::label('total_people', 'Total Personen:') !!}
                            {!! Form::number('total_people', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="col-md-4 form-group">
                            {!! Form::label('total_days', 'Nächte:') !!}
                            {!! Form::number('total_days', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="col-md-4 form-group">
                            {!! Form::label('total_amount', 'Total:') !!}
                            {!! Form::number('total_amount', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 form-group">
                            {!! Form::label('event_status_id', 'Status:') !!}
                            {!! Form::select('event_status_id', $event_statuses, null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="col-md-6 form-group">
                            {!! Form::label('user_id', 'Verantwortlicher:') !!}
                            {!! Form::select('user_id', $users, null, ['class' => 'form-control', 'required']) !!}
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
	    $('#event_status_id').val(@json(config('status.event_eigene')));
    }
</script>
@endsection