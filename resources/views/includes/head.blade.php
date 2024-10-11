<head><meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{isset($title) ? $title . ' - ' : ''}}{{config('app.name')}}</title>
  <meta content="Ein Ferienhaus zum Entspannen und gleichzeitig neue Abenteuer zu erleben. Buchen Sie gleich heute Ihren nächsten Aufenthalt bei uns." name="description">
  <meta content="Ferienhaus,Lagerhaus,Itelfingen,Übernachtung,Gruppenhaus,Luzern,Zug q" name="keywords">
  <meta name="author" content="Jérôme Sigg">
  <meta name="robots" content="index">

  <script async src="https://www.googletagmanager.com/gtag/js?id=G-F60JJ5ECJX"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-F60JJ5ECJX');
  </script>

  <!-- Favicons -->
  <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
  <link href="img/apple-touch-icon.png" rel="apple-touch-icon">

  {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous"> --}}
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">

  <script src="https://kit.fontawesome.com/da9e6dcf22.js" crossorigin="anonymous"></script>


  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
  @vite([
    'resources/css/app.css',
    'resources/js/app.js'])
  <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />

  {!! ReCaptcha::htmlScriptTagJsApi() !!}
</head>
