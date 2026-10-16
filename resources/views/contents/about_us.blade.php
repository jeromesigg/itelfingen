@extends('layouts.app')

@section('content')

    @include('includes.header')
    <div id="app">

        <main id="main" class="bg-gray-50 dark:bg-gray-900">
            <div class="mx-auto max-w-4xl px-4 pt-10 pb-14 sm:pt-16">
                <h1 class="font-[PPMigra] text-4xl text-gray-900 sm:text-5xl dark:text-white">Über uns</h1>
                <p class="mt-3 text-lg text-gray-600 dark:text-gray-400">
                    Hinter dem Ferienhaus Itelfingen steht eine gemeinnützige Genossenschaft.
                </p>

                <h2 class="mt-12 text-2xl font-semibold text-gray-900 sm:text-3xl dark:text-white">Die Genossenschaft</h2>
                <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div class="space-y-4 leading-relaxed text-gray-700 md:col-span-2 dark:text-gray-300">
                        <p>
                            Die Genossenschaft Ferienhaus Itelfingen betreibt in Itelfingen, Gemeinde Meierskappel, auf gemeinnütziger
                            Grundlage ein Ferienhaus. Sie stellt es hauptsächlich Jugendlichen und Erwachsenen für Ferien und Freizeit zur Verfügung.
                        </p>
                        <p>
                            Genossenschafter:innen können sich aktiv beteiligen, das Haus mitprägen und mitunterhalten – und profitieren
                            zudem von reduzierten Mietpreisen.
                        </p>
                        <p>
                            Wir möchten Gemeinschaft und fruchtbare Begegnungen schaffen und wünschen uns, dass an diesem Ort grosse Ideen entstehen dürfen.
                        </p>
                    </div>
                    <dl class="h-fit space-y-3 rounded-lg border border-gray-200 bg-white p-5 text-sm dark:border-gray-700 dark:bg-gray-800">
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Rechtsform</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">Gemeinnützige Genossenschaft</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Haus</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">Gepachtet von der Reformierten Kirche Zürich</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Betrieb und Unterhalt</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">Vorstand der Genossenschaft</dd>
                        </div>
                    </dl>
                </div>

                @if (count($people) > 0)
                    <h2 class="mt-14 text-2xl font-semibold text-gray-900 sm:text-3xl dark:text-white">Der Vorstand</h2>
                    <ul class="mt-6 grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-5">
                        @foreach ($people as $person)
                            <li class="text-center">
                                @if ($person->photo)
                                    <img src="{{ $person->photo->file }}" alt="{{ $person->name }}" loading="lazy"
                                        class="mx-auto h-28 w-28 rounded-full object-cover shadow-sm sm:h-32 sm:w-32">
                                @else
                                    {{-- Ohne Foto: Initialen statt eines Platzhalterbildes --}}
                                    <span aria-hidden="true"
                                        class="mx-auto flex h-28 w-28 items-center justify-center rounded-full bg-norway text-3xl font-semibold text-white sm:h-32 sm:w-32">
                                        {{ collect(preg_split('/\s+/', trim($person->name)))->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('') }}
                                    </span>
                                @endif
                                <p class="mt-3 font-semibold text-gray-900 dark:text-white">{{ $person->name }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $person->function }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="mt-14 flex flex-col gap-4 rounded-lg bg-ebonyclay p-6 text-white sm:flex-row sm:items-center sm:justify-between sm:p-8">
                    <div>
                        <p class="text-xl font-semibold">Werde Teil der Genossenschaft</p>
                        <p class="mt-1 text-white/80">Günstiger übernachten, das Haus mitgestalten und gemeinsam etwas Schönes schaffen.</p>
                    </div>
                    <a href="{{ route('applications') }}"
                        class="inline-flex shrink-0 items-center justify-center rounded-lg bg-white px-5 py-2.5 text-sm font-medium text-ebonyclay hover:bg-gray-100 focus:outline-hidden focus:ring-4 focus:ring-white/50">
                        Genossenschafter:in werden
                    </a>
                </div>

                <p class="mt-8">
                    <a href="/files/Jahresbericht_2022.pdf" target="_blank" rel="noopener"
                        class="inline-flex items-center gap-2 font-medium text-gray-900 underline hover:no-underline dark:text-white">
                        <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 13V4M7 14H5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1h-2m-1-5-4 5-4-5m9 8h.01" />
                        </svg>
                        Jahresbericht 2022 (PDF)
                    </a>
                </p>
            </div>
        </main><!-- End #main -->
        @include('includes.footer')

    </div>
@endsection
