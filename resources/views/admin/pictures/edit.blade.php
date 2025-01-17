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

                    <x-forms.form :action="route('pictures.update', $picture)" enctype="multipart/form-data" accept-charset="UTF-8" method="PATCH" :model="$picture">
                        <x-forms.container>
                            <x-forms.text label="Name:" name="name" required=true/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.text label="Symbol:" name="symbol"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.file label="Original Photo: " name="photo_id" class="photo_id" required=true/>
                            <x-forms.hidden name="cropped_photo_id" />
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.button type="submit" class="btn btn-primary">
                                Albumbild updaten
                            </x-forms.button>
                        </x-forms.container>
                    </x-forms.form>
                    <x-forms.form :action="route('pictures.destroy', $picture)" method="DELETE">
                        <x-forms.container>
                            <x-forms.button type="submit" class="btn btn-danger">
                                Albumbild löschen
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
@push('scripts')
    @include('admin/pictures/photo_cropped_js')

    @endpush