<script setup>
import { ref, computed } from 'vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import { formatCurrency, formatPercent } from '@/Utils/formatting';

const props = defineProps({
    summary: {
        type: Object,
        required: true
    },
    chart: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['update:timeframe']);

const chartMode = ref('value'); // 'value' | 'performance'
const timeframes = ['1D', '1W', '1M', '3M', 'YTD', '1Y', 'MAX'];

const switchTimeframe = (tf) => {
    emit('update:timeframe', tf);
};

const performanceChartData = computed(() => {
    let dataPoints = [];
    let label = '';
    let color = '';
    let bgColor = '';

    if (chartMode.value === 'value') {
        dataPoints = props.chart.data;
        label = 'Valor de Cartera (Estimado)';
        color = '#3b82f6'; // Blue
        bgColor = 'rgba(59, 130, 246, 0.1)';
    } else {
        // Calculate Performance %: (Value - Invested) / Invested * 100
        dataPoints = props.chart.data.map((val, i) => {
            const invested = props.chart.invested ? props.chart.invested[i] : 0;
            if (!invested || invested === 0) return 0;
            return ((val - invested) / invested) * 100;
        });
        label = 'Rendimiento (%)';
        color = '#10b981'; // Emerald
        bgColor = 'rgba(16, 185, 129, 0.1)';
    }

    return {
        labels: props.chart.labels,
        datasets: [{
            label: label,
            data: dataPoints,
            borderColor: color,
            backgroundColor: bgColor,
            tension: 0.4,
            fill: true,
            pointRadius: 0,
            pointHoverRadius: 6
        }]
    };
});

const performanceChartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            mode: 'index',
            intersect: false,
            callbacks: {
                label: (context) => {
                    if (chartMode.value === 'value') {
                        return formatCurrency(context.parsed.y);
                    } else {
                        return context.parsed.y.toFixed(2) + '%';
                    }
                }
            }
        }
    },
    scales: {
        y: {
            grid: { color: 'rgba(148, 163, 184, 0.1)' },
            ticks: { 
                color: '#94a3b8',
                callback: (val) => {
                    if (chartMode.value === 'value') {
                        return new Intl.NumberFormat('es-ES', { notation: "compact", compactDisplay: "short" }).format(val);
                    } else {
                        return val.toFixed(1) + '%';
                    }
                }
            }
        },
        x: {
            grid: { display: false },
            ticks: { maxTicksLimit: 8, color: '#94a3b8' }
        }
    },
    interaction: {
        mode: 'nearest',
        axis: 'x',
        intersect: false
    }
}));
</script>

<template>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col h-[450px] dark:bg-slate-800 dark:border-slate-700">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <!-- Left: Header & Value -->
            <div class="flex flex-col">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-1 dark:text-slate-400">Evolución de Cartera</h3>
                <div class="flex items-baseline gap-3 flex-wrap">
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white leading-none">
                        {{ formatCurrency(summary.current_value) }}
                    </h2>
                    <div class="flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-opacity-10 dark:bg-opacity-20" 
                            :class="chart.period_pl_value >= 0 ? 'bg-emerald-50 text-emerald-600 dark:text-emerald-400 dark:bg-emerald-900' : 'bg-rose-50 text-rose-600 dark:text-rose-400 dark:bg-rose-900'">
                        <span class="text-sm font-bold">
                            {{ chart.period_pl_value >= 0 ? '+' : '' }}{{ formatPercent(chart.period_pl_percent) }}
                        </span>
                        <span class="text-xs font-medium opacity-90">
                            ({{ chart.period_pl_value >= 0 ? '+' : '' }}{{ formatCurrency(chart.period_pl_value) }})
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Right: Controls -->
            <div class="flex flex-col items-end gap-2">
                <div class="flex items-center gap-3">
                    <!-- Chart Mode Toggle -->
                    <div class="flex bg-slate-100 p-1 rounded-lg dark:bg-slate-700">
                        <button 
                            @click="chartMode = 'value'"
                            :class="chartMode === 'value' ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-600 dark:text-white' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                            class="px-3 py-1 text-xs font-medium rounded-md transition-all"
                        >
                            Valor
                        </button>
                        <button 
                            @click="chartMode = 'performance'"
                            :class="chartMode === 'performance' ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-600 dark:text-white' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                            class="px-3 py-1 text-xs font-medium rounded-md transition-all"
                        >
                            %
                        </button>
                    </div>

                    <!-- Timeframe Toggles -->
                    <div class="flex bg-slate-100 p-1 rounded-lg dark:bg-slate-700">
                        <button 
                            v-for="tf in timeframes" 
                            :key="tf"
                            @click="switchTimeframe(tf)"
                            :class="filters.timeframe === tf ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-600 dark:text-white' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                            class="px-3 py-1 text-xs font-medium rounded-md transition-all"
                        >
                            {{ tf }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-grow relative w-full">
            <LineChart :data="performanceChartData" :options="performanceChartOptions" />
        </div>
    </div>
</template>
