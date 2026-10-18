@extends('layouts.admin')
@section('content')
    <section>
        <div class="container mx-auto px-4">
            <header>
                <h3 class="display">Albumbild erstellen</h3>
            </header>
            <x-forms.form :action="route('pictures.store')" enctype="multipart/form-data" accept-charset="UTF-8">
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
                        Albumbild erstellen
                    </x-forms.button>
                </x-forms.container>
            </x-forms.form>
            <div class="row">
                @include('includes.form_error')
            </div>
        </div>
        <x-crop-dialog title="Vorschaubild zuschneiden" input=".photo_id" target="cropped_photo_id" :aspect-ratio="1" :width="640" :height="640" />
    </section>
@endsection
