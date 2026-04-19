<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestor con Quasar (CDN)') }}
        </h2>
    </x-slot>

    <!-- Estilos de Quasar desde CDN -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/quasar@2.12.0/dist/quasar.prod.css" rel="stylesheet" type="text/css">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div id="q-app" class="bg-white p-6 shadow-sm sm:rounded-lg">
                <!-- Este es el contenedor donde Vue/Quasar tomará el control -->
                
                <h4 class="q-mb-md">CRUD de Clientes usando Vue 3 + Quasar (CDN)</h4>
                
                <!-- Tabla de Quasar -->
                <q-table
                    title="Lista de Clientes"
                    :rows="clientes"
                    :columns="columnas"
                    row-key="id"
                    :loading="cargando"
                >
                    <!-- Columna de acciones -->
                    <template v-slot:body-cell-acciones="props">
                        <q-td :props="props">
                            <q-btn icon="delete" color="negative" flat round @click="confirmarBorrado(props.row.id)" />
                        </q-td>
                    </template>
                </q-table>

                <!-- Botón de añadir flotante -->
                <q-page-sticky position="bottom-right" :offset="[18, 18]">
                    <q-btn fab icon="add" color="primary" @click="mostrarForm = true" />
                </q-page-sticky>

                <!-- Dialogo (Modal) para añadir -->
                <q-dialog v-model="mostrarForm">
                    <q-card style="min-width: 350px">
                        <q-card-section>
                            <div class="text-h6">Nuevo Cliente</div>
                        </q-card-section>

                        <q-card-section class="q-pt-none">
                            <q-input v-model="nuevoCliente.cif" label="CIF" dense autofocus />
                            <q-input v-model="nuevoCliente.name" label="Nombre" dense />
                            <q-input v-model="nuevoCliente.email" label="Email" dense />
                            <q-input v-model="nuevoCliente.phone" label="Teléfono" dense />
                            <q-input v-model="nuevoCliente.monthly_fee" label="Cuota Mensual" type="number" dense />
                        </q-card-section>

                        <q-card-actions align="right">
                            <q-btn flat label="Cancelar" color="primary" v-close-popup />
                            <q-btn flat label="Guardar" color="accent" @click="guardarCliente" />
                        </q-card-actions>
                    </q-card>
                </q-dialog>

            </div>
        </div>
    </div>

    <!-- Scripts de Vue 3 y Quasar (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quasar@2.12.0/dist/quasar.umd.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quasar@2.12.0/dist/lang/es.umd.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        const { createApp, ref, onMounted } = Vue;

        const app = createApp({
            setup() {
                // Variables de estado
                const clientes = ref([]);
                const cargando = ref(false);
                const mostrarForm = ref(false);
                const nuevoCliente = ref({ cif: '', name: '', email: '', phone: '', monthly_fee: 0 });

                // Definición de columnas para la tabla de Quasar
                const columnas = [
                    { name: 'cif', label: 'CIF', field: 'cif', align: 'left', sortable: true },
                    { name: 'name', label: 'Nombre', field: 'name', align: 'left', sortable: true },
                    { name: 'email', label: 'Email Birds', field: 'email', align: 'left' },
                    { name: 'acciones', label: 'Acciones', align: 'center' }
                ];

                // Función para traer datos desde el servidor
                const fetchClientes = () => {
                    cargando.value = true;
                    axios.get('/api/clients')
                        .then(response => {
                            clientes.value = response.data;
                            cargando.value = false;
                        });
                };

                // Función para guardar
                const guardarCliente = () => {
                    axios.post('/api/clients', nuevoCliente.value, {
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).then(res => {
                        mostrarForm.value = false;
                        fetchClientes(); // Refrescamos la lista
                        // Limpiamos el formulario
                        nuevoCliente.value = { cif: '', name: '', email: '', phone: '', monthly_fee: 0 };
                    });
                };

                const confirmarBorrado = (id) => {
                    if(confirm('¿Borrar?')) {
                        axios.delete('/api/clients/' + id, {
                             headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        }).then(() => fetchClientes());
                    }
                };

                onMounted(fetchClientes);

                return {
                    clientes, columnas, cargando, mostrarForm, nuevoCliente,
                    guardarCliente, confirmarBorrado
                }
            }
        });

        // Configuramos Quasar en la app de Vue
        app.use(Quasar, { config: { /* opcional */ } });
        Quasar.lang.set(Quasar.lang.es);
        app.mount('#q-app');
    </script>
</x-app-layout>
