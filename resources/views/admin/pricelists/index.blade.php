@extends('layouts.admin')

@section('content')
<section>
    <div class="container-fluid">

        <header> 
            <h1>Preisliste</h1>
        </header>
    
        <div class="row">
            <div class="col-sm-3">
                {!! Form::open(['method' => 'POST', 'action'=>'AdminPricelistController@store']) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Name:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('detail', 'Detail:') !!}
                        {!! Form::text('detail', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('price', 'Preis:') !!}
                        {!! Form::text('price', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Preis erstellen', ['class' => 'btn btn-primary'])!!}
                    </div>
                {!! Form::close()!!}
            </div>    
            <div class="col-sm-9">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" width="30%">Name</th>
                            <th scope="col" width="40%">Beschreibung</th>
                            <th scope="col" width="10%">Preis</th>
                            <th scope="col" width="10%">Archiv-Status</th>
                            <th scope="col" width="10%">Sort-Index</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($pricelists)
                        @foreach ($pricelists as $pricelist)
                            <tr>
                                <td><a href="{{route('pricelists.edit', $pricelist->id)}}">{{$pricelist->name}}</a></td>
                                <td>{{$pricelist->detail}}</td>
                                <td>{{$pricelist->price}}</td>
                                <td>{{$pricelist->archive_status['name']}}</td>
                                <td>{{$pricelist['sort-index']}}</td>
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