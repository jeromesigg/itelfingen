<!doctype html>
      @include('includes.head')  
  <body antialiased >
    @yield('content')
    @stack('scripts')
  </body>
</html>
