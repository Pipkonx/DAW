<h2>Tareas</h2>
<form method="POST">
    <label for="nif">NIF</label>
    <!-- el htmlspecialchars es para evitar inyeccion de codigo -->
    <input type="text" name="nif" placeholder="99999999A" value="<?= isset($datos['nif']) ? $datos['nif'] : '' ?>" ><br>

    <label for="nombre">Nombre: </label>
    <input type="text" name="nombre" placeholder="Pepe" value="<?= isset($datos['nombre']) ? $datos['nombre'] : '' ?>" ><br>

    <label for="apellido">Apellido: </label>
    <input type="text" name="apellido" placeholder="Vazquez" value="<?= isset($datos['apellido']) ? $datos['apellido'] : '' ?>"><br>

    <label for="telefono">Telefono: </label>
    <input type="tel" name="telefono" placeholder="999999999" value="<?= isset($datos['telefono']) ? $datos['telefono'] : '' ?>" ><br>

    <label for="descripcion">Descripcion: </label>
    <input type="text" name="descripcion" value="<?= isset($datos['descripcion']) ? $datos['descripcion'] : '' ?>"><br>

    <label for="email">Email: </label>
    <input type="email" name="email" placeholder="email@email.com" value="<?= isset($datos['email']) ? $datos['email'] : '' ?>" ><br>


    <label for="direccion">Direccion: </label>
    <input type="text" name="direccion" placeholder="av patata patata 1 1a" value="<?= isset($datos['direccion']) ? $datos['direccion'] : '' ?>" ><br>

    <label for="poblacion">Poblacion: </label>
    <select name="poblacion" id="poblacion" >
        <option value="es" <?= (isset($datos['poblacion']) && $datos['poblacion'] === 'es') ? 'selected' : '' ?>>España</option>
    </select><br>

    <label for="cp">CP</label>
    <input type="number" name="cp" placeholder="21000" value="<?= isset($datos['cp']) ? $datos['cp'] : '' ?>"><br>

    <label for="provincia">Provincia: </label>
    <select name="provincia" id="provincia">
        <option value="21001" <?= (isset($datos['provincia']) && $datos['provincia'] === '21001') ? 'selected' : '' ?>>Huelva</option>
    </select><br>

    <label for="estado">Estado: </label>
    <select name="estado" id="estado">
        <option value="B" <?= (isset($datos['estado']) && $datos['estado'] === 'B') ? 'selected' : '' ?>>Esperando ser aprobado</option>
        <option value="P" <?= (isset($datos['estado']) && $datos['estado'] === 'P') ? 'selected' : '' ?>>Pendiente</option>
        <option value="R" <?= (isset($datos['estado']) && $datos['estado'] === 'R') ? 'selected' : '' ?>>Realizada</option>
        <option value="C" <?= (isset($datos['estado']) && $datos['estado'] === 'C') ? 'selected' : '' ?>>Cancelada</option>
    </select><br>

    <label for="operario">Operario: </label>
    <input type="text" name="operario" placeholder="Nombre del operario" value="<?= isset($datos['operario']) ? $datos['operario'] : '' ?>"><br>

    <label for="Frealizacion">Fecha de realizacion</label>
    <input type="date" name="Frealizacion" value="<?= isset($datos['Frealizacion']) ? $datos['Frealizacion'] : '' ?>"><br>

    <label for="Aanteriores">Anotaciones anteriores: </label>
    <input type="text" placeholder="Notas anterires" name="Aanteriores" value="<?= isset($datos['Aanteriores']) ? $datos['Aanteriores'] : '' ?>"><br>

    <label for="fichero">Fichero</label>
    <input type="file" name="fichero"><br>

    <input type="submit" value="Guardar">
</form>
<a href="index.php">Volver</a>