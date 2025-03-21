@extends('layouts.admin')

@section('content')
<section>
    <div class="container-fluid">

        <header> 
            <h3>Geschichte</h3>
        </header>
    
        <div class="row">
            <div class="col-sm-3">
                <x-forms.form :action="route('histories.store')" enctype="multipart/form-data" accept-charset="UTF-8">
                    <x-forms.container>
                        <x-forms.text label="Titel:" name="title" required=true/>
                    </x-forms.container>
                    <x-forms.container>
                        <x-forms.text label="Untertitel:" name="subtitle"/>
                    </x-forms.container>
                    <x-forms.container>
                        <x-forms.textarea label="Beschreibung:" name="description" rows=3/>
                    </x-forms.container>
                    <x-forms.container>
                        <x-forms.file label="Photo: " name="photo_id"/>
                    </x-forms.container>
                    <x-forms.container>
                        <x-forms.button type="submit" class="btn btn-primary">
                            Geschichte erstellen
                        </x-forms.button>
                    </x-forms.container>
                </x-forms.form>
            </div>    
            <div class="col-sm-9">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" width="10%">photo</th>
                            <th scope="col" width="10%">Titel</th>
                            <th scope="col" width="15%">Untertitel</th>
                            <th scope="col" width="55%">Beschreibung</th>
                            <th scope="col" width="5%">Archiv-Status</th>
                            <th scope="col" width="5%">Sort-Index</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($histories)
                        @foreach ($histories as $history)
                            <tr>
                                <td><img height="50" src="{{$history->photo ? $history->photo->file : 'http://placehold.it/50x50'}}" alt=""></td>
                                <td><a href="{{route('histories.edit', $history->id)}}">{{$history->title}}</a></td>
                                <td>{{$history->subtitle}}</td>
                                <td>{{$history->description}}</td>
                                <td>{{$history->archive_status['name']}}</td>
                                <td>{{$history['sort-index']}}</td>
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