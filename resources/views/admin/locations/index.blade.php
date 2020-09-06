@extends('layouts.admin')

@section('content')

<section>
    <div class="container-fluid">

        <header> 
            <h1>Orte</h1>
        </header>

        <div class="row">
        
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Photo</th>
                        <th scope="col">Name</th>
                        <th scope="col">Subline</th>
                        <th scope="col">Beschreibung</th>
                        <th scope="col">Strasse</th>
                        <th scope="col">PLZ</th>
                        <th scope="col">Ort</th>
                    </tr>
                </thead>
                <tbody>
                    @if($locations)
                        @foreach ($locations as $location)
                            <tr>
                                <td><img height="50" src="{{$location->photo ? $location->photo->file : 'http://placehold.it/50x50'}}" alt=""></td>
                                <td><a href="{{route('locations.edit', $location->id)}}">{{$location->name}}</a></td>
                                <td>{{$location->subline}}</td>
                                <td>{{$location->description}}</td>
                                <td>{{$location->street}}</td>
                                <td>{{$location->plz}}</td>
                                <td>{{$location->city}}</td>
                            </tr>   
                        @endforeach
                    @endif
                </tbody>
            </table>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-5">
                    {{$locations->render()}}
                </div>
            </div>
        </div>  
    </div>  
</section>
@endsection