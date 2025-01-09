@extends('layouts.admin')
@section('content')
    <section>
        <div class="container-fluid">
            <header> 
                <h3 class="display">Rechnungsposition bearbeiten</h3>
            </header>
            <div class="row">
                 <div class="col-sm-9">
                    <x-forms.form :action="route('positions.update', $position)" method="PATCH" :model="$position">
                        <x-forms.container>
                            <x-forms.text label="Name:" name="name" required=true/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.text label="Anzeigen:" name="show" type="checkbox" value="{{$position['show']}}"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.select label="Archiv Status:" name="archive_status_id" required=true :collection="$archive_statuses"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.button type="submit" class="btn btn-primary">
                                Rechnungsposition erstellen
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