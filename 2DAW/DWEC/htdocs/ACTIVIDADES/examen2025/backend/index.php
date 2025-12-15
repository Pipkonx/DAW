<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

</head>
<body>
    <div class="container">
            <h1>Gestión de Veterinaria</h1>

            <!-- Sección de Estadísticas -->
            <div class="estadisticas-container">
                <h2>Estadísticas</h2>
                <p>Total de Clientes: <span id="totalClientes"></span></p>
                <p>Total de Mascotas: <span id="totalMascotas"></span></p>
                <p>Especie más Común: <span id="especieMasComun"></span></p>
                <p>Cliente con más Mascotas: <span id="clienteMasMascotas"></span></p>
            </div>

        <form id="formularioCliente">
            <div>
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div>
                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" required>
            </div>
            <div>
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email">
            </div>
            <div>
                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion">
            </div>
            <button type="submit">Crear Cliente</button>
        </form>

        <h2>Crear Nueva Mascota</h2>
        <form id="formularioMascota">
            <div>
                <label for="mascotaNombre">Nombre:</label>
                <input type="text" id="mascotaNombre" name="nombre" required>
            </div>
            <div>
                <label for="mascotaEspecie">Especie:</label>
                <select id="mascotaEspecie" name="especie" required>
                    <option value="">Seleccione</option>
                    <option value="Perro">Perro</option>
                    <option value="Gato">Gato</option>
                    <option value="Ave">Ave</option>
                    <option value="Reptil">Reptil</option>
                </select>
            </div>
            <div>
                <label for="mascotaRaza">Raza:</label>
                <input type="text" id="mascotaRaza" name="raza">
            </div>
            <div>
                <label for="mascotaFechaNacimiento">Fecha Nac.:</label>
                <input type="date" id="mascotaFechaNacimiento" name="fecha_nacimiento">
            </div>
            <div>
                <label for="mascotaCliente">Propietario:</label>
                <select id="mascotaCliente" name="id_cliente" required>
                    <!-- Los clientes con el DataTables -->
                </select>
            </div>
            <button type="submit">Crear Mascota</button>
        </form>

        <h2>Listado de Clientes</h2>
        <table id="tablaClientes">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Dirección</th>
                    <th>Fecha de Alta</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los clientes con el DataTables -->
            </tbody>
        </table>

        <div id="areaMascotas">
            <!-- Las mascotas con el js -->
        </div>

        <h2>Listado de Mascotas del Cliente Seleccionado</h2>
        <table id="tablaMascotas">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Especie</th>
                    <th>Raza</th>
                    <th>Fecha Nacimiento</th>
                    <th>Icono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- La mascota con el DataTables -->
            </tbody>
        </table>
    </div>

    <!-- Para editar cliente -->
    <div id="modalEditarCliente" class="modal">
        <!-- los modal son para editar y eliminar cliente -->
        <div class="modal-contenido">
            <span class="cerrar-modal">&times;</span>
            <h2>Editar Cliente</h2>
            <form id="formularioEditarCliente">
                <div>
                    <label for="editarNombre">Nombre:</label>
                    <input type="text" id="editarNombre" name="nombre" required>
                </div>
                <div>
                    <label for="editarApellidos">Apellidos:</label>
                    <input type="text" id="editarApellidos" name="apellidos" required>
                </div>
                <div>
                    <label for="editarTelefono">Teléfono:</label>
                    <input type="text" id="editarTelefono" name="telefono" required>
                </div>
                <div>
                    <label for="editarEmail">Email:</label>
                    <input type="email" id="editarEmail" name="email">
                </div>
                <div>
                    <label for="editarDireccion">Dirección:</label>
                    <input type="text" id="editarDireccion" name="direccion">
                </div>
                <button type="submit">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <script src="./js/app.js"></script>
</body>
</html>