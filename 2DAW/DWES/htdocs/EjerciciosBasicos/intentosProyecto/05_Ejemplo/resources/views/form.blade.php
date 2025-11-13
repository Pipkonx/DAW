    <form action="" method="POST" enctype="multipart/form-data">
        <label>NIF/CIF:</label><br>
        <input type="text" name="nifCif" value="{{ $nifCif }}"><br>
        {!! \App\Models\Funciones::verErrores('nif_cif') !!}
        <br>

        <label>Persona de contacto:</label><br>
        <input type="text" name="personaNombre" value="{{ $personaNombre }}"><br>
        {!! \App\Models\Funciones::verErrores('nombre_persona') !!}
        <br>
        <label>Teléfono:</label><br>
        <input type="text" name="telefono" value="<?= htmlspecialchars($telefono) ?>"><br>
             {!! \App\Models\Funciones::verErrores('telefono') !!}
        <br>

        <label>Correo electrónico:</label><br>
        <input type="text" name="correo" value="<?= htmlspecialchars($correo) ?>"><br>
        {!! \App\Models\Funciones::verErrores('correo') !!}
        <br>

        <label>Descripción de la tarea:</label><br>
        <textarea name="descripcionTarea"><?= htmlspecialchars($descripcionTarea) ?></textarea><br>
        {!! \App\Models\Funciones::verErrores('descripcion_tarea') !!}
        <br>

        <label>Dirección:</label><br>
        <input type="text" name="direccionTarea" value="<?= htmlspecialchars($direccionTarea) ?>"><br><br>

        <label>Población:</label><br>
        <input type="text" name="poblacion" value="<?= htmlspecialchars($poblacion) ?>"><br><br>

        <label>Código Postal:</label><br>
        <input type="text" name="codigoPostal" value="<?= htmlspecialchars($codigoPostal) ?>"><br><br>

        <label>Provincia:</label><br>
        <select name="provincia">
            <option value="">Seleccione provincia</option>
            <?php \App\Models\Funciones::mostrarProvincias($provincia) ?>
        </select><br>
        {!! \App\Models\Funciones::verErrores('provincia') !!}
        <br>

        <label>Estado:</label><br>
        <select name="estadoTarea">
            <option value="B" <?= $estadoTarea == "B" ? "selected" : "" ?>>Esperando ser aprobada</option>
            <option value="P" <?= $estadoTarea == "P" ? "selected" : "" ?>>Pendiente</option>
            <option value="R" <?= $estadoTarea == "R" ? "selected" : "" ?>>Realizada</option>
            <option value="C" <?= $estadoTarea == "C" ? "selected" : "" ?>>Cancelada</option>
        </select><br><br>

        <label>Operario encargado:</label><br>
        <select name="operarioEncargado">
            <option value="">Seleccione operario</option>
            <option value="Juan Pérez" <?= $operarioEncargado == "Juan Pérez" ? "selected" : "" ?>>Juan Pérez</option>
            <option value="María López" <?= $operarioEncargado == "María López" ? "selected" : "" ?>>María López</option>
            <option value="Carlos Ruiz" <?= $operarioEncargado == "Carlos Ruiz" ? "selected" : "" ?>>Carlos Ruiz</option>
            <option value="Ana María Fernández" <?= $operarioEncargado == "Ana María Fernández" ? "selected" : "" ?>>Ana María Fernández</option>
            <option value="Sara Martínez" <?= $operarioEncargado == "Sara Martínez" ? "selected" : "" ?>>Sara Martínez</option>
            <option value="Lucía Hurtado" <?= $operarioEncargado == "Lucía Hurtado" ? "selected" : "" ?>>Lucía Hurtado</option>
        </select><br><br>

        <label>Fecha de realización:</label><br>
        <input type="date" name="fechaRealizacion" value="<?= htmlspecialchars($fechaRealizacion) ?>"><br>
        {!! \App\Models\Funciones::verErrores('fechaRealizacion') !!}
        <br>

        <label for="anotacionesAnteriores">Anotaciones anteriores:</label><br>
        <textarea id="anotacionesAnteriores" name="anotacionesAnteriores"><?= htmlspecialchars($anotacionesAnteriores) ?></textarea><br><br>

        <label for="fichero_resumen">Fichero resumen:</label>
        <input type="file" id="fichero_resumen" name="fichero_resumen"><br><br>

        <label for="fotos">Fotos del trabajo:</label>
        <input type="file" id="fotos" name="fotos[]" multiple><br><br>

        <a class="btn btn-cancel" href="{!! url('/') !!}">Cancelar</a>  <input type="submit" value="Crear tarea">
    </form>
