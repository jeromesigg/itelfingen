@extends('layouts.admin')

@section('content')
    @include('includes.tinyeditor')
    <section>
        <div class="container-fluid">

            <header>
                <h3>FAQ</h3>
            </header>

            <div class="row">
                <div class="col-sm-6">
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
                <div class="col-sm-6">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col" width="10%">Photo</th>
                                <th scope="col" width="10%">Titel</th>
                                <th scope="col" width="55%">Beschreibung</th>
                                <th scope="col" width="55%">Kapitel</th>
                                <th scope="col" width="5%">Archiv-Status</th>
                                <th scope="col" width="5%">Sort-Index</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($faqs)
                            @foreach ($faqs as $faq)
                                <tr>
                                    <td><img height="50" src="{{$faq->photo ? $faq->photo->file : 'http://placehold.it/50x50'}}" alt=""></td>
                                    <td><a href="{{route('faqs.edit', $faq->id)}}">{{$faq->name}}</a></td>
                                    <td>{{$faq->description}}</td>
                                    <td>{{$faq->faq_chapter['name']}}</td>
                                    <td>{{$faq->archive_status['name']}}</td>
                                    <td>{{$faq['sort-index']}}</td>
                                    </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
