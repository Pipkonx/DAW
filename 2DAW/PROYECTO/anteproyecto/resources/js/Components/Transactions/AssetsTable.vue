<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { formatCurrency, formatPercent } from '@/Utils/formatting';
import { usePrivacy } from '@/Composables/usePrivacy';

const { isPrivacyMode } = usePrivacy();

const props = defineProps({
    assets: {
        type: Array,
        required: true
    },
    selectedAssetId: {
        type: [String, Number, Array],
        default: null
    }
});

const emit = defineEmits(['filter-asset', 'add-transaction', 'delete-asset']);

const bulkSelectedAssets = ref([]);

const toggleBulkSelection = (id) => {
    if (bulkSelectedAssets.value.includes(id)) {
        bulkSelectedAssets.value = bulkSelectedAssets.value.filter(aId => aId !== id);
    } else {
        bulkSelectedAssets.value.push(id);
    }
};

const toggleAllBulk = () => {
    const allIds = filteredAssets.value.map(a => a.id);
    const allSelected = allIds.length > 0 && allIds.every(id => bulkSelectedAssets.value.includes(id));
    
    if (allSelected) {
        bulkSelectedAssets.value = [];
    } else {
        bulkSelectedAssets.value = allIds;
    }
};

const deleteBulkSelected = () => {
    if (confirm(`¿Estás seguro de que quieres eliminar ${bulkSelectedAssets.value.length} activos? Esto borrará TODAS las operaciones asociadas de forma irreversible.`)) {
        router.delete(route('assets.bulk-destroy'), {
            data: { ids: bulkSelectedAssets.value },
            preserveScroll: true,
            onSuccess: () => {
                bulkSelectedAssets.value = [];
            }
        });
    }
};

const onDeleteAsset = (asset) => {
    if (confirm(`¿Estás seguro de que quieres eliminar ${asset.name}? Esto borrará TODAS las operaciones asociadas de forma irreversible.`)) {
        emit('delete-asset', asset);
    }
};

const assetFilter = ref('');

// Period Selector State
const selectedPeriod = ref('all'); // '1d', '1w', '1m', 'ytd', '1y', 'all'
const periods = [
    { value: '1d', label: '1D' },
    { value: '1w', label: '1S' },
    { value: '1m', label: '1M' },
    { value: 'ytd', label: 'YTD' },
    { value: '1y', label: '1A' },
    { value: 'all', label: 'Max' }
];

const filteredAssets = computed(() => {
    // Just simple search filter
    if (!assetFilter.value) return props.assets;
    const lower = assetFilter.value.toLowerCase();
    return props.assets.filter(a => 
        a.name.toLowerCase().includes(lower) || 
        a.ticker.toLowerCase().includes(lower)
    );
});

const isSelected = (assetId) => {
    if (!props.selectedAssetId) return false;
    const id = String(assetId);
    
    if (Array.isArray(props.selectedAssetId)) {
        return props.selectedAssetId.map(String).includes(id);
    }
    
    if (typeof props.selectedAssetId === 'string' && props.selectedAssetId.includes(',')) {
        return props.selectedAssetId.split(',').map(s => s.trim()).includes(id);
    }
    
    return String(props.selectedAssetId) === id;
};

const onFilterAsset = (asset) => {
    emit('filter-asset', asset);
};

const onAddTransaction = () => {
    emit('add-transaction');
};

