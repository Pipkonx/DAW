<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>@yield('titulo')</title>
  <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
  <body class="{{ $isLoginPage ?? false ? 'login-page' : '' }}">

  <div>
    <div>
      @if(!isset($isLoginPage) || !$isLoginPage)
        {{-- <form action="/EjerciciosBasicos/intentosProyecto/05_Ejemplo/public/logout" method="POST"
          class="logout-form"> --}}
          <form action="{{ url('/logout') }}" method="POST" class="logout-form">

            @csrf
            <button type="submit">Cerrar Sesión</button>
          </form>
          @if(($session['rol'] ?? '') == 'admin')
            <a href="{{ url('/admin/usuarios') }}" class="btn">Gestionar usuarios</a>
          @endif
      @endif
    </div>
    @yield('cuerpo')
  </div>
  <footer>
    <p>proyecto Rafa</p>
  </footer>
</body>

</html>