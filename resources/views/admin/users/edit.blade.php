@extends('layouts.admin')
@section('content')

    <section>
        <div class="container-fluid">
            <header> 
                <h3 class="display">Benutzer bearbeiten</h3>
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
                        <x-forms.container>
                            <x-forms.select label="Rolle:" name="role_id" required=true :collection="$roles"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.select label="Archiv Status:" name="archive_status_id" required=true :collection="$archive_statuses"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.text label="Password:" name="password" required=true type="password"/>
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
    </section>
</div>
@endsection