@extends('layouts.admin')

@section('content')
    <section>
        <div class="container-fluid">

            <header> 
                <h3>Positionen</h3>
                <a href="{{route('positions.create')}}" class="btn btn-primary btn-success btn-sm">
                    <span>Importieren</span>
                </a>
            </header>
        
            <div class="row">
                <div class="col-sm-12">
                    <table class="table">
                        <thead>
                            <tr>                                
                                <th>Bexio Code</th>
                                <th>Name</th>
                                <th>Preis</th>
                                <th>Anzeigen</th>
                                <th>Archiv-Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($positions)
                                @foreach ($positions as $position)
                                    <tr>
                                        <td>{{$position->bexio_code}}</td>
                                        <td><a href="{{route('positions.edit', $position->id)}}">{{$position->name}}</a></td>
                                        <td>{{$position->price}}</td>
                                        <td>{{$position->show ? 'Ja' : 'Nein'}}</td>
                                        <td>{{$position->archive_status['name']}}</td>
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