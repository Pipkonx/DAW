<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PortfolioHeader from '@/Components/Transactions/PortfolioHeader.vue';
import BarChart from '@/Components/Charts/BarChart.vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import PerformanceTimeSelector from '@/Components/Transactions/PerformanceTimeSelector.vue';
import PerformanceHeatmap from '@/Components/Transactions/PerformanceHeatmap.vue';
import PerformanceBreakdown from '@/Components/Transactions/PerformanceBreakdown.vue';
import AdSlot from '@/Components/AdSense/AdSlot.vue';
import AnnualBarSection from '@/Components/Dashboard/AnnualBarSection.vue';
import { usePrivacy } from '@/Composables/usePrivacy';
import { formatCurrency } from '@/Utils/formatting';

/**
 * Página principal de Análisis de Rendimiento.
 */
const props = defineProps({
    portfolios: Array,
    selectedPortfolioId: [String, Number],
    annual: Object,
    monthly: Object,
    heatmap: Array,
    detailed: Object,
    viewType: [String, Number]
});

const { isPrivacyMode } = usePrivacy();
// Estado de visualización del gráfico (barras, líneas, mapa de calor)
const chartMode = ref('bar');

/**
 * Cambia el portafolio seleccionado.
 */
const switchPortfolio = (id) => {
    router.get(route('transactions.performance'), { 
        portfolio_id: id,
        view: props.viewType
    }, { preserveState: true, preserveScroll: true });
};

/**
 * Cambia la vista temporal (Año exacto o Histórico MAX).
 */
const switchView = (v) => {
    router.get(route('transactions.performance'), { 
        portfolio_id: props.selectedPortfolioId,
        view: v
    }, { preserveState: true, preserveScroll: true });
};

// Años disponibles extraídos de las etiquetas de rendimiento anual
const availableYears = computed(() => {
    return [...props.annual.labels].sort((a,b) => b - a);
});

/**
 * Preparación de datos para los gráficos de Chart.js.
 */
const chartData = computed(() => {
    const isMax = props.viewType === 'MAX';
    const source = isMax ? props.annual : props.monthly;
    
    if (!source || !source.labels) return { labels: [], datasets: [] };

    const values = source.data;
    const avg = values.length > 0 ? values.reduce((a,b) => a + b, 0) / values.length : 0;
    
    const datasets = [{
        type: chartMode.value === 'line' ? 'line' : 'bar',
        label: isMax ? 'Rendimiento Anual (€)' : `Rendimiento Mensual ${props.viewType} (€)`,
        data: values,
        backgroundColor: chartMode.value === 'line' 
            ? 'rgba(59, 130, 246, 0.1)' 
            : values.map(val => val === 0 ? '#94a3b8' : (val > 0 ? '#10b981' : '#f43f5e')),
        borderColor: chartMode.value === 'line' ? '#3b82f6' : 'transparent',
        borderWidth: chartMode.value === 'line' ? 2 : 0,
        borderRadius: chartMode.value === 'bar' ? 6 : 0,
        barThickness: chartMode.value === 'bar' && isMax ? 40 : undefined,
        fill: chartMode.value === 'line',
        pointRadius: chartMode.value === 'line' ? 4 : 0,
        pointBackgroundColor: '#3b82f6',
    }];

    // Añadimos línea de promedio en la vista de barras
    if (chartMode.value === 'bar') {
        datasets.push({
            type: 'line',
            label: 'Retorno Promedio',
            data: values.map(() => avg),
            borderColor: 'rgba(148, 163, 184, 0.8)',
            borderWidth: 2,
            borderDash: [5, 5],
            pointRadius: 0,
            fill: false,
        });
    }

    return { labels: source.labels, datasets: datasets };
});

/**
 * Opciones de configuración para los gráficos.
 */
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
                callbacks: { label: (context) => ` ${formatCurrency(context.parsed.y)}` }
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
        onClick: (event, elements) => {
            if (chartMode.value === 'bar' && props.viewType === 'MAX' && elements.length > 0) {
                const dataIndex = elements[0].index;
                const yearClicked = props.annual.labels[dataIndex];
                if (yearClicked) switchView(yearClicked);
            }
        }
    };
});
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
                
                <!-- Cabecera de Página y Selector de Modo de Gráfico -->
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
                                {{ viewType === 'MAX' ? 'Histórico acumulado por años' : `Detalle mensual para el año ${viewType || 'seleccionado'}` }}
                            </p>
                        </div>
                    </div>

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

                <!-- 1. Análisis Anual Consolidado (Nuevo) -->
                <AnnualBarSection 
                    :data="annual" 
                    :is-privacy-mode="isPrivacyMode" 
                />

                <!-- 2. Distribución y Detalle Principal -->
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    
                    <!-- Lateral: Selector de Tiempo -->
                    <div class="lg:col-span-1">
                        <PerformanceTimeSelector 
                            :available-years="availableYears"
                            :view-type="viewType"
                            @switch="switchView"
                        />
                    </div>

                    <!-- Contenido Central: Gráfica -->
                    <div class="lg:col-span-3">
                        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 h-[500px]">
                            <PerformanceHeatmap v-if="chartMode === 'heatmap'" :heatmap="heatmap" />
                            <div v-else class="h-full relative w-full">
                                <BarChart v-if="chartMode === 'bar'" :data="chartData" :options="chartOptions" />
                                <LineChart v-if="chartMode === 'line'" :data="chartData" :options="chartOptions" />
                            </div>
                        </div>

                        <!-- Anuncio Intermedio en página larga -->
                        <AdSlot slot-id="5678901234" />
                        
                        <!-- Información contextural -->
                        <p v-if="chartMode === 'bar' && viewType === 'MAX'" class="text-xs text-slate-400 mt-4 text-center">
                            * Haz clic en una barra para ver el detalle mensual de ese año
                        </p>
                        <div v-if="chartMode === 'bar'" class="mt-6 flex flex-col md:flex-row items-center justify-center gap-4 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700/50">
                            <div class="h-0.5 w-8 border-t-2 border-dashed border-slate-400 shrink-0"></div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 max-w-2xl text-center md:text-left leading-relaxed">
                                La <strong>línea media</strong> del gráfico de barras representa el <strong>retorno promedio</strong> de la cartera de inversiones a lo largo del historial seleccionado.
                            </p>
                        </div>
                        
                        <!-- Desglose detallado -->
                        <PerformanceBreakdown :detailed="detailed" :view-type="viewType" />
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
