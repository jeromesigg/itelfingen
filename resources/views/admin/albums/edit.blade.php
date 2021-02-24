@extends('layouts.admin')

@section('content')
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h3 class="display">Album</h3>
            </header>
            <div class="row">
                <div class="col-sm-6">
                    {!! Form::model($album, ['method' => 'Patch', 'action'=>['AdminAlbumsController@update',$album->id], 'autocomplete' => 'off']) !!}
                        <div class="form-group">
                            {!! Form::label('name', 'Name:') !!}
                            {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('default_album', 'Default:') !!}
                            {!! Form::checkbox('default_album', $album['default_album'], ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Update Album', ['class' => 'btn btn-primary'])!!}
                        </div>
                    {!! Form::close()!!}
                    {!! Form::model($album, ['method' => 'DELETE', 'action'=>['AdminAlbumsController@destroy',$album->id], 'id'=> "myForm"]) !!}
                    <div class="form-group">
                        {!! Form::submit('Album löschen', ['class' => 'btn btn-danger confirm'])!!}
                    </div>
                    {!! Form::close()!!}
                 </div>
            </div>
        </div>    
    </section>         
 

@endsection