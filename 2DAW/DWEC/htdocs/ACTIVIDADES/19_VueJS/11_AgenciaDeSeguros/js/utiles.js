document.addEventListener('DOMContentLoaded', function () {
    window.app = new Vue({
        el: '#app',
        data: {
            // Datos de autenticación
            inputUsuario: '',
            inputPassword: '',
            
            // Listas generales de datos
            usuarios: [],
            provincias: [],
            municipios: [], // Municipios filtrados por provincia
            municipiosTodos: [], // Todos los municipios para búsquedas globales
            logins: [], // Usuarios del sistema (empleados/admin)
            pagos: [], // Pagos de la póliza seleccionada
            todasPolizas: [], // Todas las pólizas registradas
            
            // Objetos de trabajo (Formularios)
            usuario: { id: 0, nombre: '', apellidos: '', tlf: 123456789, localidad: '', cp: 12345, provincia: '', tipo: false, contrasena: '', login: '' },
            nuevoLogin: { id: '', nombre: '', password: '', tipo: 1 },
            polizaActual: null, // Póliza que se está gestionando actualmente
            nuevoPago: { importe: '', fecha: '' },
            
            // Estado de la interfaz (Qué panel se muestra)
            loginMensaje: '',
            logueado: true,
            mostrarFormNuevo: false,
            mostrarFormUsuario: false,
            mostrarFormPolizaVar: false,
            mostrarFormPagos: false,
            mostrarFormGestionLogins: false,
            mostrarFormNuevoLogin: false,
            mostrarDetallesUsuario: false,
            
            // Caché de pólizas por usuario para el listado expandible
            polizasData: {},
            
            // Información de sesión
            isAdmin: typeof sessionStorage !== 'undefined' ? sessionStorage.getItem('tipoUsuario') == '0' : false,
            tipologin: typeof sessionStorage !== 'undefined' ? sessionStorage.getItem('tipologin') || sessionStorage.getItem('tipoUsuario') : null,
            nombreLogin: typeof sessionStorage !== 'undefined' ? sessionStorage.getItem('nombreLogin') : '',
            
            // Filtros de búsqueda y rangos
            usuarioDesde: '',
            usuarioHasta: '',
            fechaDesde: '',
            fechaHasta: new Date().getFullYear() + '-' + String(new Date().getMonth() + 1).padStart(2, '0') + '-' + String(new Date().getDate()).padStart(2, '0'),
            intencionVerTodos: false,
            
            // Detalles del usuario para vista individual
            usuarioDetalles: null,
            
            // Fecha de hoy en formato YYYY-MM-DD
            fechaHoy: new Date().getFullYear() + '-' + String(new Date().getMonth() + 1).padStart(2, '0') + '-' + String(new Date().getDate()).padStart(2, '0'),
        },
        computed: {
            totalPagado: function () {
                return this.pagos.reduce((sum, pago) => sum + parseFloat(pago.importe || 0), 0);
            },
            usuariosDropdownRango: function () {
                if (!this.usuarios) return [];
                return [...this.usuarios].sort((a, b) => {
                    const nameA = (a.nombre + ' ' + a.apellidos).toLowerCase();
                    const nameB = (b.nombre + ' ' + b.apellidos).toLowerCase();
                    return nameA.localeCompare(nameB);
                });
            }
        },

        mounted: function () {
            if (document.getElementById('myTable')) {
                Promise.all([
                    this.cargarProvincias(),
                    this.cargarTodosMunicipios()
                ]).then(() => {
                    this.listarClientes();
                });
            }
            if (document.getElementById('myTablePolizas')) {
                Promise.all([
                    this.cargarProvincias(),
                    this.cargarTodosMunicipios(),
                    this.cargarTodosUsuarios()
                ]).then(() => {
                    this.listarTodasPolizas();
                });
            }
        },
        methods: {
            // Método central para peticiones API. Simplifica el uso de fetch y centraliza los errores.
            peticion: function (url, options = {}) {
                return fetch(url, options)
                    .then(res => {
                        if (!res.ok) throw new Error('Error de red');
                        return res.json();
                    })
                    .catch(err => {
                        console.error("Error en la petición:", err);
                        Swal.fire("Error", "No se pudo conectar con el servidor", "error");
                        throw err;
                    });
            },

            // Muestra una alerta de éxito o error basada en la respuesta del servidor.
            mensaje: function (res, exitoMsg, callback) {
                if (res.status === 'success' || res.status === 'info' || res.ok) {
                    Swal.fire("¡Hecho!", res.message || exitoMsg, "success").then(() => {
                        if (callback) callback(res);
                    });
                } else {
                    Swal.fire("Error", res.message || res.error || "Ocurrió un problema", "error");
                }
            },

            // Cierra todos los paneles laterales o formularios abiertos para evitar solapamientos.
            cerrarTodosLosPaneles: function () {
                this.mostrarFormUsuario = false;
                this.mostrarFormGestionLogins = false;
                this.mostrarFormPolizaVar = false;
                this.mostrarFormPagos = false;
                this.mostrarDetallesUsuario = false;
                this.mostrarFormNuevoLogin = false;
            },

            // Elimina una póliza tras confirmar con el usuario. Recarga el listado correspondiente.
            deletePoliza: function (idPoliza, idUsuario = null) {
                Swal.fire({
                    title: "¿Eliminar póliza?",
                    text: "¿Estás seguro de que quieres eliminar esta póliza?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    confirmButtonText: "Sí, eliminar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.peticion('php/polizas/deletePoliza.php?idPoliza=' + idPoliza)
                            .then(data => {
                                this.mensaje(data, "Póliza eliminada correctamente", () => {
                                    // Decidimos qué recargar según dónde estemos
                                    if (document.getElementById('myTablePolizas')) {
                                        this.listarTodasPolizas(); // Vista global
                                    } else if (idUsuario) {
                                        this.recargarPolizasUsuario(idUsuario); // Fila expandida en clientes
                                    } else {
                                        location.reload();
                                    }
                                });
                            });
                    }
                });
            },

            // Fuerza la actualización de las pólizas que se ven al expandir una fila de cliente.
            recargarPolizasUsuario: function (idUsuario) {
                const tr = $(`#myTable tr[data-id="${idUsuario}"]`);
                if (tr.length) {
                    const table = $('#myTable').DataTable();
                    const row = table.row(tr);
                    this.renderPolizasChildRow(idUsuario, row, tr);
                }
            },

            renderPolizasChildRow: function (idUsuario, row, tr) {
                row.child('<div class="p-2">Cargando pólizas...</div>').show();
                tr.addClass('shown');

                fetch(`php/polizas/GETpoliza.php?idUsuario=${idUsuario}`)
                    .then(response => response.json())
                    .then(polizas => {
                        // Verificación de seguridad: Si no es un array, probablemente sea un error del servidor
                        if (!Array.isArray(polizas)) {
                            console.error("Error: Se esperaba un array de pólizas, se recibió:", polizas);
                            row.child('<div class="p-2 text-danger">Error al cargar las pólizas o sesión expirada.</div>').show();
                            return;
                        }

                        this.$set(this.polizasData, idUsuario, polizas);

                        let html = `<div class="p-2 bg-light" id="polizas-container-${idUsuario}">`;

                        // Vista Listado
                        html += `<div id="polizas-list-${idUsuario}">`;
                        html += `<div class="mb-2"><button class="btn btn-sm btn-success" onclick="app.mostrarFormPoliza(${idUsuario})">Añadir Póliza</button></div>`;

                        if (polizas.length === 0) {
                            html += '<div class="text-muted">Este usuario no tiene pólizas registradas.</div>';
                        } else {
                            html += '<table class="table table-sm table-bordered mb-0">';
                            html += '<thead class="table-secondary"><tr><th>Importe</th><th>Fecha</th><th>Estado</th><th>Observaciones</th><th>Acciones</th></tr></thead>';
                            html += '<tbody>';
                            const mapEstados = { '0': 'cobrado', '1': 'a cuenta', '2': 'liquidada', '3': 'anulada', '4': 'pre-anulada' };
                            polizas.forEach(p => {
                                let fechaMostrada = p.fecha;
                                if (p.fecha && p.fecha.length >= 10) {
                                    // Extract strictly the first 10 chars (YYYY-MM-DD) natively
                                    let dateOnly = p.fecha.substring(0, 10);
                                    let fParts = dateOnly.split('-');
                                    if (fParts.length === 3) {
                                        fechaMostrada = `${fParts[2]}/${fParts[1]}/${fParts[0]}`;
                                    }
                                }
                                let estadoTexto = mapEstados[p.estado] !== undefined ? mapEstados[p.estado] : p.estado;

                                // Determinar clase según estado
                                let statusClass = '';
                                if (p.estado == '0') statusClass = 'status-cobrado';
                                else if (p.estado == '1') statusClass = 'status-acuenta';
                                else if (p.estado == '2') statusClass = 'status-liquidada';
                                else if (p.estado == '3') statusClass = 'status-anulada';
                                else if (p.estado == '4') statusClass = 'status-preanulada';

                                html += `<tr class="${statusClass}">
                                    <td>${parseFloat(p.importe).toFixed(2)} €</td>
                                    <td>${fechaMostrada}</td>
                                    <td>${estadoTexto}</td>
                                    <td>${p.observaciones}</td>
                                    <td>
                                        ${p.estado != '0' && (p.importe - (p.total_pagado || 0)) > 0 ? `<button class="btn btn-info btn-sm" onclick="app.mostrarPagos(${p.idPoliza}, ${p.importe}, '${p.estado}', ${idUsuario})">Pagos</button>` : ''}
                                        <button class="btn btn-warning btn-sm" onclick="app.mostrarFormPoliza(${idUsuario}, ${p.idPoliza})">Modificar</button>
                                        <button class="btn btn-sm btn-danger" onclick="app.deletePoliza(${p.idPoliza}, ${idUsuario})">Eliminar</button>
                                    </td>
                                </tr>`;
                            });
                            html += '</tbody></table>';
                        }
                        html += `</div>`; // Fin Listado
                        html += `</div>`;
                        row.child(html).show();
                    })
                    .catch(err => {
                        console.error("Error al cargar pólizas:", err);
                        row.child('<div class="p-2 text-danger">Error al cargar las pólizas.</div>').show();
                    });
            },

            // Prepara y muestra el panel de gestión de pagos de una póliza específica.
            mostrarPagos: function (idPoliza, importe, estado, idUsuario) { 
                this.cerrarTodosLosPaneles();
                this.polizaActual = { idPoliza: idPoliza, importe: importe, estado: estado, idUsuario: idUsuario };
                this.nuevoPago.importe = '';
                // Establecemos la fecha de hoy por defecto para el nuevo pago
                const d = new Date();
                const hoy = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
                this.nuevoPago.fecha = hoy;
                this.cargarPagos(idPoliza);
                this.mostrarFormPagos = true;
            },

            // Obtiene la lista de pagos realizados para una póliza desde el servidor.
            cargarPagos: function (idPoliza) {
                this.peticion(`php/pagos/GETpagos.php?idPoliza=${idPoliza}`)
                    .then(data => {
                        this.pagos = Array.isArray(data) ? data : [];
                    });
            },

            // Calcula el importe restante y ofrece al usuario realizar un pago total automático.
            pagarRestante: function () {
                const restante = parseFloat(this.polizaActual?.importe || 0) - this.totalPagado;
                if (restante > 0) {
                    Swal.fire({
                        title: "¿Pagar restante?",
                        text: `¿Está seguro de que desea realizar un pago por el importe restante de ${parseFloat(restante).toFixed(2)} €?`,
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#6c757d",
                        confirmButtonText: "Sí, pagar",
                        cancelButtonText: "Cancelar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.nuevoPago.importe = parseFloat(restante).toFixed(2);
                            this.$nextTick(() => {
                                this.guardarPago(); // Ejecutamos el guardado inmediatamente tras asignar el importe
                            });
                        }
                    });
                } else {
                    Swal.fire("Información", "No hay importe restante a pagar.", "info");
                }
            },

            // Envía un nuevo pago al servidor. Valida que no se pague más del total de la póliza.
            guardarPago: function () {
                if (!this.nuevoPago.importe || this.nuevoPago.importe <= 0 || !this.nuevoPago.fecha) {
                    Swal.fire("Atención", "Complete importe y fecha correctamente.", "warning");
                    return;
                }
                const restante = parseFloat(this.polizaActual?.importe || 0) - this.totalPagado;
                // Margen de error pequeño para flotantes
                if (parseFloat(this.nuevoPago.importe) > (restante + 0.01)) {
                    Swal.fire("Error", `Máximo permitido: ${restante.toFixed(2)} €`, "error");
                    return;
                }

                this.peticion('php/pagos/addPago.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `idPoliza=${this.polizaActual.idPoliza}&importe=${this.nuevoPago.importe}&fecha=${this.nuevoPago.fecha}`
                }).then(res => {
                    this.mensaje(res, "Pago registrado correctamente", () => {
                        this.nuevoPago.importe = '';
                        this.cargarPagos(this.polizaActual.idPoliza);
                        // Si la póliza se ha pagado por completo, actualizamos el estado visualmente
                        if (res.fully_paid) {
                            this.polizaActual.estado = '0'; // Forzamos estado "Cobrado" en la UI
                            if (document.getElementById('myTablePolizas')) this.listarTodasPolizas();
                            else if (this.polizaActual.idUsuario) this.recargarPolizasUsuario(this.polizaActual.idUsuario);
                        }
                    });
                });
            },

            borrarPago: function (idPago) {
                Swal.fire({
                    title: "¿Eliminar pago?",
                    text: "¿Estás seguro?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    confirmButtonText: "Sí, eliminar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.peticion('php/pagos/deletePago.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `idPago=${idPago}`
                        }).then(res => {
                            this.mensaje(res, "Pago eliminado", () => {
                                this.cargarPagos(this.polizaActual.idPoliza);
                            });
                        });
                    }
                });
            },

            // Prepara y abre el formulario para añadir o modificar una póliza.
            mostrarFormPoliza: function (idUsuario, idPoliza = null) {
                this.cerrarTodosLosPaneles();
                this.mostrarFormPolizaVar = true; // Mostramos el panel primero para que los elementos existan en el DOM

                this.$nextTick(() => {
                    // Ahora que el panel es visible, podemos usar jQuery para rellenar los campos
                    $('#form-poliza-id-usuario').val(idUsuario || '');

                    if (idPoliza) {
                        // Modo Edición
                        $('#polizaModalTitle').text('Modificar Póliza');
                        $('#form-poliza-id').val(idPoliza);

                        // Buscamos los datos de la póliza en nuestros arrays locales
                        let poliza = null;
                        if (idUsuario && this.polizasData[idUsuario]) {
                            poliza = this.polizasData[idUsuario].find(p => p.idPoliza == idPoliza);
                        }
                        if (!poliza && this.todasPolizas.length > 0) {
                            poliza = this.todasPolizas.find(p => p.idPoliza == idPoliza);
                        }

                        // Si encontramos la póliza, rellenamos los campos (Precarga)
                        if (poliza) {
                            $('#form-poliza-importe').val(poliza.importe);
                            $('#form-poliza-fecha').val(poliza.fecha ? poliza.fecha.split(' ')[0] : '');
                            $('#form-poliza-estado').val(poliza.estado);
                            $('#form-poliza-observaciones').val(poliza.observaciones);
                        }
                    } else {
                        // Modo Creación
                        $('#polizaModalTitle').text('Añadir Póliza');
                        $('#form-poliza-id').val('');
                        $('#form-poliza-importe').val('');
                        
                        // Establecemos la fecha de hoy automáticamente
                        const d = new Date();
                        const hoy = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
                        $('#form-poliza-fecha').val(hoy);

                        $('#form-poliza-estado').val('0'); // Por defecto "Cobrado"
                        $('#form-poliza-observaciones').val('');
                    }
                });
            },

            ocultarFormPoliza: function () {
                this.mostrarFormPolizaVar = false;
            },

            // Recoge los datos del formulario de póliza (vía jQuery por simplicidad con modales) y los guarda.
            guardarPoliza: function () {
                const idUsuario = $('#form-poliza-id-usuario').val();
                const idPoliza = $('#form-poliza-id').val();
                const data = {
                    importe: $('#form-poliza-importe').val(),
                    fecha: $('#form-poliza-fecha').val(),
                    estado: $('#form-poliza-estado').val(),
                    observaciones: $('#form-poliza-observaciones').val()
                };

                if (!idUsuario && !idPoliza) {
                    Swal.fire("Atención", "Seleccione un asegurado", "warning");
                    return;
                }

                // Si hay idPoliza es un UPDATE, si no es un INSERT
                let endpoint = idPoliza ? 'php/polizas/modificarPoliza.php' : 'php/polizas/addPoliza.php';
                if (idPoliza) data.idPoliza = idPoliza;
                else data.idUsuario = idUsuario;

                this.peticion(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                }).then(res => {
                    this.mensaje(res, "Póliza guardada", () => {
                        this.mostrarFormPolizaVar = false;
                        // Actualizamos la tabla donde estemos trabajando
                        if (document.getElementById('myTablePolizas')) this.listarTodasPolizas();
                        else this.recargarPolizasUsuario(idUsuario);
                    });
                });
            },

            // Muestra una tarjeta con toda la información detallada de un asegurado sin usar modales.
            verDetallesUsuario: function (idUsuario) {
                this.cerrarTodosLosPaneles();
                const usuario = this.usuarios.find(u => u.id == idUsuario);
                if (!usuario) {
                    Swal.fire("Error", "No se encontró información del asegurado.", "error");
                    return;
                }

                // Buscamos los nombres legibles de provincia y localidad
                const prov = this.provincias.find(p => p.id == usuario.provincia);
                usuario.provinciaNombre = prov ? prov.provincia : usuario.provincia || 'No especificada';

                const mun = this.municipiosTodos.find(m => m.id == usuario.localidad);
                usuario.localidadNombre = mun ? mun.municipio : usuario.localidad || 'No especificada';

                // Texto amigable para el tipo de cliente
                usuario.tipoTexto = (usuario.tipo == 1 || usuario.tipo === true || String(usuario.tipo) === 'true') ? 'Empresa' : 'Particular';

                // Obtenemos sus pólizas para mostrarlas en la tarjeta de resumen
                usuario.polizas = this.todasPolizas.filter(p => p.idUsuario == idUsuario);

                this.usuarioDetalles = usuario;
                this.mostrarDetallesUsuario = true;

                // Desplazamos la vista suavemente hasta la nueva sección de detalles
                this.$nextTick(() => {
                    const el = document.getElementById('seccion-detalles-usuario');
                    if (el) el.scrollIntoView({ behavior: 'smooth' });
                });
            },

            cargarTodosUsuarios: function () {
                return fetch('php/usuarios/GETusuario.php')
                    .then(response => response.json())
                    .then(data => {
                        this.usuarios = data;
                    });
            },
            verTodosRango: function () {
                this.intencionVerTodos = true;
                this.usuarioDesde = '';
                this.usuarioHasta = '';
                this.fechaDesde = '';
                this.fechaHasta = '';
                this.$nextTick(() => { this.aplicarFiltroDataTables(); });
            },
            cambioFiltroRango: function () {
                this.intencionVerTodos = false;
                this.$nextTick(() => { this.aplicarFiltroDataTables(); });
            },
            aplicarFiltroDataTables: function () {
                const tableId = '#myTablePolizas';
                if (!$.fn.DataTable.isDataTable(tableId)) return;
                const table = $(tableId).DataTable();

                // Limpiamos búsquedas previas de columna para evitar conflictos
                table.column(1).search('');

                // Filtro personalizado para DataTables
                $.fn.dataTable.ext.search.push(
                    (settings, data, dataIndex) => {
                        if (settings.nTable.id !== 'myTablePolizas') return true;

                        // 1. Filtro por Rango de Usuarios
                        let pasaUsuario = true;
                        if (!this.intencionVerTodos && (this.usuarioDesde || this.usuarioHasta)) {
                            const nombreEnFila = data[1]; // Columna Asegurado
                            const sortedUsers = this.usuariosDropdownRango;

                            let idxDesde = 0;
                            if (this.usuarioDesde) {
                                idxDesde = sortedUsers.findIndex(u => String(u.id) === String(this.usuarioDesde));
                            }

                            let idxHasta = sortedUsers.length - 1;
                            if (this.usuarioHasta) {
                                idxHasta = sortedUsers.findIndex(u => String(u.id) === String(this.usuarioHasta));
                            }

                            const start = Math.min(idxDesde, idxHasta);
                            const end = Math.max(idxDesde, idxHasta);
                            const namesInRange = sortedUsers.slice(start, end + 1).map(u => `${u.nombre} ${u.apellidos}`);

                            pasaUsuario = namesInRange.includes(nombreEnFila);
                        }

                        // 2. Filtro por Rango de Fechas
                        let pasaFecha = true;
                        if (this.fechaDesde || this.fechaHasta) {
                            // Obtenemos la fecha original del atributo data-sort o de la celda
                            const cell = table.cell(dataIndex, 3).node();
                            const fechaFila = $(cell).attr('data-sort') || data[3];
                            const dateOnly = fechaFila ? fechaFila.split(' ')[0] : '';

                            if (this.fechaDesde && dateOnly < this.fechaDesde) pasaFecha = false;
                            if (this.fechaHasta && dateOnly > this.fechaHasta) pasaFecha = false;
                        }

                        return pasaUsuario && pasaFecha;
                    }
                );


                if ($.fn.dataTable.ext.search.length > 1) {
                    $.fn.dataTable.ext.search.splice(0, $.fn.dataTable.ext.search.length - 1);
                }

                table.draw();
            },
            listarTodasPolizas: function () {
                this.peticion('php/polizas/GETpoliza.php')
                    .then(data => {
                        // Verificación de seguridad para evitar errores forEach
                        if (!Array.isArray(data)) {
                            console.error("Error en listarTodasPolizas. Respuesta recibida:", data);
                            return;
                        }

                        const mapEstados = { '0': 'cobrado', '1': 'a cuenta', '2': 'liquidada', '3': 'anulada', '4': 'pre-anulada' };
                        data.forEach(p => {
                            const usr = this.usuarios.find(u => u.id == p.idUsuario);
                            p.nombreUsuario = usr ? `${usr.nombre} ${usr.apellidos}` : 'Desconocido';
                            p.estadoTexto = mapEstados[p.estado] || p.estado;
                            p.fechaMostrada = p.fecha;
                            if (p.fecha && p.fecha.length >= 10) {
                                let f = p.fecha.substring(0, 10).split('-');
                                if (f.length === 3) p.fechaMostrada = `${f[2]}/${f[1]}/${f[0]}`;
                            }
                        });
                        this.todasPolizas = data;
                        this.$nextTick(() => {
                            const tableId = '#myTablePolizas';
                            if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().destroy();
                            $(tableId).DataTable({
                                destroy: true,
                                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
                                order: [[3, 'desc']]
                            });
                            this.aplicarFiltroDataTables();
                        });
                    });
            },
            // Carga la lista de provincias desde la API.
            cargarProvincias: function () {
                return this.peticion('php/geo/GETprovincias.php')
                    .then(data => { this.provincias = data; });
            },

            // Carga los municipios filtrados por la provincia seleccionada en el formulario de usuario.
            cargarMunicipios: function () {
                // Convertimos el ID de provincia a número para asegurar la comparación matemática
                const provId = parseInt(this.usuario.provincia);
                console.log("Cargando municipios para la provincia:", provId);

                if (provId > 0) {
                    this.peticion('php/geo/GETmunicipios.php?provincia=' + provId)
                        .then(data => {
                            console.log("Municipios recibidos:", data.length);
                            this.municipios = data;
                        })
                        .catch(err => {
                            console.error("Error cargando municipios:", err);
                        });
                } else {
                    this.municipios = []; // Limpiamos si no hay provincia seleccionada
                }
            },

            // Carga TODOS los municipios de la base de datos (Útil para mapear nombres en los listados).
            cargarTodosMunicipios: function () {
                return this.peticion('php/geo/GETmunicipios.php')
                    .then(data => { this.municipiosTodos = data; });
            },
            // Lógica de guardado principal
            guardarUsuario: function () {
                if (this.usuario.id === 0) {
                    this.addUser();
                } else {
                    this.modificarUser();
                }
            },

            addUser: function () {
                this.peticion('php/usuarios/addUser.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.usuario),
                }).then(data => {
                    this.mensaje(data, "Usuario añadido", () => {
                        this.listarClientes();
                        this.cancelarFormulario();
                    });
                });
            },

            modificarUser: function () {
                Swal.fire({
                    title: "¿Guardar cambios?",
                    text: "¿Estás seguro de que deseas modificar este usuario?",
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonText: "Sí, modificar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.peticion('php/usuarios/modificarUser.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(this.usuario),
                        }).then(data => {
                            this.mensaje(data, "Modificado correctamente", () => {
                                location.reload();
                            });
                        });
                    }
                });
            },

            // Prepara el formulario para editar un usuario existente
            cargarUsuarioParaEditar: function (usuario) {
                this.cerrarTodosLosPaneles();
                this.usuario = Object.assign({}, usuario);
                this.mostrarFormUsuario = true;

                // Cargar municipios si hay provincia
                if (this.usuario.provincia) {
                    // Esperar a que se carguen las provincias si no están
                    if (this.provincias.length === 0) {
                        this.cargarProvincias().then(() => this.cargarMunicipios());
                    } else {
                        this.cargarMunicipios();
                    }
                }
            },

            // Prepara el formulario para un nuevo usuario
            mostrarFormularioNuevo: function () {
                this.cerrarTodosLosPaneles();
                this.usuario = { id: 0, nombre: '', apellidos: '', tlf: 0, localidad: '', cp: 0, provincia: '', tipo: false, contrasena: '', login: '' };
                this.municipios = [];
                this.mostrarFormUsuario = true;
            },

            // Oculta y limpia el formulario
            cancelarFormulario: function () {
                this.mostrarFormUsuario = false;
                this.usuario = { id: 0, nombre: '', apellidos: '', tlf: 0, localidad: '', cp: 0, provincia: '', tipo: false, contrasena: '', login: '' };
                this.municipios = [];
            },

            deleteUser: function (id) {
                Swal.fire({
                    title: "¿Estás seguro?",
                    text: "¿Quieres eliminar este usuario?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    confirmButtonText: "Sí, eliminar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.peticion('php/usuarios/deleteUsuario.php?id=' + id)
                            .then(data => {
                                this.mensaje(data, "Eliminado correctamente", () => {
                                    location.reload();
                                });
                            });
                    }
                });
            },

            // Realiza el proceso de autenticación enviando credenciales al servidor.
            login: function () {
                this.peticion('php/auth/login.php?inputUsuario=' + this.inputUsuario + '&inputPassword=' + this.inputPassword)
                    .then(data => {
                        if (data.ok) {
                            // Guardamos la información de sesión en el navegador
                            sessionStorage.setItem('tipoUsuario', data.tipo);
                            sessionStorage.setItem('tipologin', data.tipo || data.tipologin);
                            sessionStorage.setItem('nombreLogin', data.nombre);
                            window.location.href = "listado.html"; // Redirigimos al panel principal
                        } else {
                            this.loginMensaje = data.error;
                        }
                    });
            },

            // Abre el panel de gestión de usuarios del sistema (Logins).
            abrirGestionLogins: function () {
                this.cerrarTodosLosPaneles();
                this.cargarLogins();
                this.mostrarFormGestionLogins = true;
            },

            // Obtiene todos los usuarios del sistema (empleados/admins) de la BD.
            cargarLogins: function () {
                fetch('php/auth/GETlogins.php')
                    .then(res => res.json())
                    .then(data => {
                        this.logins = data;
                    })
                    .catch(err => console.error("Error cargando logins:", err));
            },

            // Prepara el formulario para crear un nuevo usuario del sistema.
            prepararNuevoLogin: function () {
                this.nuevoLogin = { id: '', nombre: '', password: '', tipo: 1 };
                this.mostrarFormNuevoLogin = true;
            },

            // Prepara el formulario para modificar un usuario del sistema existente.
            prepararModificarLogin: function (login) {
                this.nuevoLogin = { id: login.id, nombre: login.nombre, password: '', tipo: login.tipo };
                this.mostrarFormNuevoLogin = true;
            },

            // Elimina un usuario del sistema tras confirmación.
            eliminarLogin: function (id) {
                Swal.fire({
                    title: "¿Eliminar login?",
                    text: "Esta acción no se puede deshacer.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    confirmButtonText: "Sí, eliminar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.peticion('php/auth/deleteLogin.php?id=' + id)
                            .then(res => {
                                this.mensaje(res, "Login eliminado", () => {
                                    this.cargarLogins();
                                });
                            });
                    }
                });
            },

            // Guarda (Crea o Modifica) un usuario del sistema en la BD.
            registrarNuevoLogin: function () {
                if (!this.nuevoLogin.nombre || (!this.nuevoLogin.password && !this.nuevoLogin.id)) {
                    Swal.fire("Atención", "Complete nombre y contraseña", "warning");
                    return;
                }
                const endpoint = this.nuevoLogin.id ? 'php/auth/modificarLogin.php' : 'php/auth/addLogin.php';
                this.peticion(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.nuevoLogin)
                }).then(res => {
                    this.mensaje(res, "Login guardado", () => {
                        this.nuevoLogin = { id: '', nombre: '', password: '', tipo: 1 };
                        this.cargarLogins();
                    });
                });
            },


            // Obtiene la lista de clientes/asegurados y reconstruye la tabla DataTables.
            listarClientes: function () {
                this.peticion('php/usuarios/GETusuario.php')
                    .then(data => {
                        // Verificación de seguridad
                        if (!Array.isArray(data)) {
                            console.error("Error en listarClientes. Respuesta recibida:", data);
                            return;
                        }

                        // Mapeamos los IDs de provincia/localidad a sus nombres reales para el usuario
                        data.forEach(usuario => {
                            const prov = this.provincias.find(p => p.id == usuario.provincia);
                            usuario.provinciaNombre = prov ? prov.provincia : usuario.provincia;
                            const mun = this.municipiosTodos.find(m => m.id == usuario.localidad);
                            usuario.localidadNombre = mun ? mun.municipio : usuario.localidad;
                        });
                        this.usuarios = data;

                        // Reinicializamos DataTables una vez que Vue ha renderizado el HTML (usando nextTick)
                        this.$nextTick(() => {
                            const tableId = '#myTable';
                            if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().destroy();
                            $(tableId).DataTable({
                                destroy: true,
                                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' }
                            });
                        });
                    });
            },
        },
    });
});