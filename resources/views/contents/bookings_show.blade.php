@extends('layouts.app')

@section('content')
  @include('includes.header')
  <div id="app">
    <section class="bg-gray-50 py-8 antialiased dark:bg-gray-900 md:py-16">
      <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
        <div class="mx-auto max-w-5xl md:max-w-5xl">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Deine Buchung Nr. {{str_pad($event['id'], 5, '0', STR_PAD_LEFT)}}</h2>
          <div class="mt-6 grid grid-cols-1 gap-6 sm:mt-8 sm:gap-8 md:grid-cols-2 lg:gap-2.5 xl:mt-12">
            <div>
              <ol class="relative ms-3 border-s border-dotted border-gray-200 dark:border-gray-700">
                <x-bookings.entry :event="$event" type="Created" />
                <x-bookings.entry :event="$event" type="OfferSend" />
                <x-bookings.entry :event="$event" type="InvoiceCreated" />
                <x-bookings.entry :event="$event" type="EventLastInfos" />
                <x-bookings.entry :event="$event" type="EventStay" />
                <x-bookings.entry :event="$event" type="Feedback" />
              </ol>
            </div>
    
            <div class="space-y-4">
              <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Buchungs-Informationen</h4>
                <dl class="space-y-1">
                  <dt class="font-medium text-gray-900 dark:text-white">{{$event['firstname']}} {{$event['name']}} {{isset($event['group_name']) ? "- ".$event['group_name'] : ''}}</dt>
                  <dd class="font-normal text-gray-500 dark:text-gray-400">{{$event['street']}},  {{$event['plz']}}  {{$event['city']}} <br> {{$event['email']}} <br> {{$event['telephone']}}</dd>
                </dd>
                </dl>
              </div>
    
              <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Übernachtungen</h4>
                <dl class="space-y-1">
                  <dt class="font-medium text-gray-900 dark:text-white">Vom {{Carbon\Carbon::parse($event['start_date'])->locale('de_CH')->dayName}} {{Carbon\Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y')}} bis {{Carbon\Carbon::parse($event['end_date'])->locale('de_CH')->dayName}} {{Carbon\Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y')}}</dt>
                  <dd class="font-normal text-gray-500 dark:text-gray-400">{{$event['total_people']}} Personen für {{$event['total_days']}} {{$event['total_days']>1 ? 'Nächte' : 'Nacht'}}</dd>
                </dl>
              </div>
    
              <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Zusätzlich</h4>
                <p class="font-medium text-gray-900 dark:text-white">Early Check-In {{$event['early_checkin'] ? 'dazu' : 'nicht'}} gebucht <br> Late Check-Out {{$event['late_checkout'] ? 'dazu' : 'nicht'}} gebucht</p>
              </div>
    
              <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Total</h4>
                <p class="text-lg font-bold text-gray-900 dark:text-white">{{$event['total_amount']}} CHF</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    @include('includes.footer')
  </div>
@endsection
