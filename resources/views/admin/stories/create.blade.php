@extends('layouts.admin')
@section('content')
@include('includes.tinyeditor')
    <section>
        <div class="container-fluid">
            <header> 
                <h1 class="h3 display">Story erstellen</h1>
            </header>
            <div class="row">
                {!! Form::open(['method' => 'POST', 'action'=>'AdminStoriesController@store', 'files' => true]) !!}
                <div class="form-group">
                    {!! Form::label('year', 'Jahr:') !!}
                    {!! Form::text('year', null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('title', 'Titel:') !!}
                    {!! Form::text('title', null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                        {!! Form::label('photo_id', 'Photo (1900x600px):') !!}
                        {!! Form::file('photo_id', null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('content', 'Beschreibung:') !!}
                    {!! Form::textarea('content', null, ['class' => 'form-control', 'rows'=>3]) !!}
                </div>
                <div class="form-group">
                    {!! Form::submit('Story Erstellen', ['class' => 'btn btn-primary'])!!}
                </div>
                {!! Form::close()!!}
            </div>     
        </div>
    </section>
</div>
@endsection