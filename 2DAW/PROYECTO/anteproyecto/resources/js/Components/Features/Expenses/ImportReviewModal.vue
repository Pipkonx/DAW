<script setup>
import { ref, computed, watch } from 'vue';
import Modal from '@/Components/BaseUI/Modal.vue';
import PrimaryButton from '@/Components/BaseUI/PrimaryButton.vue';
import SecondaryButton from '@/Components/BaseUI/SecondaryButton.vue';
import TextInput from '@/Components/BaseUI/TextInput.vue';

const props = defineProps({
    show: Boolean,
    transactions: Array,
    categories: Array,
    portfolios: Array,
    isImporting: Boolean,
});

const emit = defineEmits(['close', 'confirm', 'remove']);

// --- LÓGICA DE AGRUPACIÓN POR AÑO ---
const groupedByYear = computed(() => {
    const groups = {};
    props.transactions.forEach((tx) => {
        const year = tx.date ? new Date(tx.date).getFullYear() : 'Desconocido';
        if (!groups[year]) groups[year] = [];
        groups[year].push(tx);
    });
    return groups;
});

const availableYears = computed(() => {
    return Object.keys(groupedByYear.value).sort((a, b) => b - a);
});

const selectedYear = ref('all');

// Inicializar con el año más reciente cuando se abre el modal (solo una vez)
watch(() => props.show, (newShow) => {
    if (newShow && availableYears.value.length > 0) {
        // Por defecto 'Ver todas', pero si hay muchas (>50), saltamos al año más reciente
        if (props.transactions.length > 50) {
            selectedYear.value = availableYears.value[0];
        } else {
            selectedYear.value = 'all';
        }
    }
});

const filteredTransactions = computed(() => {
    if (selectedYear.value === 'all') return props.transactions;
    return groupedByYear.value[selectedYear.value] || [];
});

const isInvestment = (type) => ['buy', 'sell', 'dividend'].includes(type);

const confirmImport = () => {
    emit('confirm');
};

const closeModal = () => {
    emit('close');
};

const removeTransaction = (index) => {
    // Necesitamos encontrar el índice real en el array original de props.transactions
    const txToRemove = filteredTransactions.value[index];
    const realIndex = props.transactions.findIndex(t => t === txToRemove);
    if (realIndex !== -1) {
        emit('remove', realIndex);
    }
};
</script>

