@extends('layouts.admin')
@section('content')
    @php
        $canRefuse = ! $application['invoice_send'] && ! $application['refuse'];
        [$statusText, $statusColor] = match (true) {
            (bool) $application['refuse'] => ['Abgelehnt', 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'],
            (bool) $application['invoice_send'] => ['Rechnung versendet', 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'],
            default => ['Offen', 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'],
        };
        $card = 'p-6 bg-white border border-gray-200 rounded-lg shadow-xs dark:bg-gray-800 dark:border-gray-700';
        $label = 'text-sm font-medium text-gray-500 dark:text-gray-400';
        $value = 'text-gray-900 dark:text-white';
    @endphp

    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('applications.index') }}" class="inline-flex items-center gap-1 mb-2 text-sm text-orientalpink hover:underline">
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4"/>
                </svg>
                Zurück zur Übersicht
            </a>
            <h3 class="text-3xl font-bold dark:text-white">{{ $application['firstname'] }} {{ $application['name'] }}</h3>
            <div class="flex flex-wrap items-center gap-3 mt-2">
                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full {{ $statusColor }}">{{ $statusText }}</span>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    Bewerbung vom {{ \Carbon\Carbon::parse($application['created_at'])->format('d.m.Y') }}
                </span>
            </div>
        </div>
        @if ($canRefuse)
            <form action="{{ route('applications.refuse', $application) }}" method="POST">
                @csrf
                <button type="submit" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
                    Ablehnen
                </button>
            </form>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="{{ $card }}">
            <h4 class="mb-4 text-xl font-semibold dark:text-white">Kontakt</h4>
            <dl class="grid gap-4">
                <div>
                    <dt class="{{ $label }}">Name</dt>
                    <dd class="{{ $value }}">
                        {{ $application->salutation?->name }} {{ $application['firstname'] }} {{ $application['name'] }}
                    </dd>
                </div>
                @if ($application['organisation'])
                    <div>
                        <dt class="{{ $label }}">Organisation</dt>
                        <dd class="{{ $value }}">{{ $application['organisation'] }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="{{ $label }}">Adresse</dt>
                    <dd class="{{ $value }}">
                        {{ $application['street'] }} {{ $application['house_number'] }}<br>
                        {{ $application['plz'] }} {{ $application['city'] }}
                    </dd>
                </div>
                <div>
                    <dt class="{{ $label }}">E-Mail</dt>
                    <dd><a href="mailto:{{ $application['email'] }}" class="text-orientalpink hover:underline break-all">{{ $application['email'] }}</a></dd>
                </div>
                @if ($application['telephone'])
                    <div>
                        <dt class="{{ $label }}">Telefon</dt>
                        <dd><a href="tel:{{ preg_replace('/[^\d+]/', '', $application['telephone']) }}" class="text-orientalpink hover:underline">{{ $application['telephone'] }}</a></dd>
                    </div>
                @endif
            </dl>
        </div>

        <div class="{{ $card }} lg:col-span-2">
            <h4 class="mb-4 text-xl font-semibold dark:text-white">Bewerbung</h4>
            <dl class="grid gap-6">
                <div>
                    <dt class="{{ $label }} mb-1">Wieso möchtest du Genossenschafter:in werden?</dt>
                    {{-- whitespace-pre-line: Zeilenumbrüche aus dem Formular bleiben erhalten --}}
                    <dd class="{{ $value }} whitespace-pre-line">{{ $application['why'] }}</dd>
                </div>
                <div>
                    <dt class="{{ $label }} mb-1">Bemerkung</dt>
                    <dd class="{{ $value }} whitespace-pre-line">{{ $application['comment'] ?: '–' }}</dd>
                </div>
            </dl>

            <h4 class="mt-8 mb-4 text-xl font-semibold dark:text-white">Verlauf</h4>
            <ol class="relative border-s border-gray-200 dark:border-gray-700 ms-2">
                <li class="mb-4 ms-4">
                    <div class="absolute w-3 h-3 bg-gray-300 rounded-full mt-1.5 -start-1.5 border border-white dark:border-gray-900 dark:bg-gray-600"></div>
                    <p class="{{ $value }}">Bewerbung eingegangen</p>
                    <time class="text-sm text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($application['created_at'])->format('d.m.Y H:i') }}</time>
                </li>
                <li class="mb-4 ms-4">
                    <div class="absolute w-3 h-3 rounded-full mt-1.5 -start-1.5 border border-white dark:border-gray-900 {{ $application['invoice_send'] ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                    <p class="{{ $value }}">
                        @if ($application['invoice_send'])
                            Rechnung versendet
                        @else
                            Rechnung noch nicht versendet <span class="text-sm text-gray-500 dark:text-gray-400">(automatisch zwei Wochen nach Eingang)</span>
                        @endif
                    </p>
                    @if ($application['bexio_invoice_id'])
                        <a href="https://office.bexio.com/index.php/kb_invoice/show/id/{{ $application['bexio_invoice_id'] }}" target="_blank" class="text-sm text-orientalpink hover:underline">Rechnung in Bexio öffnen</a>
                    @endif
                </li>
                @if ($application['refuse'])
                    <li class="ms-4">
                        <div class="absolute w-3 h-3 bg-red-500 rounded-full mt-1.5 -start-1.5 border border-white dark:border-gray-900"></div>
                        <p class="{{ $value }}">Abgelehnt</p>
                    </li>
                @endif
            </ol>
        </div>
    </div>
@endsection
