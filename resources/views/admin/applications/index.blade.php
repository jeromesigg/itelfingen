@extends('layouts.admin')

@section('content')
<section>
    <div class="container-fluid">

        <header>
            <h3>Bewerbungen</h3>
        </header>

        <div class="row">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" style="width:100%" id="datatable">
                    <thead>
                        <tr>
                            <th scope="col">Datum</th>
                            <th scope="col">Name</th>
                            <th scope="col">Vorname</th>
                            <th scope="col">E-Mail</th>
                            <th scope="col">Organisation</th>
                            <th scope="col">Wieso</th>
                            <th scope="col">Bemerkung</th>
                            <th scope="col">Gesendet</th>
                            <th scope="col">Abgelehnt</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')

    <!-- ======= Javascript Section ======= -->
    <script>
        $(function () {
            var table = $('#datatable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                pageLength: 25,
                language: {
                    "url": "/lang/Datatables.json"
                },
                ajax: {
                    url: "{!! route('applications.CreateDataTables') !!}",
                },
                order: [[ 0, "asc" ]],
                columns: [
                    {
                        data: {
                            _: 'created_at.display',
                            sort: 'created_at'
                        },
                        name: 'created_at.sort'
                    },
                    { data: 'name', name: 'name' },
                    { data: 'firstname', name: 'firstname' },
                    { data: 'email', name: 'email' },
                    { data: 'organisation', name: 'organisation' },
                    { data: 'why', name: 'why' },
                    { data: 'comment', name: 'comment' },
                    { data: 'invoice_send', name: 'invoice_send' },
                    { data: 'refuse', name: 'refuse' },
                    { data: 'Actions', name: 'Actions', orderable:false,serachable:false,sClass:'text-center'},
                ]
            });
        });
    </script>
@endsection

