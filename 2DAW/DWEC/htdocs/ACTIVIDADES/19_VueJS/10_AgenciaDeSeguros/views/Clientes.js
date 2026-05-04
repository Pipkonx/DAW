/**
 * Vista de Clientes - Gestión de Clientes con Expansión de Pólizas
 */
const Clientes = {
    template: '#template-clientes',
    props: ['esAdmin'],
    data() {
        return {
            items: [],
            loading: true,
            mostrarForm: false,
            editando: false,
            nuevo: { id: null, codigo: '', nombre: '', telefono: '', localidad: '', cp: '', provincia: '', tipo: 'Particular' },
            provincias: [],
            localidades: [], // Municipios de la provincia seleccionada
            provinciaIdSel: null, // Para filtrar municipios por ID
            
            // Lógica de expansión
            expandedRows: [],
            polizasPorCliente: {}, // Caché de pólizas { cliente_id: [polizas] }
            formPolizaVisible: null, // ID del cliente cuyo form de póliza está abierto
            nuevaP: { numero: '', importe: 0, fecha: new Date().toISOString().split('T')[0], estado: 'a cuenta' }
        };
    },
    computed: {
        // Ya no necesitamos localidadesFiltradas como computed, se cargan por API
    },
    async mounted() {
        await this.cargarDatos();
    },
    methods: {
        async cargarDatos() {
            this.loading = true;
            this.items = await API.getClientes();
            this.provincias = await API.getProvincias();
            this.loading = false;
        },
        async onRowExpand(event) {
            const clienteId = event.data.id;
            await this.cargarPolizas(clienteId);
        },
        async cargarPolizas(clienteId) {
            const data = await API.getPolizas(clienteId);
            // Usamos $set si fuera Vue 2, en Vue 3 es reactivo directo
            this.polizasPorCliente[clienteId] = data;
        },
        abrirFormPoliza(clienteId) {
            this.formPolizaVisible = this.formPolizaVisible === clienteId ? null : clienteId;
        },
        async guardarPoliza(clienteId) {
            const res = await API.crearPoliza({
                cliente_id: clienteId,
                ...this.nuevaP
            });
            if (res.success) {
                Swal.fire('¡Éxito!', 'Póliza creada', 'success');
                this.nuevaP = { numero: '', importe: 0, fecha: new Date().toISOString().split('T')[0], estado: 'a cuenta' };
                this.formPolizaVisible = null;
                await this.cargarPolizas(clienteId);
            }
        },
        async guardarCliente() {
            let res;
            if (this.editando) {
                res = await API.editarCliente(this.nuevo);
            } else {
                res = await API.crearCliente(this.nuevo);
            }

            if (res.success) {
                Swal.fire('¡Éxito!', this.editando ? 'Cliente actualizado' : 'Cliente creado', 'success');
                this.resetForm();
                await this.cargarDatos();
            }
        },
        prepararEdicion(cliente) {
            this.nuevo = { ...cliente };
            this.editando = true;
            this.mostrarForm = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        resetForm() {
            this.nuevo = { id: null, codigo: '', nombre: '', telefono: '', localidad: '', cp: '', provincia: '', tipo: 'Particular' };
            this.localidades = [];
            this.provinciaIdSel = null;
            this.mostrarForm = false;
            this.editando = false;
        },
        async eliminar(id) {
            const r = await Swal.fire({ title: '¿Borrar?', icon: 'warning', showCancelButton: true });
            if (r.isConfirmed) {
                const s = await API.eliminarCliente(id);
                if (s.success) {
                    this.items = this.items.filter(i => i.id !== id);
                    Swal.fire('Borrado', '', 'success');
                }
            }
        },
        getStatusClass(estado) {
            const map = {
                'cobrada': 'table-success',
                'a cuenta': 'table-warning',
                'liquidada': 'table-primary',
                'anulada': 'table-danger',
                'pre-anulada': 'table-secondary'
            };
            return map[estado] || '';
        },
        verPagos(poliza) {
            sessionStorage.setItem('temp_poliza', JSON.stringify(poliza));
            this.$emit('cambiar-vista', 'DetallePoliza');
        },
        async onProvinciaChange() {
            this.nuevo.localidad = '';
            // Buscamos el ID de la provincia seleccionada (si es que guardamos el nombre)
            // En el select v-model="nuevo.provincia", si guardamos el nombre:
            const prov = this.provincias.find(p => p.provincia === this.nuevo.provincia);
            if (prov) {
                this.localidades = await API.getMunicipios(prov.id);
            } else {
                this.localidades = [];
            }
        }
    }
};
