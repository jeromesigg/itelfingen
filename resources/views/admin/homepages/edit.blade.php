@extends('layouts.admin')
@section('content')

    <section>
        <div class="container-fluid">
            <header> 
                <h1 class="h3 display">Homepage bearbeiten</h1>
            </header>
            <div class="row">
                 <div class="col-sm-9">
                    {!! Form::model($homepage, ['method' => 'PATCH', 'action'=>['AdminHomepageController@update', $homepage->id], 'files' => true]) !!}
                    <div class="form-group">
                        {!! Form::label('title', 'Name:') !!}
                        {!! Form::text('title', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('subtitle', 'Beschreibung:') !!}
                        {!! Form::text('subtitle', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('address', 'Adresse:') !!}
                        {!! Form::textarea('address', null, ['class' => 'form-control', 'required', 'rows' =>3]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('mail', 'E-Mail:') !!}
                        {!! Form::text('mail', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('phone', 'Telefon:') !!}
                        {!! Form::text('phone', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('main_photo_id', 'Photo:') !!}
                        {!! Form::file('main_photo_id', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('background_top_photo_id', 'Photo:') !!}
                        {!! Form::file('background_top_photo_id', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('background_bottom_photo_id', 'Photo:') !!}
                        {!! Form::file('background_bottom_photo_id', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('big_login_photo_id', 'Photo:') !!}
                        {!! Form::file('big_login_photo_id', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('small_login_photo_id', 'Photo:') !!}
                        {!! Form::file('small_login_photo_id', null, ['class' => 'form-control']) !!}
                    </div>
  
                    <div class="form-group">
                        {!! Form::submit('Homepage Updaten', ['class' => 'btn btn-primary'])!!}
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