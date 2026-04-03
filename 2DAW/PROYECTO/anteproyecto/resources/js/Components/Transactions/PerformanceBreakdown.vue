<script setup>
import { formatCurrency, formatPercent } from '@/Utils/formatting';
import { usePrivacy } from '@/Composables/usePrivacy';

/**
 * Componente para mostrar el desglose detallado de rentabilidad (Capital, Ganancias, Costos).
 */
defineProps({
    detailed: Object,
    viewType: [String, Number]
});

const { isPrivacyMode } = usePrivacy();
</script>

<template>
    <div v-if="detailed" class="mt-8 bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">
            {{ viewType === 'MAX' ? 'Desglose Global de Rentabilidad' : `Desglose de Rentabilidad - Año ${viewType}` }}
        </h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 lg:gap-8">
            <!-- Sección de Capital -->
            <div class="space-y-4">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-50 dark:border-slate-700/50 pb-2">Capital</h4>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate-600 dark:text-slate-400">Capital invertido</span>
                    <span class="text-sm font-semibold text-slate-900 dark:text-white">
                        {{ isPrivacyMode ? '****' : formatCurrency(detailed.capital_invertido) }}
                    </span>
                </div>
            </div>

            <!-- Desglose de Ganancias -->
            <div class="space-y-4 lg:col-span-1">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-50 dark:border-slate-700/50 pb-2">Desglose del rendimiento</h4>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Ganancia de precio</span>
                        <div class="text-right">
                            <span class="text-xs font-medium text-slate-400 mr-2">{{ formatPercent(detailed.price_gain_percent) }}</span>
                            <span class="text-sm font-semibold" 
                                :class="{
                                    'text-emerald-600 dark:text-emerald-400': detailed.price_gain > 0,
                                    'text-rose-600 dark:text-rose-400': detailed.price_gain < 0,
                                    'text-slate-400 dark:text-slate-500': detailed.price_gain === 0
                                }">
                                {{ isPrivacyMode ? '****' : formatCurrency(detailed.price_gain) }}
                            </span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Dividendos</span>
                        <div class="text-right">
                            <span class="text-xs font-medium text-slate-400 mr-2">0.00%</span>
                            <span class="text-sm font-semibold"
                                :class="detailed.dividends > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500'">
                                {{ isPrivacyMode ? '****' : formatCurrency(detailed.dividends) }}
                            </span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Ganancia realizada</span>
                        <div class="text-right">
                            <span class="text-xs font-medium text-slate-400 mr-2">0.00%</span>
                            <span class="text-sm font-semibold" 
                                :class="{
                                    'text-emerald-600 dark:text-emerald-400': detailed.realized_gain > 0,
                                    'text-rose-600 dark:text-rose-400': detailed.realized_gain < 0,
                                    'text-slate-400 dark:text-slate-500': detailed.realized_gain === 0
                                }">
                                {{ isPrivacyMode ? '****' : formatCurrency(detailed.realized_gain) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Costos -->
            <div class="space-y-4">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-50 dark:border-slate-700/50 pb-2">Costos de transacción</h4>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Costos de transacción</span>
                        <span class="text-sm font-semibold text-rose-600 dark:text-rose-400">
                            {{ isPrivacyMode ? '****' : '-' + formatCurrency(detailed.fees) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Impuestos</span>
                        <span class="text-sm font-semibold text-rose-600 dark:text-rose-400">
                            {{ isPrivacyMode ? '****' : '-' + formatCurrency(detailed.taxes) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Costes corrientes</span>
                        <span class="text-sm font-semibold text-rose-600 dark:text-rose-400">
                            {{ isPrivacyMode ? '****' : '-0,00 €' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Sección de Retorno de Inversión (Totales) -->
            <div class="space-y-4">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-50 dark:border-slate-700/50 pb-2">Retorno de inversión</h4>
                
                <div class="flex justify-between items-center">
                    <span class="text-sm font-bold text-slate-800 dark:text-white">Retorno Total</span>
                    <span class="text-base font-black" 
                        :class="{
                            'text-emerald-600 dark:text-emerald-400': detailed.total_roi > 0,
                            'text-rose-600 dark:text-rose-400': detailed.total_roi < 0,
                            'text-slate-400 dark:text-slate-500': detailed.total_roi === 0
                        }">
                        {{ isPrivacyMode ? '****' : formatCurrency(detailed.total_roi) }}
                    </span>
                </div>
                
                <div class="grid grid-cols-1 gap-2 mt-2">
                    <div class="bg-slate-50 dark:bg-slate-900/50 p-2 rounded-xl flex justify-between items-center">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">TIR (Tasa Interna de Rendimiento)</p>
                        <p class="text-xs font-bold text-slate-800 dark:text-white">{{ formatPercent(detailed.tir) }}</p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-900/50 p-2 rounded-xl flex justify-between items-center">
                        <p class="text-[10px] font-bold text-slate-400 uppercase text-right leading-tight max-w-[120px]">Tasa real ponderada (TWROR)</p>
                        <p class="text-xs font-bold text-slate-800 dark:text-white">{{ formatPercent(detailed.twror) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
