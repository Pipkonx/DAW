<?php

/**
 * Controlador que me permitirá gestionar las interacciones que realicemos para hacer login
 */

use Jenssegers\Blade\Blade;

include(HELPERS_PATH . 'GestorErrores.php');
include(HELPERS_PATH . 'form.php');


/**
 * Description of Tareas
 *
 * @author santi
 */
class LoginCtrl
{
    protected $model = null;
    protected $blade = null;

    public function __construct()
    {
        $this->blade = new Blade(VIEW_PATH, CACHE_PATH);
    }

    /**
     * Devuelve un objeto de tipo Tareas
     *
     * @return void
     */
    public static function getInstance()
    {
        return new self();
    }

    /**
     * Muestra el formulario para iniciar sesión
     * @return string
     */
    public function login(): string
    {
        $ge = new GestorErrores('<span style="color:red">', '</span>');
        if ($_POST) {
            // Verificamos si es login correcto con user/passwd
            if (Session::getInstance()->login(
                filter_input(INPUT_POST, 'user'),
                filter_input(INPUT_POST, 'passwd')
            )) {
                // Ha entrado, redirigimos a la página de inicio
                Session::redirect('/');
                // Aquí no se llega, redirect ha finalizao el script
            }
            // Login fallido, hay error
            $ge->AnotaError('user', 'Usuario o clave incorrectos');
        }
        // Mostramos los datos
        return $this->blade->render('login', ['ge' => $ge]);
    }

    /**
     * Cierra la sesión
     *
     * @return void
     */
    public function logout()
    {
        Session::getInstance()->logout();
        Session::redirect('/login');
    }
}