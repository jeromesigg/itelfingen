@extends('layouts.admin')
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
        <x-crop-dialog title="Bild zuschneiden" input=".old_photo_id" target="new_photo_id" :aspect-ratio="4" :width="780" :height="220" clear-input />
    </div>
@endsection
