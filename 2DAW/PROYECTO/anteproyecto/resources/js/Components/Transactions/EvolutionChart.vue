<script setup>
import { ref, computed } from 'vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import { formatCurrency, formatPercent } from '@/Utils/formatting';
import { usePrivacy } from '@/Composables/usePrivacy';

const { isPrivacyMode } = usePrivacy();

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
        color = '#6366f1'; // Indigo 500
        bgColor = 'rgba(99, 102, 241, 0.15)';
        borderColor = '#6366f1';
    } else {
        // Calculate Performance %: (Value - Invested) / Invested * 100
        dataPoints = props.chart.data.map((val, i) => {
            const invested = props.chart.invested ? props.chart.invested[i] : 0;
            if (!invested || invested === 0) return 0;
            return ((val - invested) / invested) * 100;
        });
        label = 'Rendimiento (%)';
        color = '#0ea5e9'; // Sky 500
        bgColor = 'rgba(14, 165, 233, 0.15)';
        borderColor = '#0ea5e9';
    }


    return {
        labels: props.chart.labels,
        datasets: [{
            label: label,
            data: dataPoints,
            borderColor: borderColor,
            backgroundColor: bgColor,
            borderWidth: 3,
            tension: 0.5, // Más suave
            fill: true,
            pointRadius: 0, // Ocultar puntos por defecto
            pointHoverRadius: 6,
            pointHoverBackgroundColor: '#ffffff',
            pointHoverBorderWidth: 2,
            pointHoverBorderColor: borderColor
        }]
    };
});

const performanceChartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    layout: {
        padding: {
            top: 20,
            bottom: 20,
            left: 10,
            right: 15
        }
    },
    plugins: {
        legend: { display: false },
        tooltip: {
            enabled: true,
            mode: 'index',
            intersect: false,
            backgroundColor: 'rgba(15, 23, 42, 0.9)', // Slate 900
            titleColor: '#f8fafc',
            titleFont: { size: 14, weight: '600' },
            bodyColor: '#f8fafc',
            padding: 12,
            cornerRadius: 10,
            displayColors: true,
            usePointStyle: true,
            borderColor: 'rgba(255, 255, 255, 0.1)',
            borderWidth: 1,
            callbacks: {
                label: (context) => {
                    const label = context.dataset.label || '';
                    const value = context.parsed.y;
                    if (chartMode.value === 'performance') {
                        return ` ${label}: ${value >= 0 ? '+' : ''}${value.toFixed(2)}%`;
                    }
                    return ` ${label}: ${formatCurrency(value)}`;
                }
            }
        }
    },
    scales: {
        y: {
            grid: { 
                color: 'rgba(148, 163, 184, 0.05)',
                drawBorder: false 
            },
            ticks: { 
                color: '#94a3b8',
                font: { size: 10, weight: '500' },
                maxTicksLimit: 6,
                callback: (val) => {
                    if (chartMode.value === 'value') {
                        if (val >= 1000) return (val / 1000).toFixed(1) + 'k€';
                        return val + '€';
                    } else {
                        return (val >= 0 ? '+' : '') + val.toFixed(1) + '%';
                    }
                }
            }
        },
        x: {
            grid: { display: false },
            ticks: { maxTicksLimit: 8, color: '#94a3b8', font: { size: 10 } }
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
            <div class="flex flex-col">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-2 dark:text-slate-400">
                    Evolución de Cartera
                </h3>
                <div class="flex flex-col">
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white leading-tight">
                        <span v-if="isPrivacyMode">****</span>
                        <span v-else-if="hoveredValue !== null">
                            {{ chartMode === 'value' ? formatCurrency(hoveredValue) : formatPercent(hoveredValue) }}
                        </span>
                        <span v-else>
                            {{ formatCurrency(summary.current_value) }}
                        </span>
                    </h2>
                    
                    <!-- Change Indicators (Below the number) -->
                    <div class="flex items-center mt-1 gap-3 min-h-[24px]">
                         <!-- Hover State -->
                         <template v-if="isPrivacyMode">
                            <span class="text-sm font-bold text-slate-400">****</span>
                         </template>
                         <template v-else-if="hoveredValue !== null">
                            <span class="text-sm font-bold" 
                                :class="hoveredChange >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                                {{ hoveredChange >= 0 ? '+' : '' }}{{ formatCurrency(hoveredChange) }}
                            </span>
                            <span class="text-xs font-medium text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700 px-1.5 py-0.5 rounded">
                                {{ hoveredChange >= 0 ? '+' : '' }}{{ formatPercent(hoveredChangePercent) }}
                            </span>
                         </template>
                         <!-- Default State: Total Yield since purchase -->
                         <template v-else>
                            <span class="text-sm font-bold" 
                                :class="summary.total_pl >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                                {{ summary.total_pl >= 0 ? '+' : '' }}{{ formatCurrency(summary.total_pl) }}
                            </span>
                            <span class="text-xs font-semibold px-1.5 py-0.5 rounded"
                                :class="summary.total_pl_percent >= 0 ? 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 dark:text-emerald-400' : 'text-rose-600 bg-rose-50 dark:bg-rose-900/20 dark:text-rose-400'">
                                {{ summary.total_pl_percent >= 0 ? '▲' : '▼' }} {{ formatPercent(summary.total_pl_percent) }}
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
                            :class="chartMode === 'value' ? 'bg-white text-blue-600 shadow-sm dark:bg-slate-600 dark:text-blue-400' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                            class="px-3 py-1 text-xs font-medium rounded-md transition-all"
                        >
                            Valor (€)
                        </button>
                        <button 
                            @click="chartMode = 'performance'"
                            :class="chartMode === 'performance' ? 'bg-white text-emerald-600 shadow-sm dark:bg-slate-600 dark:text-emerald-400' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                            class="px-3 py-1 text-xs font-medium rounded-md transition-all"
                        >
                            Rendimiento (%)
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

        <div class="flex-grow relative w-full min-h-0" :class="{ 'blur-sm select-none': isPrivacyMode }">
            <LineChart :data="performanceChartData" :options="performanceChartOptions" />
        </div>
    </div>
</template>
