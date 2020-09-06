@extends('layouts.admin')
@section('content')

    @include('includes.tinyeditor')
    <section>
        <div class="container-fluid">
            <header> 
                <h1 class="h3 display">Albumbild bearbeiten</h1>
            </header>
            <div class="row">
                <div class="col-sm-3" >
        
                    <img src="{{$picture->photo ? $picture->photo->file : 'http://placehold.it/350x350'}}" alt="" class="img-responsive" style="max-width: -webkit-fill-available;">
                </div> 
                <div class="col-sm-9">
                    {!! Form::model($picture, ['method' => 'PATCH', 'action'=>['AdminPicturesController@update', $picture->id], 'files' => true]) !!}
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
                        {!! Form::submit('Albumbild updaten', ['class' => 'btn btn-primary col-sm-6'])!!}
                    </div>
                    {!! Form::close()!!}
                    {!! Form::open(['method' => 'DELETE', 'action'=>['AdminPicturesController@destroy', $picture->id]]) !!}
                        <div class="form-group">
                            {!! Form::submit('Albumbild löschen', ['class' => 'btn btn-danger col-sm-6'])!!}
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