<?php

namespace App\Http\Controllers;

use App\Models\M_Usuarios;

use App\Models\M_Funciones;

class C_Usuarios extends C_Controller
{
    const USUARIOS_POR_PAGINA = 10; // Puedes ajustar esto según tus necesidades

    /**
     * Muestra la lista de usuarios con paginación.
     *
     * Verifica que el usuario autenticado tenga rol de administrador.
     * Obtiene los datos de paginación y la lista de usuarios del modelo M_Usuarios.
     * Prepara los datos para la vista 'usuarios.listar'.
     *
     * @return \Illuminate\View\View|void Retorna la vista de la lista de usuarios o redirige al login si no es administrador.
     */
    public function listar()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }

        $modelo = new M_Usuarios();
        $error = '';
        $usuarios = [];

        // Datos de paginación
        $paginaActual = (int)($_GET['pagina'] ?? 1);
        $totalElementos = $modelo->contar();
        $totalPaginas = ceil($totalElementos / self::USUARIOS_POR_PAGINA);

        $offset = (int)($paginaActual - 1) * self::USUARIOS_POR_PAGINA;
        $usuarios = $modelo->listar(self::USUARIOS_POR_PAGINA, $offset);

        $datos = ['usuarios' => $usuarios, 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas, 'baseUrl' => 'admin/usuarios'];
        if ($error) $datos['errorGeneral'] = $error;

        return view('usuarios.listar', $datos);
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     *
     * Verifica que el usuario autenticado tenga rol de administrador.
     * Prepara la vista 'usuarios.crear' para el modo de alta.
     *
     * @return \\Illuminate\\View\\View|\Illuminate\\Http\\RedirectResponse Retorna la vista del formulario de creación o redirige al login si no es administrador.
     */
    public function crear()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }
        return view('usuarios.crear', ['usuario' => null, 'modo' => 'alta']);
    }

    /**
     * Guarda un nuevo usuario en la base de datos.
     *
     * Valida los datos del formulario y, si son válidos, inserta el nuevo usuario
     * a través del modelo M_Usuarios. Redirige a la lista de usuarios con un mensaje
     * de éxito o muestra los errores en el formulario de creación.
     *
     * @param \\Illuminate\\Http\\Request $request La solicitud HTTP que contiene los datos del formulario.
     * @return \\Illuminate\\View\\View|\Illuminate\\Http\\RedirectResponse Retorna la vista del formulario con errores o redirige a la lista de usuarios.
     */
    /**
     * Guarda un nuevo usuario en la base de datos.
     *
     * Valida los datos del formulario enviados por POST y, si son válidos, inserta el nuevo usuario
     * a través del modelo M_Usuarios. Redirige a la lista de usuarios con un mensaje
     * de éxito o muestra los errores en el formulario de creación.
     *
     * @return \Illuminate\View\View|void Retorna la vista del formulario con errores o redirige a la lista de usuarios.
     */
    public function guardar()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }

        $errores = M_Funciones::validarUsuario($_POST);

        if (!empty($errores)) {
            return view('usuarios.crear', ['errores' => $errores, 'usuario' => (object)$_POST, 'modo' => 'alta']);
        }

        $modelo = new M_Usuarios();
        if (!$modelo->insertar($_POST)) {
            $errores['general'] = 'Error al crear el usuario en la base de datos.';
            return view('usuarios.crear', ['errores' => $errores, 'usuario' => (object)$_POST, 'modo' => 'alta']);
        }

        $_SESSION['mensaje'] = 'Usuario creado correctamente.';
        $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
        header("Location: $baseUrl/admin/usuarios");
        exit;
    }

    /**
     * Muestra el formulario para editar un usuario existente.
     *
     * Verifica que el usuario autenticado tenga rol de administrador.
     * Busca el usuario por su ID (obtenido de $_GET) y, si lo encuentra, prepara la vista 'usuarios.editar'.
     * Si el usuario no existe, redirige a la lista de usuarios con un mensaje de error.
     *
     * @return \Illuminate\View\View|void Retorna la vista de edición o redirige a la lista de usuarios.
     */
    public function editar($id)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }

        // $id = $_POST['id'] ?? null; // Comentado porque ahora el ID viene como parámetro de ruta
        if (!$id) {
            $_SESSION['errorGeneral'] = 'ID de usuario no proporcionado.';
            $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
            header("Location: $baseUrl/admin/usuarios");
            exit;
        }

        $modelo = new M_Usuarios();
        $usuario = $modelo->buscar((int)$id);

        if (!$usuario) {
        $_SESSION['errorGeneral'] = 'Usuario no encontrado.';
        $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
        header("Location: $baseUrl/admin/usuarios");
        exit;
        }

        return view('usuarios.editar', ['usuario' => $usuario, 'modo' => 'edicion']);
    }

    /**
     * Actualiza un usuario existente en la base de datos.
     *
     * Valida los datos del formulario enviados por POST y, si son válidos, actualiza el usuario
     * a través del modelo M_Usuarios. Redirige a la lista de usuarios con un mensaje
     * de éxito o muestra los errores en el formulario de edición.
     *
     * @return \Illuminate\View\View|void Retorna la vista de edición con errores o redirige a la lista de usuarios.
     */
    public function actualizar()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }

        $errores = M_Funciones::validarUsuario($_POST, true); // true para indicar que es edición

        if (!empty($errores)) {
            return view('usuarios.editar', ['errores' => $errores, 'usuario' => (object)$_POST, 'modo' => 'edicion']);
        }

        $modelo = new M_Usuarios();
        $modelo->actualizarUsuario($_POST);

        $_SESSION['mensaje'] = 'Usuario actualizado correctamente.';
        $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
        header("Location: $baseUrl/admin/usuarios");
        exit;
    }

    /**
     * Muestra una vista de confirmación antes de eliminar un usuario.
     *
     * Verifica que el usuario autenticado tenga rol de administrador.
     * Busca el usuario por su ID (obtenido de $_GET) y, si lo encuentra, prepara la vista de confirmación.
     * Si el usuario no existe, redirige a la lista de usuarios con un mensaje de error.
     *
     * @return \Illuminate\View\View|void Retorna la vista de confirmación o redirige a la lista de usuarios.
     */
    public function confirmarEliminacion($id)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }

        // $id = $_POST['id'] ?? null; // Comentado porque ahora el ID viene como parámetro de ruta
        if (!$id) {
            $_SESSION['errorGeneral'] = 'ID de usuario no proporcionado.';
            $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
            header("Location: $baseUrl/admin/usuarios");
            exit;
        }

        $modelo = new M_Usuarios();
        $usuario = $modelo->buscar((int)$id);

        if (!$usuario) {
            $_SESSION['errorGeneral'] = 'Usuario no encontrado.';
            $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
            header("Location: $baseUrl/admin/usuarios");
            exit;
        }

        return view('usuarios.confirmar_eliminacion', ['usuario' => $usuario]);
    }

    /**
     * Elimina un usuario de la base de datos.
     *
     * Verifica que el usuario autenticado tenga rol de administrador.
     * Elimina el usuario especificado por su ID (obtenido de $_GET) a través del modelo M_Usuarios.
     * Redirige a la lista de usuarios con un mensaje de éxito.
     *
     * @return void Redirige a la lista de usuarios con un mensaje de éxito.
     */
    public function eliminar()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            $_SESSION['errorGeneral'] = 'ID de usuario no proporcionado para eliminar.';
            $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
            header("Location: $baseUrl/admin/usuarios");
            exit;
        }

        $modelo = new M_Usuarios();
        $modelo->eliminar((int)$id);

        $_SESSION['mensaje'] = 'Usuario eliminado correctamente.';
        $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
        header("Location: $baseUrl/admin/usuarios");
        exit;
    }
}
