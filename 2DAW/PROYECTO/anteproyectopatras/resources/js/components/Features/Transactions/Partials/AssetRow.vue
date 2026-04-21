<script setup>
import { Link } from '@inertiajs/vue3';
import { formatCurrency, formatPercent } from '@/Utils/formatting';

const props = defineProps({
    asset: { type: Object, required: true },
    isSelected: { type: Boolean, default: false },
    isBulkSelected: { type: Boolean, default: false },
    isPrivacyMode: { type: Boolean, default: false },
    performance: { type: Object, required: true }
});

const emit = defineEmits(['toggle-bulk', 'filter', 'delete']);

const toggleBulk = () => emit('toggle-bulk', props.asset.id);
</script>

<template>
    <tr 
        class="hover:bg-slate-50 transition-colors dark:hover:bg-slate-700" 
        :class="{ 'bg-blue-50/50 dark:bg-blue-900/20': isSelected }"
    >
        <td class="px-6 py-4" @click.stop>
            <input 
                type="checkbox" 
                :checked="isBulkSelected"
                @change="toggleBulk"
                class="rounded border-slate-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
            />
        </td>
        <td class="px-6 py-4">
            <Link :href="route('assets.show', asset.ticker || asset.isin)" class="flex items-center space-x-4 group/asset">
                <div v-if="asset.logo" class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700 flex items-center justify-center shadow-sm group-hover/asset:ring-2 group-hover/asset:ring-blue-500 transition-all">
                    <img :src="asset.logo" class="w-full h-full object-cover" @error="asset.logo = null" />
                </div>
                <div v-else class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white shadow-sm group-hover/asset:ring-2 group-hover/asset:ring-blue-500 transition-all" :style="{ backgroundColor: asset.color || '#3b82f6' }">
                    {{ asset.ticker ? asset.ticker.substring(0,2) : asset.name.substring(0,2) }}
                </div>
                <div>
                    <div class="font-bold text-slate-900 text-base dark:text-white group-hover/asset:text-blue-600 dark:group-hover/asset:text-blue-400 transition-colors">{{ asset.name }}</div>
                    <div class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-2">
                        <span v-if="asset.ticker && asset.ticker !== asset.isin" class="font-medium text-slate-700 dark:text-slate-300">{{ asset.ticker }}</span>
                        <span v-if="asset.isin" class="text-xs text-slate-400">{{ asset.isin }}</span>
                        <span class="text-xs bg-slate-100 dark:bg-slate-700 px-1.5 py-0.5 rounded text-slate-600 dark:text-slate-300">
                            {{ isPrivacyMode ? '****' : 'x' + parseFloat(asset.quantity).toLocaleString('es-ES') }}
                        </span>
                    </div>
                </div>
            </Link>
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
                <span :class="performance.value >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'" class="font-bold">
                    {{ performance.value >= 0 ? '+' : '' }}{{ formatCurrency(performance.value) }}
                </span>
                <span :class="performance.value >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'" class="text-xs">
                    {{ performance.value >= 0 ? '+' : '' }}{{ formatPercent(performance.percentage) }}
                </span>
            </div>
        </td>
        <td class="px-6 py-4 text-right space-x-2">
            <button 
                @click="emit('filter', asset)" 
                class="p-2 rounded-lg hover:bg-slate-200 transition-colors dark:hover:bg-slate-600"
                :class="isSelected ? 'text-blue-600 bg-blue-100 dark:bg-blue-900 dark:text-blue-300' : 'text-slate-400 dark:text-slate-500'"
                title="Ver Historial de Operaciones"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </button>
            <button 
                @click.stop="emit('delete', asset)" 
                class="p-2 rounded-lg hover:bg-rose-100 text-slate-400 hover:text-rose-600 transition-colors dark:hover:bg-rose-900/30 dark:hover:text-rose-400"
                title="Eliminar Activo"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </td>
    </tr>
</template>
