@extends('layouts.app')

@section('content')
  <!-- ======= About Section ======= -->
  @include('contents.about')  

  <!-- ======= Why Us Section ======= -->
  @include('contents.why_us')  

  <!-- ======= Menu Section ======= -->
  @include('contents.pricelist')  

  <!-- ======= Booking Section ======= -->
  @include('contents.calendar')  

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
@endsection

@section('scripts')

  <!-- ======= Javascript Section ======= -->
  @include('contents.calendar_js')  

@endsection