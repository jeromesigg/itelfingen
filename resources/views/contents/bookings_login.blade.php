@extends('layouts.app')

@php
  $input = 'block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-gray-900 focus:border-gladegreen focus:ring-gladegreen dark:border-gray-600 dark:bg-gray-700 dark:text-white';
@endphp

@section('content')
  @include('includes.header')
  <div id="app">
    <main id="main" class="bg-gray-50 dark:bg-gray-900">
      <div class="mx-auto max-w-md px-4 pt-10 pb-14 sm:pt-16">
        <h1 class="font-body text-4xl text-gray-900 sm:text-5xl dark:text-white">Kundenbereich</h1>
        <p class="mt-3 text-lg text-gray-600 dark:text-gray-400">
          Hier siehst du den Stand deiner Buchung, alle Dokumente und die Checkliste für die Hausrückgabe.
        </p>

        <form method="POST" action="{{ route('bookings.check') }}" class="mt-8 space-y-5 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
          @csrf
          @if ($errors->any())
            <p class="rounded-lg bg-red-50 p-3 text-sm text-red-800 dark:bg-gray-700 dark:text-red-400" role="alert">
              {{ $errors->first() }}
            </p>
          @endif
          <div>
            <label for="id" class="mb-1 block text-sm font-medium text-gray-900 dark:text-white">Buchungsnummer</label>
            <input type="text" name="id" id="id" inputmode="numeric" autocomplete="off" placeholder="00123" value="{{ old('id') }}" required aria-describedby="id-hint" class="{{ $input }}">
            <p id="id-hint" class="mt-1 text-sm text-gray-500 dark:text-gray-400">Steht in jeder E-Mail von uns zu deiner Buchung.</p>
          </div>
          <div>
            <label for="plz" class="mb-1 block text-sm font-medium text-gray-900 dark:text-white">Postleitzahl</label>
            <input type="text" name="plz" id="plz" inputmode="numeric" autocomplete="postal-code" placeholder="6344" required aria-describedby="plz-hint" class="{{ $input }}">
            <p id="plz-hint" class="mt-1 text-sm text-gray-500 dark:text-gray-400">Die PLZ, die du bei der Buchung angegeben hast.</p>
          </div>
          <button type="submit" class="w-full rounded-lg bg-gladegreen px-5 py-2.5 text-center font-medium text-white hover:opacity-90 focus:outline-hidden focus:ring-4 focus:ring-gladegreen/50">
            Zu meiner Buchung
          </button>
        </form>

        <p class="mt-6 text-center text-gray-600 dark:text-gray-400">
          Noch keine Buchung? <a href="{{ route('home') }}#booking" class="font-medium text-gray-900 underline hover:no-underline dark:text-white">Jetzt Verfügbarkeit prüfen</a>
        </p>
      </div>
    </main>
    @include('includes.footer')
  </div>
@endsection
