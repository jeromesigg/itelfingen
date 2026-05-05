<!doctype html>
      @include('includes.head')  
  <body antialiased >
    @yield('content')
     <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
    @stack('scripts')
  </body>
</html>
