@extends('layouts.admin')
@section('content')

    @include('includes.tinyeditor')
    <section>
        <div class="container-fluid">
            <header> 
                <h1 class="h3 display">Story bearbeiten</h1>
            </header>
            <div class="row">
                <div class="col-sm-3" >
        
                    <img src="{{$story->photo ? $story->photo->file : 'http://placehold.it/250x250'}}" alt="" class="img-responsive" style="max-width: -webkit-fill-available;">
                </div> 
                <div class="col-sm-9">
                    {!! Form::model($story, ['method' => 'PATCH', 'action'=>['AdminStoriesController@update', $story->id], 'files' => true]) !!}
                    <div class="form-group">
                        {!! Form::label('year', 'Jahr:') !!}
                        {!! Form::text('year', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('title', 'Titel:') !!}
                        {!! Form::text('title', null, ['class' => 'form-control']) !!}
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
                        {!! Form::submit('Story updaten', ['class' => 'btn btn-primary col-sm-6'])!!}
                    </div>
                    {!! Form::close()!!}
                    {!! Form::open(['method' => 'DELETE', 'action'=>['AdminStoriesController@destroy', $story->id]]) !!}
                        <div class="form-group">
                            {!! Form::submit('Story löschen', ['class' => 'btn btn-danger col-sm-6'])!!}
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