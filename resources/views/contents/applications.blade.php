@extends('layouts.app')

@php
  $prices = config('pricelist');
  $memberSaving = round((1 - $prices['member_adults'] / $prices['other_adults']) * 100);
  $benefitIcon = 'mb-3 h-8 w-8 text-gladegreen';
@endphp

@section('content')
  @include('includes.header')
  <div id="app">
    <main id="main" class="bg-gray-50 dark:bg-gray-900">
      <div class="mx-auto max-w-4xl px-4 pt-10 pb-14 sm:pt-16">
        <h1 class="font-body text-4xl text-gray-900 sm:text-5xl dark:text-white">Genossenschafter:in werden</h1>
        <p class="mt-3 text-lg text-gray-600 dark:text-gray-400">
          Wir freuen uns über dein Interesse an der Genossenschaft Ferienhaus Itelfingen – und über alle, die die Zukunft und Stimmung des Hauses mitprägen möchten.
        </p>

        @if(session()->has('success'))
          {{-- Nach dem Absenden nur die Bestätigung zeigen, damit niemand zweimal abschickt --}}
          <div class="mt-8 rounded-lg border border-green-200 bg-green-50 p-6 dark:border-green-800 dark:bg-green-950">
            <p class="text-xl font-semibold text-gray-900 dark:text-white">Danke für deine Bewerbung!</p>
            <p class="mt-2 text-gray-700 dark:text-gray-300">{{ session()->get('success') }}</p>
            <a href="{{ route('home') }}" class="mt-4 inline-block font-medium text-gray-900 underline hover:no-underline dark:text-white">Zurück zur Startseite</a>
          </div>
        @else
          <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
              <svg class="{{ $benefitIcon }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5"/></svg>
              <p class="font-semibold text-gray-900 dark:text-white">{{ $memberSaving }} % günstiger übernachten</p>
              <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">CHF {{ $prices['member_adults'] }}.– statt {{ $prices['other_adults'] }}.– pro Nacht und erwachsene Person.</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
              <svg class="{{ $benefitIcon }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9a3 3 0 1 1 6 0c0 2-3 3-3 3m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
              <p class="font-semibold text-gray-900 dark:text-white">Mitgestalten</p>
              <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Ideen einbringen und umsetzen, an der GV mitbestimmen und das Haus mitunterhalten.</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
              <svg class="{{ $benefitIcon }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 17H4a1 1 0 0 1-1-1 3 3 0 0 1 3-3h1m0-3.05A2.5 2.5 0 1 1 9 5.5M19.5 17h.5a1 1 0 0 0 1-1 3 3 0 0 0-3-3h-1m0-3.05a2.5 2.5 0 1 0-2-4.45m.5 13.5h-7a1 1 0 0 1-1-1 3 3 0 0 1 3-3h3a3 3 0 0 1 3 3 1 1 0 0 1-1 1Zm-1-9.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z"/></svg>
              <p class="font-semibold text-gray-900 dark:text-white">Gemeinschaft</p>
              <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Genossenschafts-Events und Arbeitswochenenden mit kostenloser Übernachtung.</p>
            </div>
          </div>

          <p class="mt-6 text-gray-700 dark:text-gray-300">
            In Zukunft möchten wir gemeinsam auch ein soziales Angebot schaffen, zum Beispiel «Ferien für Benachteiligte».
          </p>

          <h2 class="mt-12 text-2xl font-semibold text-gray-900 dark:text-white">So läuft es ab</h2>
          <ol class="mt-4 space-y-3">
            @foreach ([
              'Du füllst das Formular aus – pro erwachsene Person (ab 18 Jahren) eines, es gibt keine Familienmitgliedschaften.',
              'Der Vorstand prüft deine Bewerbung innert zwei Wochen.',
              'Bei positivem Entscheid erhältst du per E-Mail die Rechnung für deinen Anteil von CHF 100.–.',
              'Nach der Bezahlung bist du Genossenschafter:in. Alle Infos zur Genossenschaft erhältst du per E-Mail.',
            ] as $index => $step)
              <li class="flex gap-3">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gladegreen text-sm font-semibold text-white">{{ $index + 1 }}</span>
                <span class="pt-0.5 text-gray-700 dark:text-gray-300">{{ $step }}</span>
              </li>
            @endforeach
          </ol>

          <div class="mt-12 rounded-lg border border-gray-200 bg-white p-6 sm:p-8 dark:border-gray-700 dark:bg-gray-800">
            <h2 class="mb-6 text-2xl font-semibold text-gray-900 dark:text-white">Deine Bewerbung</h2>
            @if ($errors->any())
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <strong>Bitte prüfe deine Angaben:</strong>
                    <ul class="mt-1.5 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <x-forms.form :action="route('application.store')" id='application_form'>
                <x-honeypot />
                <div class="grid md:grid-cols-3 gap-4">
                  <x-forms.container >
                      <x-forms.text label="Vorname:" name="firstname"/>
                  </x-forms.container>
                  <x-forms.container >
                      <x-forms.text label="Nachname*:" name="name" required=true/>
                  </x-forms.container>
                  <x-forms.container >
                      <x-forms.text label="Organisation:" name="organisation"/>
                  </x-forms.container>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="grid grid-cols-4 gap-4">
                        <x-forms.container class="col-span-3">
                            <x-forms.text label="Strasse*:" name="street" required=true/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.text label="Nr.:" name="house_number"/>
                        </x-forms.container>
                    </div>
                    <div class="grid grid-cols-4 gap-4">
                        <x-forms.container>
                            <x-forms.text label="PLZ*:" name="zipcode" required=true inputmode="numeric" class="autocomplete_txt"/>
                        </x-forms.container>
                        <x-forms.container class="col-span-3">
                            <x-forms.text label="Ortschaft*:" name="city" required=true class="autocomplete_txt"/>
                        </x-forms.container>
                    </div>
                    <x-forms.hidden name="city_id" class="autocomplete_txt"/>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <x-forms.container>
                        <x-forms.text label="E-Mail*:" name="email" type="email" required=true/>
                    </x-forms.container>
                    <x-forms.container >
                        <x-forms.text label="Telefon / Mobil:" name="telephone"/>
                    </x-forms.container>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <x-forms.container>
                        <x-forms.text-area label="Warum willst du Genossenschafter:in werden?*" name="why" required=true rows=3 placeholder="Wie gedenkst du vom Angebot des Ferienhauses Itelfingen Gebrauch zu machen? Wie möchtest du dich einbringen und welche Ideen hast du?"/>
                    </x-forms.container>
                    <x-forms.container>
                        <x-forms.text-area label="Hast du noch Fragen oder Bemerkungen?" name="comment" rows=3/>
                    </x-forms.container>
                </div>
                <div class="mt-2 flex items-start gap-3">
                    <input type="checkbox" id="statutes" name="statutes" value="1" required @checked(old('statutes'))
                        class="mt-1 h-4 w-4 rounded-sm border-gray-300 bg-gray-100 text-gladegreen focus:ring-2 focus:ring-gladegreen checked:bg-gladegreen">
                    <label for="statutes" class="text-gray-900 dark:text-white">
                        Ich habe die <a href="/files/Statuten_Genossenschaft_Ferienhaus_Itelfingen.pdf" target="_blank" rel="noopener" class="text-orientalpink underline hover:no-underline">Statuten</a>
                        gelesen und akzeptiere sie.*
                    </label>
                </div>
                <div class="mt-6">
                    <x-forms.button type="submit" class="btn btn-frontpage bg-gladegreen hover:bg-gladegreen" id="submit-btn">
                        Bewerbung absenden
                    </x-forms.button>
                </div>
            </x-forms.form>
          </div>
        @endif
      </div>
    </main><!-- End #main -->
      @include('includes.footer')
  </div>
@endsection
@push('scripts')
    <script type="module">

    //autocomplete script
    $(document).on('focus','.autocomplete_txt',function(){
        type = $(this).attr('name');

        if(type =='city'){
             var autoType='name';
        }
        else if(type =='zipcode'){ 
            var autoType='plz';
        }
        else if(type =='city_id'){
            var autoType='id';
        }

        $(this).autocomplete({
            minLength: 2,
            highlight: true,
            source: function( request, response ) {
                $.ajax({
                    url: "{{ route('searchajaxcity') }}",
                    dataType: "json",
                    data: {
                        term : request.term,
                        type : type,
                    },
                    success: function(data) {
                        var array = $.map(data, function (item) {
                                return {
                                label: item['plz'] + ' ' + item['name'],
                                value: item[autoType],
                                data : item
                            }
                        });
                        response(array)
                    }
                });
            },
            select: function( event, ui ) {
                var data = ui.item.data;
                $("[name='city']").val(data.name);
                $("[name='zipcode']").val(data.plz);
                $("[name='city_id']").val(data.id);
            }
        });


    });
    document.getElementById('application_form')?.addEventListener('submit', function(e) {
        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerHTML = 'Wird gesendet...';
    });
    </script>

@endpush
