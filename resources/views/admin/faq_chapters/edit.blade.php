@extends('layouts.admin')
@section('styles')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
@endsection
@section('content')

    <div>
        <div class="container-fluid">
            <header>
                <h3 class="text-3xl font-bold dark:text-white">Kapitel bearbeiten</h3>
            </header>
            <div class="row">
                 <div class="col-sm-9">
                    <x-forms.form :action="route('faq_chapters.update', $faqchapter)" enctype="multipart/form-data" accept-charset="UTF-8" method="PATCH" :model="$faqchapter">
                        <x-forms.container>
                            <x-forms.text label="Titel:" name="name" required=true/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.text label="Symbol:" name="symbol"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.file label="Photo: " name="old_photo_id" class="old_photo_id"/>
                            <x-forms.hidden name="new_photo_id" />
                        </x-forms.container>
                        <div class="form-row">                        
                            <x-forms.container class="col-md-6">
                                <x-forms.text label="Sort Index:" name="sort-index" type="number" required=true/>
                            </x-forms.container>     
                            <x-forms.container class="col-md-6">
                                <x-forms.select label="Archiv Status:" name="archive_status_id" required=true :collection="$archive_statuses"/>
                            </x-forms.container>     
                        </div>
                        <x-forms.container>
                            <x-forms.button type="submit" class="btn btn-primary">
                                FAQ Kapitel aktualisieren
                            </x-forms.button>
                        </x-forms.container>
                    </x-forms.form>
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
    </div>
@endsection
@push('scripts')
    @include('admin/faq_chapters/photo_cropped_js')
    @endpush
