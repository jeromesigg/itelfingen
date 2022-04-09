@extends('layouts.admin')
@section('content')

    @include('includes.tinyeditor')
    <section>
        <div class="container-fluid">
            <header>
                <h3 class="display">FAQ bearbeiten</h3>
            </header>
            <div class="row">
                 <div class="col-sm-9">
                    {!! Form::model($faq, ['method' => 'PATCH', 'action'=>['AdminFaqController@update', $faq->id], 'files' => true]) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Titel:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('description', 'Beschreibung:') !!}
                        {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 15]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('photo_id', 'Photo:') !!}
                        {!! Form::file('photo_id', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('archive_status_id', 'Archiv Status:') !!}
                        {!! Form::select('archive_status_id', $archive_statuses, null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('FAQ updaten', ['class' => 'btn btn-primary'])!!}
                    </div>
                    {!! Form::close()!!}
                </div>
            </div>
            <div class="row">
                @include('includes.form_error')
            </div>
        </div>
    </section>
@endsection
