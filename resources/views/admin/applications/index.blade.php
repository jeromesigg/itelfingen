@extends('layouts.admin')

@section('content')

    <header>
        <h3 class="text-3xl font-bold dark:text-white">Bewerbungen</h3>
    </header>

    <div id="tanStackTable" data-vue-component="applications-table">
        <applications-table />
    </div>
@endsection