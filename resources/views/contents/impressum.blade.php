@extends('layouts.app')

@section('content')

  @include('includes.header')
  <div id="app">

    <main id="main">
      <section class="breadcrumbs">
        <div class="px-4 mx-auto max-w-screen-2xl lg:px-6">
          <div class="d-flex justify-content-between align-items-center">
            <h2 class="text-4xl dark:text-white">Impressum</h2>
          </div>
        </div>
      </section>
      <section class="inner-page contact">
        <div class="px-4 mx-auto max-w-screen-2xl lg:px-6">
          <div class="grid lg:grid-cols-3 gap-4 mt-5">
            <div>
              <div class="info">
                <div class="address">
                  <i>
                    <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M12 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M17.8 13.938h-.011a7 7 0 1 0-11.464.144h-.016l.14.171c.1.127.2.251.3.371L12 21l5.13-6.248c.194-.209.374-.429.54-.659l.13-.155Z"/>
                    </svg>
                  </i>
                  
                  <h4>Hausadresse</h4>
                  <p>{!! nl2br($homepage->address) !!}</p>
                </div>

                <div class="address">
                  <i>
                    <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M11 16v-5.5A3.5 3.5 0 0 0 7.5 7m3.5 9H4v-5.5A3.5 3.5 0 0 1 7.5 7m3.5 9v4M7.5 7H14m0 0V4h2.5M14 7v3m-3.5 6H20v-6a3 3 0 0 0-3-3m-2 9v4m-8-6.5h1"/>
                    </svg>
                  </i>
                  <h4>Postadresse</h4>
                  <p>{!! nl2br($homepage->postaddress) !!}</p>
                </div>

                <div class="email">
                  <i>
                    <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M21 8v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8m18 0-8.029-4.46a2 2 0 0 0-1.942 0L3 8m18 0-9 6.5L3 8"/>
                    </svg>
                  </i>
                  <h4>Email</h4>
                  <p>{{$homepage->mail}}</p>
                </div>
{{--                <div class="phone">--}}
{{--                  <i class="icofont-phone"></i>--}}
{{--                  <h4>Tel. P (abends):</h4>--}}
{{--                  <p>{{$homepage->phone}}</p>--}}
{{--                </div>--}}
              </div>
            </div>
            <div class="lg:col-span-2 mt-5 mt-lg-0">
              <iframe style="border:0; width: 100%; height: 350px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d605.6247635940863!2d8.473077610557123!3d47.11300787321241!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47855566fbd6fd7f%3A0xf9c4a22f086c0f22!2sItelfingen%203%2C%206344%20Meierskappel!5e1!3m2!1sde!2sch!4v1613503423612!5m2!1sde!2sch" frameborder="0" allowfullscreen></iframe>
            </div>
          </div>
        </div>
      </section>

    </main><!-- End #main -->
        <!-- ======= Footer ======= -->
      @include('includes.footer')
  </div>
@endsection