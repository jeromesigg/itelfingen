@extends('layouts.admin')

@section('content')
<section>
    <div class="container-fluid">

        <header> 
            <h3>Bewertungen</h3>
        </header>
    
        <div class="row">
            <div class="col-sm-3">
                <x-forms.form :action="route('testimonials.store')">
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
                        <x-forms.button type="submit" class="btn btn-primary">
                            Kommentar erstellen
                        </x-forms.button>
                    </x-forms.container>
                </x-forms.form>
            </div>    
            <div class="col-sm-9">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" width="15%">Name</th>
                            <th scope="col" width="15%">Funktion</th>
                            <th scope="col" width="50%">Kommentar</th>
                            <th scope="col" width="10%">Archiv-Status</th>
                            <th scope="col" width="10%">Sort-Index</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($testimonials)
                        @foreach ($testimonials as $testimonial)
                            <tr>
                                <td><a href="{{route('testimonials.edit', $testimonial->id)}}">{{$testimonial->name}}</a></td>
                                <td>{{$testimonial->function}}</td>
                                <td>{{$testimonial->comment}}</td>
                                <td>{{$testimonial->archive_status['name']}}</td>
                                <td>{{$testimonial['sort-index']}}</td>
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