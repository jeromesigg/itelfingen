@extends('layouts.admin')

@section('content')
    <header>
        <h3 class="text-3xl font-bold dark:text-white">Checkpunkte</h3>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-testcolor">
        <x-forms.form :action="route('checkpoints.store')" enctype="multipart/form-data" accept-charset="UTF-8">
            <x-forms.container>
                <x-forms.text label="Name:" name="name"/>
            </x-forms.container>
            <x-forms.container>
                <x-forms.text-area label="Beschreibung:" name="description" rows=15/>
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
        <div class="col-span-2">
            @if($rooms)
                @foreach ($rooms as $room)
                    <h6 class="text-lg font-bold dark:text-white">{{$room->name}}</h6>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-fixed w-full">
                            <thead>
                                <tr>
                                    <th scope="col" class="px-6 w-1/4">Name</th>
                                    <th scope="col" class="px-6 w-1/2">Beschreibung</th>
                                    <th scope="col" class="px-6 w-1/8">Archiv-Status</th>
                                    <th scope="col" class="px-6 w-1/8">Sort-Index</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($room->checkpoints)
                                    @foreach ($room->checkpoints as $checkpoint)
                                        <tr>
                                            <td class="px-6 w-1/4"><a class="text-orientalpink" href="{{route('checkpoints.edit', $checkpoint)}}">{{$checkpoint->name}}</a></td>
                                            <td class="px-6 w-1/2">{{$checkpoint->description}}</td>
                                            <td class="px-6 w-1/8">{{$checkpoint->archive_status['name']}}</td>
                                            <td class="px-6 w-1/8">{{$checkpoint['sort-index']}}</td>
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
@endsection

@push('scripts')
    <script type="module">
        const editor = Jodit.make('#description', {
            buttons: ['bold', 'italic', 'underline', '|', 'ul', 'ol'],
            height: 400,
            allowResizeY: true
        });
    </script>
@endpush
