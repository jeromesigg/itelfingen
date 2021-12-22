@extends('layouts.admin')

@section('content')
<section>
    <div class="container-fluid">

        <header> 
            <h3>FAQ Kapitel</h3>
        </header>
    
        <div class="row">
            <div class="col-sm-3">
                {!! Form::open(['method' => 'POST', 'action'=>'AdminFaqChapterController@store']) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Titel:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('FAQ Kapitel erstellen', ['class' => 'btn btn-primary'])!!}
                    </div>
                {!! Form::close()!!}
            </div>    
            <div class="col-sm-9">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" width="10%">Titel</th>
                            <th scope="col" width="5%">Archiv-Status</th>
                            <th scope="col" width="5%">Sort-Index</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($faq_chapters)
                        @foreach ($faq_chapters as $faq_chapter)
                            <tr>
                                <td><a href="{{route('faq_chapters.edit', $faq_chapter->id)}}">{{$faq_chapter->name}}</a></td>
                                <td>{{$faq_chapter->archive_status['name']}}</td>
                                <td>{{$faq_chapter['sort-index']}}</td>
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