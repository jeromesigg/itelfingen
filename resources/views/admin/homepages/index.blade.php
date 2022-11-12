@extends('layouts.admin')

@section('content')
<section>
    <div class="container-fluid">

        <header>
            <h3>Homepage</h3>
        </header>

        <div class="row">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" width="10%">Name</th>
                        <th scope="col" width="20%">Beschreibung</th>
                        <th scope="col" width="15%">Adresse</th>
                        <th scope="col" width="15%">Telefon</th>
                        <th scope="col" width="15%">E-Mail</th>
                        <th scope="col" width="15%">Banner Text</th>
                        <th scope="col" width="5%">Bild Titel</th>
                        <th scope="col" width="5%">Bild Oben</th>
                        <th scope="col" width="5%">Bild Unten</th>
                        <th scope="col" width="5%">Bild Login Gross</th>
                        <th scope="col" width="5%">Bild Login Klein</th>
                    </tr>
                </thead>
                <tbody>
                    @if($homepages)
                    @foreach ($homepages as $homepage)
                        <tr>
                            <td><a href="{{route('homepages.edit', $homepage->id)}}">{{$homepage->title}}</a></td>
                            <td>{{$homepage->subtitle}}</td>
                            <td>{{$homepage->address}}</td>
                            <td>{{$homepage->phone}}</td>
                            <td>{{$homepage->mail}}</td>
                            <td>{{$homepage->green_text}}</td>
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
