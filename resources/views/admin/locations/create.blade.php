@extends('layouts.admin')
@section('content')
    <section>
        <div class="container-fluid">
            <header> 
                <h1 class="h3 display">Orte erstellen</h1>
            </header>
            <div class="row">
                {!! Form::open(['method' => 'POST', 'action'=>'AdminLocationsController@store', 'files' => true]) !!}
                <div class="form-group">
                    {!! Form::label('name', 'Name:') !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('subline', 'Subline:') !!}
                    {!! Form::text('subline', null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('description', 'Beschreibung:') !!}
                    {!! Form::text('description', null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('street', 'Strasse:') !!}
                    {!! Form::text('street', null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('plz', 'PLZ:') !!}
                    {!! Form::text('plz', null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('city', 'Ort:') !!}
                    {!! Form::text('city', null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::submit('Ort Erstellen', ['class' => 'btn btn-primary'])!!}
                </div>
                {!! Form::close()!!}
            </div>   
            <div class="row"> 
                @include('includes.form_error')
            </div>   
        </div>
    </section>
</div>
@endsection