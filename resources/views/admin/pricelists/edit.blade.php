@extends('layouts.admin')
@section('content')

    <section>
        <div class="container-fluid">
            <header> 
                <h1 class="h3 display">Preis bearbeiten</h1>
            </header>
            <div class="row">
                 <div class="col-sm-9">
                    {!! Form::model($pricelist, ['method' => 'PATCH', 'action'=>['AdminPricelistController@update', $pricelist->id]]) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Name:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('detail', 'Beschreibung:') !!}
                        {!! Form::text('detail', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('price', 'Preis:') !!}
                        {!! Form::text('price', null, ['class' => 'form-control', 'required']) !!}
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
                        {!! Form::submit('Preis Updaten', ['class' => 'btn btn-primary'])!!}
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