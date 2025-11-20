<?php
namespace App\Http\Controllers;
use App\Models\User;

class AuthController {
    public static function login($username, $password, $remember = false) {
        $user = User::findByUsername($username);
        if ($user && password_verify($password, $user['password_hash'] ?? '')) {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            $_SESSION['user'] = $user;
            if ($remember) {
                setcookie('remember_user', $username, time() + 3*24*60*60, '/');
            }
            return true;
        }
        return false;
    }

    public static function logout() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        session_destroy();
        setcookie('remember_user', '', time() - 3600, '/');
    }

    public static function check() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return isset($_SESSION['user']);
    }

    public static function user() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return $_SESSION['user'] ?? null;
    }
}