// Mock function to simulate P/L change based on period (Since we don't have historical data for all periods yet)
// In a real app, this would fetch or compute historical performance
const getAssetPerformance = (asset) => {
    // If period is 'all' (Max), use the total profit/loss
    if (selectedPeriod.value === 'all') {
        return {
            value: asset.profit_loss,
            percentage: asset.profit_loss_percentage
        };
    }
    
    // For other periods, we would ideally need historical data points.
    // For now, we will just show the total P/L as a placeholder or 0 if not available,
    // but the UI is prepared for the switch.
    // TODO: Implement historical performance calculation per asset
    return {
        value: asset.profit_loss, // Placeholder
        percentage: asset.profit_loss_percentage // Placeholder
    };
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
                <button 
                    v-if="bulkSelectedAssets.length > 0"
                    @click="deleteBulkSelected"
                    class="px-4 py-2 text-sm font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors border border-rose-200 dark:bg-rose-900/20 dark:text-rose-400 dark:border-rose-900/50 dark:hover:bg-rose-900/40"
                >
                    Eliminar ({{ bulkSelectedAssets.length }})
                </button>
                
                <!-- Period Selector -->
                <div class="flex bg-slate-100 dark:bg-slate-700 rounded-lg p-1 mr-2">
                    <button 
                        v-for="period in periods" 
                        :key="period.value"
                        @click="selectedPeriod = period.value"
                        class="px-3 py-1 text-xs font-medium rounded-md transition-all"
                        :class="selectedPeriod === period.value 
                            ? 'bg-white text-blue-600 shadow-sm dark:bg-slate-600 dark:text-white' 
                            : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                    >
                        {{ period.label }}
                    </button>
                </div>

                <PrimaryButton @click="onAddTransaction">
                    + Añadir Transacción
                </PrimaryButton>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 dark:bg-slate-700 dark:text-slate-300">
                    <tr>
                        <th class="px-6 py-4 w-4">
                            <input 
                                type="checkbox" 
                                :checked="filteredAssets.length > 0 && filteredAssets.every(a => bulkSelectedAssets.includes(a.id))"
                                @change="toggleAllBulk"
                                class="rounded border-slate-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            />
                        </th>
                        <th class="px-6 py-4">Activo</th>
                        <th class="px-6 py-4 text-right">Precio / Valor</th>
                        <th class="px-6 py-4 text-right">Retorno</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    <tr v-for="asset in filteredAssets" :key="asset.id" class="hover:bg-slate-50 transition-colors dark:hover:bg-slate-700" :class="{ 'bg-blue-50/50 dark:bg-blue-900/20': isSelected(asset.id) }">
                        <td class="px-6 py-4" @click.stop>
                            <input 
                                type="checkbox" 
                                :checked="bulkSelectedAssets.includes(asset.id)"
                                @change="toggleBulkSelection(asset.id)"
                                class="rounded border-slate-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            />
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white shadow-sm" :style="{ backgroundColor: asset.color }">
                                    {{ asset.ticker ? asset.ticker.substring(0,2) : asset.name.substring(0,2) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 text-base dark:text-white">{{ asset.name }}</div>
                                    <div class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                        <span v-if="asset.ticker && asset.ticker !== asset.isin" class="font-medium text-slate-700 dark:text-slate-300">{{ asset.ticker }}</span>
                                        <span v-if="asset.isin" class="text-xs text-slate-400">{{ asset.isin }}</span>
                                        <span class="text-xs bg-slate-100 dark:bg-slate-700 px-1.5 py-0.5 rounded text-slate-600 dark:text-slate-300">
                                            {{ isPrivacyMode ? '****' : 'x' + parseFloat(asset.quantity).toLocaleString('es-ES') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="font-medium text-slate-900 dark:text-white">{{ isPrivacyMode ? '****' : formatCurrency(asset.current_price) }}</div>
                            <div class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ isPrivacyMode ? '****' : formatCurrency(asset.current_value) }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div v-if="isPrivacyMode" class="flex flex-col items-end">
                                <span class="font-bold text-slate-400">****</span>
                                <span class="text-xs text-slate-400">****</span>
                            </div>
                            <div v-else class="flex flex-col items-end">
                                <span :class="getAssetPerformance(asset).value >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'" class="font-bold">
                                    {{ getAssetPerformance(asset).value >= 0 ? '+' : '' }}{{ formatCurrency(getAssetPerformance(asset).value) }}
                                </span>
                                <span :class="getAssetPerformance(asset).value >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'" class="text-xs">
                                    {{ getAssetPerformance(asset).value >= 0 ? '+' : '' }}{{ formatPercent(getAssetPerformance(asset).percentage) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button 
                                @click="onFilterAsset(asset)" 
                                class="p-2 rounded-lg hover:bg-slate-200 transition-colors dark:hover:bg-slate-600"
                                :class="isSelected(asset.id) ? 'text-blue-600 bg-blue-100 dark:bg-blue-900 dark:text-blue-300' : 'text-slate-400 dark:text-slate-500'"
                                title="Ver Historial de Operaciones"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </button>
                            <button 
                                @click.stop="onDeleteAsset(asset)" 
                                class="p-2 rounded-lg hover:bg-rose-100 text-slate-400 hover:text-rose-600 transition-colors dark:hover:bg-rose-900/30 dark:hover:text-rose-400"
                                title="Eliminar Activo"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <tr v-if="filteredAssets.length === 0">
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
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
