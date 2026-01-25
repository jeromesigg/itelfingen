<!-- Side Navbar -->
 <aside
 class="fixed top-0 left-0 z-40 w-64 h-screen pt-14 transition-transform -translate-x-full bg-norway border-r border-gray-200 md:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
 aria-label="Sidenav"
 id="drawer-navigation"
>
 <div class="overflow-y-auto py-5 px-3 h-full bg-norway dark:bg-gray-800">
   
   <ul class="space-y-2">
      <li>
        <a href="/admin" class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white group">
          <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5"/>
          </svg>
          <span class="ml-3">Dashboard</span>
        </a>
      </li>
      <li>
        <a href="/" target="blank" class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white group">
          <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5"/>
          </svg>
          <span class="ml-3">Zur Seite</span>
        </a>
      </li>
    </ul>
    @if (Auth::user()->isManager())
      <ul class="pt-5 mt-5 space-y-2 border-t border-gray-200 dark:border-gray-700">
        <li>
          <a href="{{route('admin.contacts.index')}}" class="flex items-center p-2 text-base font-medium rounded-lg text-gray-900 dark:text-white">
            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7141 15h4.268c.4043 0 .732-.3838.732-.8571V3.85714c0-.47338-.3277-.85714-.732-.85714H6.71411c-.55228 0-1 .44772-1 1v4m10.99999 7v-3h3v3h-3Zm-3 6H6.71411c-.55228 0-1-.4477-1-1 0-1.6569 1.34315-3 3-3h2.99999c1.6569 0 3 1.3431 3 3 0 .5523-.4477 1-1 1Zm-1-9.5c0 1.3807-1.1193 2.5-2.5 2.5s-2.49999-1.1193-2.49999-2.5S8.8334 9 10.2141 9s2.5 1.1193 2.5 2.5Z"/>
            </svg>
            <span class="ml-3">Anfragen</span>
          </a>
        </li>
        <li>
          <a href="{{route('admin.events.index')}}" class="flex items-center p-2 text-base font-medium rounded-lg text-gray-900 dark:text-white">
            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 10h16m-8-3V4M7 7V4m10 3V4M5 20h14a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Zm3-7h.01v.01H8V13Zm4 0h.01v.01H12V13Zm4 0h.01v.01H16V13Zm-8 4h.01v.01H8V17Zm4 0h.01v.01H12V17Zm4 0h.01v.01H16V17Z"/>
            </svg>
            <span class="ml-3">Buchungen</span>
          </a>
        </li>
      </ul>
    @endif
    <ul class="pt-5 mt-5 space-y-2 border-t border-gray-200 dark:border-gray-700">
      @if (Auth::user()->isAdmin())
        <li>
          <button type="button" class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group dark:text-white" aria-controls="dropdown-homepage" data-collapse-toggle="dropdown-homepage">
            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.03v13m0-13c-2.819-.831-4.715-1.076-8.029-1.023A.99.99 0 0 0 3 6v11c0 .563.466 1.014 1.03 1.007 3.122-.043 5.018.212 7.97 1.023m0-13c2.819-.831 4.715-1.076 8.029-1.023A.99.99 0 0 1 21 6v11c0 .563-.466 1.014-1.03 1.007-3.122-.043-5.018.212-7.97 1.023"/>
            </svg>
            
            <span class="flex-1 ml-3 text-left whitespace-nowrap">
              Homepage
            </span>
            <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"  clip-rule="evenodd"></path>
            </svg>
          </button>
          <ul id="dropdown-homepage" class="hidden py-2 space-y-2">
            <li>
              <a href="{{route('homepages.index')}}" class="flex items-center p-2 pl-11 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group dark:text-white " >
                Homepage
              </a>
            </li>
            <li>
              <a href="{{route('homepages.edit',1)}}" class="flex items-center p-2 pl-11 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group dark:text-white " >
                Homepage Anpassen
              </a>
            </li>
          </ul>
        </li>
        <li>
          <button type="button" class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group dark:text-white " aria-controls="dropdown-albums" data-collapse-toggle="dropdown-albums">
            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M4 18V8a1 1 0 0 1 1-1h1.5l1.707-1.707A1 1 0 0 1 8.914 5h6.172a1 1 0 0 1 .707.293L17.5 7H19a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1Z"/>
              <path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
            </svg>
            <span class="flex-1 ml-3 text-left whitespace-nowrap">
              Fotoalbum
            </span>
            <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"  clip-rule="evenodd"></path>
            </svg>
          </button>
          <ul id="dropdown-albums" class="hidden py-2 space-y-2">
            <li>
              <a href="{{route('pictures.index')}}" class="flex items-center p-2 pl-11 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group dark:text-white " >
                Fotos
              </a>
            </li>
            <li>
              <a href="{{route('pictures.create')}}" class="flex items-center p-2 pl-11 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group  dark:text-white " >
                Fotos erstellen
              </a>
            </li>
          </ul>
        </li>
        <li>
          <a href="{{route('positions.index')}}" class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white  group">
            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17.345a4.76 4.76 0 0 0 2.558 1.618c2.274.589 4.512-.446 4.999-2.31.487-1.866-1.273-3.9-3.546-4.49-2.273-.59-4.034-2.623-3.547-4.488.486-1.865 2.724-2.899 4.998-2.31.982.236 1.87.793 2.538 1.592m-3.879 12.171V21m0-18v2.2"/>
            </svg>
            <span class="ml-3">Positionen</span>
          </a>
        </li>
        <li>
          <a href="{{route('testimonials.index')}}" class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white  group">
            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.556 8.5h8m-8 3.5H12m7.111-7H4.89a.896.896 0 0 0-.629.256.868.868 0 0 0-.26.619v9.25c0 .232.094.455.26.619A.896.896 0 0 0 4.89 16H9l3 4 3-4h4.111a.896.896 0 0 0 .629-.256.868.868 0 0 0 .26-.619v-9.25a.868.868 0 0 0-.26-.619.896.896 0 0 0-.63-.256Z"/>
            </svg>
            <span class="ml-3">Bewertungen</span>
          </a>
        </li>
        <li>
          <a href="{{route('people.index')}}" class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white  group">
            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M4.5 17H4a1 1 0 0 1-1-1 3 3 0 0 1 3-3h1m0-3.05A2.5 2.5 0 1 1 9 5.5M19.5 17h.5a1 1 0 0 0 1-1 3 3 0 0 0-3-3h-1m0-3.05a2.5 2.5 0 1 0-2-4.45m.5 13.5h-7a1 1 0 0 1-1-1 3 3 0 0 1 3-3h3a3 3 0 0 1 3 3 1 1 0 0 1-1 1Zm-1-9.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z"/>
            </svg>
            <span class="ml-3">Personen</span>
          </a>
        </li>

        <li>
          <a href="{{route('histories.index')}}" class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white  group">
            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M10 12v1h4v-1m4 7H6a1 1 0 0 1-1-1V9h14v9a1 1 0 0 1-1 1ZM4 5h16a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/>
            </svg>
            <span class="ml-3">Geschichte</span>
          </a>
        </li>
      @endif
      <li>
        <a href="{{route('admin.bookings')}}" class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white group">
          <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15v4m6-6v6m6-4v4m6-6v6M3 11l6-5 6 5 5.5-5.5"/>
          </svg>
          <span class="ml-3">Auslastung</span>
        </a>
      <li>
      <li>
        <a href="{{route('newsletter.index')}}" class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white  group">
          <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7h1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h11.5M7 14h6m-6 3h6m0-10h.5m-.5 3h.5M7 7h3v3H7V7Z"/>
          </svg>
          <span class="ml-3">Newsletter</span>
        </a>
      </li>
      <li>
        <button type="button" class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group  dark:text-white" aria-controls="dropdown-faqs" data-collapse-toggle="dropdown-faqs">
          <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 9h6m-6 3h6m-6 3h6M6.996 9h.01m-.01 3h.01m-.01 3h.01M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/>
          </svg>
          <span class="flex-1 ml-3 text-left whitespace-nowrap">
            FAQs
          </span>
          <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"  clip-rule="evenodd"></path>
          </svg>
        </button>
        <ul id="dropdown-faqs" class="hidden py-2 space-y-2">
          <li>
            <a href="{{route('faqs.index')}}" class="flex items-center p-2 pl-11 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group dark:text-white " >
              FAQ Liste
            </a>
          </li>
          <li>
            <a href="{{route('faq_chapters.index')}}" class="flex items-center p-2 pl-11 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group dark:text-white " >
              FAQ Kapitel
            </a>
          </li>
        </ul>
      </li>
      <li>
        <button type="button" class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group  dark:text-white" aria-controls="dropdown-checkpoints" data-collapse-toggle="dropdown-checkpoints">
          <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 9h6m-6 3h6m-6 3h6M6.996 9h.01m-.01 3h.01m-.01 3h.01M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/>
          </svg>
          <span class="flex-1 ml-3 text-left whitespace-nowrap">
            Checkliste Reinigung
          </span>
          <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"  clip-rule="evenodd"></path>
          </svg>
        </button>
        <ul id="dropdown-checkpoints" class="hidden py-2 space-y-2">
          <li>
            <a href="{{route('checkpoints.index')}}" class="flex items-center p-2 pl-11 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group dark:text-white " >
              Checkpunkte Reinigung
            </a>
          </li>
          <li>
            <a href="{{route('rooms.index')}}" class="flex items-center p-2 pl-11 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group  dark:text-white " >
              Räume
            </a>
          </li>
        </ul>
      </li>
      <li>
        <a href="{{route('applications.index')}}" class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white  group">
          <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="square" stroke-linejoin="round" stroke-width="2" d="M7 19H5a1 1 0 0 1-1-1v-1a3 3 0 0 1 3-3h1m4-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm7.441 1.559a1.907 1.907 0 0 1 0 2.698l-6.069 6.069L10 19l.674-3.372 6.07-6.07a1.907 1.907 0 0 1 2.697 0Z"/>
          </svg>
          <span class="ml-3">Bewerbungen</span>
        </a>
      </li>
      <li>
        <a href="{{route('users.index')}}" class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white  group">
          <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="square" stroke-linejoin="round" stroke-width="2" d="M10 19H5a1 1 0 0 1-1-1v-1a3 3 0 0 1 3-3h2m10 1a3 3 0 0 1-3 3m3-3a3 3 0 0 0-3-3m3 3h1m-4 3a3 3 0 0 1-3-3m3 3v1m-3-4a3 3 0 0 1 3-3m-3 3h-1m4-3v-1m-2.121 1.879-.707-.707m5.656 5.656-.707-.707m-4.242 0-.707.707m5.656-5.656-.707.707M12 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
          </svg>
          <span class="ml-3">Benutzer</span>
        </a>
      </li>
      <li>
        <a href="{{route('admin.changes')}}" class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white  group">
          <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7h1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h11.5M7 14h6m-6 3h6m0-10h.5m-.5 3h.5M7 7h3v3H7V7Z"/>
          </svg>
          <span class="ml-3">Änderungen</span>
        </a>
      </li>
    </ul>
  </div>
</aside>

 