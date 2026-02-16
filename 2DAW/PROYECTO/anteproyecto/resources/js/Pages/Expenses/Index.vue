<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import TransactionModal from '@/Components/TransactionModal.vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import BarChart from '@/Components/Charts/BarChart.vue';
import DoughnutChart from '@/Components/Charts/DoughnutChart.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InfoTooltip from '@/Components/InfoTooltip.vue';

const props = defineProps({
    filters: Object,
    summary: Object,
    charts: Object,
    transactions: Object,
    portfolios: Array,
    categories: Array,
});

// Estado para filtros
const dateFilters = ref({
    start_date: props.filters.start_date,
    end_date: props.filters.end_date,
});

// Estado para Modal
const showModal = ref(false);
const editingTransaction = ref(null);

// Formateo de Moneda
const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value);
};

// Aplicar filtros automáticamente al cambiar fechas
const applyFilters = () => {
    router.get(route('expenses.index'), dateFilters.value, {
        preserveState: true,
        replace: true,
        only: ['summary', 'charts', 'transactions', 'filters']
    });
};

// Configuración Gráfico Tendencia (Línea Comparativa)
const trendChartData = computed(() => ({
    labels: props.charts.trend.labels,
    datasets: [
        {
            label: 'Ingresos',
            data: props.charts.trend.income,
            borderColor: '#10b981', // Emerald 500
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            pointBackgroundColor: '#ffffff',
            pointBorderColor: '#10b981',
            pointBorderWidth: 2,
            tension: 0.4,
            fill: true,
        },
        {
            label: 'Gastos',
            data: props.charts.trend.expenses,
            borderColor: '#f43f5e', // Rose 500
            backgroundColor: 'rgba(244, 63, 94, 0.1)',
            pointBackgroundColor: '#ffffff',
            pointBorderColor: '#f43f5e',
            pointBorderWidth: 2,
            tension: 0.4,
            fill: true,
        }
    ]
}));

const trendChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: {
        mode: 'index',
        intersect: false,
    },
    plugins: {
        legend: { position: 'top', align: 'end' },
        tooltip: {
            callbacks: {
                label: (context) => `${context.dataset.label}: ${formatCurrency(context.parsed.y)}`
            }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: { color: 'rgba(148, 163, 184, 0.1)' },
            ticks: { font: { size: 10 }, color: '#94a3b8' }
        },
        x: {
            grid: { display: false },
            ticks: { font: { size: 10 }, color: '#94a3b8', maxTicksLimit: 10 }
        }
    }
};

// Configuración Gráfico Categorías (Donut - Solo Gastos)
const categoryChartData = computed(() => {
    const colors = ['#f43f5e', '#fb923c', '#fbbf24', '#a3e635', '#34d399', '#22d3ee', '#818cf8', '#e879f9'];
    return {
        labels: props.charts.categories.labels,
        datasets: [{
            data: props.charts.categories.data,
            backgroundColor: colors,
            borderWidth: 0,
            hoverOffset: 4
        }]
    };
});

const categoryChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'right', labels: { usePointStyle: true, font: { size: 11 } } }
    },
    cutout: '70%'
};

// Configuración Gráfico Mensual (Barra Apilada/Comparativa)
const monthlyChartData = computed(() => ({
    labels: props.charts.monthly.labels,
    datasets: [
        {
            label: 'Ingresos',
            data: props.charts.monthly.income,
            backgroundColor: '#10b981',
            borderRadius: 4,
        },
        {
            label: 'Gastos',
            data: props.charts.monthly.expense,
            backgroundColor: '#f43f5e',
            borderRadius: 4,
        },
        {
            label: 'Ahorro',
            data: props.charts.monthly.savings,
            backgroundColor: '#3b82f6',
            borderRadius: 4,
        }
    ]
}));

const monthlyChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'top', align: 'end' },
        tooltip: {
            callbacks: {
                label: (context) => `${context.dataset.label}: ${formatCurrency(context.parsed.y)}`
            }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: { color: 'rgba(148, 163, 184, 0.1)' },
            ticks: { font: { size: 10 }, color: '#94a3b8' }
        },
        x: {
            grid: { display: false },
            ticks: { font: { size: 10 }, color: '#94a3b8' }
        }
    }
};

// Paginación
const changePage = (url) => {
    if (url) router.get(url, dateFilters.value, { preserveState: true });
};

// Modal Actions
const openTransactionModal = () => {
    editingTransaction.value = { type: 'expense' }; // Por defecto gasto, pero editable en modal
    showModal.value = true;
};

const editTransaction = (transaction) => {
    editingTransaction.value = transaction;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingTransaction.value = null;
};

// Helper para colores de tipo
const getTypeColor = (type) => {
    if (['income', 'dividend', 'gift', 'reward', 'transfer_in'].includes(type)) return 'text-emerald-600 bg-emerald-50 border-emerald-200';
    if (['expense', 'transfer_out'].includes(type)) return 'text-rose-600 bg-rose-50 border-rose-200';
    return 'text-slate-600 bg-slate-50 border-slate-200';
};

const getTypeLabel = (type) => {
    const map = {
        'income': 'Ingreso',
        'expense': 'Gasto',
        'transfer_in': 'Transf. Entrante',
        'transfer_out': 'Transf. Saliente',
        'dividend': 'Dividendo',
        'gift': 'Regalo',
        'reward': 'Recompensa'
    };
    return map[type] || type;
};
</script>

