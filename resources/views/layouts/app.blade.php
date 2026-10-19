<!doctype html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
      @include('includes.head')
  <body antialiased >
    @yield('content')
     <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js" integrity="sha384-dXMudAuWq5MLjG7Tex8rC+3U/BPQ/OUAF6UyUa7DWzfAMW1JRIf2NuW2hAyGtsvO" crossorigin="anonymous"></script>
    @stack('scripts')
  </body>
</html>
