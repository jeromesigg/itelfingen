@extends('layouts.admin')

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
    <x-crop-dialog title="Bild zuschneiden" input=".old_photo_id" target="new_photo_id" :aspect-ratio="4" :width="780" :height="220" clear-input />
@endsection
