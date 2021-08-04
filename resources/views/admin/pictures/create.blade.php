@extends('layouts.admin')
@section('styles')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
@endsection
@section('content')
    <section>
        <div class="container-fluid">
            <header> 
                <h3 class="display">Albumbild erstellen</h3>
            </header>
            <div class="row">
                {!! Form::open(['method' => 'POST', 'action'=>'AdminPicturesController@store', 'files' => true]) !!}
                <div class="form-group">
                    {!! Form::label('name', 'Name:') !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                        {!! Form::label('photo_id', 'Original Photo:') !!}
                        {!! Form::file('photo_id', ['class' => 'photo_id', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::hidden('cropped_photo_id', null, ['class' => 'form-control', 'required', 'id' => 'cropped_photo_id']) !!}
                </div>
                <div class="form-group">
                    {!! Form::submit('Albumbild Erstellen', ['class' => 'btn btn-primary'])!!}
                </div>
                {!! Form::close()!!}
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
@endsection
@section('scripts')
    @include('admin/pictures/photo_cropped_js')
@endsection