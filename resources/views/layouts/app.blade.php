<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
      @include('includes.head')  
  </head>
  <body>
    <div id="app">
      

      @include('includes.header')

      @include('includes.title') 

        

      <main class="py-4">
          @yield('content')
      </main>
      <div id="preloader"></div>
      <a href="#" class="back-to-top"><i class="bx bx-up-arrow-alt"></i></a>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('js/main.js') }}"></script>
    @yield('scripts')
  </body>
</html>
