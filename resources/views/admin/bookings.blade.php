@extends('layouts.admin')

@section('content')

  <!-- Updates Section -->
  <div class="mt-3.5">
    <div class="container-fluid">
        <div class="flex justify-between mb-5">
          <div>
            <button id="dropdownDefaultButton"
              data-dropdown-toggle="lastDaysdropdown"
              data-dropdown-placement="bottom" type="button" class="px-3 py-2 inline-flex items-center text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Jährliche Auslastung <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
          </svg></button>
          <div id="lastDaysdropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-60 dark:bg-gray-700">
                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
                  <li>
                    <a href="#" onclick="showBooking('yearly')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Jährliche Auslastung</a>
                  </li>
                  <li>
                    <a href="#" onclick="showBooking('quarter')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Quartalsweise Auslastung</a>
                  </li>
                  <li>
                    <a href="#" onclick="showBooking('monthly')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Monatliche Auslastung</a>
                  </li>
                  <li>
                    <a href="#" onclick="showBooking('heatmap')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Heatmap der Auslastung</a>
                  </li>
                  <li>
                    <a href="{{ route('admin.exportcsv') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Export File</a>
                  </li>
                </ul>
            </div>
          </div>
        </div>
        <div class="min-w-full min-h-80vh w-full my-16 bg-white rounded-lg shadow-sm dark:bg-gray-800 ">
          <div  id="data-series-chart"></div>
        </div>
    </div>
  </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.46.0/dist/apexcharts.min.js"></script>

    <script  type="module">
        var booking = @json($bookingChartYear);
        var chart_type = 'yearly';
        function showBooking(time_frame) {
          
          if (chart_type === "heatmap") {
            chart.destroy();
            chart = new ApexCharts(document.getElementById("data-series-chart"), options);
            chart.render();
          }
          chart_type = time_frame;
          switch (time_frame) {
              case 'quarter':
                booking = @json($bookingChartQuarter);
                break;
              case 'monthly':
                booking = @json($bookingChartMonthly);
                break;
              case 'heatmap':
                booking = @json($bookingHeatMap);
                break;
              default:
                booking = @json($bookingChartYear);
          }
          if (chart_type === "heatmap") {
            chart.destroy();
            const newOptions = {
              series: booking.series,
              chart: {
                  type: "heatmap", // Hier den Chart-Typ wechseln
                  height: "100%",
                  maxWidth: "100%",
                  fontFamily: "Inter, sans-serif",
                  background: 'var(--chart-bg)',
                  foreColor: 'var(--chart-text)',
              },
              plotOptions: {
                heatmap: {
                  shadeIntensity: 0.6,
                  colorScale: {
                    ranges: [
                      { from: 0, to: 0, color: '#f1f5f9', name: '0' , foreColor: 'var(--chart-text)'},
                      { from: 1, to: 5, color: '#bae6fd' },
                      { from: 6, to: 15, color: '#7dd3fc' },
                      { from: 16, to: 30, color: '#0284c7' }
                    ]
                  }
                }
              },

              dataLabels: {
                enabled: false,
              },

              yaxis: {
                reversed: true,
                labels: {
                  style: { colors: 'var(--chart-text)'},
                  offsetX: -15
                },
                axisBorder: {
                  show: true,
                  color: 'var(--chart-text)',
                  offsetX: 0
                },
                axisTicks: {
                  show: true,
                  color: 'var(--chart-text)',
                  offsetX: 0
                },
              },
              xaxis: {
                type: 'category',
                title: {
                  text: 'Kalenderwochen',
                  style: { color: 'var(--chart-text)'}
                },
                labels: {
                  style: { colors: 'var(--chart-text)'}
                },
                axisBorder: {
                  color: 'var(--chart-text)'
                },
                axisTicks: {
                  color: 'var(--chart-text)'
                },
              },
              tooltip: {
                y: {
                  formatter: val => `${val} Personen`
                }
              },
              theme: {
                mode: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light',
              },
              // Weitere Heatmap-spezifische Optionen...
            };
            
            chart = new ApexCharts(document.getElementById("data-series-chart"), newOptions);
            chart.render();
          } else {
            chart.updateOptions({
              series: [
                {
                  name: "Anzahl Tage",
                  data: booking[1],
                  color: "#1A56DB",
                },
                {
                  name: "Anzahl Übernachtungen",
                  data: booking[2],
                  color: "#7E3BF2",
                },
              ],
              xaxis: {
                categories: booking[0],
              },
            });
          }   
        }     
        window.showBooking = showBooking;
        const options = {
// add data series via arrays, learn more here: https://apexcharts.com/docs/series/
          series: [
            {
              name: "Anzahl Tage",
              data: booking[1],
              color: "#1A56DB",
            },
            {
              name: "Anzahl Übernachtungen",
              data: booking[2],
              color: "#7E3BF2",
            },
          ],
          chart: {
            height: "100%",
            maxWidth: "100%",
            type: "line",
            fontFamily: "Inter, sans-serif",
            background: 'var(--chart-bg)',
            dropShadow: {
              enabled: false,
            },
            toolbar: {
              show: false,
            },
          },
          theme: {
            mode: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light',
          },
          tooltip: {
            enabled: true,
            x: {
              show: true,
            },
          },
          labels: { style: { colors: 'var(--chart-text)' } }, // Label theming
          legend: {
            show: false
          },
          dataLabels: {
            enabled: false,
          },
          markers: {
              size: 5,
          },  
          stroke: {
            curve: 'smooth',
            width: 4,
          },
          grid: {
            show: false,
            strokeDashArray: 4,
            padding: {
              left: 2,
              right: 2,
              top: 0
            },
             style: { colors: 'var(--chart-text)'}
          },
          xaxis: {
            categories: booking[0],
            labels: {
              show: true,
              style: { colors: 'var(--chart-text)'}
            },
            axisBorder: {
              show: true,
              color: 'var(--chart-text)',
            },
            axisTicks: {
              show: true,
              color: 'var(--chart-text)',
            },

          },
          yaxis: {
            labels: {
              style: { colors: 'var(--chart-text)'},
              offsetX: -15
            },
            axisBorder: {
              show: true,
              color: 'var(--chart-text)',
              offsetX: 0
            },
            axisTicks: {
              show: true,
              color: 'var(--chart-text)',
              offsetX: 0
            },
          }
        }
        if (document.getElementById("data-series-chart") && typeof ApexCharts !== 'undefined') {
          var chart = new ApexCharts(document.getElementById("data-series-chart"), options);
          chart.render();
        }
    </script>
@endpush

