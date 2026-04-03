<script setup>
import { ref } from 'vue';
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
 * Formatea números grandes (K, M, B) para una mejor visualización en widgets.
 */
const formatCompactNumber = (number) => {
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
            <!-- Caso A: Más Negociados (Volumen de Acciones) -->
            <template v-if="activeTrendTab === 'negotiated'">
                <div v-for="asset in mostActive" :key="'neg-' + asset.ticker" class="flex items-center gap-3 group cursor-pointer" @click="router.get(route('assets.show', asset.ticker))">
                    <div class="w-9 h-9 rounded-xl bg-slate-50 dark:bg-slate-700 flex items-center justify-center border border-slate-100 dark:border-slate-600 overflow-hidden shrink-0">
                        <img :src="asset.logo" class="w-6 h-6 object-contain" />
                    </div>
                    <div class="flex-grow min-w-0">
                        <div class="text-[13px] font-bold text-slate-800 dark:text-white truncate group-hover:text-blue-600 transition-colors uppercase">{{ asset.ticker }}</div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-slate-400 font-bold uppercase">{{ formatCompactNumber(asset.volume) }} acciones</span>
                            <span class="text-[10px] font-black" :class="asset.change >= 0 ? 'text-emerald-500' : 'text-rose-500'">{{ asset.change >= 0 ? '+' : '' }}{{ asset.change.toFixed(2) }}%</span>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Caso B: Mayor Volumen de Negocio ($) -->
            <template v-if="activeTrendTab === 'volume'">
                <div v-for="asset in [...mostActive].sort((a,b) => (b.business_volume || 0) - (a.business_volume || 0))" :key="'vol-' + asset.ticker" class="flex items-center gap-3 group cursor-pointer" @click="router.get(route('assets.show', asset.ticker))">
                    <div class="w-9 h-9 rounded-xl bg-slate-50 dark:bg-slate-700 flex items-center justify-center border border-slate-100 dark:border-slate-600 overflow-hidden shrink-0">
                        <img :src="asset.logo" class="w-6 h-6 object-contain" />
                    </div>
                    <div class="flex-grow min-w-0">
                        <div class="text-[13px] font-bold text-slate-800 dark:text-white truncate group-hover:text-blue-600 transition-colors uppercase">{{ asset.ticker }}</div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-blue-500 font-black tracking-tighter">${{ formatCompactNumber(asset.business_volume || 0) }}</span>
                            <span class="text-[9px] text-slate-400 font-bold uppercase">{{ formatCompactNumber(asset.volume) }} vol.</span>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Caso C: Tendencias Sociales (Menciones en Pipkonx) -->
            <template v-if="activeTrendTab === 'social'">
                <div v-for="trend in trends" :key="'soc-' + trend.ticker" class="flex items-center gap-4 group cursor-pointer" @click="router.get(route('assets.show', trend.ticker))">
                    <div class="w-9 h-9 rounded-xl bg-slate-50 dark:bg-slate-700 flex items-center justify-center border border-slate-100 dark:border-slate-600 overflow-hidden shrink-0">
                        <img :src="trend.logo" class="w-6 h-6 object-contain" />
                    </div>
                    <div class="flex-grow min-w-0">
                        <div class="text-[13px] font-bold text-slate-800 dark:text-white truncate group-hover:text-blue-600 transition-colors">{{ trend.name }}</div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-slate-400 font-bold uppercase">{{ trend.count }} menciones</span>
                            <span class="text-[10px] text-indigo-500 font-black">+{{ trend.change }}%</span>
                        </div>
                    </div>
                </div>
                <p v-if="trends.length === 0" class="text-[10px] text-slate-400 italic text-center py-4">Sin actividad social reciente</p>
            </template>
        </div>
    </div>
</template>
