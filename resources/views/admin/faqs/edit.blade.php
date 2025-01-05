@extends('layouts.admin')
@section('content')

    @include('includes.tinyeditor')
    <section>
        <div class="container-fluid">
            <header>
                <h3 class="display">FAQ bearbeiten</h3>
            </header>
            <div class="row">
                 <div class="col-sm-9">
                    <x-forms.form :action="route('faqs.update', $faq)" enctype="multipart/form-data" accept-charset="UTF-8" method="PATCH" :model="$faq">
                        <x-forms.container>
                            <x-forms.text label="Titel:" name="name"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.textarea label="Beschreibung:" name="description" rows=15/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.file label="Photo: " name="photo_id"/>
                        </x-forms.container>
                        <div class="form-row">                        
                            <x-forms.container class="col-md-6">
                                <x-forms.select label="Kapitel:" name="faq_chapter_id" required=true :collection="$faq_chapters"/>
                            </x-forms.container>     
                            <x-forms.container class="col-md-6">
                                <x-forms.select label="Archiv Status:" name="archive_status_id" required=true :collection="$archive_statuses"/>
                            </x-forms.container>     
                        </div>
                        <x-forms.container>
                            <x-forms.button type="submit" class="btn btn-primary">
                                FAQ updaten
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
