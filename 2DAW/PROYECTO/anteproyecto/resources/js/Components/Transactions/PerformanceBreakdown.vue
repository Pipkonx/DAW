<script setup>
import { formatCurrency, formatPercent } from '@/Utils/formatting';
import { usePrivacy } from '@/Composables/usePrivacy';
import { Link } from '@inertiajs/vue3';

/**
 * Componente para mostrar el desglose detallado de rentabilidad (Capital, Ganancias, Costos).
 */
const props = defineProps({
    detailed: Object,
    annual: Object,
    viewType: [String, Number]
});

const { isPrivacyMode } = usePrivacy();

// Colores para las barras del gráfico manual
const getBarColor = (value) => {
    if (value > 0) return 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.3)]';
    if (value < 0) return 'bg-rose-500 shadow-[0_0_10px_rgba(244,63,94,0.3)]';
    return 'bg-slate-400';
};
</script>

<template>
    <div v-if="detailed" class="space-y-6">
        <!-- Tarjeta de Desglose Detallado -->
        <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl shadow-xl border border-slate-100 dark:border-slate-700 overflow-hidden relative group">
            <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>

            <h3 class="text-xl font-black text-slate-800 dark:text-white mb-8 flex items-center gap-3">
                <span class="w-1.5 h-6 bg-indigo-500 rounded-full"></span>
                {{ viewType === 'MAX' ? 'Rendimiento Global' : `Rendimiento - Año ${viewType}` }}
            </h3>
            
            <div class="flex flex-col gap-6">
                <!-- SECCIÓN: CAPITAL (LISTA) -->
                <div class="bg-slate-50 dark:bg-slate-900/50 p-6 rounded-2xl border border-slate-100 dark:border-slate-700/50">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Estructura de Capital
                    </h4>
                    <div class="flex justify-between items-center group/row">
                        <span class="text-sm text-slate-500 dark:text-slate-400 font-medium">Capital base invertido</span>
                        <span class="text-lg font-black text-slate-900 dark:text-white group-hover/row:scale-110 transition-transform origin-right">
                            {{ isPrivacyMode ? '****' : formatCurrency(detailed.capital_invertido) }}
                        </span>
                    </div>
                </div>

                <!-- SECCIÓN: RENDIMIENTO (LISTA) -->
                <div class="bg-emerald-50/50 dark:bg-emerald-900/10 p-6 rounded-2xl border border-emerald-100 dark:border-emerald-900/50">
                    <h4 class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        Distribución de Ganancias
                    </h4>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-1 border-b border-emerald-100 dark:border-emerald-900/30 border-dashed last:border-0 last:pb-0">
                            <span class="text-sm text-slate-600 dark:text-slate-400">Variación de precio</span>
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] font-black bg-white dark:bg-slate-800 px-2 py-0.5 rounded text-slate-500 border border-slate-200 dark:border-slate-700">{{ formatPercent(detailed.price_gain_percent) }}</span>
                                <span class="text-sm font-black" :class="detailed.price_gain >= 0 ? 'text-emerald-600' : 'text-rose-600'">
                                    {{ isPrivacyMode ? '****' : formatCurrency(detailed.price_gain) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-emerald-100 dark:border-emerald-900/30 border-dashed last:border-0 last:pb-0 font-medium">
                            <span class="text-sm text-slate-600 dark:text-slate-400">Dividendos acumulados</span>
                            <span class="text-sm font-black text-emerald-600">
                                {{ isPrivacyMode ? '****' : formatCurrency(detailed.dividends) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-emerald-100 dark:border-emerald-900/30 border-dashed last:border-0 last:pb-0">
                            <span class="text-sm text-slate-600 dark:text-slate-400 font-medium italic">Cashflow materializado</span>
                            <span class="text-sm font-black" :class="detailed.realized_gain >= 0 ? 'text-emerald-600' : 'text-rose-600'">
                                {{ isPrivacyMode ? '****' : formatCurrency(detailed.realized_gain) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN: GASTOS (LISTA) -->
                <div class="bg-rose-50/50 dark:bg-rose-900/10 p-6 rounded-2xl border border-rose-100 dark:border-rose-900/50">
                    <h4 class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Fricción y Costeos
                    </h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-slate-600 dark:text-slate-400 font-medium">Comisiones de operativa</span>
                            <span class="text-sm font-black text-rose-600">
                                {{ isPrivacyMode ? '****' : '-' + formatCurrency(detailed.fees) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-slate-600 dark:text-slate-400 font-medium">Impuestos repercutidos</span>
                            <span class="text-sm font-black text-rose-600">
                                {{ isPrivacyMode ? '****' : '-' + formatCurrency(detailed.taxes) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN: TOTALES -->
                <div class="bg-indigo-600 p-6 rounded-2xl shadow-indigo-500/20 shadow-2xl text-white">
                    <div class="flex justify-between items-end mb-4">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest opacity-80">Retorno Neto (ROI)</p>
                            <h4 class="text-3xl font-black">{{ isPrivacyMode ? '****' : formatCurrency(detailed.total_roi) }}</h4>
                        </div>
                        <div class="text-right">
                           <div class="text-xs font-bold bg-white/20 px-2 py-1 rounded-lg inline-block">
                                {{ formatPercent(detailed.tir) }} TIR
                           </div>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-white/10 flex justify-between items-center">
                         <span class="text-[9px] font-black uppercase opacity-60">Tasa Real (TWROR)</span>
                         <span class="text-sm font-black">{{ formatPercent(detailed.twror) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRÁFICO ANUAL (LISTA DE BARRAS VERTICALES) -->
        <div v-if="annual && annual.labels" class="bg-white dark:bg-slate-800 p-8 rounded-3xl shadow-xl border border-slate-100 dark:border-slate-700">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                    Historial de Rendimiento
                </h3>
                <Link :href="route('transactions.performance')" class="group flex items-center gap-1.5 text-[10px] font-black uppercase text-indigo-600 dark:text-indigo-400 hover:scale-105 transition-transform">
                    Ver más
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </Link>
            </div>
            
            <div class="space-y-5">
                <div v-for="(label, index) in annual.labels" :key="index" class="space-y-2 group">
                    <div class="flex justify-between text-xs font-black uppercase tracking-widest text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-200 transition-colors">
                        <span>Año {{ label }}</span>
                        <span :class="annual.data[index] >= 0 ? 'text-emerald-500' : 'text-rose-500'">
                            {{ isPrivacyMode ? '****' : formatCurrency(annual.data[index]) }}
                        </span>
                    </div>
                    <div class="h-2.5 w-full bg-slate-50 dark:bg-slate-900/50 rounded-full overflow-hidden">
                        <div 
                            class="h-full transition-all duration-1000 ease-out rounded-full"
                            :class="getBarColor(annual.data[index])"
                            :style="{ width: `${Math.min(Math.abs(annual.data[index] / 5000) * 100, 100)}%` }"
                        ></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

