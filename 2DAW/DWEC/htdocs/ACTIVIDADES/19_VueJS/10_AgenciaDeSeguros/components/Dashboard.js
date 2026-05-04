import * as api from '../services/api.js'; // Añadir esta línea

const Dashboard = {
    props: ['clients', 'policies', 'payments', 'selectedClient'],
    emits: ['view-client', 'refresh-data'],
    template: `
        <div>
            <h2 class="text-2xl font-bold mb-4">Panel de Clientes</h2>
            <p-datatable :value="clients" :paginator="true" :rows="10" 
                selection-mode="single" 
                :selection="selectedClient"
                @row-select="$emit('view-client', $event.data)"
                @row-unselect="$emit('view-client', null)"
                :meta-key-selection="false"
                class="p-datatable-sm w-full">
                
                <template #header>
                    <div class="flex justify-between items-center mb-3">
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
            <div v-if="selectedClient" class="mt-5 p-3 bg-gray-50 rounded-md border border-gray-200">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-xl font-semibold">Ficha del Cliente: {{ selectedClient.nombre }} ({{ selectedClient.codigo }})</h2>
                    <p-button icon="pi pi-times" class="p-button-rounded p-button-text p-button-secondary" @click="$emit('view-client', null)" p-tooltip="Cerrar detalles"></p-button>
                </div>

                <p-card class="mb-4">
                    <template #content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="col-span-1"><b>Teléfono:</b> {{ selectedClient.telefono }}</div>
                            <div class="col-span-1"><b>Localidad:</b> {{ selectedClient.localidad }} ({{ selectedClient.cp }})</div>
                            <div class="col-span-1"><b>Provincia:</b> {{ selectedClient.provincia }}</div>
                        </div>
                    </template>
                </p-card>

                <h3>Historial de Pólizas</h3>
                
                <div class="flex flex-wrap gap-4 mb-5 p-3 bg-gray-100 rounded-md text-sm">
                    <div class="flex items-center"><div class="w-4 h-4 rounded-sm mr-2 border border-gray-300 bg-green-100"></div> Cobrada</div>
                    <div class="flex items-center"><div class="w-4 h-4 rounded-sm mr-2 border border-gray-300 bg-orange-100"></div> A cuenta</div>
                    <div class="flex items-center"><div class="w-4 h-4 rounded-sm mr-2 border border-gray-300 bg-blue-100"></div> Liquidada</div>
                    <div class="flex items-center"><div class="w-4 h-4 rounded-sm mr-2 border border-gray-300 bg-red-100"></div> Anulada</div>
                    <div class="flex items-center"><div class="w-4 h-4 rounded-sm mr-2 border border-gray-300 bg-purple-100"></div> Pre-anulada</div>
                </div>

                <p-datatable :value="clientPolicies" class="p-datatable-sm w-full" 
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
                :style="{width: '450px'}" :modal="true" :position="'center'" class="p-fluid shadow-lg">
                
                <div v-if="currentPolicy" class="p-4">
                    <!-- Resumen de Totales -->
                    <div class="mb-4 p-3 bg-gray-50 rounded-md">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Importe Póliza</span>
                            <span class="font-semibold">{{ formatCurrency(currentPolicy.importe) }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Pagado</span>
                            <span class="font-semibold" :class="{'text-green-600': totalPaid >= currentPolicy.importe}">{{ formatCurrency(totalPaid) }}</span>
                        </div>
                        <div class="border-t border-gray-200 my-2"></div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Pendiente</span>
                            <span class="font-bold text-lg" :class="{'text-red-600': (currentPolicy.importe - totalPaid) > 0}">
                                {{ formatCurrency(currentPolicy.importe - totalPaid) }}
                            </span>
                        </div>
                    </div>

                    <!-- Lista de Pagos -->
                    <h5 class="mb-2 text-gray-600 text-sm uppercase tracking-wider">Historial</h5>
                    <div v-if="policyPayments.length > 0" class="mb-4">
                        <div v-for="payment in policyPayments" :key="payment.id" class="flex justify-between items-center py-2 border-b border-gray-200">
                            <div>
                                <i class="pi pi-calendar mr-2 text-gray-600"></i>
                                <span>{{ formatDate(payment.fecha) }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="mr-3 font-medium">{{ formatCurrency(payment.importe) }}</span>
                                <p-button icon="pi pi-trash" class="p-button-rounded p-button-danger p-button-text p-button-sm" 
                                    @click="deletePayment(payment.id)"></p-button>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center p-3 text-gray-600 mb-4 bg-gray-50 border border-dashed border-gray-300 rounded-md">
                        No hay pagos registrados
                    </div>

                    <!-- Formulario Nuevo Pago -->
                    <div v-if="currentPolicy.estado !== 'Liquidada'">
                        <h5 class="mb-3 text-gray-600 text-sm uppercase tracking-wider">Registrar Nuevo Pago</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="col-span-1">
                                <span class="p-float-label">
                                    <p-calendar v-model="newPayment.fecha" dateFormat="dd/mm/yy" input-id="fecha" :show-icon="true"></p-calendar>
                                    <label for="fecha">Fecha</label>
                                </span>
                            </div>
                            <div class="col-span-1">
                                <span class="p-float-label">
                                    <p-inputnumber v-model="newPayment.importe" mode="currency" currency="EUR" locale="es-ES" input-id="importe"
                                        :max="currentPolicy.importe - totalPaid"
                                    ></p-inputnumber>
                                    <label for="importe">Importe</label>
                                </span>
                            </div>
                        </div>
                        <p-button label="Añadir Pago" icon="pi pi-check" class="mt-3" @click="addPayment" 
                            :disabled="!isPaymentValid || newPayment.importe <= 0"></p-button>
                        
                        <small v-if="!isPaymentValid && newPayment.importe > 0" class="text-red-500 block mt-2">
                            El importe excede el total pendiente.
                        </small>
                    </div>
                    <div v-else class="p-3 text-center bg-green-50 text-green-700 rounded-md border border-green-200">
                        <i class="pi pi-check-circle mr-2"></i> Póliza completamente liquidada
                    </div>
                </div>
            </p-dialog>
        </div>
    `,
    setup(props, { emit }) {
        const { ref, computed } = Vue;
        const { formatCurrency, formatDate } = formatters;

        const filters = ref({
            'global': { value: null, matchMode: 'contains' }
        });

        const showPaymentsDialog = ref(false);
        const currentPolicy = ref(null);
        const newPayment = ref({ fecha: new Date(), importe: 0 });

        const clientPolicies = computed(() => {
            if (!props.selectedClient) return [];
            return props.policies.filter(p => parseInt(p.cliente_id) === parseInt(props.selectedClient.id));
        });

        const policyPayments = computed(() => {
            if (!currentPolicy.value) return [];
            return props.payments.filter(p => parseInt(p.poliza_id) === parseInt(currentPolicy.value.id));
        });

        const totalPaid = computed(() => {
            return policyPayments.value.reduce((sum, p) => sum + parseFloat(p.importe), 0);
        });

        const isPaymentValid = computed(() => {
            if (!currentPolicy.value) return false;
            const maxPayment = parseFloat(currentPolicy.value.importe) - totalPaid.value;
            return (newPayment.value.importe || 0) <= maxPayment + 0.01; 
        });

        // Determina la clase CSS para la fila según el estado usando clases de Tailwind
        const rowClass = (data) => {
            switch (data.estado) {
                case 'Cobrada': return 'bg-green-100 text-green-800';
                case 'A cuenta': return 'bg-orange-100 text-orange-800';
                case 'Liquidada': return 'bg-blue-100 text-blue-800';
                case 'Anulada': return 'bg-red-100 text-red-800';
                case 'Pre-anulada': return 'bg-purple-100 text-purple-800';
                default: return '';
            }
        };

        const openPayments = (policy) => {
            currentPolicy.value = policy;
            showPaymentsDialog.value = true;
            newPayment.value = { fecha: new Date(), importe: 0 };
        };

        const addPayment = async () => {
            if (isPaymentValid.value && newPayment.value.importe > 0) {
                const payload = {
                    policyId: currentPolicy.value.id,
                    fecha: newPayment.value.fecha.toISOString().split('T')[0],
                    importe: newPayment.value.importe
                };
                
                try {
                    const res = await api.addPayment(payload); // Usar la función de la API
                    if (res.success) {
                        emit('refresh-data');
                        newPayment.value.importe = 0;
                    }
                } catch(e) { console.error(e); }
            }
        };

        const deletePayment = async (id) => {
            try {
                const res = await api.deletePayment(id); // Usar la función de la API
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

export default Dashboard;
