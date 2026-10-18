@extends('layouts.admin')
@section('content')

    <div>
        <div class="container mx-auto px-4">
            <header>
                <h3 class="text-3xl font-bold dark:text-white">Empfänger bearbeiten</h3>
            </header>
            <div class="row">
                 <div class="col-sm-9">
                    <x-forms.form :action="route('newsletter.update', $newsletter)" method="PATCH" :model="$newsletter">
                        <div class="form-row">
                            <x-forms.container class="col-md-6">
                                <x-forms.text label="Name:" name="name" required=true/>
                            </x-forms.container>
                            <x-forms.container class="col-md-6">
                                <x-forms.text label="Vorname:" name="firstname"/>
                            </x-forms.container>
                        </div>
                        <x-forms.container>
                            <x-forms.text label="E-Mail:" name="email" type="email" required=true/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.text label="Buchungen:" name="bookings" type="checkbox" value="{{$newsletter['bookings']}}"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.text label="Genossenschaft:" name="members" type="checkbox" value="{{$newsletter['members']}}"/>
                        </x-forms.container>
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
    </div>
@endsection
