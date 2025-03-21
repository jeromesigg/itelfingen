@extends('layouts.admin')
@section('content')

    <section>
        <div class="container-fluid">
            <header> 
                <h3 class="display">Geschichte bearbeiten</h3>
            </header>
            <div class="row">
                 <div class="col-sm-9">
                    <x-forms.form :action="route('histories.update', $history)" enctype="multipart/form-data" accept-charset="UTF-8" method="PATCH" :model="$history">
                        <x-forms.container>
                            <x-forms.text label="Titel:" name="title" required=true/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.text label="Untertitel:" name="subtitle"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.textarea label="Beschreibung:" name="description" rows=3/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.file label="Photo: " name="photo_id"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.button type="submit" class="btn btn-primary">
                                Geschichte aktualisieren
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