<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Juego</title>
    <?php
    $login = isset($_GET['login']) ? htmlspecialchars($_GET['login']) : '';
    $error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
    $ok = isset($_GET['ok']) ? htmlspecialchars($_GET['ok']) : '';
    ?>
</head>

<body>
    <h1>Configurar Juego</h1>
    <?php if ($error): ?>
        <p><?= $error ?></p>
    <?php endif; ?>
    <?php if ($ok): ?>
        <p><?= $ok ?></p>
    <?php endif; ?>

    <p>Usuario: <?= $login ? $login : 'Invitado' ?></p>

    <form action="../../contorlador/juego.php" method="post">
        <input type="hidden" name="action" value="start">
        <input type="hidden" name="login" value="<?= $login ?>">

        <label for="categoria">Categoría</label>
        <select id="categoria" name="categoria" required>
            <option value="">Cargando...</option>
        </select>

        <label for="dificultad">Dificultad</label>
        <select id="dificultad" name="dificultad" required>
            <option value="facil">Fácil</option>
            <option value="media">Media</option>
            <option value="dificil">Difícil</option>
        </select>

        <button type="submit">Comenzar</button>
    </form>

    <script>
        fetch('../../contorlador/juego.php?action=categorias')
            .then(r => r.json())
            .then(data => {
                const sel = document.getElementById('categoria');
                sel.innerHTML = '';
                if (!Array.isArray(data) || data.length === 0) {
                    sel.innerHTML = '<option value="">Sin categorías</option>';
                    sel.disabled = true;
                    return;
                }
                data.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id_categoria;
                    opt.textContent = c.nombre_categoria;
                    sel.appendChild(opt);
                });
            })
            // el catch es para manejar los errores en la carga de categorias
            .catch(() => {
                const sel = document.getElementById('categoria');
                sel.innerHTML = '<option value="">Error cargando categorías</option>';
                sel.disabled = true;
            });
    </script>
    <p>
        <a href="mis_partidas.php?login=<?= urlencode($login) ?>">Ver mis partidas</a>
    </p>
</body>

</html>