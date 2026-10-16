@extends('layouts.app')

@section('content')

  @include('includes.header')
  <div id="app">

    <main id="main" class="bg-gray-50 dark:bg-gray-900">
      <div class="mx-auto max-w-4xl px-4 pt-10 pb-6 sm:pt-16">
        <h1 class="font-body text-4xl text-gray-900 sm:text-5xl dark:text-white">Häufige Fragen</h1>
        <p class="mt-3 text-lg text-gray-600 dark:text-gray-400">
          Deine Frage ist nicht dabei? <a href="{{ route('home') }}#contact" class="font-medium text-gray-900 underline hover:no-underline dark:text-white">Schreib uns</a>.
        </p>

        <div class="relative mt-6">
          <label for="faq-search" class="sr-only">Fragen durchsuchen</label>
          <svg class="pointer-events-none absolute inset-s-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
          </svg>
          <input type="search" id="faq-search" placeholder="Fragen durchsuchen, z. B. Parkplatz"
            class="block w-full rounded-lg border border-gray-300 bg-white py-3 ps-10 pe-3 text-gray-900 focus:border-gladegreen focus:ring-gladegreen dark:border-gray-600 dark:bg-gray-700 dark:text-white">
        </div>
      </div>

      {{-- Kapitel-Leiste: bleibt beim Scrollen unter dem Header sichtbar, auf dem Handy horizontal wischbar --}}
      <nav aria-label="Kapitel" class="sticky top-14 z-40 border-y border-gray-200 bg-gray-50/95 backdrop-blur-sm sm:top-16 dark:border-gray-700 dark:bg-gray-900/95">
        <ul class="mx-auto flex max-w-4xl gap-2 overflow-x-auto px-4 py-3">
          @foreach ($faq_chapters as $chapter)
            <li class="shrink-0" data-faq-chapter-link="{{ $chapter->id }}">
              <a href="#faq-chapter-{{ $chapter->id }}"
                class="inline-block rounded-full border border-gray-300 bg-white px-4 py-1.5 text-sm font-medium text-gray-900 hover:border-gladegreen hover:text-gladegreen dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                {{ $chapter->name }}
              </a>
            </li>
          @endforeach
        </ul>
      </nav>

      <div class="mx-auto max-w-4xl space-y-12 px-4 py-10 sm:py-14">
        @foreach ($faq_chapters as $chapter)
          <section id="faq-chapter-{{ $chapter->id }}" class="scroll-mt-16 p-0" data-faq-chapter="{{ $chapter->id }}">
            @if ($chapter->photo)
              <img src="{{ $chapter->photo->file }}" alt="" class="mb-4 h-48 w-full rounded-lg object-cover sm:h-64" loading="lazy">
            @endif
            <h2 class="mb-4 text-2xl font-semibold text-gray-900 sm:text-3xl dark:text-white">{{ $chapter->name }}</h2>

            <div class="divide-y divide-gray-200 overflow-hidden rounded-lg border border-gray-200 bg-white dark:divide-gray-700 dark:border-gray-700 dark:bg-gray-800">
              @foreach ($chapter->faqs as $faq)
                <details class="group" data-faq-item>
                  <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 font-medium text-gray-900 hover:bg-gray-50 dark:text-white dark:hover:bg-gray-700 [&::-webkit-details-marker]:hidden">
                    <span>{{ $faq->name }}</span>
                    <svg class="h-5 w-5 shrink-0 text-gray-500 transition-transform group-open:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                    </svg>
                  </summary>
                  <div class="grid gap-4 px-5 pb-5 text-gray-700 dark:text-gray-300 {{ $faq->photo ? 'md:grid-cols-2' : '' }}">
                    {{-- Beschreibung kommt als HTML aus dem Editor im Admin --}}
                    <div class="leading-relaxed [&_a]:text-orientalpink [&_a]:underline [&_li]:mb-1 [&_ol]:list-decimal [&_ol]:ps-5 [&_p]:mb-3 [&_ul]:list-disc [&_ul]:ps-5">
                      {!! $faq->description !!}
                    </div>
                    @if ($faq->photo)
                      <img src="{{ $faq->photo->file }}" alt="" class="w-full rounded-lg" loading="lazy">
                    @endif
                  </div>
                </details>
              @endforeach
            </div>
          </section>
        @endforeach

        <p id="faq-empty" class="hidden rounded-lg border border-gray-200 bg-white p-6 text-center text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
          Keine passende Frage gefunden. <a href="{{ route('home') }}#contact" class="font-medium text-gray-900 underline hover:no-underline dark:text-white">Schreib uns</a> – wir helfen gerne weiter.
        </p>
      </div>

    </main><!-- End #main -->
      @include('includes.footer')
  </div>
@endsection

@push('scripts')
  <script>
    // Suche: blendet Fragen aus, deren Titel und Antwort den Suchbegriff nicht enthalten
    document.getElementById('faq-search').addEventListener('input', (event) => {
      const query = event.target.value.trim().toLowerCase();
      let anyVisible = false;

      document.querySelectorAll('[data-faq-chapter]').forEach((chapter) => {
        let chapterVisible = false;
        chapter.querySelectorAll('[data-faq-item]').forEach((item) => {
          const matches = query === '' || item.textContent.toLowerCase().includes(query);
          item.hidden = !matches;
          item.open = query !== '' && matches;
          chapterVisible = chapterVisible || matches;
        });
        chapter.hidden = !chapterVisible;
        document.querySelector(`[data-faq-chapter-link="${chapter.dataset.faqChapter}"]`).hidden = !chapterVisible;
        anyVisible = anyVisible || chapterVisible;
      });

      document.getElementById('faq-empty').classList.toggle('hidden', anyVisible);
    });
  </script>
@endpush
