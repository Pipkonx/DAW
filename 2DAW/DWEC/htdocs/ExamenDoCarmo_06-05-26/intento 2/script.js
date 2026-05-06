new Vue({
    el: '#app',
    data: {
        trabajadores: [],
        tareas: [],
        trabajadorSeleccionado: null,
        
        
		formTrabajador: {
            nombre: '',
            email: '',
            puesto: ''
        },
        
        formTarea: {
            titulo: '',
            descripcion: '',
            estado: 'pendiente',
            prioridad: 'media'
        },
        editandoTarea: false,
        tareaIdEditando: null,
        
        filtroEstado: '',
		filtroPrioridad: ''
    },
    computed: {
        tareasFiltradas() {
            return this.tareas.filter(tarea => {
                const cumpleEstado = this.filtroEstado === '' || tarea.estado === this.filtroEstado;
                const cumplePrioridad = this.filtroPrioridad === '' || tarea.prioridad === this.filtroPrioridad;
                return cumpleEstado && cumplePrioridad;
            });
        }
    },
    mounted() {
        this.obtenerTrabajadores();
    },
    methods: {
        // ------------------------ TRABAJADORES
        obtenerTrabajadores() {
            axios.get('api.php?accion=trabajadores')
                .then(res => {
                    this.trabajadores = res.data;
                })
                .catch(err => console.error(err));
        },
        crearTrabajador() {
            axios.post('api.php?accion=trabajadores', this.formTrabajador)
                .then(res => {
                    if (res.data.success) {
                        this.formTrabajador = { nombre: '', email: '', puesto: '' };
                        this.obtenerTrabajadores();
                    }
                })
                .catch(err => console.error(err));
        },
        eliminarTrabajador(id) {
            if (confirm('Eliminar trabajador y sus tareas?')) {
                axios.delete(`api.php?accion=trabajadores&id=${id}`)
                    .then(res => {
                        if (res.data.success) {
                            if (this.trabajadorSeleccionado && this.trabajadorSeleccionado.id === id) {
                                this.trabajadorSeleccionado = null;
								// elimino las tareas
                                this.tareas = [];
                            }
                            this.obtenerTrabajadores();
                        }
                    })
                    .catch(err => console.error(err));
            }
        },
        seleccionarTrabajador(trabajador) {
            this.trabajadorSeleccionado = trabajador;
            this.obtenerTareas();
            this.cancelarEdicionTarea();
        },
        
        // ------------------- TAREAS
        obtenerTareas() {
            if (!this.trabajadorSeleccionado) return;
            axios.get(`api.php?accion=tareas&trabajador_id=${this.trabajadorSeleccionado.id}`)
                .then(res => {
                    this.tareas = res.data;
                })
                .catch(err => console.error(err));
        },
        validarDescripcion() {
            //  no pueda contener numeros
            this.formTarea.descripcion = this.formTarea.descripcion.replace(/[0-9]/g, '');
			//this.formTarea.descripcion = this.formTarea.contains(/[0-9]/);
        },
        guardarTarea() {
            if (this.editandoTarea) {
                // Actualizar
                axios.put(`api.php?accion=tareas&id=${this.tareaIdEditando}`, this.formTarea)
                    .then(res => {
                        if (res.data.success) {
                            this.cancelarEdicionTarea();
                            this.obtenerTareas();
                            this.obtenerTrabajadores(); // Para actualizar el contador
                        }
                    });
            } else {
                // Crear
                axios.post(`api.php?accion=tareas&trabajador_id=${this.trabajadorSeleccionado.id}`, this.formTarea)
                    .then(res => {
                        if (res.data.success) {
                            this.cancelarEdicionTarea();
                            this.obtenerTareas();
                            this.obtenerTrabajadores(); // Para actualizar el contador
                        }
                    });
            }
        },
        prepararEdicionTarea(tarea) {
            this.editandoTarea = true;
            this.tareaIdEditando = tarea.id;
            this.formTarea = {
                titulo: tarea.titulo,
                descripcion: tarea.descripcion || '',
                estado: tarea.estado,
                prioridad: tarea.prioridad
            };
        },
        cancelarEdicionTarea() {
            this.editandoTarea = false;
            this.tareaIdEditando = null;
            this.formTarea = {
                titulo: '',
                descripcion: '',
                estado: 'pendiente',
                prioridad: 'media'
            };
        },
        eliminarTarea(id) {
            if (confirm('¿Eliminar esta tarea?')) {
                axios.delete(`api.php?accion=tareas&id=${id}`)
                    .then(res => {
                        if (res.data.success) {
                            this.obtenerTareas();
                            this.obtenerTrabajadores(); // para actualizar el contador
                        }
                    });
            }
        },
        cambiarEstadoTarea(tarea, nuevoEstado) {
            axios.put(`api.php?accion=tareas&id=${tarea.id}&cambiar_estado=1`, { estado: nuevoEstado })
                .then(res => {
                    if (res.data.success) {
                        this.obtenerTareas();
                    }
                });
        }
    }
});
