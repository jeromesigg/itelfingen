<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{env('APP_NAME')}}">
        <meta name="author" content="Jérôme Sigg">
        <meta name="robots" content="noindex,nofollow">
        <meta name="theme-color" content="#ffffff">

        <title>{{isset($title) ? $title . ' - ' : ''}}{{config('app.name')}}</title>

        <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">

        <script src="https://kit.fontawesome.com/da9e6dcf22.js" crossorigin="anonymous"></script>
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            // It's best to inline this in `head` to avoid FOUC (flash of unstyled content) when changing pages or themes
            if (localStorage.getItem('color-theme') === 'dark' ||
                (!('color-theme' in localStorage) &&
                    window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        @yield('styles')
    </head>

    <body class="text-dark__black">
      <div class="antialiased bg-gray-50 dark:bg-gray-900">

        @include('includes/admin_topnav')
       @include('includes/admin_sidenav')

        <main class="p-4 md:ml-64 h-auto min-h-screen pt-20">
          @yield('content')
        </main>
      </div> 
      @stack('scripts')
    </body>

</html>
