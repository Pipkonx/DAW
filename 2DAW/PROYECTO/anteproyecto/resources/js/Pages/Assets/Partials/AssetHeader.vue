<script setup>
import { computed } from 'vue';
import { formatCurrency, formatPercent } from '@/Utils/formatting';

const props = defineProps({
    marketAsset: Object,
    currentPrice: Number,
    priceChange: { type: Number, default: 1.25 }
});

const isPositive = computed(() => props.priceChange >= 0);
</script>

<template>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="flex items-center gap-5">
            <!-- Logo/Icono del Activo -->
            <div class="w-16 h-16 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-2 flex items-center justify-center overflow-hidden shrink-0">
                <img v-if="marketAsset.logo_url" :src="marketAsset.logo_url" class="w-full h-full object-contain" :alt="marketAsset.ticker">
                <span v-else class="text-2xl font-black text-indigo-600">{{ marketAsset.ticker.substring(0, 2) }}</span>
            </div>
            
            <!-- Identidad del Activo -->
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-3xl font-black text-slate-800 dark:text-white leading-tight">{{ marketAsset.name }}</h1>
                    <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg text-sm font-black border border-indigo-100 dark:border-indigo-800/30">
                        {{ marketAsset.ticker }}
                    </span>
                </div>
                <div class="flex items-center gap-4 mt-2 text-sm text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">
                    <span>{{ marketAsset.type_label }}</span>
                    <span class="w-1.5 h-1.5 bg-slate-300 dark:bg-slate-600 rounded-full"></span>
                    <span>ISIN: {{ marketAsset.isin || 'Sin definir' }}</span>
                </div>
            </div>
        </div>

        <!-- Panel de Cotización -->
        <div class="flex flex-col items-end">
            <div class="text-4xl font-black text-slate-800 dark:text-white tracking-tight">
                {{ formatCurrency(currentPrice) }}
            </div>
            <div 
                class="flex items-center gap-2 mt-1 px-3 py-1 rounded-full text-xs font-black" 
                :class="isPositive ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400' : 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400'"
            >
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path v-if="isPositive" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 15l7-7 7 7" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M19 9l-7 7-7-7" />
                </svg>
                <span>{{ formatPercent(priceChange) }} (Hoy)</span>
            </div>
        </div>
    </div>
</template>
