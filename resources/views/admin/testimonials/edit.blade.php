@extends('layouts.admin')
@section('content')

    <section>
        <div class="container-fluid">
            <header> 
                <h3 class="display">Bewertung bearbeiten</h3>
            </header>
            <div class="row">
                 <div class="col-sm-9">
                    <x-forms.form :action="route('testimonials.store')" method="PATCH" :model="$testimonial">
                        <x-forms.container>
                            <x-forms.text label="Name:" name="name" required=true/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.text label="Funktion:" name="function"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.textarea label="Kommentar:" name="comment" rows=2/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.text label="Sort-Index:" name="sort-index" required=true type="number"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.select label="Archiv Status:" name="archive_status_id" required=true :collection="$archive_statuses"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.button type="submit" class="btn btn-primary">
                                Bewertung Updaten
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