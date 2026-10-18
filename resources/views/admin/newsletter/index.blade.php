@extends('layouts.admin')

@section('content')
    <div>
        <div class="container mx-auto px-4">
            <header>
                <h3 class="text-3xl font-bold dark:text-white">{{$title}}</h3>
            </header>

            <div class="my-4 grid gap-4 grid-cols-4">
                <div class="flex flex-col p-4 md:p-6 xl:p-8 space-x-0 sm:space-x-4">
                    <a class="focus:outline-hidden text-white bg-gladegreen hover:bg-gladegreen hover:text-white focus:ring-4 focus:ring-gladegreen font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gladegreen dark:hover:bg-gladegreen dark:focus:ring-gladegreen" href="{{ route('newsletter.exportBookings') }}">Adressen (Buchungen)</a>
                </div>
                <div class="flex flex-col p-4 md:p-6 xl:p-8 space-x-0 sm:space-x-4">
                    <a class="focus:outline-hidden text-white bg-gladegreen hover:bg-gladegreen hover:text-white focus:ring-4 focus:ring-gladegreen font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gladegreen dark:hover:bg-gladegreen dark:focus:ring-gladegreen"href="{{ route('newsletter.exportMembers') }}">Adressen (Genossenschaft)</a>
                </div>
                <div class="flex flex-col p-4 md:p-6 xl:p-8 space-x-0 sm:space-x-4">
                    <a class="focus:outline-hidden text-white bg-gladegreen hover:bg-gladegreen hover:text-white focus:ring-4 focus:ring-gladegreen font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gladegreen dark:hover:bg-gladegreen dark:focus:ring-gladegreen" href="{{ route('newsletter.import') }}">Adressen importieren</a>
                </div>
            </div>
            <br>
            <div id="tanStackTable" data-vue-component="newsletter-table">
                <newsletter-table />
            </div>
        </div>
    </div>
@endsection
