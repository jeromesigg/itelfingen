@extends('layouts.admin')

@section('content')
<div>
    <div class="container-fluid text-gray-600 dark:text-gray-300">

        <header>
            <h3 class="text-3xl font-bold dark:text-white">Anfragen</h3>
        </header>
        <div id="tanStackTable" data-vue-component="contacts-table">
            <contacts-table />
        </div>
    </div>
</div>
@endsection
