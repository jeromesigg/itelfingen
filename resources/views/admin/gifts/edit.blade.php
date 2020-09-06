@extends('layouts.admin')
@section('content')

    @include('includes.tinyeditor')
    <section>
        <div class="container-fluid">
            <header> 
                <h1 class="h3 display">Trauzeugen bearbeiten</h1>
            </header>
            <div class="row">
                <div class="col-sm-3" >
        
                    <img src="{{$team->photo ? $team->photo->file : 'http://placehold.it/350x350'}}" alt="" class="img-responsive" style="max-width: -webkit-fill-available;">
                </div> 
                <div class="col-sm-9">
                    {!! Form::model($team, ['method' => 'PATCH', 'action'=>['AdminTeamsController@update', $team->id], 'files' => true]) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Name:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('title', 'Titel:') !!}
                        {!! Form::text('title', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('description', 'Beschreibung:') !!}
                        {!! Form::textarea('description', null, ['class' => 'form-control', 'rows'=>3]) !!}
                    </div>
                    <div class="form-group">
                            {!! Form::label('photo_id', 'Photo (quadratisch):') !!}
                            {!! Form::file('photo_id', null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::submit('Trauzeugen updaten', ['class' => 'btn btn-primary col-sm-6'])!!}
                    </div>
                    {!! Form::close()!!}
                    {!! Form::open(['method' => 'DELETE', 'action'=>['AdminTeamsController@destroy', $team->id]]) !!}
                        <div class="form-group">
                            {!! Form::submit('Trauzeugen löschen', ['class' => 'btn btn-danger col-sm-6'])!!}
                        </div>
                    {!! Form::close()!!}
                </div>   
            </div>   
            <div class="row"> 
                @include('includes.form_error')
            </div>   
        </div>
    </section>
</div>
@endsection