@extends('layouts.admin')
@section('styles')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
@endsection
@section('content')

    <div>
        <div class="container mx-auto px-4">
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
        <div class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-gray-950/60 p-4" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="w-full max-w-5xl rounded-lg bg-white shadow-xl dark:bg-gray-800" data-modal-dialog tabindex="-1" role="document">
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white" id="modalLabel">Bild zuschneiden</h2>
                    <button type="button" class="rounded-lg p-2 text-2xl leading-none text-gray-500 hover:bg-gray-100 hover:text-gray-900 focus:outline-hidden focus:ring-4 focus:ring-gray-200 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700" data-modal-close aria-label="Schließen">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="grid gap-6 p-6 md:grid-cols-[minmax(0,2fr)_minmax(12rem,1fr)]">
                    <div class="flex min-h-64 items-center justify-center overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-900">
                        <img class="block max-h-[60vh] max-w-full" id="image" src="https://avatars0.githubusercontent.com/u/3456749" alt="Bildvorschau">
                    </div>
                    <div>
                        <p class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Vorschau</p>
                        <div class="preview aspect-square w-full overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-900"></div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4 dark:border-gray-700">
                    <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-hidden focus:ring-4 focus:ring-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 dark:focus:ring-gray-700" data-modal-close>Abbrechen</button>
                    <button type="button" class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-strong focus:outline-hidden focus:ring-4 focus:ring-primary-soft" id="crop">Zuschneiden</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @include('admin/faq_chapters/photo_cropped_js')
    @endpush
