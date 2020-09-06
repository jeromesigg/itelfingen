@extends('layouts.admin')

@section('content')

<section>
    <div class="container-fluid">

        <header> 
            <h1>Albumbilder</h1>
        </header>

        <div class="row">
        
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Photo</th>
                        <th scope="col">Name</th>
                        <th scope="col">Album</th>
                    </tr>
                </thead>
                <tbody>
                    @if($pictures)
                        @foreach ($pictures as $picture)
                            <tr>
                                <td><img height="50" src="{{$picture->photo ? $picture->photo->file : 'http://placehold.it/50x50'}}" alt=""></td>
                                <td><a href="{{route('pictures.edit', $picture->id)}}">{{$picture->name}}</a></td>
                                <td>{{$picture->album['name']}}</td>
                            </tr>   
                        @endforeach
                    @endif
                </tbody>
            </table>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-5">
                    {{$pictures->render()}}
                </div>
            </div>
        </div>  
    </div>  
</section>
@endsection