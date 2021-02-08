<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
      @include('includes.head')  
  </head>
  <body>

        

          @yield('content')
      

    <!-- jQuery -->
    <script src="{{ asset('js/main.js') }}"></script>
    @yield('scripts')
  </body>
</html>
