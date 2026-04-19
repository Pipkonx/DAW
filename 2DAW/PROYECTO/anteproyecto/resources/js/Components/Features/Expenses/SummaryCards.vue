<script setup>
import { formatCurrency } from '@/Utils/formatting';

defineProps({
    summary: {
        type: Object,
        required: true
    },
    isPrivacyMode: {
        type: Boolean,
        default: false
    }
});
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Ingresos -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 border-l-4 border-l-emerald-500 relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide">Ingresos Totales</p>
                <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ isPrivacyMode ? '****' : formatCurrency(summary.total_income) }}</h3>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">En el periodo seleccionado</p>
            </div>
            <div class="absolute right-0 top-0 p-4 opacity-10">
                <svg class="w-16 h-16 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
            </div>
        </div>

        <!-- Gastos -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 border-l-4 border-l-rose-500 relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wide">Gastos Totales</p>
                <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ isPrivacyMode ? '****' : formatCurrency(summary.total_expense) }}</h3>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">Promedio diario: {{ isPrivacyMode ? '****' : formatCurrency(summary.avg_daily_expense) }}</p>
            </div>
            <div class="absolute right-0 top-0 p-4 opacity-10">
                <svg class="w-16 h-16 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            </div>
        </div>

        <!-- Ahorro Neto -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 border-l-4 relative overflow-hidden"
             :class="summary.net_savings >= 0 ? 'border-l-blue-500' : 'border-l-orange-500'">
            <div class="relative z-10">
                <p class="text-xs font-bold uppercase tracking-wide" 
                   :class="summary.net_savings >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-orange-600 dark:text-orange-400'">
                    {{ summary.net_savings >= 0 ? 'Ahorro Neto' : 'Déficit' }}
                </p>
                <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ isPrivacyMode ? '****' : formatCurrency(summary.net_savings) }}</h3>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">
                    <span v-if="isPrivacyMode">****</span>
                    <span v-else>{{ summary.total_income > 0 ? ((summary.net_savings / summary.total_income) * 100).toFixed(1) + '% de tasa de ahorro' : '-' }}</span>
                </p>
            </div>
            <div class="absolute right-0 top-0 p-4 opacity-10">
                <svg class="w-16 h-16 text-slate-500" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/></svg>
            </div>
        </div>
    </div>
</template>
