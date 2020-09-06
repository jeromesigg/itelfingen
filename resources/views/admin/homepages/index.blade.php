@extends('layouts.admin')

@section('content')
<section>
    <div class="container-fluid">

        <header> 
            <h1>Homepage</h1>
        </header>
    
        <div class="row">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" width="10%">Name</th>
                        <th scope="col" width="30%">Beschreibung</th>
                        <th scope="col" width="12%">Bild Titel</th>
                        <th scope="col" width="12%">Bild Oben</th>
                        <th scope="col" width="12%">Bild Unten</th>
                        <th scope="col" width="12%">Bild Login Gross</th>
                        <th scope="col" width="12%">Bild Login Klein</th>
                    </tr>
                </thead>
                <tbody>
                    @if($homepages)
                    @foreach ($homepages as $homepage)
                        <tr>
                            <td><a href="{{route('homepages.edit', $homepage->id)}}">{{$homepage->title}}</a></td>
                            <td>{{$homepage->subtitle}}</td>
                            <td><img height="50" src="{{$homepage->main_photo ? $homepage->main_photo->file : 'http://placehold.it/50x50'}}" alt=""></td>
                            <td><img height="50" src="{{$homepage->top_photo ? $homepage->top_photo->file : 'http://placehold.it/50x50'}}" alt=""></td>
                            <td><img height="50" src="{{$homepage->bottom_photo ? $homepage->bottom_photo->file : 'http://placehold.it/50x50'}}" alt=""></td>
                            <td><img height="50" src="{{$homepage->big_login_photo ? $homepage->big_login_photo->file : 'http://placehold.it/50x50'}}" alt=""></td>
                            <td><img height="50" src="{{$homepage->small_login_photo ? $homepage->small_login_photo->file : 'http://placehold.it/50x50'}}" alt=""></td>
                        </tr>   
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>  
</section>
@endsection