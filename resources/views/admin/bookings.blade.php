@extends('layouts.admin')

@section('content')

  <!-- Updates Section -->
  <section class="mt-30px mb-30px">
    <div class="container-fluid">
        <h1>Jährliche Auslastung</h1>
        <div class="area-chart">
            {!! $bookingChartYear->container() !!}
        </div>
        <br>
        <h1>Quartalsweise Auslastung</h1>
        <div class="area-chart">
            {!! $bookingChartQuarter->container() !!}
        </div>
    </div>
  </section>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    {{isset($bookingChartYear) ? $bookingChartYear->script() : ''}}
    {{isset($bookingChartQuarter) ? $bookingChartQuarter->script() : ''}}
@endsection

