@extends('layouts.admin')

@section('content')
    @include('includes.tinyeditor')
    <div>
        <div class="container-fluid">

            <header>
                <h3 class="text-3xl font-bold dark:text-white">Checkpunkte</h3>
            </header>

            <div class="row">
                <div class="col-sm-4">
                    <x-forms.form :action="route('checkpoints.store')" enctype="multipart/form-data" accept-charset="UTF-8">
                        <x-forms.container>
                            <x-forms.text label="Name:" name="name"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.textarea label="Beschreibung:" name="description" rows=15/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.select label="Raum:" name="room_id" required=true :collection="$rooms_select"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.button type="submit" class="btn btn-primary">
                                Checkpunkt erstellen
                            </x-forms.button>
                        </x-forms.container>
                    </x-forms.form>
                </div>
                <div class="col-sm-8">
                    @if($rooms)
                        @foreach ($rooms as $room)
                            <h6 class="text-lg font-bold dark:text-white">{{$room->name}}</h6>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" style="width:100%" id="datatable">
    
                                    <tbody>
                                        @if($room->checkpoints)
                                            @foreach ($room->checkpoints as $checkpoint)
                                                <tr>
                                                    <td><a class="text-orientalpink" href="{{route('checkpoints.edit', $checkpoint)}}">{{$checkpoint->name}}</a></td>
                                                    <td>{{$checkpoint->description}}</td>
                                                    <td>{{$checkpoint->archive_status['name']}}</td>
                                                    <td>{{$checkpoint['sort-index']}}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection