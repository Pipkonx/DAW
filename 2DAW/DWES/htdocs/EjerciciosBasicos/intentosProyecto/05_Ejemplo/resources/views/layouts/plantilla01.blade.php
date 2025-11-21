<!doctype html>
<html lang="es">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>@yield('titulo')</title>
  {{--
  <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}
  <link rel="stylesheet" href="css/app.css">
</head>

<body>

  <div class="container">
    @yield('cuerpo')
  </div>
</body>

</html>