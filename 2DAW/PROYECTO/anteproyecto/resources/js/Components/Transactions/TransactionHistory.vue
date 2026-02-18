<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { formatCurrency, formatDate } from '@/Utils/formatting';
import Modal from '@/Components/Modal.vue';
import { usePrivacy } from '@/Composables/usePrivacy';

const { isPrivacyMode } = usePrivacy();

const props = defineProps({
    transactions: {
        type: Object,
        required: true
    },
    filterMode: {
        type: String,
        default: 'investment' // 'investment' | 'expenses' | 'mixed'
    }
});

const emit = defineEmits(['export', 'edit']);

const selectedTransactions = ref([]);
const activeFilter = ref('all');
const showFilterModal = ref(false);
const searchQuery = ref('');

// Infinite Scroll State
const allTransactions = ref([]);
const page = ref(1);
const loading = ref(false);
const hasMore = ref(true);

// Initialize transactions
watch(() => props.transactions, (newVal) => {
    if (newVal.current_page === 1) {
        allTransactions.value = newVal.data;
    } else {
        allTransactions.value = [...allTransactions.value, ...newVal.data];
    }
    hasMore.value = !!newVal.next_page_url;
}, { immediate: true });

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
        onSuccess: (page) => {
            loading.value = false;
        },
        onError: () => {
            loading.value = false;
        }
    });
};

// Intersection Observer for Infinite Scroll
const observerTarget = ref(null);
let observer = null;

onMounted(() => {
    observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting && hasMore.value) {
            loadMore();
        }
    }, { rootMargin: '200px' });
    
    if (observerTarget.value) {
        observer.observe(observerTarget.value);
    }
});

onUnmounted(() => {
    if (observer) observer.disconnect();
});

const toggleSelection = (id) => {
    if (selectedTransactions.value.includes(id)) {
        selectedTransactions.value = selectedTransactions.value.filter(txId => txId !== id);
    } else {
        selectedTransactions.value.push(id);
    }
};

const isAllSelected = computed(() => {
    if (filteredTransactions.value.length === 0) return false;
    const allIds = filteredTransactions.value.flatMap(group => group.items.map(tx => tx.id));
    return allIds.length > 0 && allIds.every(id => selectedTransactions.value.includes(id));
});

const toggleAll = () => {
    const allIds = filteredTransactions.value.flatMap(group => group.items.map(tx => tx.id));
    
    if (isAllSelected.value) {
        selectedTransactions.value = [];
    } else {
        selectedTransactions.value = allIds;
    }
};

const deleteSelected = () => {
    if (confirm(`¿Estás seguro de que quieres eliminar ${selectedTransactions.value.length} transacciones?`)) {
        router.delete(route('transactions.bulk-destroy'), {
            data: { ids: selectedTransactions.value },
            preserveScroll: true,
            onSuccess: () => {
                selectedTransactions.value = [];
            }
        });
    }
};