<template>
    <Modal :show="show" @close="closeModal" maxWidth="7xl">
        <div class="p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Revisar Operaciones Importadas
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Total: {{ transactions.length }} operaciones detectadas.
                    </p>
                </div>

                <!-- SELECTOR DE AÑO -->
                <div v-if="availableYears.length > 1" class="flex flex-wrap items-center gap-2 bg-slate-100 dark:bg-slate-900/50 p-1 rounded-xl border border-slate-200 dark:border-slate-700">
                    <button 
                        @click="selectedYear = 'all'"
                        :class="[
                            'px-3 py-1.5 text-xs font-medium rounded-lg transition-all',
                            selectedYear === 'all' 
                                ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm' 
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400'
                        ]"
                    >
                        Ver todas
                    </button>
                    <button 
                        v-for="year in availableYears" 
                        :key="year"
                        @click="selectedYear = year"
                        :class="[
                            'px-3 py-1.5 text-xs font-medium rounded-lg transition-all flex items-center gap-2',
                            selectedYear === year 
                                ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm' 
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400'
                        ]"
                    >
                        {{ year }}
                        <span class="bg-slate-200 dark:bg-slate-700 text-[10px] px-1.5 py-0.5 rounded-full">{{ groupedByYear[year].length }}</span>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto border border-slate-200 dark:border-slate-700 rounded-xl mb-6 shadow-sm max-h-[600px] overflow-y-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 border-collapse">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Activo / Concepto</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Invocación / Cant.</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Importe Total</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Asignación (Cat/Cartera)</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                        <tr v-for="(tx, index) in filteredTransactions" :key="index" :class="{'bg-indigo-50/30 dark:bg-indigo-900/10': isInvestment(tx.type)}">
                            <td class="px-4 py-2 whitespace-nowrap">
                                <TextInput type="date" v-model="tx.date" class="text-xs p-1 h-8" />
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <select v-model="tx.type" class="text-xs border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm p-1.5 h-8">
                                    <optgroup label="Comunes">
                                        <option value="expense">Gasto</option>
                                        <option value="income">Ingreso</option>
                                    </optgroup>
                                    <optgroup label="Inversiones">
                                        <option value="buy">Compra (Inversión)</option>
                                        <option value="sell">Venta (Inversión)</option>
                                        <option value="dividend">Dividendo</option>
                                    </optgroup>
                                </select>
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex flex-col gap-1 min-w-[150px]">
                                    <TextInput type="text" v-model="tx.asset_name" class="text-xs p-1 font-medium h-8" placeholder="Nombre del activo..." />
                                    <span v-if="isInvestment(tx.type)" class="text-[10px] text-indigo-500 dark:text-indigo-400 font-semibold px-1 uppercase tracking-tighter">Activo de Inversión</span>
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                <div v-if="isInvestment(tx.type)" class="flex items-center gap-2">
                                    <div class="flex flex-col gap-0.5">
                                        <label class="text-[9px] text-slate-400 uppercase">Cant.</label>
                                        <TextInput type="number" step="any" v-model.number="tx.quantity" class="text-xs p-1 h-7 w-20" placeholder="Qty" />
                                    </div>
                                    <div class="flex flex-col gap-0.5">
                                        <label class="text-[9px] text-slate-400 uppercase">Precio Unit.</label>
                                        <TextInput type="number" step="any" v-model.number="tx.price_per_unit" class="text-xs p-1 h-7 w-24" placeholder="Precio" @input="tx.amount = tx.quantity * tx.price_per_unit" />
                                    </div>
                                </div>
                                <div v-else class="text-slate-400 text-xs">-</div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <div class="flex flex-col gap-0.5">
                                    <label class="text-[9px] text-slate-400 uppercase">Importe (€)</label>
                                    <TextInput type="number" step="0.01" v-model.number="tx.amount" class="text-xs p-1 font-bold h-8 w-28" />
                                </div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <div v-if="isInvestment(tx.type)" class="flex flex-col gap-1">
                                    <label class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400">Cartera:</label>
                                    <select v-model="tx.portfolio_id" class="text-xs border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm p-1.5 h-8">
                                        <option v-for="pf in portfolios" :key="pf.id" :value="pf.id">{{ pf.name }}</option>
                                        <option :value="null">-- Crear Nueva --</option>
                                    </select>
                                    <TextInput 
                                        v-if="!tx.portfolio_id" 
                                        v-model="tx.portfolio_name" 
                                        class="text-[10px] p-1 h-6 border-dashed" 
                                        placeholder="Nombre de la cartera..." 
                                    />
                                </div>
                                <div v-else class="flex flex-col gap-1">
                                    <select v-model="tx.category_id" class="text-xs border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm p-1.5 h-8">
                                        <option :value="null">Nueva: (usar nombre)</option>
                                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                    </select>
                                    <TextInput 
                                        v-if="!tx.category_id" 
                                        v-model="tx.category_name" 
                                        class="text-[10px] p-1 h-6 border-dashed" 
                                        placeholder="Crear categoría..." 
                                    />
                                </div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right">
                                <button @click="removeTransaction(index)" class="text-rose-500 hover:text-rose-700 bg-rose-50 dark:bg-rose-900/20 p-2 rounded-lg transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="filteredTransactions.length === 0">
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <span class="text-sm font-medium">No hay operaciones para este periodo.</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div v-if="isImporting" class="flex items-center gap-2 text-indigo-500">
                         <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm font-medium">Importando todas las operaciones...</span>
                    </div>
                </div>
                <div class="flex gap-3">
                    <SecondaryButton @click="closeModal" :disabled="isImporting">
                        Cancelar
                    </SecondaryButton>
                    <PrimaryButton @click="confirmImport" :disabled="isImporting || transactions.length === 0">
                        Confirmar y Guardar Todo ({{ transactions.length }})
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </Modal>
</template>