<template>
    <Head title="Ingresos y Gastos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-end gap-4">
                <div>
                    <h2 class="text-2xl font-bold leading-tight text-slate-800 dark:text-white">
                        Ingresos y Gastos
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Controla tu flujo de caja: cuánto ganas, cuánto gastas y cuánto ahorras.
                    </p>
                </div>

                <!-- Filtros de Fecha -->
                <div class="flex items-center gap-2 bg-white dark:bg-slate-800 p-2 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
                    <div>
                        <input 
                            type="date" 
                            v-model="dateFilters.start_date"
                            @change="applyFilters"
                            class="text-xs border-none bg-slate-50 dark:bg-slate-700 rounded focus:ring-rose-500 text-slate-600 dark:text-slate-200 p-1.5"
                        />
                    </div>
                    <span class="text-slate-400">-</span>
                    <div>
                        <input 
                            type="date" 
                            v-model="dateFilters.end_date"
                            @change="applyFilters"
                            class="text-xs border-none bg-slate-50 dark:bg-slate-700 rounded focus:ring-rose-500 text-slate-600 dark:text-slate-200 p-1.5"
                        />
                    </div>
                </div>
            </div>
        </template>

        <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- 1. TARJETAS DE RESUMEN (KPIs) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Ingresos -->
                <div class="bg-white dark:bg-slate-800 p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 border-l-4 border-l-emerald-500 relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide">Ingresos Totales</p>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ formatCurrency(summary.total_income) }}</h3>
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
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ formatCurrency(summary.total_expense) }}</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">Promedio diario: {{ formatCurrency(summary.avg_daily_expense) }}</p>
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
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ formatCurrency(summary.net_savings) }}</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">
                            {{ summary.total_income > 0 ? ((summary.net_savings / summary.total_income) * 100).toFixed(1) + '% de tasa de ahorro' : '-' }}
                        </p>
                    </div>
                    <div class="absolute right-0 top-0 p-4 opacity-10">
                        <svg class="w-16 h-16 text-slate-500" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/></svg>
                    </div>
                </div>
            </div>

            <!-- 2. GRÁFICOS -->
            <div class="space-y-8 mb-8">
                <!-- Nuevo Gráfico Mensual: Ahorro vs Gano vs Gasto -->
                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 relative h-96">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Balance Mensual (Año Actual)</h3>
                        <InfoTooltip text="Comparativa mensual de tus ingresos, gastos y ahorro resultante." />
                    </div>
                    <div class="absolute inset-x-6 bottom-6 top-16">
                        <BarChart :data="monthlyChartData" :options="monthlyChartOptions" />
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Gráfico de Tendencia (2/3 ancho) -->
                    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 lg:col-span-2 relative h-80">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Evolución Ingresos vs Gastos</h3>
                        <InfoTooltip text="Compara tus entradas y salidas de dinero día a día." />
                    </div>
                    <div class="absolute inset-x-6 bottom-6 top-16">
                        <LineChart :data="trendChartData" :options="trendChartOptions" />
                    </div>
                </div>

                <!-- Gráfico de Categorías (1/3 ancho) -->
                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 relative h-80">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Gastos por Categoría</h3>
                        <InfoTooltip text="Distribución de tus gastos." />
                    </div>
                    <div class="absolute inset-x-6 bottom-6 top-16">
                        <div v-if="summary.total_expense > 0" class="w-full h-full relative">
                            <DoughnutChart :data="categoryChartData" :options="categoryChartOptions" />
                        </div>
                        <div v-else class="h-full flex items-center justify-center text-slate-400 italic text-sm">
                            No hay gastos para mostrar
                        </div>
                    </div>
                </div>
                </div>
            </div>

            <!-- 3. TABLA DETALLADA -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Historial de Movimientos</h3>
                    <div class="flex gap-2">
                        <SecondaryButton @click="router.visit(route('categories.index'))" class="dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 dark:border-slate-600">
                            Gestionar Categorías
                        </SecondaryButton>
                        <PrimaryButton @click="openTransactionModal" class="bg-blue-600 hover:bg-blue-700 focus:ring-blue-500">
                            + Registrar Transacción
                        </PrimaryButton>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 uppercase font-medium text-xs">
                            <tr>
                                <th class="px-6 py-3">Fecha</th>
                                <th class="px-6 py-3">Tipo</th>
                                <th class="px-6 py-3">Categoría</th>
                                <th class="px-6 py-3">Descripción</th>
                                <th class="px-6 py-3 text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            <tr 
                                v-for="tx in transactions.data" 
                                :key="tx.id"
                                @click="editTransaction(tx)"
                                class="hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer transition-colors group"
                            >
                                <td class="px-6 py-4 whitespace-nowrap text-slate-600 dark:text-slate-300">{{ tx.display_date }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-md text-xs font-medium border"
                                          :class="getTypeColor(tx.type)">
                                        {{ getTypeLabel(tx.type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span v-if="tx.category" class="px-2 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-md text-xs font-medium border border-slate-200 dark:border-slate-600">
                                        {{ tx.category }}
                                    </span>
                                    <span v-else class="text-slate-400">-</span>
                                </td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300">{{ tx.description || '-' }}</td>
                                <td class="px-6 py-4 text-right font-bold"
                                    :class="['expense', 'transfer_out'].includes(tx.type) ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400'">
                                    {{ ['expense', 'transfer_out'].includes(tx.type) ? '-' : '+' }}{{ formatCurrency(tx.amount) }}
                                </td>
                            </tr>
                            <tr v-if="transactions.data.length === 0">
                                <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                    No se encontraron movimientos en este periodo.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div v-if="transactions.links.length > 3" class="p-4 border-t border-slate-100 dark:border-slate-700 flex justify-center">
                    <div class="flex flex-wrap gap-1">
                        <button
                            v-for="(link, k) in transactions.links"
                            :key="k"
                            @click="changePage(link.url)"
                            v-html="link.label"
                            class="px-3 py-1 rounded text-sm transition-colors"
                            :class="{
                                'bg-slate-800 text-white dark:bg-blue-600': link.active,
                                'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700': !link.active,
                                'opacity-50 cursor-not-allowed': !link.url
                            }"
                            :disabled="!link.url"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para Crear/Editar -->
        <TransactionModal 
            :show="showModal" 
            :transaction="editingTransaction"
            :portfolios="portfolios"
            :categories="categories"
            :allowed-types="['income', 'expense']"
            @close="closeModal" 
        />
    </AuthenticatedLayout>
</template>