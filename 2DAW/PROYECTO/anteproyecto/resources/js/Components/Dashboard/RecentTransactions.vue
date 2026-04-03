<script setup>
/**
 * RecentTransactions - Dashboard Component
 * 
 * Especializado en el listado histórico de transacciones.
 * Implementa:
 * - Filtros dinámicos (Ingresos, Gastos, Inversiones).
 * - Scroll infinito mediante Intersection Observer.
 * - Diccionario semántico de iconos y colores por tipo.
 */
import { computed } from 'vue';
import { formatCurrency } from '@/Utils/formatting';

const props = defineProps({
    transactions: {
        type: Array,
        required: true
    },
    filter: {
        type: String,
        required: true
    },
    loadingMore: {
        type: Boolean,
        default: false
    },
    isPrivacyMode: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:filter', 'edit']);

// Configuración visual de los tipos de transacción
const transactionTypes = {
    income: { label: 'Ingreso', color: 'bg-emerald-50 text-emerald-700 border-emerald-100', icon: '↓' },
    expense: { label: 'Gasto', color: 'bg-rose-50 text-rose-700 border-rose-100', icon: '↑' },
    buy: { label: 'Compra', color: 'bg-blue-50 text-blue-700 border-blue-100', icon: 'BUY' },
    sell: { label: 'Venta', color: 'bg-indigo-50 text-indigo-700 border-indigo-100', icon: 'SELL' },
    dividend: { label: 'Dividendo', color: 'bg-amber-50 text-amber-700 border-amber-100', icon: '$' },
    gift: { label: 'Regalo', color: 'bg-pink-50 text-pink-700 border-pink-100', icon: '♥' },
    reward: { label: 'Recompensa', color: 'bg-purple-50 text-purple-700 border-purple-100', icon: '★' },
    transfer_in: { label: 'Transf. (Entrada)', color: 'bg-gray-50 text-gray-700 border-gray-100', icon: '→' },
    transfer_out: { label: 'Transf. (Salida)', color: 'bg-gray-50 text-gray-700 border-gray-100', icon: '←' },
};

// Agrupación automática de transacciones por mes/año para la vista
const groupedTransactions = computed(() => {
    const groups = {};
    
    // Filtrado inicial por tipo (frontend) para respuesta inmediata
    const filtered = props.transactions.filter(tx => {
        if (props.filter === 'all') return true;
        if (props.filter === 'income') return ['income', 'reward', 'dividend', 'gift', 'transfer_in'].includes(tx.type);
        if (props.filter === 'expense') return ['expense', 'transfer_out'].includes(tx.type);
        if (props.filter === 'investment') return ['buy', 'sell'].includes(tx.type);
        return true;
    });
    
    filtered.forEach(tx => {
        const date = new Date(tx.date);
        const monthYear = date.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' });
        const title = monthYear.charAt(0).toUpperCase() + monthYear.slice(1);
        
        if (!groups[title]) groups[title] = [];
        groups[title].push(tx);
    });
    
    return Object.keys(groups).map(key => ({
        monthYear: key,
        items: groups[key]
    }));
});

// Ayudante para determinar si un monto es negativo (gasto/compra)
const isNegative = (type) => ['expense', 'buy', 'transfer_out'].includes(type);

// Emisión de eventos
const setFilter = (val) => emit('update:filter', val);
const edit = (tx) => emit('edit', tx);
</script>

<template>
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Últimas Transacciones</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Haz clic en una transacción para editarla.</p>
            </div>

            <!-- Filtros Rápidos -->
            <div class="flex space-x-2 overflow-x-auto pb-1 md:pb-0 w-full md:w-auto">
                <button 
                    v-for="f in [
                        { id: 'all', label: 'Todas' },
                        { id: 'income', label: 'Ingresos' },
                        { id: 'expense', label: 'Gastos' },
                        { id: 'investment', label: 'Inversiones' }
                    ]" 
                    :key="f.id"
                    @click="setFilter(f.id)"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border transition-colors whitespace-nowrap"
                    :class="props.filter === f.id 
                        ? 'bg-slate-800 dark:bg-blue-600 text-white border-slate-800 dark:border-blue-600' 
                        : 'bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-600 hover:border-slate-300 dark:hover:border-slate-500'"
                >
                    {{ f.label }}
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 uppercase font-medium text-xs">
                    <tr>
                        <th class="px-6 py-3">Fecha</th>
                        <th class="px-6 py-3">Tipo</th>
                        <th class="px-6 py-3">Descripción / Activo</th>
                        <th class="px-6 py-3">Categoría</th>
                        <th class="px-6 py-3 text-right">Monto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    <template v-for="group in groupedTransactions" :key="group.monthYear">
                        <!-- Cabecera de Mes -->
                        <tr class="bg-slate-50/50 dark:bg-slate-700/30">
                            <td colspan="5" class="px-6 py-2 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                {{ group.monthYear }}
                            </td>
                        </tr>
                        <tr 
                            v-for="transaction in group.items" 
                            :key="transaction.id" 
                            @click="edit(transaction)"
                            class="hover:bg-blue-50 dark:hover:bg-slate-700/50 cursor-pointer transition-colors group"
                        >
                            <td class="px-6 py-4 whitespace-nowrap text-slate-600 dark:text-slate-300">{{ transaction.display_date }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium border flex items-center w-fit gap-1" :class="transactionTypes[transaction.type]?.color || 'bg-gray-100 text-gray-600'">
                                    <span>{{ transactionTypes[transaction.type]?.icon }}</span>
                                    {{ transactionTypes[transaction.type]?.label || transaction.type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-200 font-medium">
                                <div class="flex items-center">
                                    <img 
                                        v-if="transaction.asset_logo" 
                                        :src="transaction.asset_logo" 
                                        class="w-6 h-6 rounded-full mr-2 bg-slate-100 dark:bg-slate-700" 
                                        alt="logo" 
                                        @error="transaction.asset_logo = null"
                                    />
                                    <span>{{ transaction.description || transaction.asset_name || transaction.category || '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ transaction.category || '-' }}</td>
                            <td class="px-6 py-4 text-right font-bold" :class="isNegative(transaction.type) ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400'">
                                <span v-if="isPrivacyMode">****</span>
                                <span v-else>{{ isNegative(transaction.type) ? '-' : '+' }}{{ formatCurrency(transaction.amount) }}</span>
                            </td>
                        </tr>
                    </template>
                    <tr v-if="groupedTransactions.length === 0">
                        <td colspan="5" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500">
                            No hay transacciones registradas bajo este filtro.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Indicador de Carga - Scroll Infinito -->
        <div class="py-6 flex justify-center bg-slate-50/50 dark:bg-slate-700/20">
            <div v-if="loadingMore" class="flex items-center space-x-2 text-blue-600 dark:text-blue-400">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-xs font-medium">Cargando transacciones...</span>
            </div>
            <div v-else class="text-xs text-slate-400">
                Fin del historial
            </div>
        </div>
    </div>
</template>
