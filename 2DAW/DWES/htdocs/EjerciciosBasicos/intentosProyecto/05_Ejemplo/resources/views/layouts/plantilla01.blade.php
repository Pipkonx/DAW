<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>@yield('titulo')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body class="{{ $isLoginPage ?? false ? 'login-page' : '' }}">

  <div class="container">
    <div class="header-actions">
      @if(!isset($isLoginPage) || !$isLoginPage)
      <form action="/EjerciciosBasicos/intentosProyecto/05_Ejemplo/public/logout" method="POST" class="logout-form">
        @csrf
        <button type="submit" class="btn btn-danger">Cerrar Sesión</button>
      </form>
      @endif
    </div>
    @yield('cuerpo')
  </div>
</body>

</html>