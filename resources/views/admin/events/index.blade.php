@extends('layouts.admin')

@section('content')

    <header class="mb-4 flex justify-between items-center">
        <h3 class="text-3xl font-bold dark:text-white">{{$title}}</h3>
    </header>

    <a type="button" href="{{route('admin.events.create')}}" class="focus:outline-hidden text-white bg-gladegreen hover:bg-gladegreen hover:text-white focus:ring-4 focus:ring-gladegreen font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gladegreen dark:hover:bg-gladegreen dark:focus:ring-gladegreen mb-4">Buchung erstellen</a>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 bg-testcolor">
        <x-forms.form class="mb-5" :action="route('admin.homepages.comment_update', $homepage)" method="PATCH" :model="$homepage">
            <x-forms.container>
                <x-forms.text-area label="Bemerkungen:" name="event_comment" rows=5/>
            </x-forms.container>
            <x-forms.button type="submit" name="submit" class="focus:outline-hidden text-white bg-grannysmith hover:bg-grannysmith hover:text-white focus:ring-4 focus:ring-grannysmith font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-grannysmith dark:hover:bg-grannysmith dark:focus:ring-grannysmith">
                Bemerkung aktualisieren
            </x-forms.button>
        </x-forms.form>
            <x-forms.form class="mb-5" :action="route('admin.homepages.mail_text_update', $homepage)" method="PATCH" :model="$homepage">
            <x-forms.container>
                <x-forms.text-area label="Zusätzlicher Mail Text (Letzte Infos):" name="additional_mail_text" rows=5/>
            </x-forms.container>
            <x-forms.button type="submit" name="submit" class="focus:outline-hidden text-white bg-grannysmith hover:bg-grannysmith hover:text-white focus:ring-4 focus:ring-grannysmith font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-grannysmith dark:hover:bg-grannysmith dark:focus:ring-grannysmith">
                Zusätzlicher Mail Text aktualisieren
            </x-forms.button>
        </x-forms.form>
    </div>
    <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">


    <div class="container mx-auto px-4">
        <input type="hidden" value="Ab Heute" id="date_btn_value">
        <input type="hidden" value="Alle" id="status_btn_value">
        <div class="hk-reservation hk-reservation__step1 text-gray-600 dark:text-gray-300">
            <div class="hk-reservation__container container mx-auto px-4">
                <div class="hk-calendar">
                    <div class="hk-agenda">
                        <div class="d-none d-sm-block">
                            <a class="hk-agenda__prev text-orientalpink hover:underline" onclick="Agenda.prev(3); return false" href="#">
                                früheres Datum
                            </a>
                        </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            @for ($i = 0; $i <= 9; $i++)
                                <div class="col-md-4 col-sm-6 {{($i>1)?'d-none d-sm-block':''}}">
                                    <h4 id="agendaTitel{{$i}}" class="hk-agenda__title"> </h4>
                                    <table class="hk-agenda__month">
                                        <tbody id="agendaMonat{{$i}}"> </tbody>
                                    </table>
                                </div>
                            @endfor
                        </div>
                        <div class="d-none d-sm-block">
                            <a class="hk-agenda__next text-orientalpink hover:underline" onclick="Agenda.next(3); return false" href="#">
                                späteres Datum
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">

    </div>
    <div id="tanStackTable" data-vue-component="events-table"></div>
@endsection

@push('scripts')
  <!-- ======= Javascript Section ======= -->
    @include('contents.event_js')
    <script>
        // Pass contract statuses to Vue
        window.contractStatuses = @json($contract_statuses->toArray())
    </script>
  {{-- <script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3"></script>
  <script type="module">

        $(document).ready(function () {
          var table = $('#datatable').DataTable({
              responsive: true,
              processing: true,
              serverSide: true,
              pageLength: 10,
              buttons: [],
              language: {
                "url": "/lang/Datatables.json"
              },
              ajax: {
                  url: "{!! route('events.CreateDataTables') !!}",
                  data: function(d) {
                      d.date = $('#date_btn_value').val()
                      d.status = $('#status_btn_value').val()
                  }
              },
              order: [[ 0, "asc" ]],
              columns: [
                  {
                      data: {
                          _: 'start_date.display',
                          sort: 'start_date.sort'
                      },
                      name: 'start_date'
                  },
                  { data: 'number', name: 'number' },
                  { data: 'name', name: 'name' },
                  { data: 'email', name: 'email' },
                  { data: 'total_amount', name: 'total_amount' },
                  { data: 'comment', name: 'comment' },
                  { data: 'comment_intern', name: 'comment_intern' },
                  { data: 'status', name: 'status' },
              ]
          });

          // Get the container element
          var btnContainer_date = document.getElementById("date_btn");
          var btnContainer_status = document.getElementById("status_btn");

          // Get all buttons with class="btn" inside the container
          var btns_date = btnContainer_date.getElementsByTagName("button");
          var btns_status = btnContainer_status.getElementsByTagName("button");
          console.log(btns_status);

          // Loop through the buttons and add the active class to the current/clicked button
          for (var i = 0; i < btns_date.length; i++) {
              btns_date[i].addEventListener("click", function () {
                    
                  var current = btnContainer_date.getElementsByClassName("active");
                  // If there's no active class
                  if (current.length > 0) {
                    current[0].className = current[0].className.replace(" active", "");
                  }

                  // Add the active class to the current/clicked button
                  this.className += " active";
                  var active_btn = this.textContent;
                  $('#date_btn_value').val(active_btn);
              });
          }

          // Loop through the buttons and add the active class to the current/clicked button
          for (var i = 0; i < btns_status.length; i++) {
              btns_status[i].addEventListener("click", function () {
                  var current = btnContainer_status.getElementsByClassName("active");
                  // If there's no active class
                  if (current.length > 0) {
                      current[0].className = current[0].className.replace(" active", "");
                  }

                  // Add the active class to the current/clicked button
                  this.className += " active";
                  var active_btn = this.textContent;
                  $('#status_btn_value').val(active_btn);
              });
          }

          var btnsContainer = document.getElementById("filter_btns");

          // Get all buttons with class="btn" inside the container
          var btns = btnsContainer.getElementsByTagName("button");

          // Loop through the buttons and add the active class to the current/clicked button
          for (var i = 0; i < btns.length; i++) {
              btns[i].addEventListener("click", function() {
                  table.draw();
              });
          }
      });
  </script> --}}
@endpush