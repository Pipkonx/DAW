<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

</head>
<body>
    <div class="contenedor-principal">
        <div class="columna-izquierda">
            <div class="contenedor">
                <h1>Gestión de Veterinaria</h1>

                <!-- Sección de Estadísticas -->
                <div class="estadisticas-container">
                    <h2>Estadísticas</h2>
                    <p>Total de Clientes: <span id="total-clientes"></span></p>
                    <p>Total de Mascotas: <span id="total-mascotas"></span></p>
                    <p>Especie más Común: <span id="especie-mas-comun"></span></p>
                    <p>Cliente con más Mascotas: <span id="cliente-mas-mascotas"></span></p>
                </div>

                <h2>Crear Nuevo Cliente</h2>
                <form id="formulario-cliente">
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
                <form id="formulario-mascota">
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
                        <input type="date" id="mascotaFechaNacimiento" name="fech-nacimiento">
                    </div>
                    <div>
                        <label for="mascotaCliente">Propietario:</label>
                        <select id="mascotaCliente" name="id_cliente" required>
                            <!-- Los clientes con el DataTables -->
                        </select>
                    </div>
                    <button type="submit">Crear Mascota</button>
                </form>
            </div>
        </div>

        <div class="columna-derecha">
            <div class="contenedor">
                <h2>Listado de Clientes</h2>
                <table id="tabla-clientes">
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

                <div id="area-mascotas">
                    <!-- Las mascotas con el js -->
                </div>

                <h2 id="titulo-mascotas-cliente" style="display: none;">Listado de Mascotas del Cliente Seleccionado</h2>
                <table id="tabla-mascotas" style="display: none;">
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
        </div>
    </div>

    <form id="formulario-editar-cliente" style="display: none;">
        <h2>Editar Cliente</h2>
        <span class="cerrar-formulario-edicion">&times;</span>
            <div>
                <label for="editar-nombre">Nombre:</label>
                <input type="text" id="editar-nombre" name="nombre" required>
            </div>
            <div>
                <label for="editar-apellidos">Apellidos:</label>
                <input type="text" id="editar-apellidos" name="apellidos" required>
            </div>
            <div>
                <label for="editar-telefono">Teléfono:</label>
                <input type="text" id="editar-telefono" name="telefono" required>
            </div>
            <div>
                <label for="editar-email">Email:</label>
                <input type="email" id="editar-email" name="email">
            </div>
            <div>
                <label for="editar-direccion">Dirección:</label>
                <input type="text" id="editar-direccion" name="direccion">
            </div>
            <button type="submit">Guardar Cambios</button>
        </form>
    <script src="./js/app.js"></script>
</body>
</html>