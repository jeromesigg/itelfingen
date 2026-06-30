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
        <div id="tanStackTable" data-vue-component="contacts-table">
            <contacts-table />
        </div>
    </div>
</div>
@endsection


@push('scripts')

    <script>
        window.contractStatuses = ['Alle', 'Offen', 'Bearbeitet'];
    
    </script>
    {{-- <script type="module">
        $(function () {
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
    </script> --}}
@endpush
