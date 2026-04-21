@extends('layouts.admin')

@section('content')
    <div>
        <div class="container mx-auto px-4">

            <header>
                <h3 class="text-3xl font-bold dark:text-white">Benutzer</h3>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                @if (Auth::user()->isAdmin())
                    <div class="md:col-span-3">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
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
                                    <x-forms.button type="submit">
                                        Benutzer erstellen
                                    </x-forms.button>
                                </x-forms.container>
                            </x-forms.form>
                        </div>
                    </div>
                @endif
                <div class="md:col-span-9">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left text-sm font-semibold text-gray-900 dark:text-white w-6/12">Benutzername</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left text-sm font-semibold text-gray-900 dark:text-white w-3/12">Rolle</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left text-sm font-semibold text-gray-900 dark:text-white w-3/12">Aktiv</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($users)
                                    @foreach ($users as $user)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"> 
                                            <td class="border border-gray-300 dark:border-gray-600 px-4 py-2"><a class="text-orientalpink hover:underline" href="{{route('users.edit', $user->id)}}">{{$user->username}}</a></td>
                                            <td class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-700 dark:text-gray-300">{{$user->role['name']}}</td>
                                            <td class="border border-gray-300 dark:border-gray-600 px-4 py-2"><span class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded text-xs font-semibold">{{$user->is_active ? 'Aktiv' : 'Archiviert'}}</span></td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
