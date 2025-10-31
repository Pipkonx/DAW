<?php
require_once "models/User.php";

class AuthController
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $user = $userModel->login($_POST['email'], $_POST['password']);
            if ($user) {
                // Sin sesiones: generamos token simple
                $token = md5($user['email'] . time());
                header("Location: index.php?controller=Task&action=list&role={$user['rol']}&token=$token&user={$user['nombre']}");
            } else {
                echo "<div class='alert alert-danger'>Credenciales incorrectas</div>";
            }
        }
        require "views/auth/login.php";
    }

    public function logout()
    {
        // Sin sesiones, simplemente redirigimos al login
        header("Location: index.php?controller=Auth&action=login");
    }
}
