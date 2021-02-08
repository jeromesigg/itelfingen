@extends('layouts.admin')
@section('content')

    <section>
        <div class="container-fluid">
            <header> 
                <h1 class="h3 display">Benutzer bearbeiten</h1>
            </header>
            <div class="row">
                 <div class="col-sm-9">
                    {!! Form::model($user, ['method' => 'PATCH', 'action'=>['AdminUserController@update', $user->id], 'autocomplete' => 'off']) !!}
                    <div class="form-group">
                        {!! Form::label('username', 'Name:') !!}
                        {!! Form::text('username', null, ['class' => 'form-control']) !!}
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