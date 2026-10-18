<header class="sticky top-0 z-50 bg-white shadow-sm dark:bg-gray-800">
    <nav class="border-gray-200 p-1">
        <div class="flex flex-wrap justify-between items-center gap-2 mx-auto max-w-(--breakpoint-2xl) px-2 py-1">
            <a href="/" class="flex items-center">
                <img src="/img/logo.png" class="h-10 sm:h-12" alt="Ferienhaus Itelfingen" />
            </a>
            <div class="flex items-center gap-2 lg:order-2">
                <a href="/#booking" class="text-white bg-gladegreen hover:opacity-90 focus:ring-4 focus:ring-gladegreen/50 font-medium rounded-lg text-sm px-3 py-2 lg:px-5 lg:py-2.5 focus:outline-hidden">Jetzt buchen</a>
                <a href="/#contact" class="text-white bg-ebonyclay hover:opacity-90 focus:ring-4 focus:ring-ebonyclay/50 font-medium rounded-lg text-sm px-3 py-2 lg:px-5 lg:py-2.5 focus:outline-hidden">Schreib uns</a>
            </div>
            @auth
                <div class="hidden lg:flex lg:order-1">
                    <a href="/admin" target="_blank" class="font-medium text-orientalpink hover:underline">Dashboard</a>
                </div>
            @endauth
        </div>
    </nav>
</header>
