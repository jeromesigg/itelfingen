<!doctype html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
      @include('includes.head')  
  <body antialiased >
    @yield('content')
    @stack('scripts')
  </body>
</html>
