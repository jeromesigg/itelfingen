@extends('layouts.admin')

@section('content')
    @include('includes.tinyeditor')
    <div>
        <div class="container-fluid">

            <header>
                <h3 class="text-3xl font-bold dark:text-white">FAQ</h3>
            </header>

            <div class="row">
                <div class="col-sm-4">
                    <x-forms.form :action="route('faqs.store')" enctype="multipart/form-data" accept-charset="UTF-8">
                        <x-forms.container>
                            <x-forms.text label="Titel:" name="name"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.text-area label="Beschreibung:" name="description" rows=15/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.file label="Photo: " name="photo_id"/>
                        </x-forms.container>
                        <x-forms.container class="col-md-6">
                            <x-forms.select label="Kapitel:" name="faq_chapter_id" required=true :collection="$faq_chapters"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.button type="submit" class="btn btn-primary">
                                FAQ erstellen
                            </x-forms.button>
                        </x-forms.container>
                    </x-forms.form>
                </div>
                <div class="col-sm-8">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" style="width:100%" id="datatable">
                            <thead>
                                <tr>
                                    <th scope="col" >Photo</th>
                                    <th scope="col">Titel</th>
                                    <th scope="col">Beschreibung</th>
                                    <th scope="col">Kapitel</th>
                                    <th scope="col" >Archiv-Status</th>
                                    <th scope="col">Sort-Index</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

    <!-- ======= Javascript Section ======= -->
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
                    url: "{!! route('faqs.CreateDataTables') !!}",
                },
                order: [[ 5, "asc" ]],
                columns: [
                    { data: 'image', name: 'image' },
                    { data: 'name', name: 'name' },
                    { data: 'description', name: 'description' },
                    { data: 'chapter', name: 'chapter' },
                    { data: 'archive_status', name: 'archive_status' },
                    { data: 'sort-index', name: 'sort-index' },
                ]
            });
        });
    </script>
@endpush