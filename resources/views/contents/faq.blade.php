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
  
      {{-- <section class="inner-page contact">
        <div class="container">
          @foreach ($faq_chapters as $chapter)
            <div id="recent-activities-wrapper-{{$chapter['sort-index']}}" class="card updates activities">
              <a data-toggle="collapse" data-parent="#recent-activities-wrapper-{{$chapter['sort-index']}}" href="#activities-box-{{$chapter['sort-index']}}" aria-expanded="true" aria-controls="activities-box">
                <div id="activites-header" class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h5 display">
                      {{$chapter['name']}}
                    </h2>
                    <i class="fa fa-angle-down"></i>
                </div> 
              </a>
              <div id="activities-box-{{$chapter['sort-index']}}" role="tabpanel" class="collapse">
                @foreach ($chapter->faqs as $faq)  
                    <table class="table" >
                        <tbody>
                          <tr class="">
                              <td width="25%">{{$faq['name']}}<td>
                              <td width="75%">{{$faq['description']}}</td>
                          </tr>
                        </tbody>
                    </table>
                @endforeach       
              </div>
            </div> 
          @endforeach       
        </div>
      </section> --}}
      <div class="cd-faq js-cd-faq container max-width-md margin-top-lg margin-bottom-lg">
        <ul class="cd-faq__categories">
          @foreach ($faq_chapters as $chapter)
            <li><a class="cd-faq__category cd-faq__category truncate" href="#{{$chapter->name}}">{{$chapter->name}}</a></li>
          @endforeach
        </ul> <!-- cd-faq__categories -->
      
        <div class="cd-faq__items">
          @foreach ($faq_chapters as $chapter)
            <ul id="{{$chapter->name}}" class="cd-faq__group">
              <li class="cd-faq__title"><h2>{{$chapter->name}}</h2></li>
              @foreach ($chapter->faqs as $faq)  
                <li class="cd-faq__item">
                  <a class="cd-faq__trigger" href="#0"><span>{{$faq->name}}</span></a>
                  <div class="cd-faq__content">
                    <div class="text-component">
                      <div class="row">
                        @if($faq->photo)
                          <div class="col-lg-6 order-1 order-lg-1">
                            <p>{!! nl2br($faq->description) !!}</p>
                          </div>
                          <div class="col-lg-6 text-center order-2 order-lg-2">
                            <img src="{{$faq->photo ? $faq->photo->file : ''}}" alt="" class="img-fluid">
                          </div>                        
                        @else
                          <div class="col-lg-12 order-1 order-lg-1">
                            <p>{!! nl2br($faq->description) !!}</p>
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
    <footer id="footer">

      <div class="container">
        <div class="credits">
          <a href="{{route('impressum')}}">Impressum</a>
        </div>
      </div>
    </footer><!-- End Footer -->
    
    {{-- @include('cookieConsent::index') --}}
  </div>
@endsection

@section('scripts')
  <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
   
  <script src="{{ asset('js/main.js') }}"></script>
@endsection