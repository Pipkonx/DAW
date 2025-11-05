<?php
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController {
    private $usuarioModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
    }
    
    // Validar contraseña (más de 8 caracteres)
    private function validarPassword($password) {
        $errores = [];
        if (strlen($password) <= 8) {
            $errores[] = "La contraseña debe tener más de 8 caracteres";
        }
        return $errores;
    }
    
    private function validarEmail($email) {
        $errores = [];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El email no es válido";
        }
        return $errores;
    }
    
    private function validarNombre($nombre) {
        $errores = [];
        if (empty($nombre)) {
            $errores[] = "El nombre no puede estar vacío";
        } elseif (strlen($nombre) < 3) {
            $errores[] = "El nombre debe tener al menos 3 caracteres";
        }
        return $errores;
    }
    
    // Listar todos los usuarios
    public function index() {
        $usuarios = $this->usuarioModel->getAll();
        require_once __DIR__ . '/../views/usuarios/index.php';
    }
    
    // Mostrar formulario de creación
    public function crear() {
        $errores = [];
        $datos = ['nombre' => '', 'email' => '', 'password' => ''];
        
        // podriamos poner directamnete el post
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? ''
            ];
            
            // Validaciones
            // array_merge es para unir los array de errores
            $errores = array_merge(
                $this->validarNombre($datos['nombre']),
                $this->validarEmail($datos['email']),
                $this->validarPassword($datos['password'])
            );
            
            // Verificar si el email ya existe
            if (empty($errores)) {
                $usuarioExistente = $this->usuarioModel->getByEmail($datos['email']);
                if ($usuarioExistente) {
                    $errores[] = "El email ya está registrado";
                }
            }
            
            // Si no hay errores, crear usuario
            if (empty($errores)) {
                if ($this->usuarioModel->create($datos['nombre'], $datos['email'], $datos['password'])) {
                    header('Location: index.php?controller=usuario&action=index');
                    exit;
                } else {
                    $errores[] = "Error al crear el usuario";
                }
            }
        }
        
        require_once __DIR__ . '/../views/usuarios/crear.php';
    }
    
    // Mostrar formulario de edición
    public function editar() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=usuario&action=index');
            exit;
        }
        
        $usuario = $this->usuarioModel->getById($id);
        if (!$usuario) {
            header('Location: index.php?controller=usuario&action=index');
            exit;
        }
        
        $errores = [];
        $datos = $usuario;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'id' => $id,
                'nombre' => trim($_POST['nombre'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? ''
            ];
            
            // Validaciones
            $errores = array_merge(
                $this->validarNombre($datos['nombre']),
                $this->validarEmail($datos['email'])
            );
            
            // Validar contraseña solo si se proporciona una nueva
            if (!empty($datos['password'])) {
                $errores = array_merge($errores, $this->validarPassword($datos['password']));
            }
            
            // Verificar si el email ya existe (excepto para el usuario actual)
            if (empty($errores)) {
                $usuarioExistente = $this->usuarioModel->getByEmail($datos['email']);
                if ($usuarioExistente && $usuarioExistente['id'] != $id) {
                    $errores[] = "El email ya está registrado";
                }
            }
            
            // Si no hay errores, actualizar usuario
            if (empty($errores)) {
                // Si no se proporciona nueva contraseña, mantener la actual
                if (empty($datos['password'])) {
                    $datos['password'] = $usuario['password'];
                }
                
                if ($this->usuarioModel->update($id, $datos['nombre'], $datos['email'], $datos['password'])) {
                    header('Location: index.php?controller=usuario&action=index');
                    exit;
                } else {
                    $errores[] = "Error al actualizar el usuario";
                }
            }
        }
        
        require_once __DIR__ . '/../views/usuarios/editar.php';
    }
    
    // Eliminar usuario
    public function eliminar() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->usuarioModel->delete($id);
        }
        header('Location: index.php?controller=usuario&action=index');
        exit;
    }
}