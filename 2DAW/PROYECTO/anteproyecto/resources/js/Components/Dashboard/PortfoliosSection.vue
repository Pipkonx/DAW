<script setup>
/**
 * PortfoliosSection - Dashboard Component
 * 
 * Gestiona la lista de carteras y el gráfico de distribución por cartera.
 * Incluye un estado 'vacío' cuando no hay datos.
 */
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { formatCurrency } from '@/Utils/formatting';
import DoughnutChart from '@/Components/Charts/DoughnutChart.vue';
import InfoTooltip from '@/Components/InfoTooltip.vue';

const props = defineProps({
    portfolios: {
        type: Array,
        required: true
    },
    isPrivacyMode: {
        type: Boolean,
        default: false
    }
});

// Carteras ordenadas por valor total (mayor a menor)
const sortedPortfolios = computed(() => {
    return [...props.portfolios].sort((a, b) => b.total_value - a.total_value);
});

// Configuración del gráfico de carteras
const chartData = computed(() => {
    const labels = sortedPortfolios.value.map(p => p.name);
    const data = sortedPortfolios.value.map(p => p.total_value);
    const colors = [
        '#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', 
        '#ef4444', '#0ea5e9', '#f97316', '#6366f1'
    ];


    return {
        labels: labels,
        datasets: [{
            data: data,
            backgroundColor: colors.slice(0, data.length),
            borderWidth: 0,
            hoverOffset: 15,
            borderRadius: 8,
            spacing: 3,
            cutout: '72%'
        }]
    };
});

// Opciones del gráfico doughnut optimizadas
const doughnutOptions = {
    responsive: true,
    maintainAspectRatio: false,
    layout: {
        padding: 20
    },
    plugins: {
        legend: {
            position: 'bottom',
            labels: { 
                usePointStyle: true, 
                pointStyle: 'circle',
                padding: 15, 
                font: { size: 11, weight: '500' }, 
                color: '#64748b' 
            }
        },
        tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.9)',
            padding: 12,
            cornerRadius: 10,
            displayColors: true,
            usePointStyle: true,
            callbacks: {
                label: (context) => {
                    const value = context.parsed;
                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                    return ` ${context.label}: ${formatCurrency(value)} (${percentage})`;
                }
            }
        }
    }
};

</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center">
                <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 p-2 rounded-lg mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </span>
                Mis Inversiones
            </h3>
            <InfoTooltip text="Desglose de tus activos por cartera." />
        </div>

        <div v-if="sortedPortfolios.length > 0" class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Columna Izquierda: Gráfico de Inversiones (1/3) -->
            <div class="xl:col-span-1">
                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 h-80 relative flex flex-col overflow-hidden">
                    <h4 class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Distribución por Cartera</h4>
                    <div class="relative h-60 w-full mx-auto" :class="{ 'blur-sm select-none': isPrivacyMode }">
                        <DoughnutChart :data="chartData" :options="doughnutOptions" />
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Listado de Carteras en Rejilla (2/3) -->
            <div class="xl:col-span-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div v-for="portfolio in sortedPortfolios" :key="portfolio.id" class="bg-white dark:bg-slate-800 p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 hover:border-blue-200 dark:hover:border-blue-700 transition-all hover:shadow-md group">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-white text-lg group-hover:text-blue-600 transition-colors">{{ portfolio.name }}</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-1">{{ portfolio.description }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-slate-900 dark:text-white">{{ isPrivacyMode ? '****' : formatCurrency(portfolio.total_value) }}</p>
                                <p v-if="!isPrivacyMode" class="text-xs font-medium" :class="portfolio.yield >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                                    {{ portfolio.yield >= 0 ? '+' : '' }}{{ portfolio.yield.toFixed(2) }}% Rend.
                                </p>
                            </div>
                        </div>
                        
                        <div class="space-y-3 mt-4 pt-4 border-t border-slate-50 dark:border-slate-700/50">
                            <Link v-for="asset in portfolio.assets.slice(0, 3)" :key="asset.id" :href="route('assets.show', { ticker: asset.ticker || asset.isin })" class="flex justify-between items-center text-sm group/asset hover:bg-slate-50 dark:hover:bg-slate-700/40 p-1 rounded-lg transition-colors cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <div v-if="asset.logo" class="w-8 h-8 rounded-full overflow-hidden bg-white dark:bg-slate-700 flex items-center justify-center border border-slate-100 dark:border-slate-600 shadow-sm">
                                        <img :src="asset.logo" class="w-full h-full object-cover" />
                                    </div>
                                    <div v-else class="w-8 h-8 rounded-full flex items-center justify-center text-[10px] font-bold text-white shadow-sm" :style="{ backgroundColor: asset.color || '#3b82f6' }">
                                        {{ asset.ticker ? asset.ticker.substring(0,2) : asset.name.substring(0,2) }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-800 dark:text-white leading-none group-hover/asset:text-blue-600 transition-colors">{{ asset.name }}</p>
                                        <p class="text-[9px] text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest">{{ asset.ticker || asset.type }}</p>
                                    </div>
                                </div>
                                <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ isPrivacyMode ? '****' : formatCurrency(asset.current_value) }}</span>
                            </Link>
                            <div v-if="portfolio.assets.length > 3" class="text-[10px] text-blue-500 dark:text-blue-400 font-bold pt-1 uppercase tracking-tighter">
                                + {{ portfolio.assets.length - 3 }} activos adicionales
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estado Vacío (Empty State) -->
        <div v-else class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 text-center flex flex-col items-center justify-center h-80">
            <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-full mb-4">
                <svg class="w-8 h-8 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            </div>
            <h4 class="text-slate-900 dark:text-white font-medium mb-1">Sin carteras activas</h4>
            <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">No tienes carteras de inversión registradas.</p>
            <Link :href="route('transactions.index')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                Crear Cartera
            </Link>
        </div>
    </div>
</template>
