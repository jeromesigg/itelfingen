
<header class="m-1">
    <nav class="bg-white border-gray-200 dark:bg-gray-800">
        <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-2xl">
            <a href="/" class="flex items-center">
                <img src="/img/logo.png" class="mr-3 h-24 sm:h-12" alt="Itelfingen Logo" />
            </a>
            <div class="flex items-center lg:order-2">
                <a href="/#booking" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 lg:px-5 py-2 lg:py-2.5 mr-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">Jetzt buchen</a>
                <a href="/#contact" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 lg:px-5 py-2 lg:py-2.5 mr-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">Schreib uns</a>
            </div>
            <div class="hidden justify-between items-center w-full lg:flex lg:w-auto lg:order-1" id="mobile-menu-2">
                <ul class="flex flex-col mt-4 font-medium lg:flex-row lg:space-x-8 lg:mt-0">
                    <li>
                        @guest
                        @else
                        <a href="/admin" target="blank" class="text-orientalpink">Dashboard</a>
                        @endguest
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>