<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesión - DAW Finance</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ url('/') }}">
</head>
<body>
    <header>
        <h1>DAW Finance</h1>
    </header>
    <main class="container">
        <form id="loginForm" class="form-card">
            @csrf
            <h2>Inicio de sesión</h2>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="tu@correo.com">

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required placeholder="••••••••">

            <button type="submit" class="btn">Entrar</button>
            <p class="alt-link">¿No tienes cuenta? <a href="{{ route('registro') }}">Regístrate aquí</a></p>
        </form>
    </main>

    <footer>
        <p>&copy; 2026 Gestión de Finanzas Personales</p>
    </footer>

    <script type="module" src="{{ asset('js/main.js') }}"></script>
</body>
</html>
