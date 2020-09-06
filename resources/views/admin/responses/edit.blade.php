@extends('layouts.admin')

@section('content')
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Antwort-Möglichkeiten</h1>
            </header>
            <div class="row">
                <div class="col-sm-6">
                    {!! Form::model($response, ['method' => 'Patch', 'action'=>['AdminResponsesController@update',$response->id]]) !!}
                        <div class="form-group">
                            {!! Form::label('name', 'Name:') !!}
                            {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Update Antwort', ['class' => 'btn btn-primary'])!!}
                        </div>
                    {!! Form::close()!!}
                    {!! Form::model($response, ['method' => 'DELETE', 'action'=>['AdminResponsesController@destroy',$response->id], 'id'=> "myForm"]) !!}
                    <div class="form-group">
                        {!! Form::submit('Antwort löschen', ['class' => 'btn btn-danger confirm'])!!}
                    </div>
                    {!! Form::close()!!}
                 </div>
            </div>
        </div>    
    </section>         
 

@endsection