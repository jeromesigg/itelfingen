@extends('layouts.admin')
@section('styles')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
@endsection

@section('content')
<section>
    <div class="container-fluid">

        <header>
            <h3>FAQ Kapitel</h3>
        </header>

        <div class="row">
            <div class="col-sm-3">
                <x-forms.form :action="route('faq_chapters.store')" enctype="multipart/form-data" accept-charset="UTF-8">
                    <x-forms.container>
                        <x-forms.text label="Titel:" name="name"/>
                    </x-forms.container>
                    <x-forms.container>
                        <x-forms.text label="Symbol:" name="symbol"/>
                    </x-forms.container>
                    <x-forms.container>
                        <x-forms.file label="Photo: " name="old_photo_id" class="old_photo_id"/>
                        <x-forms.hidden name="new_photo_id" />
                    </x-forms.container>
                    <x-forms.container>
                        <x-forms.button type="submit" class="btn btn-primary">
                            FAQ Kapitel erstellen
                        </x-forms.button>
                    </x-forms.container>
                </x-forms.form>
            </div>
            <div class="col-sm-9">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Titel</th>
                            <th scope="col">Foto</th>
                            <th scope="col">Symbol</th>
                            <th scope="col">Archiv-Status</th>
                            <th scope="col">Sort-Index</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($faq_chapters)
                        @foreach ($faq_chapters as $faq_chapter)
                            <tr>
                                <td><a href="{{route('faq_chapters.edit', $faq_chapter->id)}}">{{$faq_chapter->name}}</a></td>
                                <td><img height="50" src="{{$faq_chapter->photo ? $faq_chapter->photo->file : 'https://placehold.it/50x50'}}" alt=""></td>
                                <td>{{$faq_chapter['symbol']}}</td>
                                <td>{{$faq_chapter->archive_status['name']}}</td>
                                <td>{{$faq_chapter['sort-index']}}</td>
                                </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
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
