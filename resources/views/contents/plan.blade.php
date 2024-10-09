<section id="plan">
    <div class="px-4 mx-auto max-w-screen-xl lg:px-6">
        <div class="section-title">
            <p>Das Ferienhaus</p>
        </div>
        <div class="md:flex">
            <ul class="flex-column space-y space-y-4 text-xl font-medium text-gray-500 dark:text-gray-400 md:me-4 mb-4 md:mb-0" id="defaultTab" data-tabs-toggle="#defaultTabContent" role="tablist"
            {{-- data-tabs-active-classes="inline-flex items-center px-4 py-3 rounded-lg hover:text-gray-900 bg-green-50 hover:bg-gray-100 w-full dark:bg-green-800 dark:hover:bg-green-700 dark:hover:text-white" --}}
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
        </div>
    
        <h3 class="text-3xl dark:text-white mt-12">
            Mehr Informationen zur Ausstattung gibt es auf unserer <a href="https://www.itelfingen.ch/faq" target="blank" class="text-[color:var(--orientalpink)] hover:underline">FAQ-Seite</a>
        </h3>


    </div>
</section>