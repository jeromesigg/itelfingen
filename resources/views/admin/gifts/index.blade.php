@extends('layouts.admin')

@section('content')
<h1>Geschenke</h1>
    
<table class="table">
    <thead>
        <tr>
            <th scope="col">Photo</th>
            <th scope="col">Name</th>
            <th scope="col">Text</th>
            <th scope="col">Schlusstext</th>
            <th scope="col">Betrag</th>
            <th scope="col">Geschenkt</th>
        </tr>
    </thead>
    <tbody>
        @if($gifts)
        @foreach ($gifts as $gift)
            <tr>
                <td><img height="50" src="{{$gift->photo ? $gift->photo->file : 'http://placehold.it/50x50'}}" alt=""></td>
                <td><a href="{{route('gifts.edit', $gift->id)}}">{{$gift->name}}</a></td>
                <td>{{$gift->description}}</td>
                <td>{{$gift->subline}}</td>
                <td>{{$gift->amount}}</td>
                <td>{{$gift->paid}}</td>
            </tr>   
        @endforeach
        @endif
    </tbody>
</table>
<div class="row">
    <div class="col-sm-6 col-sm-offset-5">
        {{$gifts->render()}}
    </div>
</div>
@endsection