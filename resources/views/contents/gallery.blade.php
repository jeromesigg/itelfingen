<section id="gallery_section" class="gallery section">
  <div class="px-4 mx-auto max-w-(--breakpoint-2xl) lg:px-6">
    <div class="section-title">
      <h2>Impressionen</h2>
    </div>

    @php
      $carouselButton = 'absolute top-0 z-30 flex h-full cursor-pointer items-center justify-center px-2 sm:px-4 group focus:outline-hidden';
      $carouselIcon = 'inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/70 shadow-sm group-hover:bg-white group-focus:ring-4 group-focus:ring-white sm:h-12 sm:w-12';
    @endphp
    <div id="custom-controls-gallery" class="relative w-full" data-carousel="slide">
      <!-- Carousel wrapper -->
      <div class="relative h-64 overflow-hidden rounded-lg sm:h-96 lg:h-144">
        @foreach ($pictures as $key=>$picture)
          <div class="hidden duration-700 ease-in-out" data-carousel-item>
              <img src="{{$picture->photo->file}}" class="absolute inset-0 block h-full w-full object-cover" alt="{{$picture->name}}" loading="lazy">
          </div>
        @endforeach
      </div>
      <!-- Slider indicators -->
      <div class="absolute bottom-4 left-1/2 z-30 flex -translate-x-1/2 gap-2">
        @foreach ($pictures as $key=>$picture)
          <button type="button" class="h-2.5 w-2.5 rounded-full" aria-current="{{ $key === 0 ? 'true' : 'false' }}" aria-label="Bild {{ $key + 1 }}" data-carousel-slide-to="{{ $key }}"></button>
        @endforeach
      </div>
      <button type="button" class="{{ $carouselButton }} inset-s-0" data-carousel-prev>
        <span class="{{ $carouselIcon }}">
          <svg class="h-5 w-5 text-gray-900 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/>
          </svg>
          <span class="sr-only">Vorheriges Bild</span>
        </span>
      </button>
      <button type="button" class="{{ $carouselButton }} inset-e-0" data-carousel-next>
        <span class="{{ $carouselIcon }}">
          <svg class="h-5 w-5 text-gray-900 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/>
          </svg>
          <span class="sr-only">Nächstes Bild</span>
        </span>
      </button>
    </div>
    <p class="mt-8 text-lg sm:text-xl dark:text-white">
      Mehr Bilder gibt es auf unserer <a href="https://www.instagram.com/itelfingen/" target="_blank" rel="noopener" class="text-orientalpink hover:underline">Instagram-Seite</a>
    </p>
  </div>
</section>
