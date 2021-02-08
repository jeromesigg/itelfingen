@extends('layouts.app')

@section('content')

@include('includes.header')

@include('includes.title') 

  <div id="app">
        
      <main class="py-4">
        <!-- ======= About Section ======= -->
        @include('contents.about')  

        <!-- ======= Why Us Section ======= -->
        @include('contents.why_us')  

        <!-- ======= Menu Section ======= -->
        @include('contents.pricelist')  

        <!-- ======= Booking Section ======= -->
        @include('contents.agenda')  

        <!-- ======= Gallery Section ======= -->
        @include('contents.gallery') 

        <!-- ======= Events Section ======= -->
        @include('contents.locations')  

        <!-- ======= Specials Section ======= -->
        @include('contents.history') 

        <!-- ======= Testimonials Section ======= -->
        @include('contents.testimonials')
      
        <!-- ======= About us Section ======= -->
        @include('contents.about_us')  

        <!-- ======= Contact Section ======= -->
        @include('contents.contacts')  
      </main>
      <div id="preloader"></div>
      <a href="#" class="back-to-top"><i class="bx bx-up-arrow-alt"></i></a>
    </div>
@endsection

@section('scripts')

  <!-- ======= Javascript Section ======= -->
  @include('contents.agenda_js')

@endsection