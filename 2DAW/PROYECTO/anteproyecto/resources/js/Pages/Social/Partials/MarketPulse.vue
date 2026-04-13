<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

/**
 * MarketPulse - Monitor de actividad y tendencias del mercado.
 * 
 * Centraliza diferentes métricas (Volumen, Negociación, Social) para dar una 
 * visión rápida del sentimiento del inversor.
 */
const props = defineProps({
    mostActive: Array,
    trends: Array,
});

const activeTrendTab = ref('negotiated'); // 'negotiated' | 'volume' | 'social'

/**
 * Garantiza que siempre se devuelvan exactamente 5 elementos para el renderizado.
 * Rellena con nulls para mostrar skeletons si es necesario.
 */
const displayPulse = computed(() => {
    let data = [];
    if (activeTrendTab.value === 'negotiated') data = props.mostActive || [];
    else if (activeTrendTab.value === 'volume') data = [...(props.mostActive || [])].sort((a,b) => (b.business_volume || 0) - (a.business_volume || 0));
    else if (activeTrendTab.value === 'social') data = props.trends || [];
    
    const sliced = data.slice(0, 5);
    const pads = Array(Math.max(0, 5 - sliced.length)).fill(null);
    return [...sliced, ...pads];
});

/**
 * Formatea números grandes (K, M, B) para una mejor visualización en widgets.
 */
const formatCompactNumber = (number) => {
    if (!number) return '0';
    if (number >= 1000000000) return (number / 1000000000).toFixed(1) + 'B';
    if (number >= 1000000) return (number / 1000000).toFixed(1) + 'M';
    if (number >= 1000) return (number / 1000).toFixed(1) + 'K';
    return number.toFixed(0);
};
</script>

<template>
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700">
        <!-- Cabecera con Indicador "Live" -->
        <h3 class="text-sm font-black uppercase tracking-widest text-slate-400 mb-6 flex items-center gap-2">
            <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span>
            Pulso del Mercado
        </h3>
        
        <!-- Selector de Métricas (Tabs) -->
        <div class="flex p-1 bg-slate-50 dark:bg-slate-900/50 rounded-xl mb-6">
            <button @click="activeTrendTab = 'negotiated'" :class="activeTrendTab === 'negotiated' ? 'bg-white dark:bg-slate-800 text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-1.5 text-[10px] font-black uppercase tracking-tighter rounded-lg transition-all">Negociados</button>
            <button @click="activeTrendTab = 'volume'" :class="activeTrendTab === 'volume' ? 'bg-white dark:bg-slate-800 text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-1.5 text-[10px] font-black uppercase tracking-tighter rounded-lg transition-all">Volumen $</button>
            <button @click="activeTrendTab = 'social'" :class="activeTrendTab === 'social' ? 'bg-white dark:bg-slate-800 text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-1.5 text-[10px] font-black uppercase tracking-tighter rounded-lg transition-all">Social</button>
        </div>

        <!-- Listado Dinámico según la Pestaña Activa -->
        <div class="space-y-5">
            <template v-for="(asset, index) in displayPulse" :key="asset ? (asset.ticker || asset.id) : 'pulse-skeleton-' + index">
                <!-- Estado Real -->
                <div v-if="asset" class="flex items-center gap-3 group cursor-pointer animate-in fade-in slide-in-from-right-2 duration-300" @click="router.get(route('assets.show', asset.ticker))">
                    <div class="w-9 h-9 rounded-xl bg-slate-50 dark:bg-slate-700 flex items-center justify-center border border-slate-100 dark:border-slate-600 overflow-hidden shrink-0">
                        <img :src="asset.logo || asset.image" class="w-6 h-6 object-contain" />
                    </div>
                    <div class="flex-grow min-w-0">
                        <div class="text-[13px] font-bold text-slate-800 dark:text-white truncate group-hover:text-blue-600 transition-colors uppercase">{{ asset.ticker }}</div>
                        <div class="flex justify-between items-center mt-0.5">
                            <span v-if="activeTrendTab === 'social'" class="text-[10px] text-slate-400 font-bold uppercase">{{ asset.count }} menciones</span>
                            <span v-else class="text-[10px] text-slate-400 font-bold uppercase">{{ formatCompactNumber(asset.volume) }} acciones</span>
                            
                            <span v-if="activeTrendTab === 'social'" class="text-[10px] text-indigo-500 font-black">+{{ asset.change }}%</span>
                            <span v-else class="text-[10px] font-black" :class="(asset.change || 0) >= 0 ? 'text-emerald-500' : 'text-rose-500'">{{ (asset.change || 0) >= 0 ? '+' : '' }}{{ (asset.change || 0).toFixed(2) }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Estado Skeleton (Placeholder) -->
                <div v-else class="flex items-center gap-3 opacity-20 grayscale blur-[0.5px]">
                    <div class="w-9 h-9 rounded-xl bg-slate-50 dark:bg-slate-700 border border-slate-100 dark:border-slate-600 shrink-0 animate-pulse"></div>
                    <div class="flex-grow space-y-1.5">
                        <div class="h-3 w-12 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"></div>
                        <div class="flex justify-between items-center">
                            <div class="h-2.5 w-20 bg-slate-100 dark:bg-slate-600 rounded animate-pulse"></div>
                            <div class="h-2.5 w-8 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"></div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
