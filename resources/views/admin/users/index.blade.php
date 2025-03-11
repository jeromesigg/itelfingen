@extends('layouts.admin')

@section('content')
    <div>
        <div class="container-fluid">

            <header>
                <h3 class="text-3xl font-bold dark:text-white">Benutzer</h3>
            </header>

            <div class="row">
                @if (Auth::user()->isAdmin())
                    <div class="col-sm-3">
                        <x-forms.form :action="route('users.create')" autocomplete="off">
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
                                <x-forms.text label="Password:" name="password" required=true type="password"/>
                            </x-forms.container>
                            <x-forms.container>
                                <x-forms.button type="submit" class="btn btn-primary">
                                    Benutzer erstellen
                                </x-forms.button>
                            </x-forms.container>
                        </x-forms.form>
                    </div>
                @endif
                <div class="col-sm-9">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col" width="10%">Benutzername</th>
                                <th scope="col" width="30%">Rolle</th>
                                <th scope="col" width="40%">Aktiv</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($users)
                            @foreach ($users as $user)
                                <tr>
                                    <td><a class="text-orientalpink" href="{{route('users.edit', $user->id)}}">{{$user->username}}</a></td>
                                    <td>{{$user->role['name']}}</td>
                                    <td>{{$user->is_active ? 'Aktiv' : 'Archiviert'}}</td>
                                    </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
