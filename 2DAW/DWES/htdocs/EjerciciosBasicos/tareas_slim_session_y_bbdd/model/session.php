<?php

/**
Clase que nos permitirá gestionar de forma sencilla las sesiones
Técnicamente no es un modelo como los que veremos más adelante en Laravel, aunque
lo ponemos aquí ya que tiene logíca de la aplicación

 */

class Session
{
    const SESS_DATA = 'sess_data';
    const IDX_ESTA_DENTRO = 'idx_dentro';
    const URL_LOGIN = 'login';

    const USUARIO = 'pepe';
    const CLAVE = '1234';

    // Más ctes o atributos como tipo de usuario, nombre, etc

    static private $_instance = null;

    /**
     * Crea el objeto y abre sesión
     */
    private function __construct()
    {
        // Iniciamos session de PHP
        session_start();
    }

    /**
     * Patron Singleton. 
     * Solo necesitamos un objeto de este tipo
     */
    public static function getInstance(): Session
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Undocumented function
     *
     * @param string $user Usuario a logearse
     * @param string $passwd Clave del usuario
     * @return boolean True si se ha logeado, false en caso contrario. Registra para futuras consutlas si esta dentro
     */
    public function login(string $user, string $passwd): bool
    {
        // Si hubiese más de un usuario, consultariamos al modelo de la base de datos
        // guardando en la sessión ($_SESSION) sus datos o el 'id' del mismo si luego lo vamos a consultar
        if (self::USUARIO == $user  && self::CLAVE == $passwd) {
            // Usuario y clave correctos
            $_SESSION[self::IDX_ESTA_DENTRO] = true;
            return true;
        }
        return false;
    }

    /**
     * Registra que ha cerrado la sessión y que el usuario ya no está logeado
     *
     * @return void
     */
    public function logout(): void
    {
        // Registra que ha salido
        $_SESSION[self::IDX_ESTA_DENTRO] = false;
    }

    /**
     * Indica si un usuario está registrado
     *
     * @return boolean
     */
    public function isLogged(): bool
    {
        return !empty($_SESSION[self::IDX_ESTA_DENTRO]);
    }

    /**
     * Comprueba si el usuario está logeado y si no está redirige a la página de login y finaliza
     * el script
     *
     * @return void
     */
    public function onlyLogged(): void
    {
        if (!$this->isLogged())
            self::redirect(self::URL_LOGIN);
    }

    /**
     * Esta functión, no debería ser de esta clase. Se pone aquí por simplificar 
     * 
     * Redirige la petición a la url indicada dentro de la aplicación
     *
     * @param string $url
     * @return void
     */
    public static function redirect(string $url): void
    {
        header('Location: ' . BASE_URL . $url);
        // Finalizamos script
        exit();
    }
}