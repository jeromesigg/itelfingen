@extends('layouts.admin')
@section('content')
    <section>
        <div class="container-fluid">
            <header> 
                <h1 class="h3 display">Albumbild erstellen</h1>
            </header>
            <div class="row">
                {!! Form::open(['method' => 'POST', 'action'=>'AdminPicturesController@store', 'files' => true]) !!}
                <div class="form-group">
                    {!! Form::label('name', 'Name:') !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('album_id', 'Album:') !!}
                    {!! Form::select('album_id', [''=>'Wähle Album'] + $albums, null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                        {!! Form::label('photo_id', 'Photo:') !!}
                        {!! Form::file('photo_id', null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::submit('Albumbild Erstellen', ['class' => 'btn btn-primary'])!!}
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