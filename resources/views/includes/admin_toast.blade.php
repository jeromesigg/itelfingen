{{-- Fehlermeldung nach einer Weiterleitung, z. B. Bexio-Fehler (App\Exceptions\Handler). Bleibt, bis sie geschlossen wird. --}}
@if (session()->has('error'))
  <div id="admin-toast" role="alert"
    class="fixed inset-x-4 top-20 z-60 flex items-start gap-3 rounded-lg border border-red-300 bg-red-50 p-4 text-sm text-red-800 shadow-lg md:inset-x-auto md:end-5 md:w-full md:max-w-md dark:border-red-800 dark:bg-gray-800 dark:text-red-400">
    <svg class="mt-0.5 h-5 w-5 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 13V8m0 8h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
    </svg>
    <p class="flex-1 break-words">{{ session('error') }}</p>
    <button type="button" data-dismiss-target="#admin-toast" aria-label="Schliessen"
      class="-m-1 shrink-0 rounded-lg p-1 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-300 dark:hover:bg-gray-700">
      <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
      </svg>
    </button>
  </div>
@endif
