<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { formatCurrency, formatDate } from '@/Utils/formatting';

const props = defineProps({
    transactions: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['export', 'edit']);

const selectedTransactions = ref([]);

const toggleSelection = (id) => {
    if (selectedTransactions.value.includes(id)) {
        selectedTransactions.value = selectedTransactions.value.filter(txId => txId !== id);
    } else {
        selectedTransactions.value.push(id);
    }
};

const toggleAll = () => {
    const allIds = props.transactions.data.map(tx => tx.id);
    const allSelected = allIds.every(id => selectedTransactions.value.includes(id));
    
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

const groupedTransactions = computed(() => {
    if (!props.transactions.data) return [];
    
    const groups = {};
    props.transactions.data.forEach(tx => {
        const date = new Date(tx.date);
        const monthYear = date.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' }).toUpperCase();
        
        if (!groups[monthYear]) {
            groups[monthYear] = [];
        }
        groups[monthYear].push(tx);
    });
    
    return Object.keys(groups).map(key => ({
        monthYear: key,
        items: groups[key]
    }));
});

const onExport = (format) => {
    emit('export', format);
};
</script>

<template>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden dark:bg-slate-800 dark:border-slate-700">
        <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Historial de Operaciones</h3>
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
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 dark:bg-slate-700 dark:text-slate-300">
                    <tr>
                        <th class="px-6 py-3 w-4">
                            <input 
                                type="checkbox" 
                                :checked="transactions.data.length > 0 && transactions.data.every(tx => selectedTransactions.includes(tx.id))"
                                @change="toggleAll"
                                class="rounded border-slate-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            />
                        </th>
                        <th class="px-6 py-3">Fecha</th>
                        <th class="px-6 py-3">Tipo</th>
                        <th class="px-6 py-3">Activo</th>
                        <th class="px-6 py-3 text-right">Cantidad</th>
                        <th class="px-6 py-3 text-right">Precio</th>
                        <th class="px-6 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    <template v-for="group in groupedTransactions" :key="group.monthYear">
                        <tr class="bg-slate-50/50 dark:bg-slate-700/30">
                            <td colspan="7" class="px-6 py-2 text-xs font-bold text-slate-400 dark:text-slate-500 text-center tracking-widest uppercase">
                                ─ ─ ─ ─ ─ {{ group.monthYear }} ─ ─ ─ ─ ─
                            </td>
                        </tr>
                        <tr v-for="tx in group.items" :key="tx.id" 
                            @click="$emit('edit', tx)"
                            class="hover:bg-slate-50 transition-colors dark:hover:bg-slate-700 cursor-pointer">
                            <td class="px-6 py-4 whitespace-nowrap" @click.stop>
                                <input 
                                    type="checkbox" 
                                    :checked="selectedTransactions.includes(tx.id)"
                                    @change="toggleSelection(tx.id)"
                                    class="rounded border-slate-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400">{{ formatDate(tx.date) }}</td>
                            <td class="px-6 py-4">
                                <span :class="{
                                    'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300': ['dividend', 'reward', 'gift'].includes(tx.type),
                                    'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300': tx.type === 'buy',
                                    'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300': tx.type === 'sell',
                                    'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300': !['buy', 'sell', 'dividend', 'reward', 'gift'].includes(tx.type)
                                }" class="px-2 py-1 rounded-full text-xs font-semibold capitalize">
                                    {{ tx.type === 'buy' ? 'Compra' : (tx.type === 'sell' ? 'Venta' : tx.type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">{{ tx.asset ? tx.asset.ticker : '-' }}</td>
                            <td class="px-6 py-4 text-right dark:text-slate-300">{{ parseFloat(tx.quantity) }}</td>
                            <td class="px-6 py-4 text-right dark:text-slate-300">{{ formatCurrency(tx.price_per_unit) }}</td>
                            <td class="px-6 py-4 text-right font-bold text-slate-800 dark:text-white">{{ formatCurrency(tx.amount) }}</td>
                        </tr>
                    </template>
                    <tr v-if="groupedTransactions.length === 0">
                        <td colspan="7" class="px-6 py-8 text-center text-slate-500 italic dark:text-slate-400">
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
</template>
