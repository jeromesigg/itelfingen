@extends('layouts.admin')
@section('content')

    <section>
        <div class="container-fluid">
            <header> 
                <h3 class="display">Kapitel bearbeiten</h3>
            </header>
            <div class="row">
                 <div class="col-sm-9">
                    {!! Form::model($faqchapter, ['method' => 'PATCH', 'action'=>['AdminFAQChapterController@update', $faqchapter->id], 'files' => true]) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Titel:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('archive_status_id', 'Archiv Status:') !!}
                        {!! Form::select('archive_status_id', $archive_statuses, null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('sort-index', 'Sort Index:') !!}
                        {!! Form::text('sort-index', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Kapitel updaten', ['class' => 'btn btn-primary'])!!}
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