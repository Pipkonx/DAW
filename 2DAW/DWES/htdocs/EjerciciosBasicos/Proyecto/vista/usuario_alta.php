<h2>Tareas</h2>
<form method="POST">
    <label for="nif">NIF</label>
    <input type="text" name="nif" placeholder="99999999A" value="<?= isset($datos['nif']) ? $datos['nif'] : '' ?>" required><br>

    <label for="nombre">Nombre: </label>
    <input type="text" name="nombre" placeholder="Pepe" required><br>

    <label for="apellido">Apellido: </label>
    <input type="text" name="apellido" placeholder="Vazquez"><br>

    <label for="telefono">Telefono: </label>
    <input type="tel" name="telefono" placeholder="999999999" required><br>

    <label for="descripcion">Descripcion: </label>
    <input type="text" name="descripcion"><br>

    <label for="email">Email: </label>
    <input type="email" name="email" placeholder="email@email.com" required><br>


    <label for="direccion">Direccion: </label>
    <input type="direccion" name="direccion" placeholder="av patata patata 1 1a" required><br>

    <label for="poblacion">Poblacion: </label>
    <select name="poblacion" id="poblacion" required>
        <option value="es">España</option>
    </select><br>

    <label for="cp">CP</label>
    <input type="number" name="cp" value="cp" placeholder="21000" ><br>

    <label for="provincia">Provincia: </label>
    <select name="provincia" id="provincia">
        <option value="21001">Huelva</option>
    </select><br>

    <label for="estado">Estado: </label>
    <select name="estado" id="estado">
        <option value="B">Esperando ser aprobado</option>
        <option value="P">Pendiente</option>
        <option value="R">Realizada</option>
        <option value="C">Cancelada</option>
    </select><br>

    <label for="operario">Operario: </label>
    <input type="text" name="operario" placeholder="Nombre del operario" ><br>

    <label for="Frealizacion">Fecha de realizacion</label>
    <input type="date" name="Frealizacion"><br>

    <label for="Aanteriores">Anotaciones anteriores: </label>
    <input type="text" placeholder="Notas anterires" name="Aanteriores"><br>

    <label for="fichero">Fichero</label>
    <input type="file" value="fichero" name="fichero"><br>

    <input type="submit" value="Guardar">
</form>
<a href="index.php">Volver</a>