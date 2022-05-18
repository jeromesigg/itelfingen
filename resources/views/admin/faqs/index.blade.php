@extends('layouts.admin')

@section('content')
    @include('includes.tinyeditor')
    <section>
        <div class="container-fluid">

            <header>
                <h3>FAQ</h3>
            </header>

            <div class="row">
                <div class="col-sm-4">
                    {!! Form::open(['method' => 'POST', 'action'=>'AdminFaqController@store', 'files' => true]) !!}
                        <div class="form-group">
                            {!! Form::label('name', 'Titel:') !!}
                            {!! Form::text('name', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('description', 'Beschreibung:') !!}
                            {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 15]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('photo_id', 'Photo:') !!}
                            {!! Form::file('photo_id', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-6 form-group">
                            {!! Form::label('faq_chapter_id', 'Kapitel:') !!}
                            {!! Form::select('faq_chapter_id', $faq_chapters, null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('FAQ erstellen', ['class' => 'btn btn-primary'])!!}
                        </div>
                    {!! Form::close()!!}
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
                {{--                                        <td><img height="50" src="{{$faq->photo ? $faq->photo->file : 'http://placehold.it/50x50'}}" alt=""></td>--}}
                {{--                                        <td><a href="{{route('faqs.edit', $faq->id)}}">{{$faq->name}}</a></td>--}}

            });
        });
    </script>
@endsection
