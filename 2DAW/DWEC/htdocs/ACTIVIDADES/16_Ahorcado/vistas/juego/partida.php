<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partida</title>
    <?php
    $login = isset($_GET['login']) ? htmlspecialchars($_GET['login']) : '';
    $idPalabra = isset($_GET['id_palabra']) ? intval($_GET['id_palabra']) : 0;
    $palabra = isset($_GET['palabra']) ? htmlspecialchars($_GET['palabra']) : '';
    $maxfallos = isset($_GET['maxfallos']) ? intval($_GET['maxfallos']) : 6;
    ?>
</head>
<body>
    <h1>Partida de Ahorcado</h1>
    <p>Usuario: <?= $login ?></p>

    <div>
        <p>Fallos: <span id="fallos">0</span> / <span id="maxfallos"><?= $maxfallos ?></span></p>
        <p id="palabras">_ _ _ _ _</p>
        <div id="teclado"></div>
        <p id="mensaje"></p>
        <button id="start">Empezar</button>
        <button id="end">Terminar</button>
    </div>

    <form id="finalForm" action="../../contorlador/juego.php" method="post" style="display:none">
        <input type="hidden" name="action" value="finalizar">
        <input type="hidden" name="login" value="<?= $login ?>">
        <input type="hidden" name="id_palabra" value="<?= $idPalabra ?>">
        <input type="hidden" name="letras_acertadas" id="f_acertadas" value="0">
        <input type="hidden" name="letras_falladas" id="f_falladas" value="0">
        <input type="hidden" name="palabra_acertada" id="f_acertada" value="0">
        <input type="hidden" name="puntuacion_obtenida" id="f_puntos" value="0">
    </form>
    <script>
        // Config desde servidor
        const secreta = '<?= $palabra ?>'.toLowerCase();
        const maxfallos = <?= $maxfallos ?>;
        let adivinadas = new Set();
        let fallos = 0;
        let finalizado = false;

        function iniciarJuego() {
            adivinadas = new Set();
            fallos = 0;
            finalizado = false;
            document.getElementById('maxfallos').textContent = String(maxfallos);
            actualizarPalabra();
            actualizarFallos();
            establecerMensaje('');
            renderizarAlfabeto();
        }

        function terminarJuego() {
            finalizado = true;
            revelarPalabra();
            establecerMensaje('Partida terminada.');
            deshabilitarAlfabeto();
            enviarResultados(false);
        }

        function actualizarPalabra() {
            let display = '';
            for (let i = 0; i < secreta.length; i++) {
                if (adivinadas.has(secreta[i])) {
                    display += secreta[i];
                } else {
                    display += '_';
                }
                if (i < secreta.length - 1) display += ' ';
            }
            document.getElementById('palabras').textContent = display;
            verificarVictoria();
        }

        function revelarPalabra() {
            let mostrar = '';
            for (let i = 0; i < secreta.length; i++) {
                mostrar += secreta[i];
                if (i < secreta.length - 1) mostrar += ' ';
            }
            document.getElementById('palabras').textContent = mostrar;
        }

        function actualizarFallos() {
            document.getElementById('fallos').textContent = String(fallos);
        }

        function establecerMensaje(msg) {
            document.getElementById('mensaje').textContent = msg;
        }

        function renderizarAlfabeto() {
            const cont = document.getElementById('teclado');
            cont.innerHTML = '';
            const letras = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','Ñ','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
            for (let i = 0; i < letras.length; i++) {
                const btn = document.createElement('button');
                btn.textContent = letras[i];
                btn.className = 'letra';
                btn.disabled = finalizado;
                btn.addEventListener('click', () => elegir(letras[i].toLowerCase()));
                cont.appendChild(btn);
            }
        }

        function deshabilitarAlfabeto() {
            const botones = document.querySelectorAll('.letra');
            for (let i = 0; i < botones.length; i++) {
                botones[i].disabled = true;
            }
        }

        function elegir(letra) {
            if (finalizado || adivinadas.has(letra)) return;
            adivinadas.add(letra);
            if (secreta.indexOf(letra) !== -1) {
                actualizarPalabra();
                establecerMensaje('Bien');
            } else {
                fallos++;
                actualizarFallos();
                establecerMensaje('Letra incorrecta');
                if (fallos >= maxfallos) {
                    finalizado = true;
                    establecerMensaje('Has perdido, la palabra era: ' + secreta);
                    revelarPalabra();
                    deshabilitarAlfabeto();
                    enviarResultados(false);
                }
            }
        }

        function verificarVictoria() {
            let ganado = true;
            for (let i = 0; i < secreta.length; i++) {
                if (!adivinadas.has(secreta[i])) {
                    ganado = false;
                    break;
                }
            }
            if (ganado) {
                finalizado = true;
                establecerMensaje('¡Ganaste!');
                deshabilitarAlfabeto();
                enviarResultados(true);
            }
        }

        function enviarResultados(acertada) {
            const acertadas = Array.from(adivinadas).filter(l => secreta.includes(l)).length; // aproximado
            const form = document.getElementById('finalForm');
            document.getElementById('f_acertadas').value = acertadas;
            document.getElementById('f_falladas').value = fallos;
            document.getElementById('f_acertada').value = acertada ? '1' : '0';
            // puntuación simple
            const puntos = Math.max(0, (secreta.length * 10) - (fallos * 5));
            document.getElementById('f_puntos').value = puntos;
            form.submit();
        }

        document.getElementById('start').addEventListener('click', iniciarJuego);
        document.getElementById('end').addEventListener('click', terminarJuego);
    </script>
</body>
</html>