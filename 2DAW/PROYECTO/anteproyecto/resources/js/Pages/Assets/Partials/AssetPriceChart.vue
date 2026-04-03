<script setup>
import { computed } from 'vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import { formatCurrency } from '@/Utils/formatting';

const props = defineProps({
    localChartData: Array,
    chartRange: String,
    isLoadingChart: Boolean
});

const emit = defineEmits(['updateRange']);

const formattedChartData = computed(() => {
    return {
        labels: props.localChartData.map(d => d.date),
        datasets: [{
            label: 'Precio',
            data: props.localChartData.map(d => d.close),
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 0,
            pointHoverRadius: 6,
            borderWidth: 3
        }]
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            mode: 'index',
            intersect: false,
            backgroundColor: '#1e293b',
            titleColor: '#94a3b8',
            bodyColor: '#fff',
            borderColor: '#334155',
            borderWidth: 1,
            padding: 12,
            displayColors: false,
            callbacks: {
                label: (context) => formatCurrency(context.parsed.y)
            }
        }
    },
    scales: {
        y: {
            position: 'right',
            grid: { color: 'rgba(148, 163, 184, 0.1)' },
            ticks: { 
                color: '#94a3b8',
                font: { size: 10 },
                callback: (val) => formatCurrency(val)
            }
        },
        x: {
            grid: { display: false },
            ticks: { 
                color: '#94a3b8',
                maxRotation: 0,
                autoSkip: true,
                maxTicksLimit: 6,
                font: { size: 10 }
            }
        }
    }
};
</script>

<template>
    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <!-- Selector de Rangos -->
        <div class="p-6 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/20 border-b border-slate-100 dark:border-slate-700">
            <div class="flex gap-1 bg-slate-200/50 dark:bg-slate-900/50 p-1 rounded-xl">
                <button 
                    v-for="r in ['1D', '1W', '1M', 'YTD', '1Y', 'MAX']" :key="r"
                    @click="$emit('updateRange', r)"
                    class="px-4 py-1.5 rounded-lg text-xs font-black transition-all"
                    :class="chartRange === r ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                >
                    {{ r }}
                </button>
            </div>
            <div class="flex items-center gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Vivo: Data en Tiempo Real
            </div>
        </div>

        <!-- Visor del Gráfico -->
        <div class="h-[400px] p-6 relative">
            <div v-if="isLoadingChart" class="absolute inset-0 z-10 bg-white/60 dark:bg-slate-800/60 flex items-center justify-center backdrop-blur-[2px]">
                <div class="animate-spin rounded-full h-8 w-8 border-4 border-indigo-500 border-t-transparent"></div>
            </div>
            <LineChart :data="formattedChartData" :options="chartOptions" />
        </div>
    </div>
</template>
