@extends('layouts.admin')

@section('content')
<section>
    <div class="container mx-auto px-4">
        <header class="mb-6">  
           <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Personen</h3>
        </header>
    
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <div class="md:col-span-3">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                    <x-forms.form :action="route('people.store')" enctype="multipart/form-data" accept-charset="UTF-8">
                        <x-forms.container>
                            <x-forms.text label="Name:" name="name" required=true/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.text label="Funktion:" name="function"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.file label="Photo: " name="photo_id"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.button type="submit">
                                Person erstellen
                            </x-forms.button>
                        </x-forms.container>
                    </x-forms.form>
                </div>    
            </div>    
            <div class="md:col-span-9">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    <table class="w-full border-collapse">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left text-sm font-semibold text-gray-900 dark:text-white w-1/12">Bild</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left text-sm font-semibold text-gray-900 dark:text-white w-3/12">Name</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left text-sm font-semibold text-gray-900 dark:text-white w-4/12">Funktion</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left text-sm font-semibold text-gray-900 dark:text-white w-2/12">Archiv-Status</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left text-sm font-semibold text-gray-900 dark:text-white w-2/12">Sort-Index</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($people)
                                @foreach ($people as $person)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">  
                                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2"><img height="50" src="{{$person->photo ? $person->photo->file : 'http://placehold.it/50x50'}}" alt="" class="rounded"></td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2"><a class="text-orientalpink hover:underline" href="{{route('people.edit', $person->id)}}">{{$person->name}}</a></td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-700 dark:text-gray-300">{{$person->function}}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2"><span class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded text-xs font-semibold">{{$person->archive_status['name']}}</span></td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-center">{{$person['sort-index']}}</td>
                                    </tr>   
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>  
</section>
@endsection