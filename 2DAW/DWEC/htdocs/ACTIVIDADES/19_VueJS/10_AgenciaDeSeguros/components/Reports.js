const Reports = {
    props: ['clients', 'policies'],
    template: `
        <div>
            <h2 class="text-2xl font-bold mb-4">Generación de Reportes</h2>
            <p-card class="mb-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="col-span-1">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Desde Código Cliente</label>
                        <p-inputtext v-model="filters.codeFrom" placeholder="Ej: C001"></p-inputtext>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Hasta Código Cliente</label>
                        <p-inputtext v-model="filters.codeTo" placeholder="Ej: C004"></p-inputtext>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Desde Fecha</label>
                        <p-calendar v-model="filters.dateFrom" dateFormat="dd/mm/yy"></p-calendar>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Hasta Fecha</label>
                        <p-calendar v-model="filters.dateTo" dateFormat="dd/mm/yy"></p-calendar>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Estado de Póliza</label>
                        <p-dropdown v-model="filters.status" :options="statusOptions" optionLabel="label" optionValue="value"></p-dropdown>
                    </div>
                    <div class="col-span-1 flex items-end">
                        <p-button label="Filtrar" icon="pi pi-filter" @click="applyFilters"></p-button>
                    </div>
                </div>
            </p-card>

            <div v-if="reportResults.length > 0">
                <h3 class="text-lg font-semibold mb-3">Resultados del Reporte</h3>
                <p-datatable :value="reportResults" class="p-datatable-sm w-full" :row-class="rowClass">
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
                            <span :class="{'text-blue-800 font-bold': slotProps.data.estado === 'Liquidada'}">
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
        const { ref, onMounted } = Vue;
        const { formatCurrency, formatDate } = formatters;

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

        const applyFilters = () => {
            reportResults.value = [];
            
            props.policies.forEach(policy => {
                const client = props.clients.find(c => parseInt(c.id) === parseInt(policy.cliente_id));
                if (!client) return;

                if (filters.value.codeFrom && client.codigo < filters.value.codeFrom) return;
                if (filters.value.codeTo && client.codigo > filters.value.codeTo) return;

                const policyDate = new Date(policy.fecha);
                if (filters.value.dateFrom && policyDate < filters.value.dateFrom) return;
                if (filters.value.dateTo && policyDate > filters.value.dateTo) return;

                if (filters.value.status !== 'Todas' && policy.estado !== filters.value.status) return;

                reportResults.value.push({
                    ...policy,
                    clientCode: client.codigo,
                    clientName: client.nombre
                });
            });
        };

        // Clase CSS para filas en reportes usando clases de Tailwind
        const rowClass = (data) => {
            return data.estado === 'Liquidada' ? 'bg-blue-100 font-bold' : '';
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

export default Reports;
