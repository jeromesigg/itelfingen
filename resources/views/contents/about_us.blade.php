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
                            <li><a href="{{route('home')}}">Home</a></li>
                            <li>Über uns</li>
                        </ol>
                    </div>
                </div>
            </section>
            <section id="about_us" class="about_us">
                <div class="container" data-aos="fade-up">
                    <div class="section-title">
                        <p>Was macht die Genossenschaft Ferienhaus Itelfingen</p>
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
                                <div class="col-lg-3 col-md-6">
                                    <div class="member" data-aos="zoom-in" data-aos-delay="100">
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

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js" integrity="sha512-0QbL0ph8Tc8g5bLhfVzSqxe9GERORsKhIn1IrpxDAgUsbBGz/V7iSav2zzW325XGd1OMLdL4UiqRJj702IeqnQ==" crossorigin="anonymous"></script>

    <script src="{{ asset('js/main.js') }}"></script>
@endsection
