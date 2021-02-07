@extends('layouts.admin')

@section('content')
<section>
    <div class="container-fluid">

        <header> 
            <h1>Personen</h1>
        </header>
    
        <div class="row">
            <div class="col-sm-3">
                {!! Form::open(['method' => 'POST', 'action'=>'AdminPersonController@store', 'files' => true]) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Name:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('function', 'Funktion:') !!}
                        {!! Form::text('function', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('photo_id', 'Photo:') !!}
                        {!! Form::file('photo_id', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Person erstellen', ['class' => 'btn btn-primary'])!!}
                    </div>
                {!! Form::close()!!}
            </div>    
            <div class="col-sm-9">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" width="10%">Bild</th>
                            <th scope="col" width="30%">Name</th>
                            <th scope="col" width="40%">Funktion</th>
                            <th scope="col" width="10%">Archiv-Status</th>
                            <th scope="col" width="10%">Sort-Index</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($people)
                        @foreach ($people as $person)
                            <tr>  
                                <td><img height="50" src="{{$person->photo ? $person->photo->file : 'http://placehold.it/50x50'}}" alt=""></td>
                                <td><a href="{{route('people.edit', $person->id)}}">{{$person->name}}</a></td>
                                <td>{{$person->function}}</td>
                                <td>{{$person->archive_status['name']}}</td>
                                <td>{{$person['sort-index']}}</td>
                                </tr>   
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>  
</section>
@endsection