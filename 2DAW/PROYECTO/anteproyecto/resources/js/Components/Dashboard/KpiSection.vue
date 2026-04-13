<script setup>
/**
 * KpiSection - Dashboard Component
 * 
 * Este componente muestra las tarjetas de resumen principal (KPIs).
 * Sigue el principio SRP al encargarse únicamente de la visualización
 * de métricas de alto nivel (Patrimonio, Inversiones, Gastos).
 */
import { formatCurrency } from '@/Utils/formatting';
import InfoTooltip from '@/Components/InfoTooltip.vue';

const props = defineProps({
    summary: {
        type: Object,
        required: true // Espera { netWorth, cash, investmentsTotal }
    },
    expenses: {
        type: Object,
        required: true // Espera { monthlyTotal, monthlyIncome }
    },
    portfoliosCount: {
        type: Number,
        default: 0
    },
    isPrivacyMode: {
        type: Boolean,
        default: false
    }
});
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Tarjeta: Patrimonio -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 relative group hover:shadow-md transition-shadow">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none overflow-hidden rounded-2xl inset-0">
                <svg class="absolute top-4 right-4 w-24 h-24 text-blue-600 dark:text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.95V5h-2.93v1.74c-1.81.44-2.43 1.41-2.43 2.51 0 1.91 1.66 2.52 3.97 3.06 1.77.42 2.34 1.05 2.34 1.81 0 .93-.93 1.54-2.34 1.54-1.47 0-2.09-.73-2.14-1.8h-1.8c.06 1.64 1.13 2.76 2.8 3.08v1.78h2.93v-1.77c1.9-.45 2.51-1.47 2.51-2.67 0-1.99-1.72-2.56-4.03-3.08z"/></svg>
            </div>
            <div class="relative z-10">
                <div class="flex items-center mb-2">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Patrimonio</p>
                    <InfoTooltip text="Suma total de tus inversiones y ahorros." />
                </div>
                <h3 class="text-3xl font-bold text-slate-900 dark:text-white leading-tight">{{ isPrivacyMode ? '****' : formatCurrency(summary.netWorth) }}</h3>
                <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1">
                    <div class="flex items-center text-sm font-bold" :class="(summary.investmentsTotal - summary.investmentsCost) >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                        <span v-if="!isPrivacyMode" class="flex items-center gap-1">
                            {{ (summary.investmentsTotal - summary.investmentsCost) >= 0 ? '▲' : '▼' }} 
                            {{ formatCurrency(Math.abs(summary.investmentsTotal - summary.investmentsCost)) }}
                        </span>
                        <span v-else>****</span>
                    </div>
                    <div v-if="!isPrivacyMode" class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-tighter" :class="(summary.investmentsYield || 0) >= 0 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400'">
                        {{ (summary.investmentsYield || 0).toFixed(2) }}%
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-50 dark:border-slate-700/50 flex items-center text-xs">
                    <span class="text-slate-500 dark:text-slate-400">Ahorros: </span>
                    <span class="font-semibold text-slate-700 dark:text-slate-300 ml-1">{{ isPrivacyMode ? '****' : formatCurrency(summary.cash) }}</span>
                </div>
            </div>
        </div>

        <!-- Tarjeta: Total Inversiones -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 relative group hover:shadow-md transition-shadow">
            <div class="absolute inset-0 overflow-hidden rounded-2xl pointer-events-none opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="absolute top-4 right-4 w-24 h-24 text-emerald-600 dark:text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
            </div>
            <div class="relative z-10">
                <div class="flex items-center mb-2">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Inversiones</p>
                    <InfoTooltip text="Valor actual de mercado de todos tus activos en carteras." />
                </div>
                <h3 class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 leading-tight">{{ isPrivacyMode ? '****' : formatCurrency(summary.investmentsTotal) }}</h3>
                <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                    Valor total de mercado
                </div>
                <div class="mt-4 pt-4 border-t border-slate-50 dark:border-slate-700/50 flex items-center text-xs">
                    <span class="font-semibold text-slate-700 dark:text-slate-300">{{ portfoliosCount }} carteras activas</span>
                    <span class="text-slate-400 dark:text-slate-500 ml-1">gestionadas</span>
                </div>
            </div>
        </div>

        <!-- Tarjeta: Gastos Mensuales -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 relative group hover:shadow-md transition-shadow">
            <div class="absolute inset-0 overflow-hidden rounded-2xl pointer-events-none opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="absolute top-4 right-4 w-24 h-24 text-rose-600 dark:text-rose-500" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 9c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm6 12H6v-1c0-2 4-3.1 6-3.1s6 1.1 6 3.1v1z"/></svg>
            </div>
            <div class="relative z-10">
                <div class="flex items-center mb-2">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Gastos del Mes</p>
                    <InfoTooltip text="Total de gastos registrados este mes." />
                </div>
                <h3 class="text-3xl font-bold text-rose-600 dark:text-rose-400 leading-tight">{{ isPrivacyMode ? '****' : '-' + formatCurrency(expenses.monthlyTotal) }}</h3>
                <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                    Flujo de salida este mes
                </div>
                <div class="mt-4 pt-4 border-t border-slate-50 dark:border-slate-700/50 flex items-center text-xs">
                    <span class="text-slate-500 dark:text-slate-400">Ingresos: </span>
                    <span class="font-semibold text-emerald-600 dark:text-emerald-400 ml-1">{{ isPrivacyMode ? '****' : '+' + formatCurrency(expenses.monthlyIncome) }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
