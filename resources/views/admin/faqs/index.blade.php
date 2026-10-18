@extends('layouts.admin')

@section('content')
    <header>
        <h3 class="text-3xl font-bold dark:text-white">FAQ</h3>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-testcolor">
        <x-forms.form :action="route('faqs.store')" enctype="multipart/form-data" accept-charset="UTF-8">
            <x-forms.container>
                <x-forms.text label="Titel:" name="name"/>
            </x-forms.container>
            <x-forms.container>
                <x-forms.text-area label="Beschreibung:" name="description" rows=15/>
            </x-forms.container>
            <x-forms.container>
                <x-forms.file label="Photo: " name="photo_id"/>
            </x-forms.container>
            <x-forms.container class="col-md-6">
                <x-forms.select label="Kapitel:" name="faq_chapter_id" required=true :collection="$faq_chapters"/>
            </x-forms.container>
            <x-forms.container>
                <x-forms.button type="submit" class="btn btn-primary">
                    FAQ erstellen
                </x-forms.button>
            </x-forms.container>
        </x-forms.form>
        <div class="col-span-3">
            <div id="tanStackTable" data-vue-component="faqs-table"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module">
        const editor = Jodit.make('#description', {
            buttons: ['bold', 'italic', 'underline', '|', 'ul', 'ol'],
            height: 400,
            allowResizeY: true
        });
    </script>
@endpush
