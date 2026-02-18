<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { formatCurrency, formatDate } from '@/Utils/formatting';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    transactions: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['export', 'edit']);

const selectedTransactions = ref([]);
const activeFilter = ref('all');
const showFilterModal = ref(false);
const searchQuery = ref('');

const toggleSelection = (id) => {
    if (selectedTransactions.value.includes(id)) {
        selectedTransactions.value = selectedTransactions.value.filter(txId => txId !== id);
    } else {
        selectedTransactions.value.push(id);
    }
};

const toggleAll = () => {
    const allIds = filteredTransactions.value.flatMap(group => group.items.map(tx => tx.id));
    const allSelected = allIds.length > 0 && allIds.every(id => selectedTransactions.value.includes(id));
    
    if (allSelected) {
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

const filterTypes = [
    { value: 'all', label: 'Todos', icon: 'M4 6h16M4 12h16M4 18h16' },
    { value: 'buy', label: 'Compra', icon: 'M12 4v16m8-8H4' },
    { value: 'sell', label: 'Venta', icon: 'M20 12H4' },
    { value: 'dividend', label: 'Dividendos', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    { value: 'staking', label: 'Staking', icon: 'M13 10V3L4 14h7v7l9-11h-7z' },
    { value: 'interest', label: 'Interés', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
    { value: 'coupon', label: 'Cupón', icon: 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z' }
];

const activeFilterLabel = computed(() => {
    return filterTypes.find(f => f.value === activeFilter.value)?.label || 'Todos';
});

const filteredTransactions = computed(() => {
    if (!props.transactions.data) return [];
    
    let filtered = props.transactions.data;
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
    const qty = parseFloat(tx.quantity);
    const price = formatCurrency(tx.price_per_unit);
    
    switch (tx.type) {
        case 'buy': return `Compró ${qty} a ${price}`;
        case 'sell': return `Vendió ${qty} a ${price}`;
        case 'dividend': return `Dividendo recibido`;
        case 'reward': return `Recompensa recibida`;
        case 'gift': return `Regalo recibido`;
        default: return `${tx.description || tx.type}`;
    }
};

const getTypeIcon = (type) => {
    switch (type) {
        case 'buy': return 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400';
        case 'sell': return 'bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400';
        case 'dividend': return 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400';
        default: return 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400';
    }
};

const getTypeIconSvg = (type) => {
     switch (type) {
        case 'buy': return 'M12 4v16m8-8H4'; // Plus
        case 'sell': return 'M20 12H4'; // Minus
        case 'dividend': return 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'; // Dollar
        default: return 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'; // Info
    }
}

const onExport = (format) => {
    emit('export', format);
};
</script>

<template>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden dark:bg-slate-800 dark:border-slate-700">
        <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex flex-col md:flex-row justify-between items-center gap-4">
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
            </div>

            <div class="flex gap-2 items-center">
                <button 
                    v-if="selectedTransactions.length > 0"
                    @click="deleteSelected"
                    class="px-3 py-1 text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors border border-rose-200 dark:bg-rose-900/20 dark:text-rose-400 dark:border-rose-900/50 dark:hover:bg-rose-900/40"
                >
                    Eliminar ({{ selectedTransactions.length }})
                </button>
                <div class="h-4 w-px bg-slate-200 dark:bg-slate-700 mx-1"></div>
                <button @click="onExport('pdf')" class="px-3 py-1 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-900/50 dark:hover:bg-red-900/40">
                    PDF
                </button>
                <button @click="onExport('excel')" class="px-3 py-1 text-xs font-medium text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors border border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-900/50 dark:hover:bg-emerald-900/40">
                    Excel
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <!-- Cabecera Oculta o Simplificada -->
                <thead class="sr-only text-xs text-slate-500 uppercase bg-slate-50 dark:bg-slate-700 dark:text-slate-300">
                    <tr>
                        <th class="px-6 py-3 w-4">Select</th>
                        <th class="px-6 py-3">Detalle</th>
                        <th class="px-6 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    <template v-for="group in filteredTransactions" :key="group.monthYear">
                        <!-- Cabecera de Mes: Alineada a la izquierda y con formato limpio -->
                        <tr class="bg-slate-50/50 dark:bg-slate-700/30">
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
                                            {{ getActionText(tx) }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Total -->
                            <td class="px-6 py-4 text-right align-middle">
                                <span class="font-bold text-slate-800 dark:text-white block">
                                    {{ formatCurrency(tx.amount) }}
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
        <!-- Paginación Simple -->
        <div class="p-4 flex justify-center space-x-2" v-if="transactions.links && transactions.links.length > 3">
            <template v-for="(link, k) in transactions.links" :key="k">
                <component 
                    :is="link.url ? Link : 'span'" 
                    :href="link.url" 
                    v-html="link.label"
                    class="px-3 py-1 text-xs rounded-md border transition-colors"
                    :class="link.active ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 dark:hover:bg-slate-700'"
                />
            </template>
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
