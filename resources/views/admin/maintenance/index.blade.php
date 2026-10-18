@extends('layouts.admin')

@section('content')
    <div class="block p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <h2 class="text-4xl font-bold dark:text-white mb-5">Wartung</h2>

        @if (session('maintenance_output') !== null)
            <h3 class="text-xl font-bold dark:text-white mb-2">Ausgabe</h3>
            <pre class="p-4 mb-6 overflow-x-auto text-sm text-gray-900 bg-gray-100 rounded-lg dark:bg-gray-900 dark:text-white">{{ session('maintenance_output') ?: 'Keine Ausgabe.' }}</pre>
        @endif

        <h3 class="text-xl font-bold dark:text-white mb-2">Datenbank</h3>
        @if (count($pending) === 0)
            <p class="mb-4 text-gray-700 dark:text-gray-400">Keine offenen Migrationen.</p>
        @else
            <p class="mb-2 text-gray-700 dark:text-gray-400">Offene Migrationen:</p>
            <ul class="mb-4 space-y-1 text-gray-700 list-disc list-inside dark:text-gray-400">
                @foreach ($pending as $migration)
                    <li>{{ $migration }}</li>
                @endforeach
            </ul>
        @endif
        <form action="{{ route('admin.maintenance.migrate') }}" method="POST" class="mb-6">
            @csrf
            <button type="submit" @disabled(count($pending) === 0) class="focus:outline-none text-white bg-gladegreen hover:bg-gladegreen focus:ring-4 focus:ring-gladegreen font-medium rounded-lg text-sm px-5 py-2.5 disabled:opacity-50">
                Migrationen ausführen
            </button>
        </form>

        <h3 class="text-xl font-bold dark:text-white mb-2">Bexio</h3>
        <div class="flex flex-wrap items-center gap-3 mb-4">
            @if ($bexioToken)
                <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-800 text-sm font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">
                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                    Verbunden
                </span>
                <span class="text-sm text-gray-700 dark:text-gray-400">Token gültig bis {{ $bexioToken->expires_at->format('d.m.Y H:i') }}, wird automatisch erneuert.</span>
            @else
                <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-800 text-sm font-medium px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">
                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                    Nicht verbunden
                </span>
                <span class="text-sm text-gray-700 dark:text-gray-400">Offerten und Rechnungen können erst nach dem Verbinden erstellt werden.</span>
            @endif
        </div>
        <a type="button" href="{{ route('admin.bexio.connect') }}" class="inline-flex items-center gap-2 mb-6 focus:outline-none text-white bg-grannysmith hover:bg-grannysmith hover:text-white focus:ring-4 focus:ring-grannysmith font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-grannysmith dark:hover:bg-grannysmith dark:focus:ring-grannysmith">
            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                @if ($bexioToken)
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.651 7.65a7.131 7.131 0 0 0-12.68 3.15M18.001 4v4h-4m-7.652 8.35a7.13 7.13 0 0 0 12.68-3.15M6 20v-4h4"/>
                @else
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.213 9.787a3.391 3.391 0 0 0-4.795 0l-3.425 3.426a3.39 3.39 0 0 0 4.795 4.794l.321-.304m-.321-4.49a3.39 3.39 0 0 0 4.795 0l3.424-3.426a3.39 3.39 0 0 0-4.794-4.795l-1.028.961"/>
                @endif
            </svg>
            {{ $bexioToken ? 'Bexio neu verbinden' : 'Bexio verbinden' }}
        </a>

        <h3 class="text-xl font-bold dark:text-white mb-2">Cache</h3>
        <p class="mb-4 text-gray-700 dark:text-gray-400">Nach einem Deploy, wenn Änderungen an Konfiguration, Routen oder Views nicht sichtbar sind.</p>
        <form action="{{ route('admin.maintenance.clear-cache') }}" method="POST">
            @csrf
            <button type="submit" class="focus:outline-none text-white bg-grannysmith hover:bg-grannysmith focus:ring-4 focus:ring-grannysmith font-medium rounded-lg text-sm px-5 py-2.5">
                Cache leeren
            </button>
        </form>
    </div>
@endsection
