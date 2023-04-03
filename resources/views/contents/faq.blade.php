@extends('layouts.app')

@section('content')

  @include('includes.header')
  <div id="app">

    <main id="main">
      <section class="breadcrumbs">
        <div class="container">
          <div class="d-flex justify-content-between align-items-center">
            <h2>FAQ</h2>
            <ol>
              <li><a href="{{route('home')}}">Home</a></li>
              <li>FAQ</li>
            </ol>
          </div>
        </div>
      </section>

      <div class="cd-faq js-cd-faq container max-width-md margin-top-lg margin-bottom-lg">
        <ul class="cd-faq__categories">
          @foreach ($faq_chapters as $chapter)
            <li>
                <a class="cd-faq__category cd-faq__category truncate" href="#{{$chapter->name}}">
                    @if(isset($chapter->symbol))
                        <i class="fas {{$chapter->symbol}}"></i>
                    @endif{{$chapter->name}}
                </a>
            </li>
          @endforeach
        </ul> <!-- cd-faq__categories -->

        <div class="cd-faq__items">
          @foreach ($faq_chapters as $chapter)
            <ul id="{{$chapter->name}}" class="cd-faq__group">

              <div class="cd-faq__title">
                  <img class="cd-faq__title-image" src="{{$chapter->photo ? $chapter->photo->file : ''}}" height="250px" alt="">
                  <h2 class="cd-faq__title-text">
                      @if(isset($chapter->symbol))
                          <i class="fas {{$chapter->symbol}}"></i>
                      @endif
                      {{$chapter->name}}
                  </h2>
              </div>
              @foreach ($chapter->faqs as $faq)
                <li class="cd-faq__item">
                  <a class="cd-faq__trigger" href="#0"><span>{{$faq->name}}</span></a>
                  <div class="cd-faq__content">
                    <div class="text-component">
                      <div class="row">
                        @if($faq->photo)
                          <div class="col-lg-6 order-1 order-lg-1">
                              <p>{!! $faq->description !!}</p>
                          </div>
                          <div class="col-lg-6 text-center order-2 order-lg-2">
                            <img src="{{$faq->photo ? $faq->photo->file : ''}}" alt="" class="img-fluid">
                          </div>
                        @else
                          <div class="col-lg-12 order-1 order-lg-1">
                            <p>{!! $faq->description !!}</p>
                          </div>
                        @endif
                      </div>
                    </div>
                  </div> <!-- cd-faq__content -->
                </li>
              @endforeach
            </ul> <!-- cd-faq__group -->
          @endforeach
        </div> <!-- cd-faq__items -->

        <a href="#0" class="cd-faq__close-panel text-replace">Schliessen</a>

        <div class="cd-faq__overlay" aria-hidden="true"></div>
      </div> <!-- cd-faq -->

    </main><!-- End #main -->
        <!-- ======= Footer ======= -->
      @include('includes.footer')

    {{-- @include('cookieConsent::index') --}}
  </div>
@endsection

@section('scripts')
  <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

  <script src="{{ asset('js/main.js') }}"></script>
@endsection
