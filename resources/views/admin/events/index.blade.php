@extends('layouts.admin')

@section('content')

    <header class="mb-4 flex justify-between items-center">
        <h3 class="text-3xl font-bold dark:text-white">{{$title}}</h3>
    </header>

    @if (Auth::user()->canManageBookings())
    <a type="button" href="{{route('admin.events.create')}}" class="focus:outline-hidden text-white bg-gladegreen hover:bg-gladegreen hover:text-white focus:ring-4 focus:ring-gladegreen font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gladegreen dark:hover:bg-gladegreen dark:focus:ring-gladegreen mb-4">Buchung erstellen</a>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 bg-testcolor">
        <x-forms.form class="mb-5" :action="route('admin.homepages.comment_update', $homepage)" method="PATCH" :model="$homepage">
            <x-forms.container>
                <x-forms.text-area label="Bemerkungen:" name="event_comment" rows=5/>
            </x-forms.container>
            <x-forms.button type="submit" name="submit" class="focus:outline-hidden text-white bg-grannysmith hover:bg-grannysmith hover:text-white focus:ring-4 focus:ring-grannysmith font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-grannysmith dark:hover:bg-grannysmith dark:focus:ring-grannysmith">
                Bemerkung aktualisieren
            </x-forms.button>
        </x-forms.form>
            <x-forms.form class="mb-5" :action="route('admin.homepages.mail_text_update', $homepage)" method="PATCH" :model="$homepage">
            <x-forms.container>
                <x-forms.text-area label="Zusätzlicher Mail Text (Letzte Infos):" name="additional_mail_text" rows=5/>
            </x-forms.container>
            <x-forms.button type="submit" name="submit" class="focus:outline-hidden text-white bg-grannysmith hover:bg-grannysmith hover:text-white focus:ring-4 focus:ring-grannysmith font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-grannysmith dark:hover:bg-grannysmith dark:focus:ring-grannysmith">
                Zusätzlicher Mail Text aktualisieren
            </x-forms.button>
        </x-forms.form>
    </div>
    @endif
    <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">


    <div class="container mx-auto px-4">
        <input type="hidden" value="Ab Heute" id="date_btn_value">
        <input type="hidden" value="Alle" id="status_btn_value">
        <div class="hk-reservation hk-reservation__step1 text-gray-600 dark:text-gray-300">
            <div class="hk-reservation__container container mx-auto px-4">
                <div class="hk-calendar" data-vue-component="booking-calendar" data-props="{{ json_encode([
                    'days' => (object) $booked_days,
                    'mode' => 'admin',
                    'months' => 9,
                    'editUrl' => route('admin.events.edit', '__ID__'),
                ]) }}"></div>
            </div>
        </div>
        <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">

    </div>
    <div id="tanStackTable" data-vue-component="events-table"></div>
@endsection

@push('scripts')
    <script>
        // Pass contract statuses to Vue
        window.contractStatuses = @json($contract_statuses->toArray())
    </script>
@endpush
