<section id="plan">
    <div class="px-4 mx-auto max-w-screen-xl lg:px-6">
        <div class="section-title">
            <p>Das Ferienhaus</p>
        </div>
        {{-- <div class="md:flex">
            <ul class="flex-column space-y space-y-4 text-xl font-medium text-gray-500 dark:text-gray-400 md:me-4 mb-4 md:mb-0" id="defaultTab" data-tabs-toggle="#defaultTabContent" role="tablist"
            {{-- data-tabs-active-classes="inline-flex items-center px-4 py-3 rounded-lg hover:text-gray-900 bg-green-50 hover:bg-gray-100 w-full dark:bg-green-800 dark:hover:bg-green-700 dark:hover:text-white"
            data-tabs-inactive-classes="px-4 py-3"
            data-tabs-active-classes="inline-flex items-center px-4 py-3 rounded-lg hover:text-gray-900 bg-gray-50 hover:bg-gray-100 w-full dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white">
                <li class="me-2" role="presentation">
                    <button id="groundfloor-tab" data-tabs-target="#groundfloor" type="button" role="tab" aria-controls="groundfloor" aria-selected="true">Erdgeschoss</button>
                </li>
                <li class="me-2" role="presentation">
                    <button id="firstfloor-tab" data-tabs-target="#firstfloor" type="button" role="tab" aria-controls="firstfloor" aria-selected="false">Obergeschoss</button>
                </li>

            </ul>
            <div id="defaultTabContent">
                <div class="hidden p-6 rounded-lg w-10/12" id="groundfloor" role="tabpanel" aria-labelledby="groundfloor-tab">
                    <!-- List -->
                    <img src="images/Erdgeschoss.webp" alt="" class="img-fluid">
                </div>
                <div class="hidden p-6 rounded-lg w-10/12" id="firstfloor" role="tabpanel" aria-labelledby="firstfloor-tab">
                    <!-- List -->
                    <img src="images/Obergeschoss.webp" alt="" class="img-fluid">
                </div>
            </div>
        </div> --}}

        

        <div class="w-full bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
            <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 border-b border-gray-200 rounded-t-lg bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:bg-gray-800" id="defaultTab" data-tabs-toggle="#defaultTabContent" role="tablist">
                <li class="me-2">
                    <button id="about-tab" data-tabs-target="#about" type="button" role="tab" aria-controls="about" aria-selected="true" class="inline-block p-4 text-blue-600 rounded-ss-lg hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-blue-500">About</button>
                </li>
                <li class="me-2">
                    <button id="services-tab" data-tabs-target="#services" type="button" role="tab" aria-controls="services" aria-selected="false" class="inline-block p-4 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-gray-300">Services</button>
                </li>
                <li class="me-2">
                    <button id="statistics-tab" data-tabs-target="#statistics" type="button" role="tab" aria-controls="statistics" aria-selected="false" class="inline-block p-4 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-gray-300">Facts</button>
                </li>
            </ul>
            <div id="defaultTabContent">
                <div class="hidden p-4 bg-white rounded-lg md:p-8 dark:bg-gray-800" id="about" role="tabpanel" aria-labelledby="about-tab">
                    <h2 class="mb-3 text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">Powering innovation & trust at 200,000+ companies worldwide</h2>
                    <p class="mb-3 text-gray-500 dark:text-gray-400">Empower Developers, IT Ops, and business teams to collaborate at high velocity. Respond to changes and deliver great customer and employee service experiences fast.</p>
                    <a href="#" class="inline-flex items-center font-medium text-blue-600 hover:text-blue-800 dark:text-blue-500 dark:hover:text-blue-700">
                        Learn more
                        <svg class=" w-2.5 h-2.5 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                    </a>
                </div>
                <div class="hidden p-4 bg-white rounded-lg md:p-8 dark:bg-gray-800" id="services" role="tabpanel" aria-labelledby="services-tab">
                    <h2 class="mb-5 text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white">We invest in the world’s potential</h2>
                    <!-- List -->
                    <ul role="list" class="space-y-4 text-gray-500 dark:text-gray-400">
                        <li class="flex space-x-2 rtl:space-x-reverse items-center">
                            <svg class="flex-shrink-0 w-3.5 h-3.5 text-blue-600 dark:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                            </svg>
                            <span class="leading-tight">Dynamic reports and dashboards</span>
                        </li>
                        <li class="flex space-x-2 rtl:space-x-reverse items-center">
                            <svg class="flex-shrink-0 w-3.5 h-3.5 text-blue-600 dark:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                            </svg>
                            <span class="leading-tight">Templates for everyone</span>
                        </li>
                        <li class="flex space-x-2 rtl:space-x-reverse items-center">
                            <svg class="flex-shrink-0 w-3.5 h-3.5 text-blue-600 dark:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                            </svg>
                            <span class="leading-tight">Development workflow</span>
                        </li>
                        <li class="flex space-x-2 rtl:space-x-reverse items-center">
                            <svg class="flex-shrink-0 w-3.5 h-3.5 text-blue-600 dark:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                            </svg>
                            <span class="leading-tight">Limitless business automation</span>
                        </li>
                    </ul>
                </div>
                <div class="hidden p-4 bg-white rounded-lg md:p-8 dark:bg-gray-800" id="statistics" role="tabpanel" aria-labelledby="statistics-tab">
                    <dl class="grid max-w-screen-xl grid-cols-2 gap-8 p-4 mx-auto text-gray-900 sm:grid-cols-3 xl:grid-cols-6 dark:text-white sm:p-8">
                        <div class="flex flex-col">
                            <dt class="mb-2 text-3xl font-extrabold">73M+</dt>
                            <dd class="text-gray-500 dark:text-gray-400">Developers</dd>
                        </div>
                        <div class="flex flex-col">
                            <dt class="mb-2 text-3xl font-extrabold">100M+</dt>
                            <dd class="text-gray-500 dark:text-gray-400">Public repositories</dd>
                        </div>
                        <div class="flex flex-col">
                            <dt class="mb-2 text-3xl font-extrabold">1000s</dt>
                            <dd class="text-gray-500 dark:text-gray-400">Open source projects</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

    
        <h3 class="text-3xl dark:text-white mt-12">
            Mehr Informationen zur Ausstattung gibt es auf unserer <a href="https://www.itelfingen.ch/faq" target="blank" class="text-[color:var(--orientalpink)] hover:underline">FAQ-Seite</a>
        </h3>


    </div>
</section>