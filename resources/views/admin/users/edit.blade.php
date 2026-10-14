@extends('layouts.admin')
@section('content')

    <div>
        <div class="container mx-auto px-4">
            <header> 
                <h3 class="text-3xl font-bold dark:text-white">Benutzer bearbeiten</h3>
            </header>
            <div class="row">
                 <div class="col-sm-9">
                    <x-forms.form :action="route('users.update', $user)" autocomplete="off" method="PATCH" :model="$user">
                        <x-forms.container>
                            <x-forms.text label="Username:" name="username" required=true/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.text label="Name:" name="fullname"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.text label="Handy-Nummer:" name="phone"/>
                        </x-forms.container>
                        @if (Auth::user()->isAdmin())
                        <x-forms.container>
                            <x-forms.select label="Rolle:" name="role_id" required=true :collection="$roles"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.select label="Archiv Status:" name="archive_status_id" required=true :collection="$archive_statuses"/>
                        </x-forms.container>
                        @endif
                        <x-forms.container>
                            <x-forms.text label="Passwort (leer lassen = unverändert, mindestens 10 Zeichen):" name="password" type="password"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.button type="submit" class="btn btn-primary">
                                Benutzer Updaten
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