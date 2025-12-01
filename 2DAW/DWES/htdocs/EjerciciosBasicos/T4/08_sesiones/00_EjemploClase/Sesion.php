<?php

class Sesion
{
    private static ?self $instance = null;

    /**
     * Constructor privado
     */
    private function __construct()
    {
        session_start();
    }

    /**
     * Patron singleton
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (! self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function estaDentro(): bool
    {
        return  !empty($_SESSION['dentro']);
    }

    /**
     * Cierra la sesión del usuario.
     *
     * @return void
     */
    public function salir() : void
    {
        $_SESSION['dentro'] = false;
    }

    public function registraUsuario(int $userId)
    {
        $_SESSION['dentro'] = true;
        $_SESSION['userId'] = $userId;
    }

    public function obligaAQueEstaDentro()
    {
        if (!$this->estaDentro()) {
            header('Location: login_entrar.php');
            exit;
        }
    }

    public function destruir() : void
    {
        session_destroy();
    }
}
