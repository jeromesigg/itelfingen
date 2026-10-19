@extends('layouts.admin')
@section('content')

    <header>
        <h3 class="text-3xl font-bold dark:text-white">Räume</h3>
    </header>


    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-testcolor">
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
        <div class="col-span-2">
            <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                <table class="w-full text-left rtl:text-right text-body">
                    <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                        <tr>
                            <th scope="col" class="px-6 py-3 w-3/4">Name</th>
                            <th scope="col" class="px-6 py-3 w-1/8">Archiv-Status</th>
                            <th scope="col" class="px-6 py-3 w-1/8">Sort-Index</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($rooms)
                        @foreach ($rooms as $room)
                            <tr class="bg-neutral-primary border-b border-default">
                                <td class="px-6 py-4 w-3/4"><a class="text-orientalpink" href="{{route('rooms.edit', $room)}}">{{$room->name}}</a></td>
                                <td class="px-6 py-4 w-1/8">{{$room->archive_status['name']}}</td>
                                <td class="px-6 py-4 w-1/8">{{$room['sort-index']}}</td>
                                </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
