<script setup>
import { useExpenseCharts } from '@/Composables/useExpenseCharts';
import LineChart from '@/Components/Charts/LineChart.vue';
import BarChart from '@/Components/Charts/BarChart.vue';
import DoughnutChart from '@/Components/Charts/DoughnutChart.vue';
import InfoTooltip from '@/Components/InfoTooltip.vue';

const props = defineProps({
    charts: {
        type: Object,
        required: true
    },
    summary: {
        type: Object,
        required: true
    },
    isPrivacyMode: {
        type: Boolean,
        default: false
    }
});

const {
    trendChartData,
    trendChartOptions,
    categoryChartData,
    categoryChartOptions,
    monthlyChartData,
    monthlyChartOptions
} = useExpenseCharts(props);
</script>

<template>
    <div class="space-y-8 mb-8">
        <!-- Nuevo Gráfico Mensual: Ahorro vs Gano vs Gasto -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 relative h-96">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Balance Mensual (Año Actual)</h3>
                <InfoTooltip text="Comparativa mensual de tus ingresos, gastos y ahorro resultante." />
            </div>
            <div class="absolute inset-x-6 bottom-6 top-16">
                <div v-if="summary.total_income > 0 || summary.total_expense > 0" class="w-full h-full relative" :class="{ 'blur-sm select-none': isPrivacyMode }">
                    <BarChart :data="monthlyChartData" :options="monthlyChartOptions" />
                </div>
                <div v-else class="h-full flex flex-col items-center justify-center text-slate-400">
                    <svg class="w-12 h-12 mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span class="text-sm italic">No hay datos suficientes para mostrar el balance mensual</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Gráfico de Tendencia (2/3 ancho) -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 lg:col-span-2 relative h-80">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Evolución del Saldo</h3>
                    <InfoTooltip text="Muestra cómo ha crecido o disminuido tu saldo acumulado a lo largo del periodo." />
                </div>
                <div class="absolute inset-x-6 bottom-6 top-16">
                    <div v-if="summary.total_income > 0 || summary.total_expense > 0" class="w-full h-full relative" :class="{ 'blur-sm select-none': isPrivacyMode }">
                        <LineChart :data="trendChartData" :options="trendChartOptions" />
                    </div>
                    <div v-else class="h-full flex flex-col items-center justify-center text-slate-400">
                        <svg class="w-12 h-12 mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                        <span class="text-sm italic">Registra movimientos para ver tu evolución</span>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Categorías (1/3 ancho) -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 relative h-80">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Gastos por Categoría</h3>
                    <InfoTooltip text="Distribución de tus gastos." />
                </div>
                <div class="absolute inset-x-6 bottom-6 top-16">
                    <div v-if="summary.total_expense > 0 && charts.categories.data.length > 0" class="w-full h-full relative" :class="{ 'blur-sm select-none': isPrivacyMode }">
                        <DoughnutChart :data="categoryChartData" :options="categoryChartOptions" />
                    </div>
                    <div v-else class="h-full flex items-center justify-center text-slate-400 italic text-sm">
                        No hay gastos categorizados para mostrar
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
