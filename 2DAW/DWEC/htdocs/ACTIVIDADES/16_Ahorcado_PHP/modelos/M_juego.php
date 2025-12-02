<?php

require_once __DIR__ . '/../conexion/DB.php';

class M_juego {
    private $pdo;

    public function __construct() {
        $this->pdo = DB::getInstance()->getConnection();
    }

    public function obtenerCategorias(): array {
        $declaracion = $this->pdo->query('SELECT id_categoria, nombre_categoria FROM CATEGORIAS ORDER BY nombre_categoria');
        return $declaracion->fetchAll(PDO::FETCH_ASSOC);
    }

    public function iniciarPartida(string $nombreUsuario, int $idCategoria, string $dificultadJuego): array {
        if ($nombreUsuario === '' || $idCategoria <= 0 || $dificultadJuego === '') {
            return ['success' => false, 'message' => 'Datos inválidos.'];
        }

        $condicionLongitud = '';
        $maximoFallos = 6;

        switch ($dificultadJuego) {
            case 'facil':
                $condicionLongitud = 'CHAR_LENGTH(texto_palabra) <= 5';
                $maximoFallos = 8;
                break;
            case 'media':
                $condicionLongitud = 'CHAR_LENGTH(texto_palabra) BETWEEN 6 AND 8';
                $maximoFallos = 6;
                break;
            case 'dificil':
                $condicionLongitud = 'CHAR_LENGTH(texto_palabra) >= 9';
                $maximoFallos = 5;
                break;
            default:
                return ['success' => false, 'message' => 'Dificultad inválida.'];
        }

        $sqlPalabra = "SELECT id_palabra, texto_palabra FROM PALABRAS WHERE id_categoria = ? AND " .
            $condicionLongitud . " ORDER BY RAND() LIMIT 1";
        $declaracion = $this->pdo->prepare($sqlPalabra);
        $declaracion->execute([$idCategoria]);
        $palabraSeleccionada = $declaracion->fetch(PDO::FETCH_ASSOC);

        if (!$palabraSeleccionada) {
            return ['success' => false, 'message' => 'No hay palabras para esa categoría/dificultad.'];
        }

        $idPalabra = $palabraSeleccionada['id_palabra'];
        $textoPalabra = $palabraSeleccionada['texto_palabra'];

        $urlRedireccion = '../vistas/juego/V_partida.php?login=' . urlencode($nombreUsuario)
            . '&id_palabra=' . $idPalabra
            . '&palabra=' . urlencode($textoPalabra)
            . '&dificultad=' . urlencode($dificultadJuego)
            . '&maxfallos=' . $maximoFallos;
        
        return ['success' => true, 'urlRedireccion' => $urlRedireccion];
    }

    public function finalizarPartida(string $nombreUsuario, int $idPalabraJugada, int $letrasAcertadas, int $letrasFalladas, bool $palabraAcertada, int $puntuacionObtenida): array {
        if ($nombreUsuario === '' || $idPalabraJugada <= 0) {
            return ['success' => false, 'message' => 'Datos inválidos.'];
        }

        $declaracionUsuario = $this->pdo->prepare('SELECT id_jugador FROM JUGADORES WHERE login = ? LIMIT 1');
        $declaracionUsuario->execute([$nombreUsuario]);
        $filaJugador = $declaracionUsuario->fetch(PDO::FETCH_ASSOC);

        if (!$filaJugador) {
            return ['success' => false, 'message' => 'Jugador no encontrado.'];
        }
        $idJugador = $filaJugador['id_jugador'];

        $insertarPartida = $this->pdo->prepare('INSERT INTO PARTIDAS (id_jugador, id_palabra_jugada, fecha_partida, letras_acertadas, letras_falladas, palabra_acertada, puntuacion_obtenida) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $fechaActual = date('Y-m-d H:i:s');
        if ($insertarPartida->execute([$idJugador, $idPalabraJugada, $fechaActual, $letrasAcertadas, $letrasFalladas, $palabraAcertada, $puntuacionObtenida])) {
            return ['success' => true, 'urlRedireccion' => '../vistas/juego/V_configurar.php?login=' . urlencode($nombreUsuario) . '&ok=Partida+guardada'];
        } else {
            return ['success' => false, 'message' => 'Error al guardar la partida.'];
        }
    }
}
