@extends('layouts.admin')

@section('content')
<div>
    <div class="container mx-auto px-4">

        <header class="mb-4 flex justify-between items-center">
            <h3 class="text-3xl font-bold dark:text-white">{{$title}}</h3>
        </header>

        <a type="button" href="{{route('admin.events.create')}}" class="focus:outline-hidden text-white bg-gladegreen hover:bg-gladegreen hover:text-white focus:ring-4 focus:ring-gladegreen font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gladegreen dark:hover:bg-gladegreen dark:focus:ring-gladegreen mb-4">Buchung erstellen</a>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
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
        <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">


        <input type="hidden" value="Ab Heute" id="date_btn_value">
        <input type="hidden" value="Alle" id="status_btn_value">
        <div class="hk-reservation hk-reservation__step1 text-gray-600 dark:text-gray-300">
            <div class="hk-reservation__container container mx-auto px-4">
                <div class="hk-calendar">
                    <div class="hk-agenda">
                        <div class="d-none d-sm-block">
                            <a class="hk-agenda__prev text-orientalpink hover:underline" onclick="Agenda.prev(3); return false" href="#">
                                früheres Datum
                            </a>
                        </div>
                         <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            @for ($i = 0; $i <= 9; $i++)
                                <div class="col-md-4 col-sm-6 {{($i>1)?'d-none d-sm-block':''}}">
                                    <h4 id="agendaTitel{{$i}}" class="hk-agenda__title"> </h4>
                                    <table class="hk-agenda__month">
                                        <tbody id="agendaMonat{{$i}}"> </tbody>
                                    </table>
                                </div>
                            @endfor
                        </div>
                        <div class="d-none d-sm-block">
                            <a class="hk-agenda__next text-orientalpink hover:underline" onclick="Agenda.next(3); return false" href="#">
                                späteres Datum
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
   
    </div>
    <div id="app"></div>
</div>
@endsection

@push('scripts')
  <!-- ======= Javascript Section ======= -->
  @include('contents.event_js')
    <script>
    // Pass contract statuses to Vue
    window.contractStatuses = @json($contract_statuses->toArray())
  </script>
@endpush