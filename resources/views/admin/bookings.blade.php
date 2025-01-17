@extends('layouts.admin')

@section('content')

  <!-- Updates Section -->
  <section class="mt-30px mb-30px">
    <div class="container-fluid">
        <div class="form-row">
            <div class="col-3">
                <button class="btn btn-primary" onclick="showBooking('yearly')">Jährliche Auslastung</button>
            </div>
            <div class="col-3">
                <button class="btn btn-primary" onclick="showBooking('quarter')">Quartalsweise Auslastung</button>
            </div>
            <div class="col-3">
                <button class="btn btn-primary" onclick="showBooking('monthly')">Monatliche Auslastung</button>
            </div>
            <div class="col-3">
                <a class="btn btn-primary" href="{{ route('admin.exportcsv') }}">Export File</a>
            </div>

        </div>
        <div id="yearly">
            <br>
            <h1>Jährliche Auslastung</h1>
            <div class="area-chart" style="height: 900px">
                {!! $bookingChartYear->container() !!}
            </div>
        </div>
        <div id="quarter">
            <br>
            <h1>Quartalsweise Auslastung</h1>
            <div class="area-chart" style="height: 900px">
                {!! $bookingChartQuarter->container() !!}
            </div>
        </div>
        <div id="monthly">
            <br>
            <h1>Monatliche Auslastung</h1>
            <div class="area-chart" style="height: 900px">
                {!! $bookingChartMonthly->container() !!}
            </div>
        </div>
    </div>
  </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    {{$bookingChartYear->script()}}
    {{$bookingChartQuarter->script() }}
    {{$bookingChartMonthly->script() }}

    <script>
        $( document ).ready(function(){
            document.getElementById("monthly").style.display = "none";
            document.getElementById("quarter").style.display = "none";
            document.getElementById("yearly").style.display = "block";
        });
        function showBooking(time_frame) {
            switch (time_frame) {
                case 'quarter':
                    document.getElementById("monthly").style.display = "none";
                    document.getElementById("quarter").style.display = "block";
                    document.getElementById("yearly").style.display = "none";
                    break;
                case 'monthly':
                    document.getElementById("monthly").style.display = "block";
                    document.getElementById("quarter").style.display = "none";
                    document.getElementById("yearly").style.display = "none";
                    break;
                default:
                    document.getElementById("monthly").style.display = "none";
                    document.getElementById("quarter").style.display = "none";
                    document.getElementById("yearly").style.display = "none";
                    document.getElementById("yearly").style.display = "block";
            }
        }

    </script>
@endpush

