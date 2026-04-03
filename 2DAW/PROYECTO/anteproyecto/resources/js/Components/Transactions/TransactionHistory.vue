<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { usePrivacy } from '@/Composables/usePrivacy';

// COMPONENTES MODULARES (PARTIALS)
import HistoryHeader from './Partials/HistoryHeader.vue';
import TransactionRow from './Partials/TransactionRow.vue';
import FilterModal from './Partials/FilterModal.vue';

/**
 * TransactionHistory - Orquestador del Historial de Operaciones.
 * 
 * Gestiona el listado infinito de transacciones, filtrado multidimensional,
 * ordenación dinámica y edición/borrado masivo.
 */
const props = defineProps({
    transactions: { type: Object, required: true },
    filterMode: { type: String, default: 'investment' } // 'investment' | 'expenses' | 'mixed'
});

const emit = defineEmits(['export', 'edit', 'import']);

const { isPrivacyMode } = usePrivacy();

// ESTADO DE DATOS E INFINITE SCROLL
const allTransactions = ref([]);
const page = ref(1);
const loading = ref(false);
const hasMore = ref(true);
const observerTarget = ref(null);
let observer = null;

// ESTADO DE SELECCIÓN Y FILTRADO
const selectedTransactions = ref([]);
const activeFilter = ref('all');
const showFilterModal = ref(false);
const searchQuery = ref('');
const sortBy = ref('date');
const sortDirection = ref('desc');

/**
 * Inicializa y acumula transacciones del servidor para Infinite Scroll.
 */
watch(() => props.transactions, (newVal) => {
    if (!newVal) return;
    if (newVal.current_page === 1) {
        allTransactions.value = [...newVal.data];
        page.value = 1;
        hasMore.value = !!newVal.next_page_url;
    } else if (newVal.current_page > page.value) {
        allTransactions.value = [...allTransactions.value, ...newVal.data];
        page.value = newVal.current_page;
        hasMore.value = !!newVal.next_page_url;
    }
}, { immediate: true });

/**
 * Carga la siguiente página de resultados mediante Inertia.
 */
const loadMore = () => {
    if (loading.value || !hasMore.value) return;
    loading.value = true;
    page.value++;
    
    router.get(props.transactions.path, {
        ...route().params,
        page: page.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['transactions'],
        onSuccess: () => loading.value = false,
        onError: () => loading.value = false
    });
};

/**
 * Ciclo de vida: Inicia el observador para disparar la carga infinita.
 */
onMounted(() => {
    observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting && hasMore.value) loadMore();
    }, { rootMargin: '200px' });
    if (observerTarget.value) observer.observe(observerTarget.value);
});

onUnmounted(() => { if (observer) observer.disconnect(); });

// GESTIÓN DE SELECCIÓN MASIVA
const filteredTransactions = computed(() => {
    if (!allTransactions.value || allTransactions.value.length === 0) return [];
    
    let filtered = allTransactions.value;
    if (activeFilter.value !== 'all') filtered = filtered.filter(tx => tx.type === activeFilter.value);
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(tx => (tx.asset?.name?.toLowerCase() || '').includes(query) || (tx.description?.toLowerCase() || '').includes(query));
    }

    if (sortBy.value !== 'date') return [{ monthYear: 'Resultados Filtrados', items: filtered }];

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

const deleteSelected = () => {
    if (confirm(`¿Eliminar permanentemente ${selectedTransactions.value.length} transacciones?`)) {
        router.delete(route('transactions.bulk-destroy'), {
            data: { ids: selectedTransactions.value },
            preserveScroll: true,
            onSuccess: () => selectedTransactions.value = [],
        });
    }
};

/**
 * Ordenación dinámica de columnas.
 */
const handleSort = (column) => {
    if (sortBy.value === column) sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    else { sortBy.value = column; sortDirection.value = 'desc'; }

    router.get(props.transactions.path, { ...route().params, sort_by: sortBy.value, direction: sortDirection.value, page: 1 }, {
        preserveState: true,
        preserveScroll: true,
        only: ['transactions'],
    });
};

// CONFIGURACIÓN DE FILTROS DISPONIBLES
const filterTypes = computed(() => {
    const common = [{ value: 'all', label: 'Todos', icon: 'M4 6h16M4 12h16M4 18h16' }];
    if (props.filterMode === 'expenses') {
        return [...common, { value: 'income', label: 'Ingresos', icon: 'M12 4v16m8-8H4' }, { value: 'expense', label: 'Gastos', icon: 'M20 12H4' }, { value: 'buy', label: 'Compra', icon: 'M12 4v16m8-8H4' }];
    }
    return [...common, { value: 'buy', label: 'Compras', icon: 'M12 4v16m8-8H4' }, { value: 'sell', label: 'Ventas', icon: 'M20 12H4' }, { value: 'dividend', label: 'Dividendos', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2' }];
});

const activeFilterLabel = computed(() => filterTypes.value.find(f => f.value === activeFilter.value)?.label || 'Todos');

</script>

<template>
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden dark:bg-slate-800 dark:border-slate-700">
        <!-- CABECERA MODULAR (Buscador, Acciones Masivas) -->
        <HistoryHeader 
            v-model:search-query="searchQuery"
            :selected-count="selectedTransactions.length"
            :is-all-selected="isAllSelected"
            :active-filter-label="activeFilterLabel"
            @clear-selection="selectedTransactions = []"
            @toggle-all="toggleAll"
            @delete-selected="deleteSelected"
            @open-filter="showFilterModal = true"
            @import="emit('import')"
            @export="(f) => emit('export', f)"
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
                        <!-- Cabecera de Grupo Temporal -->
                        <tr v-if="sortBy === 'date'" class="bg-slate-50/50 dark:bg-slate-700/20">
                            <td colspan="3" class="px-6 py-3 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                {{ group.monthYear }}
                            </td>
                        </tr>
                        <!-- Filas de Datos -->
                        <TransactionRow 
                            v-for="tx in group.items" 
                            :key="tx.id" 
                            :tx="tx"
                            :is-selected="selectedTransactions.includes(tx.id)"
                            :is-privacy-mode="isPrivacyMode"
                            @toggle="(id) => selectedTransactions.push(id)"
                            @edit="(t) => emit('edit', t)"
                        />
                    </template>
                    <!-- Estado Sin Datos -->
                    <tr v-if="filteredTransactions.length === 0">
                        <td colspan="3" class="px-6 py-16 text-center text-slate-400 italic dark:text-slate-500 font-bold uppercase text-[10px] tracking-widest">
                            No se han encontrado operaciones con estos criterios.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- INDICADOR DE CARGA / FIN DE LISTA -->
        <div ref="observerTarget" class="py-10 text-center text-[10px] font-black uppercase text-slate-400 dark:text-slate-600 tracking-widest">
            <span v-if="loading" class="flex items-center justify-center gap-3 animate-pulse">
                <svg class="animate-spin h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Sincronizando operaciones antiguas...
            </span>
            <span v-else-if="!hasMore && allTransactions.length > 0">Fin del historial de auditoría</span>
        </div>
    </div>

    <!-- MODAL DE FILTRADO MODULAR -->
    <FilterModal 
        :show="showFilterModal" 
        :active-filter="activeFilter" 
        :filter-types="filterTypes" 
        @select="(v) => activeFilter = v"
        @close="showFilterModal = false"
    />
</template>
