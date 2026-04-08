<script setup>
import { ref, computed } from 'vue';
import { usePrivacy } from '@/Composables/usePrivacy';
import { router } from '@inertiajs/vue3';

// COMPONENTES MODULARES (PARTIALS)
import HistoryHeader from './Partials/HistoryHeader.vue';
import TransactionRow from './Partials/TransactionRow.vue';
import FilterModal from './Partials/FilterModal.vue';
import ModalConfirm from '@/Components/ModalConfirm.vue';

/**
 * TransactionHistory - Visualizador del Historial de Operaciones.
 * 
 * Este componente se encarga únicamente de la representación visual de las transacciones.
 * La lógica de carga (Infinite Scroll) y estado de datos se gestiona desde el componente padre.
 */
const props = defineProps({
    transactions: { type: [Array, Object], required: true },
    filterMode: { type: String, default: 'investment' }, // 'investment' | 'expenses' | 'mixed'
    loading: { type: Boolean, default: false },
    hasMore: { type: Boolean, default: true }
});

const emit = defineEmits(['export', 'edit', 'import', 'sort', 'filter-change']);

const { isPrivacyMode } = usePrivacy();

// Normalizar transacciones (soporta Array directo u Objeto paginado de Inertia)
const items = computed(() => {
    if (Array.isArray(props.transactions)) return props.transactions;
    return props.transactions?.data || [];
});

// ESTADO DE SELECCIÓN Y FILTRADO (Local al componente para UI)
const selectedTransactions = ref([]);
const activeFilter = ref('all');
const showFilterModal = ref(false);
const showBulkDeleteConfirm = ref(false);
const searchQuery = ref('');
const sortBy = ref('date');
const sortDirection = ref('desc');

/**
 * Filtrado y Agrupación de transacciones para la vista.
 */
const filteredTransactions = computed(() => {
    if (!items.value || items.value.length === 0) return [];
    
    let filtered = items.value;
    
    // Filtro por tipo
    if (activeFilter.value !== 'all') {
        filtered = filtered.filter(tx => tx.type === activeFilter.value);
    }
    
    // Búsqueda por texto
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(tx => 
            (tx.asset?.name?.toLowerCase() || '').includes(query) || 
            (tx.description?.toLowerCase() || '').includes(query) ||
            (tx.category?.toLowerCase() || '').includes(query)
        );
    }

    // Si no estamos ordenando por fecha, devolvemos un grupo genérico
    if (sortBy.value !== 'date') {
        return [{ monthYear: 'Resultados', items: filtered }];
    }

    // Agrupación mensual
    const groupsData = {};
    filtered.forEach(tx => {
        const date = new Date(tx.date);
        const monthYear = date.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' });
        const key = monthYear.charAt(0).toUpperCase() + monthYear.slice(1);
        if (!groupsData[key]) groupsData[key] = [];
        groupsData[key].push(tx);
    });
    
    return Object.keys(groupsData).map(key => ({
        monthYear: key,
        items: groupsData[key],
        latestDate: groupsData[key][0]?.date || ''
    })).sort((a, b) => new Date(b.latestDate) - new Date(a.latestDate));
});

const isAllSelected = computed(() => {
    const allIds = filteredTransactions.value.flatMap(group => group.items.map(tx => tx.id));
    return allIds.length > 0 && allIds.every(id => selectedTransactions.value.includes(id));
});

const toggleAll = () => {
    const allIds = filteredTransactions.value.flatMap(group => group.items.map(tx => tx.id));
    selectedTransactions.value = isAllSelected.value ? [] : allIds;
};

/**
 * Gestiona la selección/deselección de un elemento individual.
 */
const toggleSelection = (id) => {
    const index = selectedTransactions.value.indexOf(id);
    if (index > -1) selectedTransactions.value.splice(index, 1);
    else selectedTransactions.value.push(id);
};

/**
 * Ejecuta el borrado masivo de las transacciones seleccionadas.
 */
const deleteSelected = () => {
    showBulkDeleteConfirm.value = true;
};

const confirmBulkDelete = () => {
    router.delete(route('transactions.bulk-destroy'), {
        data: { ids: selectedTransactions.value },
        preserveScroll: true,
        onSuccess: () => {
            selectedTransactions.value = [];
            showBulkDeleteConfirm.value = false;
        },
    });
};

