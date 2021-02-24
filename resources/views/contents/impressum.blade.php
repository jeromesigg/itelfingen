@extends('layouts.app')

@section('content')

  @include('includes.header')
  <div id="app">
        
    <main id="main">
      <section class="breadcrumbs">
        <div class="container">
          <div class="d-flex justify-content-between align-items-center">
            <h2>Impressum</h2>
            <ol>
              <li><a href="{{route('home')}}">Home</a></li>
              <li>Impressum</li>
            </ol>
          </div>
        </div>
      </section>
  
      <section class="inner-page contact">
        <div class="container">
          <div class="row mt-5">


          <div class="col-lg-4">
              <div class="info">
                <div class="address">
                  <i class="icofont-google-map"></i>
                  <h4>Hausadresse:</h4>
                  <p>{!! nl2br($homepage->address) !!}</p>
                </div>
      
                <div class="email">
                  <i class="icofont-envelope"></i>
                  <h4>Email:</h4>
                  <p>{{$homepage->mail}}</p>
                </div>
      
                <div class="phone">
                  <i class="icofont-phone"></i>
                  <h4>Tel. P (abends):</h4>
                  <p>{{$homepage->phone}}</p>
                </div>
      
              </div>
            </div>
              <div class="col-lg-8 mt-5 mt-lg-0">
                <iframe style="border:0; width: 100%; height: 350px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d605.6247635940863!2d8.473077610557123!3d47.11300787321241!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47855566fbd6fd7f%3A0xf9c4a22f086c0f22!2sItelfingen%203%2C%206344%20Meierskappel!5e1!3m2!1sde!2sch!4v1613503423612!5m2!1sde!2sch" frameborder="0" allowfullscreen></iframe>
            </div>
          </div>
        </div>
      </section>
  
    </main><!-- End #main --> 
        <!-- ======= Footer ======= -->
    <footer id="footer">

      <div class="container">
        <div class="credits">
          <a href="{{route('impressum')}}">Impressum</a>
        </div>
      </div>
    </footer><!-- End Footer -->
    <div id="preloader"></div>
    <a href="#" class="back-to-top"><i class="bx bx-up-arrow-alt"></i></a>
    
    {{-- @include('cookieConsent::index') --}}
  </div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js" integrity="sha512-0QbL0ph8Tc8g5bLhfVzSqxe9GERORsKhIn1IrpxDAgUsbBGz/V7iSav2zzW325XGd1OMLdL4UiqRJj702IeqnQ==" crossorigin="anonymous"></script>

<script src="{{ asset('js/main.js') }}"></script>
@endsection