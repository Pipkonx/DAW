<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PortfolioHeader from '@/Components/Transactions/PortfolioHeader.vue';
import BarChart from '@/Components/Charts/BarChart.vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import { formatCurrency, formatPercent } from '@/Utils/formatting';
import { usePrivacy } from '@/Composables/usePrivacy';

const { isPrivacyMode } = usePrivacy();

const props = defineProps({
    portfolios: Array,
    selectedPortfolioId: [String, Number],
    annual: Object,
    monthly: Object,
    heatmap: Array,
    detailed: Object,
    viewType: [String, Number]
});

// Chart View State (bar, line, heatmap)
const chartMode = ref('bar'); 

const switchPortfolio = (id) => {
    router.get(route('transactions.performance'), { 
        portfolio_id: id,
        view: props.viewType
    }, { preserveState: true, preserveScroll: true });
};

const switchView = (v) => {
    router.get(route('transactions.performance'), { 
        portfolio_id: props.selectedPortfolioId,
        view: v
    }, { preserveState: true, preserveScroll: true });
};

// Available Years
const availableYears = computed(() => {
    const years = [...props.annual.labels].sort((a,b) => b - a); // Descending
    return years;
});

// Chart Data Calculation
const chartData = computed(() => {
    const isMax = props.viewType === 'MAX';
    const source = isMax ? props.annual : props.monthly;
    
    // Si no hay datos, devolver vacío seguro
    if (!source || !source.labels) {
        return { labels: [], datasets: [] };
    }

    const values = source.data;
    
    const avg = values.length > 0 ? values.reduce((a,b) => a + b, 0) / values.length : 0;
    
    const datasets = [{
        type: chartMode.value === 'line' ? 'line' : 'bar',
        label: isMax ? 'Rendimiento Anual (€)' : `Rendimiento Mensual ${props.viewType} (€)`,
        data: values,
        backgroundColor: chartMode.value === 'line' 
            ? 'rgba(59, 130, 246, 0.1)' // Azul muy suave para el área
            : values.map(val => val === 0 ? '#94a3b8' : (val > 0 ? '#10b981' : '#f43f5e')),
        borderColor: chartMode.value === 'line' ? '#3b82f6' : 'transparent',
        borderWidth: chartMode.value === 'line' ? 2 : 0,
        borderRadius: chartMode.value === 'bar' ? 6 : 0,
        barThickness: chartMode.value === 'bar' && isMax ? 40 : undefined,
        fill: chartMode.value === 'line',
        pointRadius: chartMode.value === 'line' ? 4 : 0,
        pointBackgroundColor: '#3b82f6',
    }];

    if (chartMode.value === 'bar') {
        datasets.push({
            type: 'line',
            label: 'Retorno Promedio',
            data: values.map(() => avg),
            borderColor: 'rgba(148, 163, 184, 0.8)', // slate-400 opacity
            borderWidth: 2,
            borderDash: [5, 5],
            pointRadius: 0,
            fill: false,
        });
    }

    return {
        labels: source.labels,
        datasets: datasets
    };
});

const chartOptions = computed(() => {
    return {
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
        },
        onClick: (event, elements, chart) => {
            if (chartMode.value === 'bar' && props.viewType === 'MAX' && elements.length > 0) {
                const dataIndex = elements[0].index;
                const yearClicked = props.annual.labels[dataIndex];
                if (yearClicked) switchView(yearClicked);
            }
        }
    };
});

// Heatmap color logic
const getHeatmapColor = (value) => {
    if (value === 0) return 'bg-slate-100 dark:bg-slate-800 text-slate-400';
    if (value > 0) {
        if (value > 500) return 'bg-emerald-500 text-white';
        if (value > 100) return 'bg-emerald-400 text-emerald-900';
        return 'bg-emerald-200 text-emerald-800';
    } else {
        if (value < -500) return 'bg-rose-500 text-white';
        if (value < -100) return 'bg-rose-400 text-rose-900';
        return 'bg-rose-200 text-rose-800';
    }
};

</script>

