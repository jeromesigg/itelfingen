@extends('layouts.admin')
@section('content')
<div>
    <div class="container-fluid">

        <header>
            <h3 class="text-3xl font-bold dark:text-white">Räume</h3>
        </header>

        <div class="row">
            <div class="col-sm-3">
                <x-forms.form :action="route('rooms.store')" accept-charset="UTF-8">
                    <x-forms.container>
                        <x-forms.text label="Raum:" name="name" required=true/>
                    </x-forms.container>
                    <x-forms.container>
                        <x-forms.button type="submit" class="btn btn-primary">
                            Raum erstellen
                        </x-forms.button>
                    </x-forms.container>
                </x-forms.form>
            </div>
            <div class="col-sm-9">
                <table class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Archiv-Status</th>
                            <th scope="col">Sort-Index</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($rooms)
                        @foreach ($rooms as $room)
                            <tr>
                                <td><a class="text-orientalpink" href="{{route('rooms.edit', $room)}}">{{$room->name}}</a></td>
                                <td>{{$room->archive_status['name']}}</td>
                                <td>{{$room['sort-index']}}</td>
                                </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection