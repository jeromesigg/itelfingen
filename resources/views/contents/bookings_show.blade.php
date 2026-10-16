@extends('layouts.app')

@section('content')
  @include('includes.header')
  @php
    $start = Carbon\Carbon::parse($event['start_date'])->locale('de_CH');
    $end = Carbon\Carbon::parse($event['end_date'])->locale('de_CH');
    $nights = $event['total_days'].' '.($event['total_days'] > 1 ? 'Nächte' : 'Nacht');
    $card = 'rounded-lg border border-gray-200 bg-white p-6 shadow-xs dark:border-gray-700 dark:bg-gray-800';
    $codes = array_filter([
        'Türcode' => $event['code'],
        'Abfallcontainer' => $start->lte(today()) ? '4315' : null,
    ]);
  @endphp
  <div id="app">
    <section class="bg-gray-50 py-8 antialiased dark:bg-gray-900 md:py-16">
      <div class="mx-auto max-w-(--breakpoint-2xl) px-4 2xl:px-0">
        <div class="mx-auto max-w-5xl">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Deine Buchung Nr. {{ $event->number() }}</h2>
          <p class="mt-1 text-gray-500 dark:text-gray-400">
            {{ $start->isoFormat('dd, DD.MM.YYYY') }} – {{ $end->isoFormat('dd, DD.MM.YYYY') }} · {{ $event['total_people'] }} Personen · {{ $nights }}
          </p>

          <div class="mt-6">
            <x-bookings.status :event="$event" :offer-link="$offerLink" />
          </div>

          <div class="mt-6 grid grid-cols-1 gap-8 sm:mt-8 md:grid-cols-2">
            <div class="order-last md:order-0">
              <h3 class="mb-6 text-lg font-semibold text-gray-900 dark:text-white">Ablauf</h3>
              <ol class="relative ms-3 border-s border-dotted border-gray-200 dark:border-gray-700">
                <x-bookings.entry :event="$event" type="Created" />
                <x-bookings.entry :event="$event" type="OfferSend" :document-link="$offerLink" />
                <x-bookings.entry :event="$event" type="InvoiceCreated" />
                <x-bookings.entry :event="$event" type="EventLastInfos" />
                <x-bookings.entry :event="$event" type="EventStay" />
                <x-bookings.entry :event="$event" type="Feedback" :document-link="$invoiceLink" />
              </ol>
            </div>

            <div class="space-y-6">
              @if ($event['last_info'] && $codes)
                <div class="{{ $card }}">
                  <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Deine Codes</h3>
                  <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @foreach ($codes as $label => $code)
                      <div class="flex items-center justify-between gap-3 rounded-lg bg-gray-50 px-4 py-3 dark:bg-gray-700">
                        <div>
                          <p class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</p>
                          <p class="font-mono text-2xl font-bold tracking-widest text-gray-900 dark:text-white">{{ $code }}</p>
                        </div>
                        <button type="button" data-copy="{{ $code }}" aria-label="{{ $label }} kopieren"
                          class="rounded-lg p-2 text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-hidden focus:ring-4 focus:ring-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white dark:focus:ring-gray-700">
                          <svg data-copy-icon class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8v3a1 1 0 0 1-1 1H5m11 4h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1h-7a1 1 0 0 0-1 1v1m4 3v10a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-7.13a1 1 0 0 1 .24-.65L7.7 8.35A1 1 0 0 1 8.46 8H13a1 1 0 0 1 1 1Z" />
                          </svg>
                          <svg data-copied-icon class="hidden h-5 w-5 text-green-600 dark:text-green-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5" />
                          </svg>
                        </button>
                      </div>
                    @endforeach
                  </div>
                </div>
              @endif

              <div class="{{ $card }}">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Buchungsdetails</h3>
                <dl class="grid grid-cols-1 gap-x-6 gap-y-3 sm:grid-cols-[auto_1fr]">
                  <dt class="text-sm text-gray-500 dark:text-gray-400">Gast</dt>
                  <dd class="font-medium text-gray-900 dark:text-white">
                    {{ $event['firstname'] }} {{ $event['name'] }}@if ($event['group_name'])<br><span class="font-normal text-gray-500 dark:text-gray-400">{{ $event['group_name'] }}</span>@endif
                  </dd>

                  <dt class="text-sm text-gray-500 dark:text-gray-400">Kontakt</dt>
                  <dd class="text-gray-900 dark:text-white">
                    {{ $event['street'] }} {{ $event['house_number'] }}, {{ $event['plz'] }} {{ $event['city'] }}<br>
                    <a href="mailto:{{ $event['email'] }}" class="hover:underline">{{ $event['email'] }}</a>
                    @if ($event['telephone'])
                      <br><a href="tel:{{ preg_replace('/\s+/', '', $event['telephone']) }}" class="hover:underline">{{ $event['telephone'] }}</a>
                    @endif
                  </dd>

                  <dt class="text-sm text-gray-500 dark:text-gray-400">Anreise</dt>
                  <dd class="text-gray-900 dark:text-white">
                    {{ $start->isoFormat('dd, DD.MM.YYYY') }}, ab {{ $event['early_checkin'] ? '10:00 Uhr (Early Check-In)' : '15:00 Uhr' }}
                  </dd>

                  <dt class="text-sm text-gray-500 dark:text-gray-400">Abreise</dt>
                  <dd class="text-gray-900 dark:text-white">
                    {{ $end->isoFormat('dd, DD.MM.YYYY') }}, bis {{ $event['late_checkout'] ? '17:00 Uhr (Late Check-Out)' : '10:00 Uhr' }}
                  </dd>

                  <dt class="text-sm text-gray-500 dark:text-gray-400">Belegung</dt>
                  <dd class="text-gray-900 dark:text-white">{{ $event['total_people'] }} Personen, {{ $nights }}</dd>
                </dl>

                <div class="mt-4 flex items-baseline justify-between border-t border-gray-200 pt-4 dark:border-gray-700">
                  <span class="font-semibold text-gray-900 dark:text-white">Total</span>
                  <span class="text-2xl font-bold text-gray-900 dark:text-white">CHF {{ number_format((float) $event['total_amount'], 2, '.', "'") }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    @include('includes.footer')
  </div>
@endsection

@push('scripts')
  <script>
    // navigator.clipboard gibt es nur über HTTPS; sonst über ein verstecktes Textfeld kopieren.
    async function copyText(text) {
      if (navigator.clipboard && window.isSecureContext) {
        await navigator.clipboard.writeText(text);
        return;
      }
      const field = document.createElement('textarea');
      field.value = text;
      field.setAttribute('readonly', '');
      field.style.position = 'fixed';
      field.style.opacity = '0';
      document.body.appendChild(field);
      field.select();
      document.execCommand('copy');
      field.remove();
    }

    document.querySelectorAll('[data-copy]').forEach((button) => {
      button.addEventListener('click', async () => {
        await copyText(button.dataset.copy);
        button.querySelector('[data-copy-icon]').classList.add('hidden');
        button.querySelector('[data-copied-icon]').classList.remove('hidden');
        setTimeout(() => {
          button.querySelector('[data-copy-icon]').classList.remove('hidden');
          button.querySelector('[data-copied-icon]').classList.add('hidden');
        }, 1500);
      });
    });
  </script>
@endpush
