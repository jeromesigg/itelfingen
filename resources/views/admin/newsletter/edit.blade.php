@extends('layouts.admin')
@section('content')

    <section>
        <div class="container-fluid">
            <header>
                <h3 class="display">Empfänger bearbeiten</h3>
            </header>
            <div class="row">
                 <div class="col-sm-9">
                    {!! Form::model($newsletter, ['method' => 'PATCH', 'action'=>['NewsletterController@update', $newsletter]]) !!}
                    <div class="form-row">
                        <div class="col-md-6 form-group">
                        {!! Form::label('name', 'Name:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-6 form-group">
                            {!! Form::label('firstname', 'Vorname:') !!}
                            {!! Form::text('firstname', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('email', 'E-Mail:') !!}
                        {!! Form::email('email', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('booking', 'Buchungen:') !!}
                        {!! Form::checkbox('bookings', '1', null) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('member', 'Genossenschaft:') !!}
                        {!! Form::checkbox('members', '1', null) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Person updaten', ['class' => 'btn btn-primary'])!!}
                    </div>
                    {!! Form::close()!!}
                </div>
            </div>
            <div class="row">
                @include('includes.form_error')
            </div>
        </div>
    </section>
@endsection
