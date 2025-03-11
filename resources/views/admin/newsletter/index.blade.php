@extends('layouts.admin')

@section('content')
    <div>
        <div class="container-fluid">
            <header>
                <h3 class="text-3xl font-bold dark:text-white">{{$title}}</h3>
            </header>

            <div class="form-row">
                <div class="col-3">
                    <a class="btn btn-primary" href="{{ route('newsletter.exportBookings') }}">Adressen (Buchungen)</a>
                </div>
                <div class="col-3">
                    <a class="btn btn-primary" href="{{ route('newsletter.exportMembers') }}">Adressen (Genossenschaft)</a>
                </div>
                <div class="col-3">
                    <a class="btn btn-primary" href="{{ route('newsletter.import') }}">Adressen importieren</a>
                </div>
                <div class="col-3">
                </div>
    
            </div>
            <br>
            <div class="table-responsive">
                <table class="table table-striped table-bordered" style="width:100%" id="datatable">
                    <thead>
                        <tr>
                            <th scope="col" >Name</th>
                            <th scope="col">Vorname</th>
                            <th scope="col">E-Mail</th>
                            <th scope="col">Buchungen</th>
                            <th scope="col">Genossenschaft</th>
                        </tr>
                    </thead>
                </table>
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
                pageLength: 25,
                buttons: [],
                language: {
                    "url": "/lang/Datatables.json"
                },
                ajax: {
                    url: "{!! route('newsletter.CreateDataTables') !!}",
                },
                order: [[ 0, "asc" ]],
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'firstname', name: 'firstname' },
                    { data: 'email', name: 'email' },
                    { data: 'bookings', name: 'bookings' },
                    { data: 'members', name: 'members' },
                ]

            });
        });
    </script>
@endpush