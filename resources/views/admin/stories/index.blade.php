@extends('layouts.admin')

@section('content')
<h1>Unsere Geschichte</h1>
    
<table class="table">
    <thead>
        <tr>
            <th scope="col">Photo</th>
            <th scope="col">Jahr</th>
            <th scope="col">Titel</th>
            <th scope="col">Text</th>
        </tr>
    </thead>
    <tbody>
        @if($stories)
        @foreach ($stories as $story)
            <tr>
                <td><img height="50" src="{{$story->photo ? $story->photo->file : 'http://placehold.it/50x50'}}" alt=""></td>
                <td>{{$story->year}}</td>
                <td><a href="{{route('stories.edit', $story->id)}}">{{$story->title}}</a></td>
                <td>{{$story->content}}</td>
            </tr>   
        @endforeach
        @endif
    </tbody>
</table>
<div class="row">
    <div class="col-sm-6 col-sm-offset-5">
        {{$stories->render()}}
    </div>
</div>
@endsection