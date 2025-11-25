<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>
<body>
    <div class="login-container">
        <div class="login-row">
            <div class="login-column">
                <div class="login-card">
                    <div class="login-card-header">Iniciar Sesión</div>
                    <div class="login-card-body">
                        <form method="POST" action="{{ route('acceso') }}">
                            @csrf
                            <div class="form-group">
                                <label for="email" class="form-label-custom">Correo Electrónico</label>
                                <input type="email" class="form-input-custom @error('email') input-error @enderror" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <div class="error-message">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password" class="form-label-custom">Contraseña</label>
                                <input type="password" class="form-input-custom @error('password') input-error @enderror" id="password" name="password" required autocomplete="current-password">
                                @error('password')
                                    <div class="error-message">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input-custom" id="remember" name="remember">
                                <label class="form-check-label-custom" for="remember">Recordarme</label>
                            </div>
                            <button type="submit" class="button-primary">Iniciar Sesión</button>
                            <a href="{{ route('registro') }}" class="button-link">¿No tienes cuenta? Regístrate</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>