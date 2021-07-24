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
                                    <div class="form-group">
                                        {!! Form::label('comment', 'Bemerkung:') !!}
                                        {!! Form::textarea('comment', null, ['class' => 'form-control', 'rows' =>3]) !!}
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            {!! Form::submit('Buchung updaten', ['class' => 'btn btn-primary'])!!}
                                        </div>
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
                                        <div class="col-md-6 form-group">
                                            {!! Form::label('total_days', 'Tage:') !!}
                                            {!! Form::number('total_days', null, ['class' => 'form-control', 'required']) !!}
                                        </div>
                                        <div class="col-md-6 form-group">
                                            {!! Form::label('total_amount', 'Total:') !!}
                                            {!! Form::number('total_amount', null, ['class' => 'form-control', 'required']) !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('event_status_id', 'Status:') !!}
                                        {!! Form::select('event_status_id', $event_statuses, null, ['class' => 'form-control', 'required']) !!}
                                    </div>
                                    
                                    <br>
                                    <div class="form-group">
                                        {!! Form::label('contract', 'Vertrag:') !!}
                                        @if ($event->contract)
                                            <a href="{{ URL::to('contracts',$event->contract)  }}" target="_blank">{{ $event->contract}}</a>
                                        @endif
                                        <br>
                                        {!! Form::file('contract', null, ['class' => 'form-control']) !!}
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        {!! Form::label('contract_signed', 'Vertrag unterzeichnet:') !!}
                                        @if ($event->contract_signed)
                                            <a href="{{ URL::to('contracts/signed',$event->contract_signed)  }}" target="_blank">{{ $event->contract_signed}}</a>
                                        @endif
                                        <br>
                                        {!! Form::file('contract_signed', null, ['class' => 'form-control']) !!}
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        {!! Form::label('contract_statuses', 'Vertrag-Status:') !!}
                                        {!! Form::select('contract_statuses', $contract_statuses, null, ['class' => 'form-control', 'required']) !!}
                                    </div>
                                </div>
                            </div>
                        {!! Form::close()!!}
                    </div>
                    <div class="form-group col-md-3">
                        <div class="form-group">
                            <a type='submit' class = 'btn btn-secondary' href="{{ route('events.downloadcontract', $event->id) }}">Vertrag herunterladen</a>
                        </div>
                        <div class="form-group">
                            {!! Form::open(['method' => 'DELETE', 'action'=>['AdminEventController@destroy', $event->id]]) !!}
                                <div class="form-group">
                                    {!! Form::submit('Buchung löschen', ['class' => 'btn btn-danger'])!!}
                                </div>
                            {!! Form::close()!!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row"> 
                @include('includes.form_error')
            </div>   
        </div>
    </section>
@endsection