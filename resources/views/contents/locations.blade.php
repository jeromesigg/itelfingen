<section id="locations" class="locations">
    @if($application_enabled)
        <div class="relative z-10 mx-auto flex h-full max-w-(--breakpoint-2xl) flex-col items-start justify-center px-4 lg:px-6">
            <div class="section-title">
                <h2 class="text-white">Werde Teil der Genossenschaft</h2>
            </div>
            <p class="max-w-xl text-lg text-white sm:text-xl">
                Günstiger übernachten, das Haus mitgestalten und gemeinsam etwas Schönes schaffen.
            </p>
            <a href="{{ route('applications') }}"
                class="mt-6 inline-flex items-center justify-center rounded-lg bg-white px-6 py-3 text-base font-medium text-ebonyclay hover:bg-gray-100 focus:outline-hidden focus:ring-4 focus:ring-white/50">
                Genossenschafter:in werden
            </a>
        </div>
    @endif
</section><!-- End Locations Section -->
