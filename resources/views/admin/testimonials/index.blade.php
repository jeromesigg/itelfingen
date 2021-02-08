@extends('layouts.admin')

@section('content')
<section>
    <div class="container-fluid">

        <header> 
            <h1>Bewertungen</h1>
        </header>
    
        <div class="row">
            <div class="col-sm-3">
                {!! Form::open(['method' => 'POST', 'action'=>'AdminTestimonialController@store', 'autocomplete' => 'off']) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Name:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('function', 'Funktion:') !!}
                        {!! Form::text('function', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('comment', 'Kommentar:') !!}
                        {!! Form::textarea('comment', null, ['class' => 'form-control', 'rows' => 2]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Kommentar erstellen', ['class' => 'btn btn-primary'])!!}
                    </div>
                {!! Form::close()!!}
            </div>    
            <div class="col-sm-9">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" width="15%">Name</th>
                            <th scope="col" width="15%">Funktion</th>
                            <th scope="col" width="50%">Kommentar</th>
                            <th scope="col" width="10%">Archiv-Status</th>
                            <th scope="col" width="10%">Sort-Index</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($testimonials)
                        @foreach ($testimonials as $testimonial)
                            <tr>
                                <td><a href="{{route('testimonials.edit', $testimonial->id)}}">{{$testimonial->name}}</a></td>
                                <td>{{$testimonial->function}}</td>
                                <td>{{$testimonial->comment}}</td>
                                <td>{{$testimonial->archive_status['name']}}</td>
                                <td>{{$testimonial['sort-index']}}</td>
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