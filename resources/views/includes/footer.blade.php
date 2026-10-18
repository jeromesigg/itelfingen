@php
    $footerLink = 'text-white/80 hover:text-white hover:underline';
@endphp
<footer id="footer" class="bg-ebonyclay text-white">
    <div class="mx-auto grid max-w-(--breakpoint-2xl) grid-cols-1 gap-8 px-4 py-10 sm:grid-cols-3 lg:px-6">
        <div>
            <p class="mb-2 font-semibold">Ferienhaus Itelfingen</p>
            <p class="text-white/80">{!! nl2br(e($homepage->address)) !!}</p>
        </div>
        <div>
            <p class="mb-2 font-semibold">Kontakt</p>
            <ul class="space-y-1">
                <li><a href="{{ route('home') }}#contact" class="{{ $footerLink }}">Kontaktformular</a></li>
                @if ($homepage->mail)
                    <li><a href="mailto:{{ $homepage->mail }}" class="{{ $footerLink }}">{{ $homepage->mail }}</a></li>
                @endif
                <li><a href="https://www.instagram.com/itelfingen/" target="_blank" rel="noopener" class="{{ $footerLink }}">Instagram</a></li>
            </ul>
        </div>
        <div>
            <p class="mb-2 font-semibold">Mehr</p>
            <ul class="space-y-1">
                <li><a href="{{ route('faq') }}" class="{{ $footerLink }}">FAQ</a></li>
                <li><a href="{{ route('about_us') }}" class="{{ $footerLink }}">Über uns</a></li>
                <li><a href="{{ route('applications') }}" class="{{ $footerLink }}">Genossenschaft beitreten</a></li>
                <li><a href="{{ route('bookings.login') }}" class="{{ $footerLink }}">Kundenlogin</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-white/20">
        <div class="mx-auto flex max-w-(--breakpoint-2xl) flex-wrap justify-between gap-2 px-4 py-4 text-sm text-white/60 lg:px-6">
            <span>© {{ date('Y') }} Genossenschaft Ferienhaus Itelfingen</span>
            <span>
                <a href="{{ route('impressum') }}" class="hover:text-white hover:underline">Impressum / Datenschutz</a>
                · <a href="{{ route('login') }}" class="hover:text-white hover:underline">Login</a>
            </span>
        </div>
    </div>
</footer><!-- End Footer -->
