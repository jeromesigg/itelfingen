@extends('layouts.admin')

@section('content')
<section>
    <div class="container-fluid">

        <header> 
            <h1>Geschichte</h1>
        </header>
    
        <div class="row">
            <div class="col-sm-3">
                {!! Form::open(['method' => 'POST', 'action'=>'AdminHistoryController@store', 'files' => true]) !!}
                    <div class="form-group">
                        {!! Form::label('title', 'Titel:') !!}
                        {!! Form::text('title', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('shorttitle', 'Kurztitel:') !!}
                        {!! Form::text('shorttitle', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('subtitle', 'Untertitel:') !!}
                        {!! Form::text('subtitle', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('description', 'Beschreibung:') !!}
                        {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('photo_id', 'Photo:') !!}
                        {!! Form::file('photo_id', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Geschichte erstellen', ['class' => 'btn btn-primary'])!!}
                    </div>
                {!! Form::close()!!}
            </div>    
            <div class="col-sm-9">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" width="10%">photo</th>
                            <th scope="col" width="10%">Titel</th>
                            <th scope="col" width="20%">Kurztitel</th>
                            <th scope="col" width="25%">Untertitel</th>
                            <th scope="col" width="25%">Beschreibung</th>
                            <th scope="col" width="5%">Archiv-Status</th>
                            <th scope="col" width="5%">Sort-Index</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($histories)
                        @foreach ($histories as $history)
                            <tr>
                                <td><img height="50" src="{{$history->photo ? $history->photo->file : 'http://placehold.it/50x50'}}" alt=""></td>
                                <td><a href="{{route('histories.edit', $history->id)}}">{{$history->title}}</a></td>
                                <td>{{$history->shorttitle}}</td>
                                <td>{{$history->subtitle}}</td>
                                <td>{{$history->description}}</td>
                                <td>{{$history->archive_status['name']}}</td>
                                <td>{{$history['sort-index']}}</td>
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