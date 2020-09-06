@extends('layouts.admin')

@section('content')
<section>
    <div class="container-fluid">

        <header> 
            <h1>Anmeldungen</h1>
        </header>
    
        <div class="row">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" width="10%">Name</th>
                        <th scope="col" width="10%">Vorname</th>
                        <th scope="col" width="10%">E-Mail</th>
                        <th scope="col" width="30%">Text</th>
                        <th scope="col" width="30%">Antwort</th>
                        <th scope="col" width="10%">Erstellt</th>
                    </tr>
                </thead>
                <tbody>
                    @if($invitations)
                    @foreach ($invitations as $invitation)
                        <tr>
                            <td>{{$invitation->name}}</td>
                            <td>{{$invitation->firstname}}</td>
                            <td><a target="blank" href="mailto:{{$invitation->email}}">{{$invitation->email}}</a></td>
                            <td>{{$invitation->content}}</td>
                            <td>{{$invitation->response['name']}}</td>
                            <td>{{$invitation->created_at ? $invitation->created_at->diffForHumans() : 'Kein Datum'}}</td>
                        </tr>   
                    @endforeach
                    @endif
                </tbody>
            </table>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-5">
                    {{$invitations->render()}}
                </div>
            </div>
        </div>
    </div>  
</section>
@endsection