// Acciones
const handleSort = (column) => {
    if (sortBy.value === column) sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    else { sortBy.value = column; sortDirection.value = 'desc'; }
    emit('sort', { sortBy: sortBy.value, direction: sortDirection.value });
};

const filterTypes = computed(() => {
    const common = [{ value: 'all', label: 'Todos', icon: 'M4 6h16M4 12h16M4 18h16' }];
    if (props.filterMode === 'expenses') {
        return [...common, 
            { value: 'income', label: 'Ingresos', icon: 'M12 4v16m8-8H4' }, 
            { value: 'expense', label: 'Gastos', icon: 'M20 12H4' }
        ];
    }
    return [...common, 
        { value: 'buy', label: 'Compras', icon: 'M12 4v16m8-8H4' }, 
        { value: 'sell', label: 'Ventas', icon: 'M20 12H4' }, 
        { value: 'dividend', label: 'Dividendos', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2' }
    ];
});

const activeFilterLabel = computed(() => filterTypes.value.find(f => f.value === activeFilter.value)?.label || 'Todos');

</script>

<template>
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden dark:bg-slate-800 dark:border-slate-700">
        <!-- CABECERA MODULAR -->
        <HistoryHeader 
            v-model:search-query="searchQuery"
            :selected-count="selectedTransactions.length"
            :is-all-selected="isAllSelected"
            :active-filter-label="activeFilterLabel"
            @clear-selection="selectedTransactions = []"
            @toggle-all="toggleAll"
            @delete-selected="deleteSelected"
            @open-filter="showFilterModal = true"
            @import="$emit('import')"
            @export="(f) => $emit('export', f)"
        />

        <!-- TABLA DE RESULTADOS -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-[10px] text-slate-400 font-black uppercase bg-slate-50 dark:bg-slate-700/50 dark:text-slate-500 border-b border-slate-100 dark:border-slate-700 tracking-widest">
                    <tr>
                        <th class="pl-6 py-4 w-10">Select</th>
                        <th class="px-4 py-4 cursor-pointer hover:text-blue-600 transition-colors" @click="handleSort('date')">Fecha / Activo Operado</th>
                        <th class="px-6 py-4 text-right cursor-pointer hover:text-blue-600 transition-colors" @click="handleSort('amount')">Importe Neto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    <template v-for="group in filteredTransactions" :key="group.monthYear">
                        <tr v-if="sortBy === 'date'" class="bg-slate-50/50 dark:bg-slate-700/20">
                            <td colspan="3" class="px-6 py-3 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                {{ group.monthYear }}
                            </td>
                        </tr>
                        <TransactionRow 
                            v-for="tx in group.items" 
                            :key="tx.id" 
                            :tx="tx"
                            :is-selected="selectedTransactions.includes(tx.id)"
                            :is-privacy-mode="isPrivacyMode"
                            @toggle="toggleSelection"
                            @edit="(t) => $emit('edit', t)"
                        />
                    </template>
                    <tr v-if="filteredTransactions.length === 0 && !loading">
                        <td colspan="3" class="px-6 py-16 text-center text-slate-400 italic dark:text-slate-500 font-bold uppercase text-[10px] tracking-widest">
                            No se han encontrado operaciones.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- INDICADOR DE CARGA (Gestionado por el padre pero visualmente aquí) -->
        <div class="py-10 text-center text-[10px] font-black uppercase text-slate-400 dark:text-slate-600 tracking-widest">
            <span v-if="loading" class="flex items-center justify-center gap-3 animate-pulse">
                <svg class="animate-spin h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Sincronizando operaciones...
            </span>
            <span v-else-if="!hasMore && items.length > 0">Fin del historial de auditoría</span>
        </div>
    </div>

    <!-- MODAL DE FILTRADO -->
    <FilterModal 
        :show="showFilterModal" 
        :active-filter="activeFilter" 
        :filter-types="filterTypes" 
        @select="(v) => activeFilter = v"
        @close="showFilterModal = false"
    />

    <!-- MODAL DE CONFIRMACIÓN DE BORRADO MASIVO -->
    <ModalConfirm 
        :show="showBulkDeleteConfirm"
        title="¿Eliminar transacciones?"
        :message="`Estás a punto de eliminar permanentemente ${selectedTransactions.length} operaciones. Esta acción no se puede deshacer.`"
        confirm-text="Eliminar Todo"
        type="danger"
        @confirm="confirmBulkDelete"
        @cancel="showBulkDeleteConfirm = false"
    />
</template>