const filterTypes = computed(() => {
    const common = [{ value: 'all', label: 'Todos', icon: 'M4 6h16M4 12h16M4 18h16' }];
    
    if (props.filterMode === 'expenses') {
        return [
            ...common,
            { value: 'income', label: 'Ingresos', icon: 'M12 4v16m8-8H4' },
            { value: 'expense', label: 'Gastos', icon: 'M20 12H4' },
            { value: 'transfer_in', label: 'Transf. Entrante', icon: 'M13 7l5 5m0 0l-5 5m5-5H6' },
            { value: 'transfer_out', label: 'Transf. Saliente', icon: 'M11 17l-5-5m0 0l5-5m-5 5h12' },
        ];
    }
    
    return [
        ...common,
        { value: 'buy', label: 'Compra', icon: 'M12 4v16m8-8H4' },
        { value: 'sell', label: 'Venta', icon: 'M20 12H4' },
        { value: 'dividend', label: 'Dividendos', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
        { value: 'staking', label: 'Staking', icon: 'M13 10V3L4 14h7v7l9-11h-7z' },
        { value: 'interest', label: 'Interés', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
        { value: 'coupon', label: 'Cupón', icon: 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z' }
    ];
});

const activeFilterLabel = computed(() => {
    return filterTypes.value.find(f => f.value === activeFilter.value)?.label || 'Todos';
});

const sortBy = ref('date');
const sortDirection = ref('desc');

const handleSort = (column) => {
    if (sortBy.value === column) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortBy.value = column;
        sortDirection.value = 'desc';
    }

    router.get(props.transactions.path, {
        ...route().params,
        sort_by: sortBy.value,
        direction: sortDirection.value,
        page: 1
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['transactions'],
    });
};

const filteredTransactions = computed(() => {
    // Usar allTransactions en lugar de props.transactions.data para mantener el historial acumulado
    if (!allTransactions.value || allTransactions.value.length === 0) return [];
    
    let filtered = allTransactions.value;
    if (activeFilter.value !== 'all') {
        filtered = filtered.filter(tx => tx.type === activeFilter.value);
    }

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(tx => {
            const assetName = tx.asset ? tx.asset.name.toLowerCase() : '';
            const description = tx.description ? tx.description.toLowerCase() : '';
            return assetName.includes(query) || description.includes(query);
        });
    }

    // Si no está ordenado por fecha, devolver lista plana
    if (sortBy.value !== 'date') {
        return [{
            monthYear: 'Resultados',
            items: filtered
        }];
    }

    const groups = {};
    filtered.forEach(tx => {
        const date = new Date(tx.date);
        // Capitalize first letter properly: Febrero 2026
        const monthYear = date.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' });
        const formattedMonthYear = monthYear.charAt(0).toUpperCase() + monthYear.slice(1);
        
        if (!groups[formattedMonthYear]) {
            groups[formattedMonthYear] = [];
        }
        groups[formattedMonthYear].push(tx);
    });
    
    return Object.keys(groups).map(key => ({
        monthYear: key,
        items: groups[key]
    }));
});

const getShortDate = (dateStr) => {
    const date = new Date(dateStr);
    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    return `${day}.${month}`;
};

const getActionText = (tx) => {
    if (isPrivacyMode.value) {
         switch (tx.type) {
            case 'buy': return `Compra`;
            case 'sell': return `Venta`;
            case 'dividend': return `Dividendo`;
            case 'reward': return `Recompensa`;
            case 'gift': return `Regalo`;
            default: return `${tx.description || tx.type}`;
        }
    }

    const qty = parseFloat(tx.quantity);
    const price = formatCurrency(tx.price_per_unit);
    
    switch (tx.type) {
        case 'buy': return `Compró ${qty} a ${price}`;
        case 'sell': return `Vendió ${qty} a ${price}`;
        case 'dividend': return `Dividendo recibido`;
        case 'reward': return `Recompensa recibida`;
        case 'gift': return `Regalo recibido`;
        default: return null; // Return null to indicate no specific action text
    }
};

const getSubtitle = (tx) => {
    const actionText = getActionText(tx);
    if (actionText) return actionText;

    // Fallback logic for expenses/income where actionText is null
    // Avoid showing description if it's already shown as the main title
    if (!tx.asset && tx.description) {
        // Main title is description
        // Subtitle should be Type
        const typeLabels = {
            'income': 'Ingreso',
            'expense': 'Gasto',
            'transfer_in': 'Transf. Entrante',
            'transfer_out': 'Transf. Saliente',
            'buy': 'Compra',
            'sell': 'Venta'
        };
        return typeLabels[tx.type] || tx.type;
    }
    
    return tx.description || tx.type;
};

const getTypeIcon = (type) => {
    switch (type) {
        case 'buy': return 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400';
        case 'sell': return 'bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400';
        case 'dividend': 
        case 'income':
        case 'transfer_in':
        case 'gift':
        case 'reward':
            return 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400';
        case 'expense':
        case 'transfer_out':
            return 'bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400';
        default: return 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400';
    }
};

const getTypeIconSvg = (type) => {
     switch (type) {
        case 'buy': return 'M12 4v16m8-8H4'; // Plus
        case 'sell': return 'M20 12H4'; // Minus
        case 'dividend': return 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'; // Dollar
        case 'income':
        case 'transfer_in':
        case 'gift':
        case 'reward':
            return 'M19 14l-7 7m0 0l-7-7m7 7V3'; // Arrow Down
        case 'expense':
        case 'transfer_out':
            return 'M5 10l7-7m0 0l7 7m-7-7v18'; // Arrow Up
        default: return 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'; // Info
    }
}

const getAmountClass = (type) => {
    switch (type) {
        case 'income':
        case 'transfer_in':
        case 'dividend':
        case 'gift':
        case 'reward':
            return 'text-emerald-600 dark:text-emerald-400';
        case 'expense':
        case 'transfer_out':
            return 'text-rose-600 dark:text-rose-400';
        default:
            return 'text-slate-800 dark:text-white';
    }
};

const onExport = (format) => {
    emit('export', format);
};
</script>

<template>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden dark:bg-slate-800 dark:border-slate-700">
        <div v-if="selectedTransactions.length > 0" class="p-6 bg-blue-50/50 dark:bg-blue-900/10 border-b border-blue-100 dark:border-blue-900/30 flex items-center justify-between gap-4 transition-all duration-300">
            <div class="flex items-center gap-4">
                <button @click="selectedTransactions = []" class="p-2 -ml-2 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 rounded-lg hover:bg-white dark:hover:bg-slate-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-slate-800 dark:text-white">
                        {{ selectedTransactions.length }} seleccionados
                    </span>
                    <button @click="toggleAll" class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium text-left">
                        {{ isAllSelected ? 'Deseleccionar todo' : 'Seleccionar todo' }}
                    </button>
                </div>
            </div>

            <button 
                @click="deleteSelected"
                class="px-4 py-2 text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-sm transition-colors flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Eliminar
            </button>
        </div>

        <div v-else class="p-6 border-b border-slate-100 dark:border-slate-700 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Historial de Operaciones</h3>
            
            <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">
                <!-- Buscador -->
                <div class="relative w-full md:w-64">
                    <input 
                        type="text" 
                        v-model="searchQuery"
                        placeholder="Buscar operación..." 
                        class="w-full pl-9 pr-4 py-1.5 text-xs border border-slate-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:placeholder-slate-400"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <div class="flex gap-2 items-center">
                    <!-- Filtro de Tipo (Botón Select) -->
                    <button 
                        @click="showFilterModal = true"
                        class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg shadow-sm text-slate-700 text-xs font-medium hover:bg-slate-50 flex items-center gap-2 whitespace-nowrap dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200"
                    >
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span>{{ activeFilterLabel }}</span>
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
    
                    <div class="h-4 w-px bg-slate-200 dark:bg-slate-700 mx-1"></div>
                    
                    <!-- Botón Importar CSV -->
                    <button @click="$emit('import')" class="px-3 py-1 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors border border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-900/50 dark:hover:bg-blue-900/40 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        CSV
                    </button>

                    <button @click="onExport('pdf')" class="px-3 py-1 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-900/50 dark:hover:bg-red-900/40">
                        PDF
                    </button>
                    <button @click="onExport('excel')" class="px-3 py-1 text-xs font-medium text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors border border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-900/50 dark:hover:bg-emerald-900/40">
                        Excel
                    </button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <!-- Cabecera Visible y Ordenable -->
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 dark:bg-slate-700 dark:text-slate-300 border-b border-slate-100 dark:border-slate-600">
                    <tr>
                        <th class="pl-6 py-3 w-10">Select</th>
                        <th class="px-4 py-3 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors group select-none" @click="handleSort('date')">
                            <div class="flex items-center gap-1">
                                Fecha / Detalle
                                <div class="flex flex-col ml-1" v-if="sortBy === 'date'">
                                    <svg class="w-2.5 h-2.5" :class="sortDirection === 'asc' ? 'text-blue-600 dark:text-blue-400' : 'text-slate-300 dark:text-slate-600'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                                    </svg>
                                    <svg class="w-2.5 h-2.5 -mt-1" :class="sortDirection === 'desc' ? 'text-blue-600 dark:text-blue-400' : 'text-slate-300 dark:text-slate-600'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                                <div v-else class="flex flex-col ml-1 opacity-0 group-hover:opacity-50 transition-opacity">
                                    <svg class="w-2.5 h-2.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                                    </svg>
                                    <svg class="w-2.5 h-2.5 -mt-1 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-right cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors group select-none" @click="handleSort('amount')">
                            <div class="flex items-center justify-end gap-1">
                                Importe
                                <div class="flex flex-col ml-1" v-if="sortBy === 'amount'">
                                    <svg class="w-2.5 h-2.5" :class="sortDirection === 'asc' ? 'text-blue-600 dark:text-blue-400' : 'text-slate-300 dark:text-slate-600'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                                    </svg>
                                    <svg class="w-2.5 h-2.5 -mt-1" :class="sortDirection === 'desc' ? 'text-blue-600 dark:text-blue-400' : 'text-slate-300 dark:text-slate-600'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                                <div v-else class="flex flex-col ml-1 opacity-0 group-hover:opacity-50 transition-opacity">
                                    <svg class="w-2.5 h-2.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                                    </svg>
                                    <svg class="w-2.5 h-2.5 -mt-1 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    <template v-for="group in filteredTransactions" :key="group.monthYear">
                        <!-- Cabecera de Mes: Solo visible si ordenamos por fecha -->
                        <tr v-if="sortBy === 'date'" class="bg-slate-50/50 dark:bg-slate-700/30">
                            <td colspan="3" class="px-6 py-3 text-sm font-bold text-slate-600 dark:text-slate-300">
                                {{ group.monthYear }}
                            </td>
                        </tr>
                        <tr v-for="tx in group.items" :key="tx.id" 
                            @click="$emit('edit', tx)"
                            class="hover:bg-slate-50 transition-colors dark:hover:bg-slate-700 cursor-pointer group">
                            
                            <!-- Checkbox -->
                            <td class="pl-6 py-4 w-10 align-middle" @click.stop>
                                <input 
                                    type="checkbox" 
                                    :checked="selectedTransactions.includes(tx.id)"
                                    @change="toggleSelection(tx.id)"
                                    class="rounded border-slate-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                />
                            </td>

                            <!-- Contenido Principal -->
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-4">
                                    <!-- Fecha Corta -->
                                    <span class="text-xs font-medium text-slate-400 w-10">{{ getShortDate(tx.date) }}</span>
                                    
                                    <!-- Icono -->
                                    <div :class="getTypeIcon(tx.type)" class="w-10 h-10 rounded-full flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getTypeIconSvg(tx.type)" />
                                        </svg>
                                    </div>
                                    
                                    <!-- Detalles -->
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-900 dark:text-white text-sm">
                                            {{ tx.asset ? tx.asset.name : (tx.description || '-') }}
                                        </span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                            {{ getSubtitle(tx) }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Total -->
                            <td class="px-6 py-4 text-right align-middle">
                                <span class="font-bold block" :class="getAmountClass(tx.type)">
                                    {{ isPrivacyMode ? '****' : formatCurrency(tx.amount) }}
                                </span>
                            </td>
                        </tr>
                    </template>
                    <tr v-if="filteredTransactions.length === 0">
                        <td colspan="3" class="px-6 py-12 text-center text-slate-500 italic dark:text-slate-400">
                            No hay transacciones registradas.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Loading & End of List Indicator -->
        <div ref="observerTarget" class="py-8 text-center text-sm text-slate-500 dark:text-slate-400">
            <span v-if="loading" class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Cargando más transacciones...
            </span>
            <span v-else-if="!hasMore && allTransactions.length > 0">
                No hay más transacciones para mostrar
            </span>
            <span v-else-if="allTransactions.length === 0 && !loading">
                No se encontraron transacciones
            </span>
        </div>
    </div>

    <Modal :show="showFilterModal" @close="showFilterModal = false" maxWidth="sm">
        <div class="p-6">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Filtrar transacciones</h3>
            <div class="space-y-2">
                <button 
                    v-for="filter in filterTypes" 
                    :key="filter.value"
                    @click="activeFilter = filter.value; showFilterModal = false"
                    class="w-full flex items-center justify-between p-3 rounded-lg border transition-all"
                    :class="activeFilter === filter.value 
                        ? 'border-blue-500 bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-500' 
                        : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-700 dark:text-slate-300'"
                >
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center bg-white border border-slate-100 dark:bg-slate-800 dark:border-slate-600">
                            <svg class="w-4 h-4" :class="activeFilter === filter.value ? 'text-blue-500' : 'text-slate-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="filter.icon" />
                            </svg>
                        </div>
                        <span class="font-medium">{{ filter.label }}</span>
                    </div>
                    <div v-if="activeFilter === filter.value" class="w-2 h-2 rounded-full bg-blue-500"></div>
                </button>
            </div>
            <div class="mt-6">
                <button 
                    @click="showFilterModal = false"
                    class="w-full py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-lg transition-colors dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-slate-300"
                >
                    Cancelar
                </button>
            </div>
        </div>
    </Modal>
</template>
