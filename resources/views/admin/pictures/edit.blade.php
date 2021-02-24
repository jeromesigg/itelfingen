@extends('layouts.admin')
@section('styles')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
@endsection
@section('content')
    <section>
        <div class="container-fluid">
            <header> 
                <h3 class="display">Albumbild bearbeiten</h3>
            </header>
            <div class="row">
                <div class="col-sm-3" >
        
                    <img src="{{$picture->photo ? $picture->photo->file : 'http://placehold.it/350x350'}}" alt="" class="img-responsive" style="max-width: -webkit-fill-available;">
                </div> 
                <div class="col-sm-9">
                    {!! Form::model($picture, ['method' => 'PATCH', 'action'=>['AdminPicturesController@update', $picture->id], 'files' => true]) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Name:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('album_id', 'Album:') !!}
                        {!! Form::select('album_id', [''=>'Wähle Album'] + $albums, null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                            {!! Form::label('photo_id', 'Original Photo:') !!}
                            {!! Form::file('photo_id', ['class' => 'photo_id', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::hidden('cropped_photo_id', null, ['class' => 'form-control', 'required', 'id' => 'cropped_photo_id']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::submit('Albumbild updaten', ['class' => 'btn btn-primary col-sm-6'])!!}
                    </div>
                    {!! Form::close()!!}
                    {!! Form::open(['method' => 'DELETE', 'action'=>['AdminPicturesController@destroy', $picture->id]]) !!}
                        <div class="form-group">
                            {!! Form::submit('Albumbild löschen', ['class' => 'btn btn-danger col-sm-6'])!!}
                        </div>
                    {!! Form::close()!!}
                </div>   
            </div>   
            <div class="row"> 
                @include('includes.form_error')
            </div>   
        </div>
        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Vorschaubild zuschneiden</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="img-container">
                            <div class="row">
                                <div class="col-md-8">
                                    <img id="image" src="https://avatars0.githubusercontent.com/u/3456749">
                                </div>
                                <div class="col-md-4">
                                    <div class="preview"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                        <button type="button" class="btn btn-primary" id="crop">Zuschneiden</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
@section('scripts')
    @include('admin/pictures/photo_cropped_js')
@endsection