@extends('layouts.admin')
@section('content')

    @include('includes.tinyeditor')
    <section>
        <div class="container-fluid">
            <header> 
                <h1 class="h3 display">Ablauf erstellen</h1>
            </header>
            <div class="row">
                {!! Form::open(['method' => 'POST', 'action'=>'AdminShedulesController@store', 'files' => true]) !!}
                <div class="form-group">
                    {!! Form::label('time', 'Zeit:') !!}
                    {!! Form::time('time', null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('title', 'Titel:') !!}
                    {!! Form::text('title', null, ['class' => 'form-control', 'required']) !!}
                </div>                 
                <div class="form-group">
                    {!! Form::label('icon', 'Icon:') !!}
                    {!! Form::text('icon', null, ['class' => 'form-control', 'required']) !!}
                </div> 
                <div class="form-group">
                    {!! Form::label('location_id', 'Ort:') !!}
                    {!! Form::select('location_id', [''=>'Wähle Ort'] + $locations, null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                        {!! Form::label('photo_id', 'Photo:') !!}
                        {!! Form::file('photo_id', null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('content', 'Beschreibung:') !!}
                    {!! Form::textarea('content', null, ['class' => 'form-control', 'rows'=>3]) !!}
                </div>
                <div class="form-group">
                    {!! Form::submit('Ablauf Erstellen', ['class' => 'btn btn-primary'])!!}
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