<template>
    <Head title="Análisis de Rendimiento" />

    <AuthenticatedLayout>
        <template #header>
            <PortfolioHeader 
                :portfolios="portfolios"
                :selected-portfolio-id="selectedPortfolioId"
                @switch="switchPortfolio"
            />
        </template>

        <div class="py-8 bg-slate-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Page Title -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/40 flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Análisis de Rendimiento</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                {{ viewType === 'MAX' ? 'Histórico acumulado por años' : `Detalle mensual para el año ${viewType}` }}
                            </p>
                        </div>
                    </div>

                    <!-- Chart Mode Selector -->
                    <div class="flex items-center bg-slate-100 dark:bg-slate-700/50 p-1 rounded-xl">
                        <button @click="chartMode = 'bar'" :class="chartMode === 'bar' ? 'bg-white dark:bg-slate-600 shadow-sm text-blue-600 dark:text-blue-400' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'" class="p-2 rounded-lg transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        </button>
                        <button @click="chartMode = 'line'" :class="chartMode === 'line' ? 'bg-white dark:bg-slate-600 shadow-sm text-blue-600 dark:text-blue-400' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'" class="p-2 rounded-lg transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" /></svg>
                        </button>
                        <button @click="chartMode = 'heatmap'" :class="chartMode === 'heatmap' ? 'bg-white dark:bg-slate-600 shadow-sm text-blue-600 dark:text-blue-400' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'" class="p-2 rounded-lg transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                        </button>
                    </div>
                </div>

                <!-- Main Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    
                    <!-- Sidebar: Time Selector -->
                    <div class="lg:col-span-1 space-y-4">
                        <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 px-2">Temporalidad</h3>
                            <button 
                                @click="switchView('MAX')"
                                class="w-full text-left px-4 py-3 rounded-xl mb-2 font-medium transition-colors"
                                :class="viewType === 'MAX' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'hover:bg-slate-50 text-slate-700 dark:hover:bg-slate-700/50 dark:text-slate-300'"
                            >
                                Histórico (MAX)
                            </button>
                            <div class="border-t border-slate-100 dark:border-slate-700 my-2"></div>
                            <div class="space-y-1">
                                <button 
                                    v-for="year in availableYears" :key="year"
                                    @click="switchView(year)"
                                    class="w-full text-left px-4 py-2 rounded-xl text-sm font-medium transition-colors"
                                    :class="viewType === String(year) || viewType === Number(year) ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'hover:bg-slate-50 text-slate-600 dark:hover:bg-slate-700/50 dark:text-slate-400'"
                                >
                                    Año {{ year }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content: Visualization -->
                    <div class="lg:col-span-3">
                        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 h-[500px]">
                            
                            <!-- Heatmap View -->
                            <div v-if="chartMode === 'heatmap'" class="h-full overflow-x-auto">
                                <div class="min-w-[800px] h-full flex flex-col">
                                    <div class="grid gap-2 mb-2 font-medium text-xs text-slate-400 uppercase text-center tracking-wider" style="grid-template-columns: repeat(13, minmax(0, 1fr));">
                                        <div class="text-left font-bold pl-2">Año</div>
                                        <div v-for="month in ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']" :key="month">
                                            {{ month }}
                                        </div>
                                    </div>
                                    <div class="flex-1 flex flex-col gap-2 overflow-y-auto pr-2 pb-2">
                                        <div v-for="row in heatmap" :key="row.year" class="grid gap-2 group" style="grid-template-columns: repeat(13, minmax(0, 1fr));">
                                            <div class="font-bold text-slate-700 dark:text-slate-300 flex items-center pl-2">{{ row.year }}</div>
                                            <div v-for="(val, idx) in row.months" :key="idx" 
                                                class="rounded-lg flex flex-col justify-center items-center h-14 transition-transform group-hover:scale-105"
                                                :class="getHeatmapColor(val)"
                                            >
                                                <span v-if="val !== 0" class="text-[10px] font-bold opacity-90">{{ formatCurrency(val, 0) }}</span>
                                                <span v-else class="text-xl font-bold opacity-40">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Standard Charts -->
                            <div v-else class="h-full relative w-full">
                                <BarChart v-if="chartMode === 'bar'" :data="chartData" :options="chartOptions" />
                                <LineChart v-if="chartMode === 'line'" :data="chartData" :options="chartOptions" />
                            </div>
                        </div>
                        <p v-if="chartMode === 'bar' && viewType === 'MAX'" class="text-xs text-slate-400 mt-4 text-center">
                            * Haz clic en una barra para ver el detalle mensual de ese año
                        </p>
                        
                        <div v-if="chartMode === 'bar'" class="mt-6 flex flex-col md:flex-row items-center justify-center gap-4 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700/50">
                            <div class="h-0.5 w-8 border-t-2 border-dashed border-slate-400 shrink-0"></div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 max-w-2xl text-center md:text-left leading-relaxed">
                                La <strong>línea media</strong> del gráfico de barras representa el <strong>retorno promedio</strong> de la cartera de inversiones a lo largo del historial seleccionado. Nos permite identificar visualmente qué periodos rinden por encima del estándar o nos lastran, evaluando así la consistencia general.
                            </p>
                        </div>
                        
                        <!-- Detailed Breakdown grid section underneath the graph -->
                        <div v-if="detailed" class="mt-8 bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">
                                {{ viewType === 'MAX' ? 'Desglose Global de Rentabilidad' : `Desglose de Rentabilidad - Año ${viewType}` }}
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                                <!-- Capital Section -->
                                <div class="space-y-4">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-50 dark:border-slate-700/50 pb-2">Capital</h4>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-slate-600 dark:text-slate-400">Capital invertido</span>
                                        <span class="text-sm font-semibold text-slate-900 dark:text-white">
                                            {{ isPrivacyMode ? '****' : formatCurrency(detailed.capital_invertido) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Desglose Section -->
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

                                <!-- Costos Section -->
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
                                        <!-- Costes Corrientes -->
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-slate-600 dark:text-slate-400">Costes corrientes</span>
                                            <span class="text-sm font-semibold text-rose-600 dark:text-rose-400">
                                                {{ isPrivacyMode ? '****' : '-0,00 €' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Totales & Métricas Section -->
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
                                            <p class="text-[10px] font-bold text-slate-400 uppercase">Tasa interna de rentabilidad (TIR)</p>
                                            <p class="text-xs font-bold text-slate-800 dark:text-white">{{ formatPercent(detailed.tir) }}</p>
                                        </div>
                                        <div class="bg-slate-50 dark:bg-slate-900/50 p-2 rounded-xl flex justify-between items-center">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase text-right leading-tight max-w-[120px]">Tasa real de retorno ponderada en el tiempo</p>
                                            <p class="text-xs font-bold text-slate-800 dark:text-white">{{ formatPercent(detailed.twror) }}</p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
