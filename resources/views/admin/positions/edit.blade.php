@extends('layouts.admin')
@section('content')
    <section>
        <div class="container-fluid">
            <header> 
                <h3 class="display">Rechnungsposition bearbeiten</h3>
            </header>
            <div class="row">
                 <div class="col-sm-9">
                    {!! Form::model($position, ['method' => 'PATCH', 'action'=>['AdminPricelistPositionController@update', $position->id]]) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Name:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('show', 'Anzeigen:') !!}
                        {!! Form::checkbox('show', '1', null) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('archive_status_id', 'Archiv Status:') !!}
                        {!! Form::select('archive_status_id', $archive_statuses, null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Rechnungsposition updaten', ['class' => 'btn btn-primary'])!!}
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