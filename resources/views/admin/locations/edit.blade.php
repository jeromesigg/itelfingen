@extends('layouts.admin')
@section('content')

    <section>
        <div class="container-fluid">
            <header> 
                <h1 class="h3 display">ORt bearbeiten</h1>
            </header>
            <div class="row">
                <div class="col-sm-3" >
        
                    <img src="{{$location->photo ? $location->photo->file : 'http://placehold.it/350x350'}}" alt="" class="img-responsive" style="max-width: -webkit-fill-available;">
                </div> 
                <div class="col-sm-9">
                    {!! Form::model($location, ['method' => 'PATCH', 'action'=>['AdminLocationsController@update', $location->id], 'files' => true]) !!}
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
                    {!! Form::open(['method' => 'DELETE', 'action'=>['AdminLocationsController@destroy', $location->id]]) !!}
                        <div class="form-group">
                            {!! Form::submit('Ort löschen', ['class' => 'btn btn-danger col-sm-6'])!!}
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