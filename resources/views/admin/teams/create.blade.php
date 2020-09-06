@extends('layouts.admin')
@section('content')
@include('includes.tinyeditor')
    <section>
        <div class="container-fluid">
            <header> 
                <h1 class="h3 display">Trauzeugen erstellen</h1>
            </header>
            <div class="row">
                {!! Form::open(['method' => 'POST', 'action'=>'AdminTeamsController@store', 'files' => true]) !!}
                <div class="form-group">
                    {!! Form::label('name', 'Name:') !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('title', 'Titel:') !!}
                    {!! Form::text('title', null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('description', 'Beschreibung:') !!}
                    {!! Form::textarea('description', null, ['class' => 'form-control', 'rows'=>3]) !!}
                </div>
                <div class="form-group">
                        {!! Form::label('photo_id', 'Photo (quadratisch):') !!}
                        {!! Form::file('photo_id', null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::submit('Trauzeugen Erstellen', ['class' => 'btn btn-primary'])!!}
                </div>
                {!! Form::close()!!}
            </div>     
        </div>
    </section>
</div>
@endsection