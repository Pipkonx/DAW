<script setup>
import { ref, computed } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { formatCurrency, formatPercent } from '@/Utils/formatting';

const props = defineProps({
    assets: {
        type: Array,
        required: true
    },
    selectedAssetId: {
        type: [String, Number],
        default: null
    }
});

const emit = defineEmits(['filter-asset', 'add-transaction']);

const assetFilter = ref('');

const filteredAssets = computed(() => {
    if (!assetFilter.value) return props.assets;
    const lower = assetFilter.value.toLowerCase();
    return props.assets.filter(a => 
        a.name.toLowerCase().includes(lower) || 
        a.ticker.toLowerCase().includes(lower)
    );
});

const onFilterAsset = (asset) => {
    emit('filter-asset', asset);
};

const onAddTransaction = () => {
    emit('add-transaction');
};
</script>

<template>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden dark:bg-slate-800 dark:border-slate-700">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4 dark:border-slate-700">
            <div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Posiciones Activas</h3>
                <p class="text-sm text-slate-500 mt-1 dark:text-slate-400">Desglose detallado de tus activos actuales</p>
            </div>
            <div class="flex items-center gap-2">
                 <TextInput
                    v-model="assetFilter"
                    placeholder="Filtrar por nombre o ticker..."
                    class="text-sm w-full sm:w-64"
                />
                <PrimaryButton @click="onAddTransaction">
                    + Añadir Transacción
                </PrimaryButton>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 dark:bg-slate-700 dark:text-slate-300">
                    <tr>
                        <th class="px-6 py-4">Activo</th>
                        <th class="px-6 py-4 text-right">Precio Actual</th>
                        <th class="px-6 py-4 text-right">Valor Total</th>
                        <th class="px-6 py-4 text-right">Retorno</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    <tr v-for="asset in filteredAssets" :key="asset.id" class="hover:bg-slate-50 transition-colors dark:hover:bg-slate-700" :class="{ 'bg-blue-50/50 dark:bg-blue-900/20': selectedAssetId == asset.id }">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white shadow-sm" :style="{ backgroundColor: asset.color }">
                                    {{ asset.ticker ? asset.ticker.substring(0,2) : asset.name.substring(0,2) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 text-base dark:text-white">{{ asset.ticker }}</div>
                                    <div class="text-sm text-slate-500 dark:text-slate-400">{{ asset.name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="font-medium text-slate-900 dark:text-white">{{ formatCurrency(asset.current_price) }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ asset.quantity }} un.</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-bold text-slate-900 text-base dark:text-white">{{ formatCurrency(asset.current_value) }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex flex-col items-end">
                                <span :class="asset.profit_loss >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'" class="font-bold">
                                    {{ asset.profit_loss >= 0 ? '+' : '' }}{{ formatPercent(asset.profit_loss_percentage) }}
                                </span>
                                <span :class="asset.profit_loss >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'" class="text-xs">
                                    {{ asset.profit_loss >= 0 ? '+' : '' }}{{ formatCurrency(asset.profit_loss) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button 
                                @click="onFilterAsset(asset)" 
                                class="p-2 rounded-lg hover:bg-slate-200 transition-colors dark:hover:bg-slate-600"
                                :class="selectedAssetId == asset.id ? 'text-blue-600 bg-blue-100 dark:bg-blue-900 dark:text-blue-300' : 'text-slate-400 dark:text-slate-500'"
                                title="Ver Historial de Operaciones"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <tr v-if="filteredAssets.length === 0">
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <div class="flex flex-col items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-300 dark:text-slate-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-lg font-medium text-slate-900 dark:text-white">No hay activos en esta cartera</p>
                                <p class="text-sm text-slate-500 mt-1 dark:text-slate-400">Añade tu primera transacción para ver tus posiciones</p>
                                <button @click="onAddTransaction" class="mt-4 text-blue-600 font-medium hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                    + Añadir Transacción
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
