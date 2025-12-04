<?php

namespace App\Http\Controllers;

use App\Models\M_Usuarios;
use Illuminate\Http\Request;
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
     * @return \\Illuminate\\View\\View|\Illuminate\\Http\\RedirectResponse Retorna la vista de la lista de usuarios o redirige al login si no es administrador.
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

        $paginationData = $this->getPaginationData($modelo, self::USUARIOS_POR_PAGINA);
        $paginaActual = $paginationData['paginaActual'];
        $totalElementos = $paginationData['totalElementos'];
        $totalPaginas = $paginationData['totalPaginas'];

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
    public function guardar(Request $request)
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
        $modelo->insertar($_POST);

        return redirect('/admin/usuarios')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Muestra el formulario para editar un usuario existente.
     *
     * Verifica que el usuario autenticado tenga rol de administrador.
     * Busca el usuario por su ID y, si lo encuentra, prepara la vista 'usuarios.editar'.
     * Si el usuario no existe, redirige a la lista de usuarios con un mensaje de error.
     *
     * @param \\Illuminate\\Http\\Request $request La solicitud HTTP que contiene el ID del usuario a editar.
     * @return \\Illuminate\\View\\View|\Illuminate\\Http\\RedirectResponse Retorna la vista de edición o redirige a la lista de usuarios.
     */
    public function editar(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }

        $id = $request->input('id');
        $modelo = new M_Usuarios();
        $usuario = $modelo->buscar((int)$id);

        if (!$usuario) {
            return redirect('/admin/usuarios')->with('error', 'Usuario no encontrado.');
        }

        return view('usuarios.editar', ['usuario' => $usuario, 'modo' => 'edicion']);
    }

    /**
     * Actualiza un usuario existente en la base de datos.
     *
     * Valida los datos del formulario y, si son válidos, actualiza el usuario
     * a través del modelo M_Usuarios. Redirige a la lista de usuarios con un mensaje
     * de éxito o muestra los errores en el formulario de edición.
     *
     * @param \\Illuminate\\Http\\Request $request La solicitud HTTP que contiene los datos actualizados del usuario.
     * @return \\Illuminate\\View\\View|\Illuminate\\Http\\RedirectResponse Retorna la vista de edición con errores o redirige a la lista de usuarios.
     */
    public function actualizar(Request $request)
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

        return redirect('/admin/usuarios')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Muestra una vista de confirmación antes de eliminar un usuario.
     *
     * Verifica que el usuario autenticado tenga rol de administrador.
     * Busca el usuario por su ID y, si lo encuentra, prepara la vista de confirmación.
     * Si el usuario no existe, redirige a la lista de usuarios con un mensaje de error.
     *
     * @param \\Illuminate\\Http\\Request $request La solicitud HTTP que contiene el ID del usuario a eliminar.
     * @return \\Illuminate\\View\\View|\Illuminate\\Http\\RedirectResponse Retorna la vista de confirmación o redirige a la lista de usuarios.
     */
    public function confirmarEliminacion(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }

        $id = $request->input('id');
        $modelo = new M_Usuarios();
        $usuario = $modelo->buscar((int)$id);

        if (!$usuario) {
            return redirect('/admin/usuarios')->with('error', 'Usuario no encontrado.');
        }

        return view('usuarios.confirmar_eliminacion', ['usuario' => $usuario]);
    }

    /**
     * Elimina un usuario de la base de datos.
     *
     * Verifica que el usuario autenticado tenga rol de administrador.
     * Elimina el usuario especificado por su ID a través del modelo M_Usuarios.
     * Redirige a la lista de usuarios con un mensaje de éxito.
     *
     * @param \\Illuminate\\Http\\Request $request La solicitud HTTP que contiene el ID del usuario a eliminar.
     * @return \\Illuminate\\Http\\RedirectResponse Redirige a la lista de usuarios con un mensaje de éxito.
     */
    public function eliminar(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }

        $id = $request->input('id');
        $modelo = new M_Usuarios();
        $modelo->eliminar((int)$id);

        return redirect('/admin/usuarios')->with('success', 'Usuario eliminado correctamente.');
    }
}
