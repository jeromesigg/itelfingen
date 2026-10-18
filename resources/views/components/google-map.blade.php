{{-- Google Maps erst nach Klick laden: vorher werden keine Daten an Google übertragen (siehe Datenschutzerklärung) --}}
@php
    $mapSrc = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d605.6247635940863!2d8.473077610557123!3d47.11300787321241!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47855566fbd6fd7f%3A0xf9c4a22f086c0f22!2sItelfingen%203%2C%206344%20Meierskappel!5e1!3m2!1sde!2sch!4v1613503423612!5m2!1sde!2sch';
@endphp
<div {{ $attributes->merge(['class' => 'flex h-88 w-full flex-col items-center justify-center gap-3 rounded-lg bg-white/40 p-6 text-center']) }} data-google-map="{{ $mapSrc }}">
    <p class="text-gray-900">Beim Laden der Karte werden Daten an Google übertragen.</p>
    <button type="button" data-google-map-load
        class="inline-flex items-center justify-center rounded-lg bg-ebonyclay px-5 py-2.5 text-sm font-medium text-white hover:opacity-90 focus:outline-hidden focus:ring-4 focus:ring-ebonyclay/50">
        Karte laden
    </button>
    <a href="https://www.google.com/maps/search/?api=1&query=Itelfingen+3+6344+Meierskappel" target="_blank" rel="noopener" class="text-sm font-medium text-gray-900 underline hover:no-underline">In Google Maps öffnen</a>
</div>

@once
    @push('scripts')
        <script>
            document.querySelectorAll('[data-google-map-load]').forEach((button) => {
                button.addEventListener('click', () => {
                    const map = button.closest('[data-google-map]');
                    const iframe = document.createElement('iframe');
                    iframe.src = map.dataset.googleMap;
                    iframe.title = 'Karte: Ferienhaus Itelfingen';
                    iframe.className = 'h-88 w-full rounded-lg border-0';
                    iframe.allowFullscreen = true;
                    map.replaceWith(iframe);
                });
            });
        </script>
    @endpush
@endonce
