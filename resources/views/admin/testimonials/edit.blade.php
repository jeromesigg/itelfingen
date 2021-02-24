@extends('layouts.admin')
@section('content')

    <section>
        <div class="container-fluid">
            <header> 
                <h3 class="display">Bewertung bearbeiten</h3>
            </header>
            <div class="row">
                 <div class="col-sm-9">
                    {!! Form::model($testimonial, ['method' => 'PATCH', 'action'=>['AdminTestimonialController@update', $testimonial->id], 'autocomplete' => 'off']) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Name:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('function', 'Function:') !!}
                        {!! Form::text('function', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('comment', 'Kommentar:') !!}
                        {!! Form::textarea('comment', null, ['class' => 'form-control', 'required', 'rows' => 2]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('sort-index', 'Sort-Index:') !!}
                        {!! Form::text('sort-index', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('archive_status_id', 'Archiv-Status:') !!}
                        {!! Form::select('archive_status_id', $archive_statuses, null, ['class' => 'form-control', 'required']) !!}
                    </div>
                   
  
                    <div class="form-group">
                        {!! Form::submit('Bewertung Updaten', ['class' => 'btn btn-primary'])!!}
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