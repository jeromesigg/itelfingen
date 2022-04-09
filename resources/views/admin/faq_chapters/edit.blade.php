@extends('layouts.admin')
@section('styles')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
@endsection
@section('content')

    <section>
        <div class="container-fluid">
            <header>
                <h3 class="display">Kapitel bearbeiten</h3>
            </header>
            <div class="row">
                 <div class="col-sm-9">
                    {!! Form::model($faqchapter, ['method' => 'PATCH', 'action'=>['AdminFaqChapterController@update', $faqchapter->id], 'files' => true]) !!}
                     <div class="form-group">
                         {!! Form::label('name', 'Titel:') !!}
                         {!! Form::text('name', null, ['class' => 'form-control']) !!}
                     </div>
                     <div class="form-group">
                         {!! Form::label('symbol', 'Symbol:') !!}
                         {!! Form::text('symbol', null, ['class' => 'form-control']) !!}
                     </div>
                     <div class="form-group">
                         {!! Form::label('old_photo_id', 'Original Photo:') !!}
                         {!! Form::file('old_photo_id', ['class' => 'old_photo_id']) !!}
                     </div>
                     <div class="form-group">
                         {!! Form::hidden('new_photo_id', null, ['class' => 'form-control', 'id' => 'new_photo_id']) !!}
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
        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Bild zuschneiden</h5>
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
    @include('admin/faq_chapters/photo_cropped_js')
@endsection
