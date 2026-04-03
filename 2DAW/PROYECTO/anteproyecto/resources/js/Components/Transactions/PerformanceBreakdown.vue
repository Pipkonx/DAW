<script setup>
import { computed } from 'vue';
import BarChart from '@/Components/Charts/BarChart.vue';
import { formatCurrency, formatPercent } from '@/Utils/formatting';
import { usePrivacy } from '@/Composables/usePrivacy';

import { Link } from '@inertiajs/vue3';

const { isPrivacyMode } = usePrivacy();

const props = defineProps({
    detailed: {
        type: Object,
        required: true
    },
    annual: {
        type: Object,
        required: true
    }
});

// Annual Gains/Losses Chart Data
const annualChartData = computed(() => {
    return {
        labels: props.annual.labels,
        datasets: [{
            label: 'Rendimiento Anual (€)',
            data: props.annual.data,
            backgroundColor: props.annual.data.map(val => {
                if (val === 0) return '#94a3b8'; // Slate 400 (Neutral)
                return val > 0 ? '#10b981' : '#f43f5e';
            }),
            borderRadius: 6,
            borderWidth: 0,
            barThickness: 30,
        }]
    };
});

const annualChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.9)',
            padding: 12,
            cornerRadius: 10,
            callbacks: {
                label: (context) => ` ${formatCurrency(context.parsed.y)}`
            }
        }
    },
    scales: {
        y: {
            grid: { color: 'rgba(148, 163, 184, 0.05)', drawBorder: false },
            ticks: { color: '#94a3b8', font: { size: 10 } }
        },
        x: {
            grid: { display: false },
            ticks: { color: '#94a3b8', font: { size: 11, weight: '600' } }
        }
    }
};

</script>

<template>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col dark:bg-slate-800 dark:border-slate-700">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Rendimiento</h3>
            <Link :href="route('transactions.performance')" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline cursor-pointer">ver más</Link>
        </div>

        <!-- Vertical Bar Chart -->
        <div class="h-48 mb-8 relative" :class="{ 'blur-sm select-none': isPrivacyMode }">
            <BarChart :data="annualChartData" :options="annualChartOptions" />
        </div>

        <!-- Detailed Breakdown -->
        <div class="space-y-6">
            <!-- Capital Section -->
            <div>
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 border-b border-slate-50 dark:border-slate-700/50 pb-2">Capital</h4>
                <div class="flex justify-between items-center py-1">
                    <span class="text-sm text-slate-600 dark:text-slate-400">Capital invertido</span>
                    <span class="text-sm font-semibold text-slate-900 dark:text-white">
                        {{ isPrivacyMode ? '****' : formatCurrency(detailed.capital_invertido) }}
                    </span>
                </div>
            </div>

            <!-- Desglose Section -->
            <div>
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 border-b border-slate-50 dark:border-slate-700/50 pb-2">Desglose del rendimiento</h4>
                <div class="space-y-2">
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

            <!-- Costos Section -->
            <div>
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 border-b border-slate-50 dark:border-slate-700/50 pb-2">Costos</h4>
                <div class="space-y-2">
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
                </div>
            </div>

            <!-- Totales Section -->
            <div class="pt-4 border-t-2 border-slate-50 dark:border-slate-700/50 mt-4 space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-base font-bold text-slate-800 dark:text-white">Retorno de inversión total</span>
                    <span class="text-base font-black" 
                        :class="{
                            'text-emerald-600 dark:text-emerald-400': detailed.total_roi > 0,
                            'text-rose-600 dark:text-rose-400': detailed.total_roi < 0,
                            'text-slate-400 dark:text-slate-500': detailed.total_roi === 0
                        }">
                        {{ isPrivacyMode ? '****' : formatCurrency(detailed.total_roi) }}
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div class="bg-slate-50 dark:bg-slate-900/50 p-3 rounded-xl">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">TIR</p>
                        <p class="text-sm font-bold text-slate-800 dark:text-white">{{ formatPercent(detailed.tir) }}</p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-900/50 p-3 rounded-xl">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Tasa Real (TWROR)</p>
                        <p class="text-sm font-bold text-slate-800 dark:text-white">{{ formatPercent(detailed.twror) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
