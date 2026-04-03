<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

/**
 * MarketMovers - Resumen de los activos más volátiles del día.
 * 
 * Permite al usuario identificar rápidamente oportunidades o riesgos de mercado.
 */
const props = defineProps({
    topGainers: Array,
    topLosers: Array,
});

const activeMoverType = ref('gainers'); // Estado reactivo: 'gainers' | 'losers'
</script>

<template>
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700">
        <!-- Cabecera del Widget -->
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-black uppercase tracking-widest text-slate-400">Top Movers Hoy</h3>
            <div class="flex bg-slate-50 dark:bg-slate-900 rounded-lg p-1">
                <button 
                    @click="activeMoverType = 'gainers'"
                    class="p-1.5 rounded-md transition-all"
                    :class="activeMoverType === 'gainers' ? 'bg-white dark:bg-slate-800 shadow-sm text-emerald-500' : 'text-slate-400'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </button>
                <button 
                    @click="activeMoverType = 'losers'"
                    class="p-1.5 rounded-md transition-all"
                    :class="activeMoverType === 'losers' ? 'bg-white dark:bg-slate-800 shadow-sm text-rose-500' : 'text-slate-400'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Listado de Activos Filtrado -->
        <div class="space-y-4">
            <div 
                v-for="mover in (activeMoverType === 'gainers' ? topGainers : topLosers)?.slice(0, 5)" 
                :key="mover.symbol" 
                class="flex items-center justify-between group cursor-pointer animate-in fade-in duration-300" 
                @click="router.get(route('assets.show', mover.symbol))"
            >
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-700/50 flex items-center justify-center border border-slate-100 dark:border-slate-600 overflow-hidden">
                        <img :src="mover.image" class="w-7 h-7 object-contain" />
                    </div>
                    <div>
                        <div class="text-sm font-bold text-slate-800 dark:text-white group-hover:text-blue-500 transition-colors uppercase">{{ mover.symbol }}</div>
                        <div class="text-[10px] text-slate-500 uppercase font-black tracking-tighter truncate max-w-[80px]">{{ mover.name }}</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs font-black" :class="activeMoverType === 'gainers' ? 'text-emerald-500' : 'text-rose-500'">
                        {{ activeMoverType === 'gainers' ? '+' : '' }}{{ mover.changesPercentage?.toFixed(2) }}%
                    </div>
                    <div class="text-[10px] text-slate-400 font-bold tracking-tight">${{ mover.price?.toFixed(2) }}</div>
                </div>
            </div>
            <!-- Estado Vacío -->
            <p v-if="!(activeMoverType === 'gainers' ? topGainers : topLosers)?.length" class="text-[10px] text-slate-400 italic text-center py-4">No hay datos de mercado actuales</p>
        </div>
    </div>
</template>
