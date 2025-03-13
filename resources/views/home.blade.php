@extends('layouts.app')

@section('content')

  @include('includes.header')

  @include('includes.title')

  <div id="app">

    @include('contents.main')
        <!-- ======= Footer ======= -->
    @include('includes.footer')
  </div>
@endsection

@push('scripts')
  @include('contents.event_js')
@endpush
