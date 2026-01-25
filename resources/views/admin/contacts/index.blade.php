@extends('layouts.admin')

@section('content')
<div>
    <div class="container-fluid text-gray-600 dark:text-gray-300">

        <header>
            <h3 class="text-3xl font-bold dark:text-white">Anfragen</h3>
        </header>
        <div id="filter_btns">
            <div id="done_btn">
                <div class="row" style="width: 20%">
                    <div class="col-md-6">
                        <button class="btn btn-primary">Alle</button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-primary active">Offen</button>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <input type="hidden" value="Offen" id="done_btn_value">
        <div class="row">
            <div class="table-responsive">
                <table class="table table-striped table-bordered text-gray-600 dark:text-gray-300" style="width:100%" id="datatable">
                    <thead>
                        <tr>
                            <th scope="col" width="10%">Erstellt</th>
                            <th scope="col" width="10%">Von</th>
                            <th scope="col" width="15%">E-Mail</th>
                            <th scope="col" width="15%">Betreff</th>
                            <th scope="col" width="30%">Text</th>
                            <th scope="col" width="5%">Bearbeitet</th>
                            <th scope="col" width="10%">von</th>
                            <th scope="col" width="5%">Aktion</th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection


@push('scripts')

    <script type="module">
        $(function () {
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
                    url: "{!! route('contacts.CreateDataTables') !!}",
                    data: function(d) {
                        d.done = $('#done_btn_value').val()
                    }
                },
                order: [[ 0, "DESC" ]],
                columns: [
                    {
                        data: {
                            _: 'created_at.display',
                            sort: 'created_at.sort'
                        },
                        name: 'created_at'
                    },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'subject', name: 'subject' },
                    { data: 'content', name: 'content' },
                    { data: 'done', name: 'done' },
                    { data: 'user', name: 'user' },
                    { data: 'Actions', name: 'Actions', orderable:false,serachable:false,sClass:'text-center'},
                ]
            });

            // Get the container element
            var btnContainer = document.getElementById("done_btn");

            // Get all buttons with class="btn" inside the container
            var btns = btnContainer.getElementsByClassName("btn");

            // Loop through the buttons and add the active class to the current/clicked button
            for (var i = 0; i < btns.length; i++) {
                btns[i].addEventListener("click", function () {
                    var current = btnContainer.getElementsByClassName("active");
                    // If there's no active class
                    if (current.length > 0) {
                        current[0].className = current[0].className.replace(" active", "");
                    }

                    // Add the active class to the current/clicked button
                    this.className += " active";
                    var active_btn = this.textContent;
                    $('#done_btn_value').val(active_btn);
                    table.draw();
                });
            }

            var btnsContainer = document.getElementById("filter_btns");

            // Get all buttons with class="btn" inside the container
            var btns = btnsContainer.getElementsByClassName("btn");

            // Loop through the buttons and add the active class to the current/clicked button
            for (var i = 0; i < btns.length; i++) {
                btns[i].addEventListener("click", function() {
                });
            }
        });
    </script>
@endpush
