@extends('layouts.app')

@section('content')

  @include('includes.header')

  @include('includes.title')

  <div id="app">

    @include('contents.main')
        <!-- ======= Footer ======= -->
    @include('includes.footer')
    <a href="#" class="back-to-top"><i class="bx bx-up-arrow-alt"></i></a>
  </div>
@endsection

@section('scripts')
  @include('contents.event_js')
@endsection
