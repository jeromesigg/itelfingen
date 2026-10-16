<head><meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{isset($title) ? $title . ' - ' : ''}}{{config('app.name')}}</title>
  <meta content="Ein Ferienhaus zum Entspannen und gleichzeitig neue Abenteuer zu erleben. Buch gleich heute deinen nächsten Aufenthalt bei uns." name="description">
  <meta content="Ferienhaus,Lagerhaus,Itelfingen,Übernachtung,Gruppenhaus,Luzern,Zug" name="keywords">
  <meta name="author" content="Jérôme Sigg">
  <meta name="robots" content="index">

  <!-- Favicons -->
  <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">

  {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous"> --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script> --}}

  @vite([
    'resources/css/app.css',
    'resources/js/app.js'])
</head>
