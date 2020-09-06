@extends('layouts.admin')
@section('content')

    @include('includes.tinyeditor')
    <section>
        <div class="container-fluid">
            <header> 
                <h1 class="h3 display">Ablauf bearbeiten</h1>
            </header>
            <div class="row">
                <div class="col-sm-3" >
        
                    <img src="{{$shedule->photo ? $shedule->photo->file : 'http://placehold.it/350x350'}}" alt="" class="img-responsive" style="max-width: -webkit-fill-available;">
                </div> 
                <div class="col-sm-9">
                    {!! Form::model($shedule, ['method' => 'PATCH', 'action'=>['AdminShedulesController@update', $shedule->id], 'files' => true]) !!}
                    <div class="form-group">
                        {!! Form::label('time', 'Zeit:') !!}
                        {!! Form::time('time', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('title', 'Titel:') !!}
                        {!! Form::text('title', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('location_id', 'Ort:') !!}
                        {!! Form::select('location_id', [''=>'Wähle Ort'] + $locations, null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                            {!! Form::label('photo_id', 'Photo:') !!}
                            {!! Form::file('photo_id', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('body', 'Beschreibung:') !!}
                        {!! Form::textarea('body', null, ['class' => 'form-control', 'rows'=>3]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Ablauf updaten', ['class' => 'btn btn-primary col-sm-6'])!!}
                    </div>
                    {!! Form::close()!!}
                    {!! Form::open(['method' => 'DELETE', 'action'=>['AdminShedulesController@destroy', $shedule->id]]) !!}
                        <div class="form-group">
                            {!! Form::submit('Ablauf löschen', ['class' => 'btn btn-danger col-sm-6'])!!}
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