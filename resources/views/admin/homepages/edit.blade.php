@extends('layouts.admin')
@section('content')

    <section>
        <div class="container-fluid">
            <header>
                <h3 class="display">Homepage bearbeiten</h3>
            </header>
            <div class="row">
                 <div class="col-sm-9">
                    <x-forms.form :action="route('homepages.update', $homepage)" enctype="multipart/form-data" accept-charset="UTF-8" method="PATCH" :model="$homepage">
                        <x-forms.container>
                            <x-forms.text label="Name:" name="title" required=true/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.text label="Beschreibung:" name="subtitle"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.textarea label="Adresse:" name="address" required=true rows=3/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.textarea label="Post-Adresse:" name="postaddress" required=true rows=3/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.text label="E-Mail:" name="email"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.text label="Telefon:" name="phone"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.text label="Banner-Text:" name="green_text"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.file label="Haupt-Photo: " name="main_photo_id"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.file label="1. Photo: " name="background_top_photo_id"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.file label="Grosses Photo Umgebung: " name="background_bottom_photo_id"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.file label="Grosses Photo Login: " name="big_login_photo_id"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.file label="Kleines Photo Login: " name="small_login_photo_id"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.button type="submit" class="btn btn-primary">
                                Homepage aktualisieren
                            </x-forms.button>
                        </x-forms.container>
                    </x-forms.form>
                </div>
            </div>
            <div class="row">
                @include('includes.form_error')
            </div>
        </div>
    </section>
@endsection
