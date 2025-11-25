
                <button type="button" aria-controls="navbarContenido" aria-expanded="false" aria-label="Toggle navigation">
                    <span></span>
                </button>

                <div id="navContent">
                    <ul>
                        <!-- Enlaces de navegación adicionales si se necesitan -->
                    </ul>

                    <!-- Usuario autenticado a la derecha -->
                    @auth
                        <div>
                            <button type="button" id="userMenu"
                                aria-controls="userMenu" aria-haspopup="true">
                                {{ Auth::user()->name }}
                            </button>
                            <ul>
                                <li><a href="{{ route('panel') }}">Panel de control</a></li>
                                <li><a href="{{ route('password.form') }}">Cambiar contraseña</a></li>
                                <li><a href="{{ route('name.form') }}">Modificar nombre</a></li>
                                <li>
                                    <hr>
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit">Cerrar sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endauth

                    @guest
                        <div>
                            <a href="{{ route('acceso') }}">Acceder</a>
                            <a href="{{ route('registro') }}">Registrarse</a>
                        </div>
                    @endguest
                </div>
            </div>
        </nav>


    <!-- Contenedor principal con mensajes de estado -->
    <div>
        @if(session('success'))
            <div>{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Sección variable de contenido -->
        @yield('content')
    </div>


<script src="{{ asset('js/app.js') }}"></script>
</body>

</html>