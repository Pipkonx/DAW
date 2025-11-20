<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin: 2rem; }
        form { max-width: 360px; padding: 1rem; border: 1px solid #ddd; border-radius: 8px; }
        label { display:block; margin-top: .75rem; }
        input { width: 100%; padding: .5rem; margin-top: .25rem; }
        button { margin-top: 1rem; padding:.5rem .75rem; }
    </style>
</head>
<body>
    <h1>Iniciar sesión</h1>
    <form method="POST" action="login">
        @csrf
        <label>Usuario
            <input type="text" name="username" required>
        </label>
        <label>Contraseña
            <input type="password" name="password" required>
        </label>
        <label style="display:flex;align-items:center;gap:.5rem;margin-top:.75rem;">
            <input type="checkbox" name="remember" value="1"> Recordarme
        </label>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>
