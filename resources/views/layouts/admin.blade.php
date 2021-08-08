<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{env('APP_NAME')}}">
        <meta name="author" content="Jérôme Sigg">
        <meta name="robots" content="all,follow">

        <title>{{env('APP_NAME')}}</title>

        <!-- Bootstrap Core CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

        <script src="https://kit.fontawesome.com/da9e6dcf22.js" crossorigin="anonymous"></script>
        <link href="{{asset('css/admin.css')}}" rel="stylesheet">

        @yield('styles')


    </head>

    <body>
        @include('includes/admin_sidenav')
        
        @include('includes/admin_topnav')

        @yield('content')
        <footer class="main-footer">
            <div class="container-fluid">
              <div class="row">
                <div class="col-sm-6">
                  <p>Jérôme &copy; 2021</p>
                </div>
                <div class="col-sm-6 text-right">
                  <p>Version 1.0.0</p>
                </div>
              </div>
            </div>
        </footer>
        </div>
        

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
        
        <script src="{{asset('js/admin.js')}}"></script>
        @yield('scripts')        
    </body>

</html>