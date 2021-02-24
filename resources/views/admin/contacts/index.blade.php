@extends('layouts.admin')

@section('content')
<section>
    <div class="container-fluid">

        <header> 
            <h3>Anfragen</h3>
        </header>
    
        <div class="row">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" width="10%">Von</th>
                        <th scope="col" width="15%">E-Mail</th>
                        <th scope="col" width="15%">Betreff</th>
                        <th scope="col" width="30%">Text</th>
                        <th scope="col" width="5%">Bearbeitet</th>
                        <th scope="col" width="10%">von</th>
                        <th scope="col" width="10%">Erstellt</th>
                        <th scope="col" width="5%">Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    @if($contacts)
                    @foreach ($contacts as $contact)
                        <tr>
                            <td>{{$contact->name}}</td>
                            <td><a target="blank" href="mailto:{{$contact->email}}">{{$contact->email}}</a></td>
                            <td>{{$contact->subject}}</td>
                            <td>{{$contact->content}}</td>
                            <td>{{$contact->done ? 'Ja' : 'Nein'}}</td>
                            <td>{{$contact->user ? $contact->user['username'] : ''}}</td>
                            <td>{{$contact->created_at ? $contact->created_at->diffForHumans() : 'Kein Datum'}}</td>
                            <td>{!! Form::model($contact, ['method' => 'PATCH', 'action'=>['AdminContactController@update', $contact->id]]) !!}
                                    {!! Form::submit('Bearbeitet', ['class' => 'btn btn-primary'])!!}
                                {!! Form::close()!!}
                            </td>
                        </tr>   
                    @endforeach
                    @endif
                </tbody>
            </table>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-5">
                    {{$contacts->render()}}
                </div>
            </div>
        </div>
    </div>  
</section>
@endsection