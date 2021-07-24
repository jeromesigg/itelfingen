@extends('layouts.admin')

@section('content')
<section>
    <div class="container-fluid">

        <header> 
            <h3>Benutzer</h3>
        </header>
    
        <div class="row">
            <div class="col-sm-3">
                {!! Form::open(['method' => 'POST', 'action'=>'AdminUserController@store', 'autocomplete' => 'off']) !!}
                    <div class="form-group">
                        {!! Form::label('username', 'Name:') !!}
                        {!! Form::text('username', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('fullname', 'Name:') !!}
                        {!! Form::text('fullname', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('phone', 'Handy-Nummer:') !!}
                        {!! Form::text('phone', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('signature', 'Unterschrift:') !!}
                        {!! Form::file('signature', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('role_id', 'Role:') !!}
                        {!! Form::select('role_id', [''=>'Wähle Rolle'] + $roles, null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('is_active', 'Status:') !!}
                        {!! Form::select('is_active', array(1 => "Aktiv", 0 => 'Archiviert'), null,  ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('password', 'Password:') !!}
                        {!! Form::password('password', ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Benutzer erstellen', ['class' => 'btn btn-primary'])!!}
                    </div>
                {!! Form::close()!!}
            </div>    
            <div class="col-sm-9">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" width="10%">Benutzername</th>
                            <th scope="col" width="30%">Rolle</th>
                            <th scope="col" width="40%">Aktiv</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($users)
                        @foreach ($users as $user)
                            <tr>  
                                <td><a href="{{route('users.edit', $user->id)}}">{{$user->username}}</a></td>
                                <td>{{$user->role['name']}}</td>
                                <td>{{$user->isactive ? 'Aktiv' : 'Archiviert'}}</td>
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