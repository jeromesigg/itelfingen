@extends('layouts.app')

@section('content')

    @include('includes.header')
    <div id="app">

        <main id="main">
            <section class="breadcrumbs">
                <div class="container">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2>Über uns</h2>
                        <ol>
                            <li><a href="{{route('home')}}" class="text-orientalpink">Home</a></li>
                            <li>Über uns</li>
                        </ol>
                    </div>
                </div>
            </section>
            <section id="about_us" class="about_us">
                <div class="container">
                    <div class="section-title">
                        <p>Was macht die Genossenschaft Ferienhaus Itelfingen?</p>
                    </div>

                    <div class="row">
                        <p>
                            Die Genossenschaft Ferienhaus Itelfingen bezweckt, in Itelfingen, Gemeinde Meierskappel, auf gemeinnütziger Grundlage ein Ferienhaus
                            zu betreiben. Sie stellt es hauptsächlich Jugendlichen und Erwachsenen für Ferien und Freizeit zur Benützung zur Verfügung.
                            Die Genossenschaft pachtet das Ferienhaus von der Reformierten Kirche Zürich. Der Vorstand der Genossenschaft kümmert sich um
                            Betrieb und Instandhaltung des Ferienhauses. Genossenschafter können sich aktiv beteiligen und das schöne Haus mitprägen und
                            mitunterhalten, zudem profitieren Sie von reduzierten Mietpreisen. Die Genossenschaft möchte Gemeinschaft und fruchtbare Begegnungen
                            schaffen und wünscht sich, dass an diesem Ort grosse Ideen entstehen dürfen.
                        </p>
                    </div>

                    <div class="row">
                        <a href='/files/Jahresbericht_2022.pdf' target="blank">Jahresbericht 2022</a>
                    </div>

                    @if(count($people)>0)
                        <br>
                        <hr>
                        <br>
                        <div class="section-title">
                            <p>Wer steckt hinter der Genossenschaft Ferienhaus Itelfingen</p>
                        </div>

                        <div class="row">
                            @foreach ($people as $person)
                                <div class="col-lg-4 col-md-6">
                                    <div class="member">
                                        <img src="{{$person->photo ? $person->photo->file : 'https://loremflickr.com/350/400/face?random='. $person->id}}" class="img-fluid" alt="">
                                        <div class="member-info">
                                            <div class="member-info-content">
                                                <h4>{{$person->name}}</h4>
                                                <span>{{$person->function}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    @endif
                </div>
            </section>
        </main><!-- End #main -->
        <!-- ======= Footer ======= -->
        @include('includes.footer')
        <div id="preloader"></div>
        <a href="#" class="back-to-top"><i class="bx bx-up-arrow-alt"></i></a>

    </div>
@endsection