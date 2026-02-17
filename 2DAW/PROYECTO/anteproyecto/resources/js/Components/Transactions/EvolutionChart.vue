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

// Reactive state for hovering
const hoveredValue = ref(null);
const hoveredLabel = ref(null);
const hoveredChange = ref(null);
const hoveredChangePercent = ref(null);

const switchTimeframe = (tf) => {
    emit('update:timeframe', tf);
};

// Reset hover state when mouse leaves chart area
const resetHover = () => {
    hoveredValue.value = null;
    hoveredLabel.value = null;
    hoveredChange.value = null;
    hoveredChangePercent.value = null;
};

const performanceChartData = computed(() => {
    let dataPoints = [];
    let label = '';
    let color = '';
    let bgColor = '';
    let borderColor = '';

    if (chartMode.value === 'value') {
        dataPoints = props.chart.data;
        label = 'Valor de Cartera';
        color = '#3b82f6'; // Blue
        bgColor = (ctx) => {
            const canvas = ctx.chart.ctx;
            const gradient = canvas.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');
            return gradient;
        };
        borderColor = '#3b82f6';
    } else {
        // Calculate Performance %: (Value - Invested) / Invested * 100
        dataPoints = props.chart.data.map((val, i) => {
            const invested = props.chart.invested ? props.chart.invested[i] : 0;
            if (!invested || invested === 0) return 0;
            return ((val - invested) / invested) * 100;
        });
        label = 'Rendimiento (%)';
        color = '#10b981'; // Emerald
        bgColor = (ctx) => {
            const canvas = ctx.chart.ctx;
            const gradient = canvas.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');
            return gradient;
        };
        borderColor = '#10b981';
    }

    return {
        labels: props.chart.labels,
        datasets: [{
            label: label,
            data: dataPoints,
            borderColor: borderColor,
            backgroundColor: bgColor,
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointRadius: 0,
            pointHoverRadius: 6,
            pointHoverBackgroundColor: '#ffffff',
            pointHoverBorderWidth: 2,
        }]
    };
});

const performanceChartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            enabled: true,
            mode: 'index',
            intersect: false,
            backgroundColor: 'rgba(255, 255, 255, 0.9)',
            titleColor: '#1e293b',
            titleFont: { size: 13, weight: '600' },
            bodyColor: 'transparent',
            borderColor: '#e2e8f0',
            borderWidth: 1,
            displayColors: false,
            padding: 10,
            callbacks: {
                title: (context) => {
                    return context[0].label;
                },
                label: () => {
                    return null; // Return null to completely remove the label line
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
        mode: 'index',
        intersect: false,
    },
    onHover: (event, elements) => {
        if (elements && elements.length > 0) {
            const index = elements[0].index;
            const label = props.chart.labels[index];
            const value = props.chart.data[index]; // Use raw data for consistency
            const invested = props.chart.invested ? props.chart.invested[index] : 0;
            
            hoveredLabel.value = label;
            hoveredValue.value = value;
            
            // Calculate Total Profit/Loss at this point (Value - Invested)
            // This shows the accumulated gain up to that moment
            const profit = value - invested;
            const profitPercent = invested !== 0 ? (profit / invested) * 100 : 0;
            
            hoveredChange.value = profit;
            hoveredChangePercent.value = profitPercent;
        } else {
            // Optional: reset on mouse out of points, but keeping last value is often better UX
            // resetHover(); 
        }
    }
}));
</script>

<template>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col h-[450px] dark:bg-slate-800 dark:border-slate-700 overflow-hidden" @mouseleave="resetHover">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 shrink-0">
            <!-- Left: Header & Value -->
            <div class="flex flex-col justify-center">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-2 dark:text-slate-400">
                    Evolución de Cartera
                </h3>
                <div class="flex items-center gap-4 h-12">
                    <div class="min-w-[200px]">
                        <h2 class="text-3xl font-bold text-slate-900 dark:text-white leading-none">
                            <span v-if="hoveredValue !== null">
                                {{ chartMode === 'value' ? formatCurrency(hoveredValue) : formatPercent(hoveredValue) }}
                            </span>
                            <span v-else>
                                {{ formatCurrency(summary.current_value) }}
                            </span>
                        </h2>
                    </div>
                    
                    <!-- Change Indicator (Vertical Stack) -->
                    <div class="flex flex-col justify-center leading-none">
                         <!-- Hover State -->
                         <template v-if="hoveredValue !== null">
                            <span class="text-sm font-bold mb-1" 
                                :class="hoveredChange >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                                {{ hoveredChange >= 0 ? '+' : '' }}{{ formatCurrency(hoveredChange) }}
                            </span>
                            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">
                                {{ hoveredChange >= 0 ? '+' : '' }}{{ formatPercent(hoveredChangePercent) }}
                            </span>
                         </template>

                         <!-- Default State -->
                         <template v-else>
                            <span class="text-sm font-bold mb-1"
                                :class="chart.period_pl_value >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                                {{ chart.period_pl_value >= 0 ? '+' : '' }}{{ formatCurrency(chart.period_pl_value) }}
                            </span>
                            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">
                                {{ chart.period_pl_value >= 0 ? '+' : '' }}{{ formatPercent(chart.period_pl_percent) }}
                            </span>
                         </template>
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

        <div class="flex-grow relative w-full min-h-0">
            <LineChart :data="performanceChartData" :options="performanceChartOptions" />
        </div>
    </div>
</template>
