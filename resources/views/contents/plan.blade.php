<section id="plan" class="history">
{{--     <div class="container" >
      <div class="section-title">
        <p>Das Ferienhaus</p>
      </div>
      <div class="row">
        <div class="col-lg-3">
          <ul class="nav nav-tabs flex-column">
              <li class="nav-item">
                <a class="nav-link active show" data-toggle="tab" href="#Erdgeschoss">Erdgeschoss</a>
              </li>
              <li class="nav-item">
                  <a class="nav-link " data-toggle="tab" href="#Obergeschoss">Obergeschoss</a>
              </li>
          </ul>
        </div>
        <div class="col-lg-9 mt-4 mt-lg-0">
          <div class="tab-content">
            <div class="tab-pane active show" id="Erdgeschoss">
                <img src="images/Erdgeschoss.webp" alt="" class="img-fluid">
            </div>
            <div class="tab-pane" id="Obergeschoss">
                <img src="images/Obergeschoss.webp" alt="" class="img-fluid">
            </div>
          </div>
        </div>
      </div>
        <h3 style="margin-top: 20px">
            Mehr Informationen zur Ausstattung gibt es auf unserer <a href="https://www.itelfingen.ch/faq" target="blank">FAQ-Seite</a>
        </h3>
    </div>
   --}}
    <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
        <div class="section-title">
            <p>Das Ferienhaus</p>
        </div>
        {{-- <div class="w-full bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
            <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 border-b border-gray-200 rounded-t-lg bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:bg-gray-800" id="defaultTab" data-tabs-toggle="#defaultTabContent" role="tablist">
                <li class="me-2">
                    <button id="groundfloor-tab" data-tabs-target="#groundfloor" type="button" role="tab" aria-controls="groundfloor" aria-selected="true" class="inline-block p-4 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-gray-300">Erdgeschoss</button>
                </li>
                <li class="me-2">
                    <button id="firstfloor-tab" data-tabs-target="#firstfloor" type="button" role="tab" aria-controls="firstfloor" aria-selected="false" class="inline-block p-4 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-gray-300">Obergeschoss</button>
                </li>
            </ul>
            <div id="defaultTabContent">
                <div class="hidden p-4 bg-white rounded-lg md:p-8 dark:bg-gray-800" id="groundfloor" role="tabpanel" aria-labelledby="groundfloor-tab">
                    <h2 class="mb-5 text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white">Grundriss des Erdgeschosses</h2>
                    <!-- List -->
                    <img src="images/Erdgeschoss.webp" alt="" class="img-fluid">
                </div>
                <div class="hidden p-4 bg-white rounded-lg md:p-8 dark:bg-gray-800" id="firstfloor" role="tabpanel" aria-labelledby="firstfloor-tab">
                    <h2 class="mb-5 text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white">Grundriss des Obergeschosses</h2>
                    <!-- List -->
                    <img src="images/Obergeschoss.webp" alt="" class="img-fluid">
                </div>
            </div>
        </div> --}}
        

<div class="md:flex">
    <ul class="flex-column space-y space-y-4 text-sm font-medium text-gray-500 dark:text-gray-400 md:me-4 mb-4 md:mb-0" id="defaultTab" data-tabs-toggle="#defaultTabContent" role="tablist">
        <li>
            <button id="groundfloor-tab" data-tabs-target="#groundfloor" type="button" role="tab" aria-controls="groundfloor" aria-selected="true" class="inline-flex items-center px-4 py-3 rounded-lg hover:text-gray-900 bg-gray-50 hover:bg-gray-100 w-full dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white">Erdgeschoss</button>
        </li>
        <li class="me-2">
            <button id="firstfloor-tab" data-tabs-target="#firstfloor" type="button" role="tab" aria-controls="firstfloor" aria-selected="false" class="inline-flex items-center px-4 py-3 rounded-lg hover:text-gray-900 bg-gray-50 hover:bg-gray-100 w-full dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white">Obergeschoss</button>
        </li>

    </ul>
    <div id="defaultTabContent">
        <div class="hidden p-6 bg-gray-50 text-medium text-gray-500 dark:text-gray-400 dark:bg-gray-800 rounded-lg w-9/12" id="groundfloor" role="tabpanel" aria-labelledby="groundfloor-tab">
            <h2 class="mb-5 text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white">Grundriss des Erdgeschosses</h2>
            <!-- List -->
            <img src="images/Erdgeschoss.webp" alt="" class="img-fluid">
        </div>
        <div class="hidden p-6 bg-gray-50 text-medium text-gray-500 dark:text-gray-400 dark:bg-gray-800 rounded-lg w-9/12" id="firstfloor" role="tabpanel" aria-labelledby="firstfloor-tab">
            <h2 class="mb-5 text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white">Grundriss des Obergeschosses</h2>
            <!-- List -->
            <img src="images/Obergeschoss.webp" alt="" class="img-fluid">
        </div>
    </div>
</div>


    </div>
</section>