@extends('layouts.admin')
@section('content')

    <div>
        <div class="container-fluid">
            <header>
                <h3 class="text-3xl font-bold dark:text-white">Raum bearbeiten</h3>
            </header>
            <div class="row">
                 <div class="col-sm-9">
                    <x-forms.form :action="route('rooms.update', $room)" method="PATCH" :model="$room">
                        <x-forms.container>
                            <x-forms.text label="Name:" name="name" required=true/>
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
                                Raum aktualisieren
                            </x-forms.button>
                        </x-forms.container>
                    </x-forms.form>
                </div>
            </div>
            <div class="row">
                @include('includes.form_error')
            </div>
        </div>
    </div>
@endsection