@extends('layouts.admin')

@section('content')

<section>
    <div class="container-fluid">

        <header> 
            <h1>Tagesablauf</h1>
        </header>

        <div class="row">
        
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Photo</th>
                        <th scope="col">Zeit</th>
                        <th scope="col">Titel</th>
                        <th scope="col">Text</th>
                        <th scope="col">Ort</th>
                        <th scope="col">Link</th>
                    </tr>
                </thead>
                <tbody>
                    @if($shedules)
                    @foreach ($shedules as $shedule)
                        <tr>
                            <td><img height="50" src="{{$shedule->photo ? $shedule->photo->file : 'http://placehold.it/50x50'}}" alt=""></td>
                            <td>{{$shedule->time}}</td>
                            <td><a href="{{route('shedules.edit', $shedule->id)}}">{{$shedule->title}}</a></td>
                            <td>{{$shedule->content}}</td>
                            <td>{{$shedule->location['name']}}</td>
                            <td>{{$shedule->link}}</td>
                        </tr>   
                    @endforeach
                    @endif
                </tbody>
            </table>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-5">
                    {{$shedules->render()}}
                </div>
            </div>
        </div>  
    </div>  
</section>
@endsection