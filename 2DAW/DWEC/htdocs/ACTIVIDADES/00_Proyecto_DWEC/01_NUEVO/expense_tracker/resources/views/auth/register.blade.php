<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>
<body>
    <div class="register-container">
        <div class="register-row">
            <div class="register-column">
                <div class="register-card">
                    <div class="register-card-header">Registrarse</div>
                    <div class="register-card-body">
                        <form method="POST" action="{{ route('registro') }}">
                            @csrf
                            <div class="form-group">
                                <label for="name" class="form-label-custom">Nombre</label>
                                <input type="text" class="form-input-custom @error('name') input-error @enderror" id="name" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                @error('name')
                                    <div class="error-message">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="email" class="form-label-custom">Correo Electrónico</label>
                                <input type="email" class="form-input-custom @error('email') input-error @enderror" id="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                                @error('email')
                                    <div class="error-message">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password" class="form-label-custom">Contraseña</label>
                                <input type="password" class="form-input-custom @error('password') input-error @enderror" id="password" name="password" required autocomplete="new-password">
                                @error('password')
                                    <div class="error-message">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label-custom">Confirmar Contraseña</label>
                                <input type="password" class="form-input-custom" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                            </div>
                            <button type="submit" class="button-primary">Registrarse</button>
                            <a href="{{ route('acceso') }}" class="button-link">¿Ya tienes cuenta? Inicia Sesión</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>