<?php
// Incluye los archivos necesarios para la conexión a la base de datos y utilidades.
require_once __DIR__ . '/../conexion/DB.php';
require_once __DIR__ . '/utilsController.php';

// Obtiene una instancia de la conexión a la base de datos.
$pdo = DB::getInstance()->getConnection();

/**
 * Función para manejar el inicio de sesión de usuarios.
 * Verifica las credenciales proporcionadas y redirige al usuario si son válidas.
 *
 * @param PDO $conexion La conexión a la base de datos.
 * @param string $nombreUsuario El nombre de usuario proporcionado.
 * @param string $contrasena La contraseña proporcionada.
 */
function manejarInicioSesion(PDO $conexion, string $nombreUsuario, string $contrasena)
{
    // Verifica que el nombre de usuario y la contraseña no estén vacíos.
    if ($nombreUsuario === '' || $contrasena === '') {
        redirect('../vistas/autentificarse/loginView.php?error=Credenciales+inv%C3%A1lidas');
    }

    // Prepara y ejecuta una consulta para obtener el ID y la contraseña hasheada del jugador.
    $declaracion = $conexion->prepare('SELECT id_jugador, contrasena FROM JUGADORES WHERE login = ? LIMIT 1');
    $declaracion->execute([$nombreUsuario]);
    $usuario = $declaracion->fetch();

    // Verifica si el usuario existe.
    if (!$usuario) {
        redirect('../vistas/autentificarse/loginView.php?error=Usuario+no+encontrado');
    }

    // Verifica si la contraseña proporcionada coincide con la contraseña almacenada.
    if ($contrasena !== $usuario['contrasena']) {
        redirect('../vistas/autentificarse/loginView.php?error=Contrase%C3%B1a+incorrecta');
    }

    // Si las credenciales son válidas, redirige al usuario a la página de configuración del juego.
    redirect('../vistas/juego/configurarView.php?login=' . urlencode($nombreUsuario));
}

/**
 * Función para manejar el registro de nuevos usuarios.
 * Valida los datos, verifica la existencia del usuario y lo registra en la base de datos.
 *
 * @param PDO $conexion La conexión a la base de datos.
 * @param string $nombreUsuario El nombre de usuario proporcionado.
 * @param string $contrasena La contraseña proporcionada.
 * @param string $confirmarContrasena La confirmación de la contraseña.
 */
function manejarRegistro(PDO $conexion, string $nombreUsuario, string $contrasena, string $confirmarContrasena)
{
    // Verifica que todos los campos estén completos.
    if ($nombreUsuario === '' || $contrasena === '' || $confirmarContrasena === '') {
        redirect('../vistas/autentificarse/register.php?error=Completa+todos+los+campos');
    }
    // Verifica que las contraseñas coincidan.
    if ($contrasena !== $confirmarContrasena) {
        redirect('../vistas/autentificarse/register.php?error=Las+contrase%C3%B1as+no+coinciden');
    }

    // Comprueba si el nombre de usuario ya existe en la base de datos.
    $verificarExistencia = $conexion->prepare('SELECT COUNT(*) AS c FROM JUGADORES WHERE login = ?');
    $verificarExistencia->execute([$nombreUsuario]);
    $existeUsuario = (int)$verificarExistencia->fetchColumn() > 0;

    if ($existeUsuario) {
        redirect('../vistas/autentificarse/register.php?error=El+usuario+ya+existe');
    }

    // Inserta el nuevo usuario en la base de datos.
    $insertarUsuario = $conexion->prepare('INSERT INTO JUGADORES (login, contrasena, es_administrador) VALUES (?, ?, 0)');
    $insertarUsuario->execute([$nombreUsuario, $contrasena]);

    // Redirige al usuario a la página de inicio de sesión con un mensaje de éxito.
    redirect('../vistas/autentificarse/loginView.php?ok=Registro+exitoso&login=' . urlencode($nombreUsuario));
}

// Determina la acción a realizar basándose en los datos POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $accion = $_POST['action'];
        $nombreUsuario = trim($_POST['login'] ?? '');
        $contrasena = $_POST['password'] ?? '';

        switch ($accion) {
            case 'login':
                manejarInicioSesion($pdo, $nombreUsuario, $contrasena);
                break;
            case 'register':
                $confirmarContrasena = $_POST['confirm'] ?? '';
                manejarRegistro($pdo, $nombreUsuario, $contrasena, $confirmarContrasena);
                break;
            default:
                // Acción no reconocida.
                http_response_code(400);
                echo 'Acción no soportada';
                exit;
        }
    } else {
        // Si no se especifica una acción, se asume que es un intento de inicio de sesión o registro sin acción definida.
        // Esto puede ocurrir si el formulario no tiene un campo 'action' o si se accede directamente.
        http_response_code(400);
        echo 'Acción no especificada';
        exit;
    }
}

// Si la solicitud no es POST o no se maneja ninguna acción, se considera una acción no soportada.
http_response_code(400);
echo 'Acción no soportada';
