@php
  $prices = config('pricelist');
  $memberSaving = round((1 - $prices['member_adults'] / $prices['other_adults']) * 100);
@endphp
<section id="pricelist" class="pricelist section-blue">
  <div class="px-4 mx-auto max-w-(--breakpoint-2xl) lg:px-6">

      <div class="section-title">
        <h2>Unsere Preise</h2>
      </div>

      <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:gap-12">
        <div>
          <h3 class="mb-4 text-2xl text-white sm:text-3xl">Übernachtungen</h3>
          <div class="overflow-hidden rounded-lg border border-white/20">
            <table class="w-full text-left text-white">
              <thead class="bg-white/10 text-sm sm:text-base">
                <tr>
                  <th scope="col" class="px-3 py-3 font-medium text-white sm:px-4"><span class="sr-only">Alter</span></th>
                  <th scope="col" class="px-3 py-3 font-medium text-white sm:px-4">Genossen&shy;schafter:innen</th>
                  <th scope="col" class="px-3 py-3 font-medium text-white sm:px-4">Privat&shy;personen</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/20">
                <tr>
                  <th scope="row" class="px-3 py-3 font-normal text-white sm:px-4">Erwachsene<br><span class="text-sm text-white/70">ab 16 Jahren</span></th>
                  <td class="px-3 py-3 text-white text-lg font-semibold sm:px-4 sm:text-xl">CHF {{ $prices['member_adults'] }}.–</td>
                  <td class="px-3 py-3 text-white text-lg sm:px-4 sm:text-xl">CHF {{ $prices['other_adults'] }}.–</td>
                </tr>
                <tr>
                  <th scope="row" class="px-3 py-3 font-normal text-white sm:px-4">Kinder<br><span class="text-sm text-white/70">6–16 Jahre</span></th>
                  <td class="px-3 py-3 text-white text-lg font-semibold sm:px-4 sm:text-xl">CHF {{ $prices['member_kids'] }}.–</td>
                  <td class="px-3 py-3 text-white text-lg sm:px-4 sm:text-xl">CHF {{ $prices['other_kids'] }}.–</td>
                </tr>
              </tbody>
            </table>
          </div>
          <p class="mt-3 text-sm text-white/80">
            Pro Nacht und Person. Dazu einmalig CHF {{ $prices['cleaning'] }}.– Reinigungs- und Buchungspauschale.
          </p>

          @if($application_enabled)
            <div class="mt-6 flex flex-col gap-4 rounded-lg bg-white/10 p-5 sm:flex-row sm:items-center sm:justify-between">
              <p class="text-white">
                <span class="font-semibold">Als Genossenschafter:in übernachtest du {{ $memberSaving }} % günstiger</span>
                <br><span class="text-sm text-white/80">und gestaltest das Ferienhaus mit.</span>
              </p>
              <a href="{{ route('applications') }}"
                class="inline-flex shrink-0 items-center justify-center rounded-lg bg-white px-5 py-2.5 text-sm font-medium text-ebonyclay hover:bg-gray-100 focus:outline-hidden focus:ring-4 focus:ring-white/50">
                Genossenschafter:in werden
              </a>
            </div>
          @endif
        </div>

        <div>
          <h3 class="mb-4 text-2xl text-white sm:text-3xl">Buchungsbedingungen</h3>
          <ul class="space-y-3 text-white">
            @foreach ([
              'Das Ferienhaus muss mindestens für eine Übernachtung gebucht werden.',
              'Im Preis sind alle Taxen für Strom, Warmwasser und Heizung enthalten.',
              'Drei Parkplätze sind im Preis enthalten. Jeder weitere kostet CHF '.$prices['parking'].'.– pro Tag (bis zu 5 weitere Plätze).',
              'Annullationskosten bei einer definitiven Buchung: ab 3 Monaten vor Mietbeginn 50 %, ab 14 Tagen vor Mietbeginn 100 % des Totalbetrags.',
              'Über die Feiertage (Weihnachten, Neujahr und Ostern) gilt eine Mindestbuchung von zwei Übernachtungen für 10 Personen.',
            ] as $condition)
              <li class="flex gap-3">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-norway" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5" />
                </svg>
                <span class="text-white">{{ $condition }}</span>
              </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  </section><!-- End pricelist Section -->
