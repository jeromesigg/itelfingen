@extends('layouts.admin')
@section('content')

    <section>
        <div class="container-fluid">
            <header> 
                <h3 class="display">Benutzer bearbeiten</h3>
            </header>
            <div class="row">
                 <div class="col-sm-9">
                    {!! Form::model($user, ['method' => 'PATCH', 'action'=>['AdminUserController@update', $user->id], 'autocomplete' => 'off', 'files' => true]) !!}
                    <div class="form-group">
                        {!! Form::label('username', 'Benutzername:') !!}
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
                        @if ($user->signature)
                            <a href="{{route('download_signature', $user)}}" target="_blank">Unterschrift</a>
                        @endif
                        <br>
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
                        {!! Form::submit('Benutzer Updaten', ['class' => 'btn btn-primary'])!!}
                    </div>
                    {!! Form::close()!!}
                </div>   
            </div>   
            <div class="row"> 
                @include('includes.form_error')
            </div>   
        </div>
    </section>
</div>
@endsection