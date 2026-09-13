@extends('layouts.admin')
@section('styles')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
@endsection

@section('content')
    <header>
        <h3 class="text-3xl font-bold dark:text-white">FAQ Kapitel</h3>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-testcolor">
        <x-forms.form :action="route('faq_chapters.store')" enctype="multipart/form-data" accept-charset="UTF-8">
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
            <x-forms.container>
                <x-forms.button type="submit" class="btn btn-primary">
                    FAQ Kapitel erstellen
                </x-forms.button>
            </x-forms.container>
        </x-forms.form>
        <div class="col-span-3">
            <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                <table class="w-full text-sm text-left rtl:text-right text-body">
                    <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">Titel</th>
                            <th scope="col" class="px-6 py-3 font-medium">Foto</th>
                            <th scope="col" class="px-6 py-3 font-medium">Symbol</th>
                            <th scope="col" class="px-6 py-3 font-medium">Archiv-Status</th>
                            <th scope="col" class="px-6 py-3 font-medium">Sort-Index</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($faq_chapters)
                        @foreach ($faq_chapters as $faq_chapter)
                            <tr class="bg-neutral-primary border-b border-default">
                                <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap"><a class="text-orientalpink" href="{{route('faq_chapters.edit', $faq_chapter->id)}}">{{$faq_chapter->name}}</a></th>
                                <td class="px-6 py-4"><img height="50" src="{{$faq_chapter->photo ? $faq_chapter->photo->file : 'https://placehold.it/50x50'}}" alt=""></td>
                                <td class="px-6 py-4">{{$faq_chapter['symbol']}}</td>
                                <td class="px-6 py-4">{{$faq_chapter->archive_status['name']}}</td>
                                <td class="px-6 py-4">{{$faq_chapter['sort-index']}}</td>
                                </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
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
@endsection
@push('scripts')
    @include('admin/faq_chapters/photo_cropped_js')
@endpush
