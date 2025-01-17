@extends('layouts.admin')

@section('content')
<section>
    <div class="container-fluid">

        <header>
            <h3>{{$title}}</h3>
        </header>

        <div id="filter_btns">
            <div id="date_btn">
                <div class="row" style="width: 20%">
                    <div class="col-md-6">
                         <button class="btn btn-primary">Alle</button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-primary active">Ab Heute</button>
                    </div>
                </div>
            </div>
            <div id="status_btn">
                <br>
                <div class="row">
                    <div class="col-md-1">
                        <button class="btn btn-secondary active">Alle</button>
                    </div>
                    @foreach ($contract_statuses as $contract_status)
                        <div class="col-md-1">
                            <button class="btn btn-secondary">{{$contract_status}}</button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <input type="hidden" value="Ab Heute" id="date_btn_value">
        <input type="hidden" value="Alle" id="status_btn_value">
        <br>
        <div class="hk-reservation hk-reservation__step1">
            <div class="hk-reservation__container container-fluid">
                <div class="row">
                    <div class="col-lg-12 hk-calendar">
                        <div class="hk-agenda">
                            <div class="d-none d-sm-block">
                                <a class="hk-agenda__prev" onclick="Agenda.prev(3); return false" href="#">
                                    früheres Datum
                                </a>
                            </div>
                            <div class="row">
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
                                <a class="hk-agenda__next" onclick="Agenda.next(3); return false" href="#">
                                    späteres Datum
                                </a>
                            </div>
                            <!-- mobile buttons -->
                            <div class="row d-flex d-sm-none">
                                <div class="col-6">
                                    <a class="hk-agenda__prev hk-agenda__prev--mobile" onclick="Agenda.prev(1); return false" href="#">
                                        früheres Datum
                                    </a>
                                </div>
                                <div class="col-6 text-right">
                                    <a class="hk-agenda__next hk-agenda__next--mobile" onclick="Agenda.next(1); return false" href="#">
                                        späteres Datum
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <table class="table table-striped table-bordered" style="width:100%" id="datatable">
            <thead>
                <tr>
                    <th scope="col" width="8%">Datum</th>
                    <th scope="col" width="7%">Nr.</th>
                    <th scope="col" width="10%">Name</th>
                    <th scope="col" width="15%">E-Mail</th>
                    <th scope="col" width="5%">Total</th>
                    <th scope="col" width="25%">Bemerkung</th>
                    <th scope="col" width="15%">Bemerkung Intern</th>
                    <th scope="col" width="15%">Status</th>
                </tr>
            </thead>
        </table>

    </div>
</section>
@endsection

@push('scripts')

  <!-- ======= Javascript Section ======= -->
  @include('contents.event_js')
  <script>
      $(function () {
          var table = $('#datatable').DataTable({
              responsive: true,
              processing: true,
              serverSide: true,
              pageLength: 10,
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
          var btns_date = btnContainer_date.getElementsByClassName("btn");
          var btns_status = btnContainer_status.getElementsByClassName("btn");

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
          var btns = btnsContainer.getElementsByClassName("btn");

          // Loop through the buttons and add the active class to the current/clicked button
          for (var i = 0; i < btns.length; i++) {
              btns[i].addEventListener("click", function() {
                  table.draw();
              });
          }
      });
  </script>
@endpush