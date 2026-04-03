<script setup>
import { formatCurrency, formatPercent } from '@/Utils/formatting';

const props = defineProps({
    marketAsset: Object
});
</script>

<template>
    <div class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Columna Izquierda: Descripción y Datos Clave -->
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Descripción del Activo
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300 font-medium leading-relaxed">
                        {{ marketAsset.description || 'Sin descripción detallada por el momento.' }}
                    </p>
                    
                    <div class="my-8 border-t border-slate-50 dark:border-slate-700/50"></div>

                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-8 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        Datos Clave de Mercado
                    </h3>
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-y-10 gap-x-8">
                        <div v-for="item in [
                            { label: 'Sector', value: marketAsset.sector || 'N/A' },
                            { label: 'Industria', value: marketAsset.industry || 'N/A' },
                            { label: 'Capitalización', value: marketAsset.market_cap ? formatCurrency(marketAsset.market_cap) : 'N/A' },
                            { label: 'ISIN', value: marketAsset.isin || 'N/A' },
                            { label: 'Tipo', value: marketAsset.type_label || 'Otros' },
                            { label: 'TER (Coste)', value: marketAsset.ter ? formatPercent(marketAsset.ter) : 'N/A' }
                        ]" :key="item.label" class="group">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 group-hover:text-indigo-500 transition-colors">{{ item.label }}</p>
                            <p class="text-lg font-black text-slate-800 dark:text-slate-100 truncate" :title="item.value">{{ item.value }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Análisis Fundamental -->
            <div class="space-y-6">
                <div class="bg-indigo-600 rounded-3xl p-8 text-white flex flex-col justify-between shadow-xl shadow-indigo-500/20 relative overflow-hidden group min-h-[300px]">
                    <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10">
                        <h3 class="font-black text-[10px] uppercase tracking-[0.2em] opacity-80 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            Análisis Fundamental
                        </h3>
                        <p class="text-2xl font-black">Rating: Estable</p>
                        <p class="text-sm opacity-80 mt-4 leading-relaxed font-medium">
                            Basado en sentimiento algorítmico y datos históricos. La volatilidad es moderada.
                        </p>
                    </div>
                    <button class="relative z-10 mt-10 w-full bg-white text-indigo-600 font-black py-4 rounded-2xl shadow-lg hover:bg-slate-50 transition-all text-sm">
                        Ver Informe IA
                    </button>
                </div>
            </div>
        </div>

        <!-- Ponderaciones (Solo para ETFs o Activos con desglose) -->
        <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
            <h3 class="text-lg font-black text-slate-800 dark:text-white mb-10">Desglose de Cartera</h3>
            <div v-if="marketAsset.sectorWeightings || marketAsset.countryWeightings" class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Por Sector -->
                <div v-if="marketAsset.sectorWeightings" class="space-y-6">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">Ponderación Sectorial</h4>
                    <div class="space-y-4">
                        <div v-for="sw in marketAsset.sectorWeightings" :key="sw.sector" class="space-y-2">
                            <div class="flex justify-between text-xs font-bold">
                                <span>{{ sw.sector }}</span>
                                <span class="text-indigo-500 font-black">{{ sw.weightPercentage.toFixed(2) }}%</span>
                            </div>
                            <div class="h-2 bg-slate-100 dark:bg-slate-900 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-500 transition-all duration-1000" :style="{ width: sw.weightPercentage + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Por País -->
                <div v-if="marketAsset.countryWeightings" class="space-y-6">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">Distribución Geográfica</h4>
                    <div class="space-y-4">
                        <div v-for="cw in marketAsset.countryWeightings" :key="cw.country" class="space-y-2">
                            <div class="flex justify-between text-xs font-bold">
                                <span>{{ cw.country }}</span>
                                <span class="text-emerald-500 font-black">{{ cw.weightPercentage.toFixed(2) }}%</span>
                            </div>
                            <div class="h-2 bg-slate-100 dark:bg-slate-900 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 transition-all duration-1000" :style="{ width: cw.weightPercentage + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="h-32 flex items-center justify-center border-2 border-dashed border-slate-100 dark:border-slate-800 rounded-2xl">
                <p class="text-slate-400 font-black text-xs uppercase tracking-widest">Información de desglose no disponible</p>
            </div>
        </div>
    </div>
</template>
