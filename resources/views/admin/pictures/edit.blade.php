@extends('layouts.admin')
@section('content')
    <section>
        <div class="container mx-auto px-4">
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
        <x-crop-dialog title="Vorschaubild zuschneiden" input=".photo_id" target="cropped_photo_id" :aspect-ratio="1" :width="640" :height="640" />
    </section>
@endsection
