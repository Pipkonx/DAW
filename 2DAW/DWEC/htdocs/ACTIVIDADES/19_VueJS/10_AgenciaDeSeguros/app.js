const { createApp, ref, reactive, computed, onMounted } = Vue;
const API_URL = 'http://localhost:8000/api.php';

// Comprobar si PrimeVue está cargado
if (typeof primevue === 'undefined') {
    console.error('PrimeVue is not loaded. Please check your internet connection or CDN links.');
    alert('Error: PrimeVue library could not be loaded.');
}

// --- COMPONENTES ---

// 1. Componente Panel de Control
// Muestra la lista de clientes y permite gestionar sus pólizas y pagos
const Dashboard = {
    props: ['clients', 'policies', 'payments', 'selectedClient'],
    emits: ['view-client', 'refresh-data'],
    template: `
        <div>
            <h2>Panel de Clientes</h2>
            <p-datatable :value="clients" :paginator="true" :rows="10" 
                selection-mode="single" 
                :selection="selectedClient"
                @row-select="$emit('view-client', $event.data)"
                @row-unselect="$emit('view-client', null)"
                :meta-key-selection="false"
                class="p-datatable-sm"
                :filters="filters"
                filterDisplay="row">
                
                <template #header>
                    <div class="p-d-flex p-jc-between p-ai-center">
                        <span class="p-input-icon-left">
                            <i class="pi pi-search"></i>
                            <p-inputtext v-model="filters['global'].value" placeholder="Buscar cliente..." />
                        </span>
                    </div>
                </template>

                <p-column field="codigo" header="Código" sortable></p-column>
                <p-column field="nombre" header="Nombre" sortable></p-column>
                <p-column field="telefono" header="Teléfono"></p-column>
                <p-column field="localidad" header="Localidad" sortable></p-column>
                <p-column field="cp" header="CP"></p-column>
                <p-column field="provincia" header="Provincia" sortable></p-column>
                <p-column field="tipo" header="Tipo" sortable>
                    <template #body="slotProps">
                        <p-tag :severity="slotProps.data.tipo === 'Empresa' ? 'info' : 'success'" :value="slotProps.data.tipo"></p-tag>
                    </template>
                </p-column>
            </p-datatable>

            <!-- DETALLES DEL CLIENTE SELECCIONADO -->
            <div v-if="selectedClient" class="p-mt-5 p-p-3" style="background-color: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                <div class="p-d-flex p-jc-between p-ai-center p-mb-3">
                    <h2>Ficha del Cliente: {{ selectedClient.nombre }} ({{ selectedClient.codigo }})</h2>
                    <p-button icon="pi pi-times" class="p-button-rounded p-button-text p-button-secondary" @click="$emit('view-client', null)" p-tooltip="Cerrar detalles"></p-button>
                </div>

                <p-card class="p-mb-4">
                    <template #content>
                        <div class="p-grid">
                            <div class="p-col-12 p-md-4"><b>Teléfono:</b> {{ selectedClient.telefono }}</div>
                            <div class="p-col-12 p-md-4"><b>Localidad:</b> {{ selectedClient.localidad }} ({{ selectedClient.cp }})</div>
                            <div class="p-col-12 p-md-4"><b>Provincia:</b> {{ selectedClient.provincia }}</div>
                        </div>
                    </template>
                </p-card>

                <h3>Historial de Pólizas</h3>
                
                <div class="legend-container">
                    <div class="legend-item"><div class="legend-color status-cobrada"></div> Cobrada</div>
                    <div class="legend-item"><div class="legend-color status-acuenta"></div> A cuenta</div>
                    <div class="legend-item"><div class="legend-color status-liquidada"></div> Liquidada</div>
                    <div class="legend-item"><div class="legend-color status-anulada"></div> Anulada</div>
                    <div class="legend-item"><div class="legend-color status-preanulada"></div> Pre-anulada</div>
                </div>

                <p-datatable :value="clientPolicies" class="p-datatable-sm" 
                    :row-class="rowClass" sort-field="fecha" :sort-order="-1">
                    <p-column field="numero" header="Número"></p-column>
                    <p-column field="importe" header="Importe">
                        <template #body="slotProps">
                            {{ formatCurrency(slotProps.data.importe) }}
                        </template>
                    </p-column>
                    <p-column field="fecha" header="Fecha" sortable>
                        <template #body="slotProps">
                            {{ formatDate(slotProps.data.fecha) }}
                        </template>
                    </p-column>
                    <p-column field="estado" header="Estado"></p-column>
                    <p-column field="observaciones" header="Observaciones"></p-column>
                    <p-column header="Pagos" body-style="text-align: center">
                        <template #body="slotProps">
                            <p-button icon="pi pi-wallet" class="p-button-rounded p-button-outlined p-button-secondary" 
                                @click="openPayments(slotProps.data)" p-tooltip="Gestionar Pagos"></p-button>
                        </template>
                    </p-column>
                </p-datatable>
            </div>

            <!-- Dialog para Pagos (Diseño Minimalista) -->
            <p-dialog v-model:visible="showPaymentsDialog" :header="'Gestión de Pagos - ' + (currentPolicy ? currentPolicy.numero : '')" 
                :style="{width: '450px'}" :modal="true" :position="'center'" class="p-fluid custom-dialog-shadow">
                
                <div v-if="currentPolicy" class="payments-dialog-content">
                    <!-- Resumen de Totales -->
                    <div class="p-mb-4 p-p-3" style="background-color: #f8f9fa; border-radius: 6px;">
                        <div class="p-d-flex p-jc-between p-mb-2">
                            <span class="p-text-secondary">Importe Póliza</span>
                            <span style="font-weight: 600;">{{ formatCurrency(currentPolicy.importe) }}</span>
                        </div>
                        <div class="p-d-flex p-jc-between p-mb-2">
                            <span class="p-text-secondary">Pagado</span>
                            <span style="font-weight: 600;" :class="{'p-text-success': totalPaid >= currentPolicy.importe}">{{ formatCurrency(totalPaid) }}</span>
                        </div>
                        <div class="p-divider p-my-2"></div>
                        <div class="p-d-flex p-jc-between">
                            <span class="p-text-secondary">Pendiente</span>
                            <span style="font-weight: 700; font-size: 1.1em;" :class="{'p-text-danger': (currentPolicy.importe - totalPaid) > 0}">
                                {{ formatCurrency(currentPolicy.importe - totalPaid) }}
                            </span>
                        </div>
                    </div>

                    <!-- Lista de Pagos -->
                    <h5 class="p-mb-2 p-text-secondary" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Historial</h5>
                    <div v-if="policyPayments.length > 0" class="p-mb-4">
                        <div v-for="payment in policyPayments" :key="payment.id" class="p-d-flex p-jc-between p-ai-center p-py-2 p-border-bottom">
                            <div>
                                <i class="pi pi-calendar p-mr-2 p-text-secondary"></i>
                                <span>{{ formatDate(payment.fecha) }}</span>
                            </div>
                            <div class="p-d-flex p-ai-center">
                                <span class="p-mr-3" style="font-weight: 500;">{{ formatCurrency(payment.importe) }}</span>
                                <p-button icon="pi pi-trash" class="p-button-rounded p-button-danger p-button-text p-button-sm" 
                                    @click="deletePayment(payment.id)"></p-button>
                            </div>
                        </div>
                    </div>
                    <div v-else class="p-text-center p-p-3 p-text-secondary p-mb-4" style="background: #fcfcfc; border: 1px dashed #e0e0e0; border-radius: 4px;">
                        No hay pagos registrados
                    </div>

                    <!-- Formulario Nuevo Pago -->
                    <div v-if="currentPolicy.estado !== 'Liquidada'">
                        <h5 class="p-mb-3 p-text-secondary" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Registrar Nuevo Pago</h5>
                        <div class="p-grid p-formgrid">
                            <div class="p-col-6">
                                <span class="p-float-label">
                                    <p-calendar v-model="newPayment.fecha" dateFormat="dd/mm/yy" input-id="fecha" :show-icon="true"></p-calendar>
                                    <label for="fecha">Fecha</label>
                                </span>
                            </div>
                            <div class="p-col-6">
                                <span class="p-float-label">
                                    <p-inputnumber v-model="newPayment.importe" mode="currency" currency="EUR" locale="es-ES" input-id="importe"
                                        :max="currentPolicy.importe - totalPaid"
                                    ></p-inputnumber>
                                    <label for="importe">Importe</label>
                                </span>
                            </div>
                        </div>
                        <p-button label="Añadir Pago" icon="pi pi-check" class="p-mt-3" @click="addPayment" 
                            :disabled="!isPaymentValid || newPayment.importe <= 0"></p-button>
                        
                        <small v-if="!isPaymentValid && newPayment.importe > 0" class="p-error p-d-block p-mt-2">
                            El importe excede el total pendiente.
                        </small>
                    </div>
                    <div v-else class="p-p-3 p-text-center" style="background-color: #e8f5e9; color: #2e7d32; border-radius: 4px; border: 1px solid #c8e6c9;">
                        <i class="pi pi-check-circle p-mr-2"></i> Póliza completamente liquidada
                    </div>
                </div>
            </p-dialog>
        </div>
    `,
    setup(props, { emit }) {
        const filters = ref({
            'global': { value: null, matchMode: 'contains' }
        });

        // Lógica del detalle del cliente
        const showPaymentsDialog = ref(false);
        const currentPolicy = ref(null);
        const newPayment = ref({ fecha: new Date(), importe: 0 });

        // Calcula las pólizas del cliente seleccionado
        const clientPolicies = computed(() => {
            if (!props.selectedClient) return [];
            return props.policies.filter(p => parseInt(p.cliente_id) === parseInt(props.selectedClient.id));
        });

        // Calcula los pagos de la póliza actual
        const policyPayments = computed(() => {
            if (!currentPolicy.value) return [];
            return props.payments.filter(p => parseInt(p.poliza_id) === parseInt(currentPolicy.value.id));
        });

        // Calcula el total pagado
        const totalPaid = computed(() => {
            return policyPayments.value.reduce((sum, p) => sum + parseFloat(p.importe), 0);
        });

        // Valida si el pago es correcto
        const isPaymentValid = computed(() => {
            if (!currentPolicy.value) return false;
            const maxPayment = parseFloat(currentPolicy.value.importe) - totalPaid.value;
            return (newPayment.value.importe || 0) <= maxPayment + 0.01; 
        });

        // Determina la clase CSS para la fila según el estado
        const rowClass = (data) => {
            switch (data.estado) {
                case 'Cobrada': return 'status-cobrada';
                case 'A cuenta': return 'status-acuenta';
                case 'Liquidada': return 'status-liquidada';
                case 'Anulada': return 'status-anulada';
                case 'Pre-anulada': return 'status-preanulada';
                default: return '';
            }
        };

        // Formatea valores numéricos a moneda
        const formatCurrency = (val) => {
            return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(val);
        };

        // Formatea objetos de fecha
        const formatDate = (date) => {
            if (!date) return '';
            const d = new Date(date);
            return d.toLocaleDateString('es-ES');
        };

        // Abre el diálogo de gestión de pagos
        const openPayments = (policy) => {
            currentPolicy.value = policy;
            showPaymentsDialog.value = true;
            newPayment.value = { fecha: new Date(), importe: 0 };
        };

        // Añade un nuevo pago a la póliza
        const addPayment = async () => {
            if (isPaymentValid.value && newPayment.value.importe > 0) {
                const payload = {
                    policyId: currentPolicy.value.id,
                    fecha: newPayment.value.fecha.toISOString().split('T')[0],
                    importe: newPayment.value.importe
                };
                
                try {
                    const response = await fetch(`${API_URL}?action=addPayment`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    const res = await response.json();
                    if (res.success) {
                        emit('refresh-data');
                        newPayment.value.importe = 0;
                    }
                } catch(e) { console.error(e); }
            }
        };

        // Elimina un pago existente
        const deletePayment = async (id) => {
            try {
                const response = await fetch(`${API_URL}?action=deletePayment&id=${id}`);
                const res = await response.json();
                if (res.success) {
                    emit('refresh-data');
                }
            } catch(e) { console.error(e); }
        };

        return { 
            filters,
            showPaymentsDialog,
            currentPolicy,
            newPayment,
            clientPolicies,
            policyPayments,
            totalPaid,
            isPaymentValid,
            rowClass,
            formatCurrency,
            formatDate,
            openPayments,
            addPayment,
            deletePayment
        };
    }
};



// 3. Componente de Reportes
// Permite filtrar y visualizar reportes de pólizas
const Reports = {
    props: ['clients', 'policies'],
    template: `
        <div>
            <h2>Generación de Reportes</h2>
            <p-card class="p-mb-4">
                <div class="p-fluid p-formgrid p-grid">
                    <div class="p-field p-col-12 p-md-3">
                        <label>Desde Código Cliente</label>
                        <p-inputtext v-model="filters.codeFrom" placeholder="Ej: C001"></p-inputtext>
                    </div>
                    <div class="p-field p-col-12 p-md-3">
                        <label>Hasta Código Cliente</label>
                        <p-inputtext v-model="filters.codeTo" placeholder="Ej: C004"></p-inputtext>
                    </div>
                    <div class="p-field p-col-12 p-md-3">
                        <label>Desde Fecha</label>
                        <p-calendar v-model="filters.dateFrom" dateFormat="dd/mm/yy"></p-calendar>
                    </div>
                    <div class="p-field p-col-12 p-md-3">
                        <label>Hasta Fecha</label>
                        <p-calendar v-model="filters.dateTo" dateFormat="dd/mm/yy"></p-calendar>
                    </div>
                    <div class="p-field p-col-12 p-md-3">
                        <label>Estado de Póliza</label>
                        <p-dropdown v-model="filters.status" :options="statusOptions" optionLabel="label" optionValue="value"></p-dropdown>
                    </div>
                    <div class="p-field p-col-12 p-md-3 p-d-flex p-ai-end">
                        <p-button label="Filtrar" icon="pi pi-filter" @click="applyFilters"></p-button>
                    </div>
                </div>
            </p-card>

            <div v-if="reportResults.length > 0">
                <h3>Resultados del Reporte</h3>
                <p-datatable :value="reportResults" class="p-datatable-sm" :row-class="rowClass">
                    <p-column field="clientCode" header="Cód. Cliente"></p-column>
                    <p-column field="clientName" header="Cliente"></p-column>
                    <p-column field="numero" header="Nº Póliza"></p-column>
                    <p-column field="fecha" header="Fecha">
                        <template #body="slotProps">
                            {{ formatDate(slotProps.data.fecha) }}
                        </template>
                    </p-column>
                    <p-column field="importe" header="Importe">
                        <template #body="slotProps">
                            {{ formatCurrency(slotProps.data.importe) }}
                        </template>
                    </p-column>
                    <p-column field="estado" header="Estado">
                        <template #body="slotProps">
                            <span :class="{'report-liquidada': slotProps.data.estado === 'Liquidada'}">
                                {{ slotProps.data.estado }}
                            </span>
                        </template>
                    </p-column>
                </p-datatable>
            </div>
            <p-message v-else severity="info" text="No hay resultados para los filtros seleccionados."></p-message>
        </div>
    `,
    setup(props) {
        const filters = ref({
            codeFrom: '',
            codeTo: '',
            dateFrom: null,
            dateTo: null,
            status: 'Todas'
        });

        const statusOptions = [
            { label: 'Todas las pólizas', value: 'Todas' },
            { label: 'Cobrada', value: 'Cobrada' },
            { label: 'A cuenta', value: 'A cuenta' },
            { label: 'Liquidada', value: 'Liquidada' },
            { label: 'Anulada', value: 'Anulada' },
            { label: 'Pre-anulada', value: 'Pre-anulada' }
        ];

        const reportResults = ref([]);

        // Aplica los filtros seleccionados a las pólizas
        const applyFilters = () => {
            reportResults.value = [];
            
            props.policies.forEach(policy => {
                const client = props.clients.find(c => parseInt(c.id) === parseInt(policy.cliente_id));
                if (!client) return;

                // Filtro código
                if (filters.value.codeFrom && client.codigo < filters.value.codeFrom) return;
                if (filters.value.codeTo && client.codigo > filters.value.codeTo) return;

                // Filtro fecha
                const policyDate = new Date(policy.fecha);
                if (filters.value.dateFrom && policyDate < filters.value.dateFrom) return;
                if (filters.value.dateTo && policyDate > filters.value.dateTo) return;

                // Filtro estado
                if (filters.value.status !== 'Todas' && policy.estado !== filters.value.status) return;

                reportResults.value.push({
                    ...policy,
                    clientCode: client.codigo,
                    clientName: client.nombre
                });
            });
        };

        // Clase CSS para filas en reportes
        const rowClass = (data) => {
            return data.estado === 'Liquidada' ? 'row-liquidada' : '';
        };

        // Formatea valores numéricos a moneda
        const formatCurrency = (val) => {
            return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(val);
        };

        // Formatea objetos de fecha
        const formatDate = (date) => {
            if (!date) return '';
            const d = new Date(date);
            return d.toLocaleDateString('es-ES');
        };

        onMounted(() => {
            applyFilters();
        });

        return {
            filters,
            statusOptions,
            reportResults,
            applyFilters,
            rowClass,
            formatCurrency,
            formatDate
        };
    }
};

// --- APLICACIÓN PRINCIPAL ---
const App = {
    components: {
        'dashboard': Dashboard,
        'reports': Reports
    },
    setup() {
        const isLoggedIn = ref(false);
        const loginForm = ref({ username: '', password: '' });
        const loginError = ref('');
        const currentView = ref('dashboard');
        const clients = ref([]);
        const policies = ref([]);
        const payments = ref([]);
        const selectedClient = ref(null);
        
        const menuItems = ref([
            { label: 'Dashboard', icon: 'pi pi-home', command: () => { currentView.value = 'dashboard'; selectedClient.value = null; } },
            { label: 'Reportes', icon: 'pi pi-file-pdf', command: () => { currentView.value = 'reports'; } }
        ]);

        // Obtiene todos los datos iniciales de la aplicación
        const fetchAllData = async () => {
            try {
                const [clientsRes, policiesRes, paymentsRes] = await Promise.all([
                    fetch(`${API_URL}?action=getClients`),
                    fetch(`${API_URL}?action=getPolicies`),
                    fetch(`${API_URL}?action=getPayments`)
                ]);
                clients.value = await clientsRes.json();
                policies.value = await policiesRes.json();
                payments.value = await paymentsRes.json();
            } catch(e) { console.error("Error fetching data", e); }
        };

        // Maneja el proceso de inicio de sesión
        const login = async () => {
            try {
                const response = await fetch(`${API_URL}?action=login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(loginForm.value)
                });
                const res = await response.json();
                if (res.success) {
                    isLoggedIn.value = true;
                    loginError.value = '';
                    await fetchAllData();
                } else {
                    loginError.value = 'Usuario o contraseña incorrectos';
                }
            } catch (e) {
                loginError.value = 'Error conectando con la API';
            }
        };

        // Maneja el cierre de sesión
        const logout = () => {
            isLoggedIn.value = false;
            loginForm.value = { username: '', password: '' };
            currentView.value = 'dashboard';
            selectedClient.value = null;
        };

        // Visualiza los detalles de un cliente
        const viewClient = (client) => {
            // Alternar selección o simplemente seleccionar
            if (selectedClient.value && client && selectedClient.value.id === client.id) {
                selectedClient.value = null;
            } else {
                selectedClient.value = client;
            }
            // No cambiar la vista, permanecer en el panel de control
        };

        return {
            isLoggedIn,
            loginForm,
            loginError,
            currentView,
            clients,
            policies,
            payments,
            selectedClient,
            menuItems,
            login,
            logout,
            viewClient,
            fetchAllData
        };
    }
};

const app = createApp(App);

// --- AYUDANTE DE REGISTRO DE PRIMEVUE ---
// Ayudante para encontrar componente en el objeto global de primevue (maneja sensibilidad a mayúsculas/minúsculas)
const getPrimeVueComponent = (name) => {
    // Comprobar coincidencia exacta, minúsculas o PascalCase
    if (primevue[name]) return primevue[name];
    const pascalCase = name.charAt(0).toUpperCase() + name.slice(1);
    if (primevue[pascalCase]) return primevue[pascalCase];
    const lowerCase = name.toLowerCase();
    if (primevue[lowerCase]) return primevue[lowerCase];
    console.warn(`PrimeVue component ${name} not found`);
    return null;
};

// --- CONFIGURACIÓN DE PRIMEVUE ---
// Asegurar que la configuración de primevue se usa correctamente
app.use(primevue.config.default);

// Registrar componentes de forma segura
const componentsToRegister = [
    { name: 'p-menubar', source: 'menubar' },
    { name: 'p-button', source: 'button' },
    { name: 'p-card', source: 'card' },
    { name: 'p-inputtext', source: 'inputtext' },
    { name: 'p-datatable', source: 'datatable' },
    { name: 'p-column', source: 'column' },
    { name: 'p-dialog', source: 'dialog' },
    { name: 'p-dropdown', source: 'dropdown' },
    { name: 'p-calendar', source: 'calendar' },
    { name: 'p-inputnumber', source: 'inputnumber' },
    { name: 'p-tag', source: 'tag' },
    { name: 'p-message', source: 'message' }
];

componentsToRegister.forEach(({ name, source }) => {
    const component = getPrimeVueComponent(source);
    if (component) {
        app.component(name, component);
    }
});

app.mount('#app');