@extends('layouts.admin')
@section('content')

    <section>
        <div class="container-fluid">
            <header>
                <h3 class="display">Person bearbeiten</h3>
            </header>
            <div class="row">
                 <div class="col-sm-9">
                    <x-forms.form :action="route('people.update', $person)" enctype="multipart/form-data" accept-charset="UTF-8" method="PATCH" :model="$person">
                        <x-forms.container>
                            <x-forms.text label="Name:" name="name" required=true/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.text label="Funktion:" name="function"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.file label="Photo: " name="photo_id"/>
                        </x-forms.container>
                        <div class="row">
                            <x-forms.container class="col-sm-6">
                                <x-forms.select label="Archiv-Status:" name="archive_status_id" :collection="$archive_statuses"/>
                            </x-forms.container>
                            <x-forms.container class="col-sm-6">
                                <x-forms.text label="Sort-Index:" name="sort-index" required=true type="number"/>
                            </x-forms.container>
                        </div>
                        <x-forms.container>
                            <x-forms.button type="submit" class="btn btn-primary">
                                Person aktualisieren
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
</div>
@endsection
