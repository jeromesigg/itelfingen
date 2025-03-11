@extends('layouts.admin')

@section('content')
<section>
    <div class="container-fluid">

        <header> 
            <h3>Personen</h3>
        </header>
    
        <div class="row">
            <div class="col-sm-3">
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
                        <x-forms.button type="submit" class="btn btn-primary">
                            Person erstellen
                        </x-forms.button>
                    </x-forms.container>
                </x-forms.form>
            </div>    
            <div class="col-sm-9">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" width="10%">Bild</th>
                            <th scope="col" width="30%">Name</th>
                            <th scope="col" width="40%">Funktion</th>
                            <th scope="col" width="10%">Archiv-Status</th>
                            <th scope="col" width="10%">Sort-Index</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($people)
                        @foreach ($people as $person)
                            <tr>  
                                <td><img height="50" src="{{$person->photo ? $person->photo->file : 'http://placehold.it/50x50'}}" alt=""></td>
                                <td><a class="text-orientalpink" href="{{route('people.edit', $person->id)}}">{{$person->name}}</a></td>
                                <td>{{$person->function}}</td>
                                <td>{{$person->archive_status['name']}}</td>
                                <td>{{$person['sort-index']}}</td>
                                </tr>   
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>  
</section>
@endsection