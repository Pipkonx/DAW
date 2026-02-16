<script setup>
/**
 * Dashboard Principal
 * 
 * Este componente es el centro de control del usuario.
 * Utiliza Vue 3 Composition API con <script setup> para una sintaxis más limpia.
 * Recibe los datos (props) directamente del controlador de Laravel (Inertia).
 */
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import TransactionModal from '@/Components/TransactionModal.vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import DoughnutChart from '@/Components/Charts/DoughnutChart.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InfoTooltip from '@/Components/InfoTooltip.vue';

// Definición de Props: Datos que vienen del Backend (DashboardController)
const props = defineProps({
    summary: Object,          // { netWorth, cash, investmentsTotal }
    portfolios: Array,        // Lista de carteras con sus métricas y activos
    expenses: Object,         // { monthlyTotal, monthlyIncome, byCategory: [...] }
    charts: Object,           // { netWorthLabels, netWorthData, portfolioHistory }
    recentTransactions: Array,// Últimas transacciones
    allAssetsList: Array,     // Lista simple de activos (para referencias si es necesario)
    categories: Array,        // Lista de categorías disponibles
});

// Estado reactivo
const showModal = ref(false);
const editingTransaction = ref(null);
const chartMode = ref('global'); // 'global' | 'portfolios'
const transactionFilter = ref('all'); // 'all' | 'income' | 'expense' | 'investment'

// Abrir modal para nueva transacción
const openNewTransaction = () => {
    editingTransaction.value = null;
    showModal.value = true;
};

// Abrir modal para editar transacción
const editTransaction = (transaction) => {
    editingTransaction.value = transaction;
    showModal.value = true;
};

// Utilidad para formatear moneda (Euro)
const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value);
};

// Diccionario de Tipos de Transacción
const transactionTypes = {
    income: { label: 'Ingreso', color: 'bg-emerald-50 text-emerald-700 border-emerald-100', icon: '↓' },
    expense: { label: 'Gasto', color: 'bg-rose-50 text-rose-700 border-rose-100', icon: '↑' },
    buy: { label: 'Compra', color: 'bg-blue-50 text-blue-700 border-blue-100', icon: 'BUY' },
    sell: { label: 'Venta', color: 'bg-indigo-50 text-indigo-700 border-indigo-100', icon: 'SELL' },
    dividend: { label: 'Dividendo', color: 'bg-amber-50 text-amber-700 border-amber-100', icon: '$' },
    gift: { label: 'Regalo', color: 'bg-pink-50 text-pink-700 border-pink-100', icon: '♥' },
    reward: { label: 'Recompensa', color: 'bg-purple-50 text-purple-700 border-purple-100', icon: '★' },
    transfer_in: { label: 'Transf. (Entrada)', color: 'bg-gray-50 text-gray-700 border-gray-100', icon: '→' },
    transfer_out: { label: 'Transf. (Salida)', color: 'bg-gray-50 text-gray-700 border-gray-100', icon: '←' },
};

// ---------------------------------------------------------
// CONFIGURACIÓN DE GRÁFICOS
// ---------------------------------------------------------

// 1. Gráfico Patrimonio (Line Chart) - Soporta modo Global y Por Cartera
const netWorthChartData = computed(() => {
    if (chartMode.value === 'global') {
        return {
            labels: props.charts.netWorthLabels,
            datasets: [
                {
                    label: 'Patrimonio Neto (€)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)', // Azul transparente
                    borderColor: '#3b82f6', // Azul Tailwind 500
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4,
                    data: props.charts.netWorthData
                }
            ]
        };
    } else {
        // Modo Por Cartera
        const datasets = [];
        const colors = ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#ec4899', '#6366f1'];
        
        let i = 0;
        // Asumiendo que props.charts.portfolioHistory es un objeto { portfolioId: [data...] }
        for (const [id, data] of Object.entries(props.charts.portfolioHistory || {})) {
            const portfolio = props.portfolios.find(p => p.id == id) || { name: 'Sin Cartera' };
            datasets.push({
                label: portfolio.name,
                borderColor: colors[i % colors.length],
                backgroundColor: 'transparent',
                pointBackgroundColor: '#ffffff',
                pointBorderColor: colors[i % colors.length],
                tension: 0.4,
                data: data
            });
            i++;
        }
        return {
            labels: props.charts.netWorthLabels,
            datasets: datasets
        };
    }
});

const lineChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: true, position: 'top', align: 'end' }, // Mostrar leyenda si hay múltiples líneas
        tooltip: {
            backgroundColor: '#1e293b',
            titleColor: '#f8fafc',
            bodyColor: '#f8fafc',
            padding: 10,
            cornerRadius: 8,
            callbacks: {
                label: (context) => `${context.dataset.label}: ${formatCurrency(context.parsed.y)}`
            }
        }
    },
    scales: {
        y: {
            grid: { color: '#f1f5f9' },
            ticks: {
                callback: (value) => new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumSignificantDigits: 3 }).format(value),
                font: { size: 11 },
                color: '#64748b'
            },
            border: { display: false }
        },
        x: {
            grid: { display: false },
            ticks: { font: { size: 11 }, color: '#64748b' },
            border: { display: false }
        }
    }
};

// 2. Gráfico Distribución de Inversiones (Por Cartera)
const portfolioDistributionData = computed(() => {
    const labels = props.portfolios.map(p => p.name);
    const data = props.portfolios.map(p => p.total_value);
    const colors = ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#ec4899', '#6366f1'];

    return {
        labels: labels,
        datasets: [{
            data: data,
            backgroundColor: colors.slice(0, data.length),
            borderWidth: 0,
            hoverOffset: 4
        }]
    };
});

// 3. Gráfico de Gastos (Por Categoría)
const expensesDistributionData = computed(() => {
    const labels = props.expenses.byCategory.map(c => c.category);
    const data = props.expenses.byCategory.map(c => c.total);
    const colors = ['#ef4444', '#f97316', '#f59e0b', '#eab308', '#84cc16', '#22c55e', '#06b6d4', '#3b82f6', '#6366f1', '#a855f7'];

    return {
        labels: labels,
        datasets: [{
            data: data,
            backgroundColor: colors.slice(0, data.length),
            borderWidth: 0,
            hoverOffset: 4
        }]
    };
});

const doughnutOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'right',
            labels: { usePointStyle: true, padding: 15, font: { size: 11 }, color: '#475569' }
        },
        tooltip: {
            callbacks: {
                label: (context) => {
                    const value = context.parsed;
                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                    return ` ${context.label}: ${formatCurrency(value)} (${percentage})`;
                }
            }
        }
    },
    cutout: '70%'
};

// Filtro de Transacciones
const filteredTransactions = computed(() => {
    if (transactionFilter.value === 'all') return props.recentTransactions;
    return props.recentTransactions.filter(t => {
        if (transactionFilter.value === 'income') return ['income', 'dividend', 'reward', 'gift', 'transfer_in'].includes(t.type);
        if (transactionFilter.value === 'expense') return ['expense', 'transfer_out'].includes(t.type);
        if (transactionFilter.value === 'investment') return ['buy', 'sell'].includes(t.type);
        return true;
    });
});

</script>

<template>
    <Head title="Panel Financiero" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold leading-tight text-slate-800">
                        Panel Financiero
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Resumen de tu situación financiera actual
                    </p>
                </div>
            </div>
        </template>

        <div class="py-8 space-y-8">
            
            <!-- 1. RESUMEN PRINCIPAL (KPIs) -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Patrimonio Neto -->
                    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 relative group hover:shadow-md transition-shadow">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none overflow-hidden rounded-2xl inset-0">
                            <svg class="absolute top-4 right-4 w-24 h-24 text-blue-600 dark:text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.95V5h-2.93v1.74c-1.81.44-2.43 1.41-2.43 2.51 0 1.91 1.66 2.52 3.97 3.06 1.77.42 2.34 1.05 2.34 1.81 0 .93-.93 1.54-2.34 1.54-1.47 0-2.09-.73-2.14-1.8h-1.8c.06 1.64 1.13 2.76 2.8 3.08v1.78h2.93v-1.77c1.9-.45 2.51-1.47 2.51-2.67 0-1.99-1.72-2.56-4.03-3.08z"/></svg>
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center mb-2">
                                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Patrimonio Neto</p>
                                <InfoTooltip text="Suma total de Inversiones + Efectivo/Ahorros." />
                            </div>
                            <h3 class="text-3xl font-bold text-slate-900 dark:text-white">{{ formatCurrency(summary.netWorth) }}</h3>
                            <div class="mt-4 flex items-center text-sm">
                                <span class="text-slate-500 dark:text-slate-400">Efectivo: </span>
                                <span class="font-semibold text-slate-700 dark:text-slate-300 ml-1">{{ formatCurrency(summary.cash) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Total Inversiones -->
                    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 relative group hover:shadow-md transition-shadow">
                        <div class="absolute inset-0 overflow-hidden rounded-2xl pointer-events-none opacity-10 group-hover:opacity-20 transition-opacity">
                            <svg class="absolute top-4 right-4 w-24 h-24 text-emerald-600 dark:text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center mb-2">
                                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Inversiones</p>
                                <InfoTooltip text="Valor actual de mercado de todos tus activos en carteras." />
                            </div>
                            <h3 class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ formatCurrency(summary.investmentsTotal) }}</h3>
                            <div class="mt-4 flex items-center text-sm">
                                <span class="text-slate-500 dark:text-slate-400">{{ portfolios.length }} carteras activas</span>
                            </div>
                        </div>
                    </div>

                    <!-- Gastos Mensuales -->
                    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 relative group hover:shadow-md transition-shadow">
                        <div class="absolute inset-0 overflow-hidden rounded-2xl pointer-events-none opacity-10 group-hover:opacity-20 transition-opacity">
                            <svg class="absolute top-4 right-4 w-24 h-24 text-rose-600 dark:text-rose-500" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 9c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm6 12H6v-1c0-2 4-3.1 6-3.1s6 1.1 6 3.1v1z"/></svg>
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center mb-2">
                                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Gastos (Mes Actual)</p>
                                <InfoTooltip text="Total de gastos registrados este mes." />
                            </div>
                            <h3 class="text-3xl font-bold text-rose-600 dark:text-rose-400">-{{ formatCurrency(expenses.monthlyTotal) }}</h3>
                            <div class="mt-4 flex items-center text-sm">
                                <span class="text-slate-500 dark:text-slate-400">Ingresos: </span>
                                <span class="font-semibold text-emerald-600 dark:text-emerald-400 ml-1">+{{ formatCurrency(expenses.monthlyIncome) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. SECCIÓN DIVIDIDA: INVERSIONES vs GASTOS -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    <!-- COLUMNA IZQUIERDA: INVERSIONES -->
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center">
                                <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 p-2 rounded-lg mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                </span>
                                Carteras de Inversión
                            </h3>
                            <InfoTooltip text="Desglose de tus inversiones por cartera." />
                        </div>

                        <!-- Gráfico Distribución Carteras -->
                        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 h-80 relative">
                            <div class="relative w-full h-full">
                                <DoughnutChart :data="portfolioDistributionData" :options="doughnutOptions" />
                            </div>
                        </div>

                        <!-- Lista de Carteras -->
                        <div class="space-y-4">
                            <div v-for="portfolio in portfolios" :key="portfolio.id" class="bg-white dark:bg-slate-800 p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 hover:border-blue-200 dark:hover:border-blue-700 transition-colors">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-bold text-slate-800 dark:text-white text-lg">{{ portfolio.name }}</h4>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ portfolio.description }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-slate-900 dark:text-white">{{ formatCurrency(portfolio.total_value) }}</p>
                                        <p class="text-xs font-medium" :class="portfolio.yield >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                                            {{ portfolio.yield >= 0 ? '+' : '' }}{{ portfolio.yield.toFixed(2) }}% Rend.
                                        </p>
                                    </div>
                                </div>
                                <!-- Mini desglose de activos (top 3) -->
                                <div class="space-y-1 mt-3 pt-3 border-t border-slate-50 dark:border-slate-700">
                                    <div v-for="asset in portfolio.assets.slice(0, 3)" :key="asset.id" class="flex justify-between text-sm">
                                        <span class="text-slate-600 dark:text-slate-400">{{ asset.name }}</span>
                                        <span class="text-slate-800 dark:text-slate-200 font-medium">{{ formatCurrency(asset.current_value) }}</span>
                                    </div>
                                    <div v-if="portfolio.assets.length > 3" class="text-xs text-blue-500 dark:text-blue-400 font-medium pt-1">
                                        + {{ portfolio.assets.length - 3 }} activos más...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- COLUMNA DERECHA: GASTOS -->
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center">
                                <span class="bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 p-2 rounded-lg mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                </span>
                                Gastos del Mes
                            </h3>
                            <InfoTooltip text="Desglose de gastos por categoría." />
                        </div>

                         <!-- Gráfico Distribución Gastos -->
                        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 h-80 relative">
                            <div v-if="expenses.byCategory.length > 0" class="relative w-full h-full">
                                <DoughnutChart :data="expensesDistributionData" :options="doughnutOptions" />
                            </div>
                            <div v-else class="h-full flex items-center justify-center text-slate-400 dark:text-slate-500 italic">
                                No hay gastos registrados este mes.
                            </div>
                        </div>

                        <!-- Lista de Categorías de Gastos -->
                        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 uppercase font-medium text-xs">
                                    <tr>
                                        <th class="px-4 py-3">Categoría</th>
                                        <th class="px-4 py-3 text-right">Total</th>
                                        <th class="px-4 py-3 text-right">%</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                    <tr v-for="cat in expenses.byCategory" :key="cat.category" class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                        <td class="px-4 py-3 font-medium text-slate-700 dark:text-slate-300">{{ cat.category }}</td>
                                        <td class="px-4 py-3 text-right text-rose-600 dark:text-rose-400 font-bold">{{ formatCurrency(cat.total) }}</td>
                                        <td class="px-4 py-3 text-right text-slate-500 dark:text-slate-400">
                                            {{ expenses.monthlyTotal > 0 ? ((cat.total / expenses.monthlyTotal) * 100).toFixed(1) : 0 }}%
                                        </td>
                                    </tr>
                                    <tr v-if="expenses.byCategory.length === 0">
                                        <td colspan="3" class="px-4 py-6 text-center text-slate-400 dark:text-slate-500 italic">
                                            Sin gastos este mes
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            <!-- 3. GRÁFICO DE EVOLUCIÓN (PATRIMONIO / CARTERAS) -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                        <div class="flex items-center">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Evolución Financiera</h3>
                            <InfoTooltip text="Visualiza cómo ha crecido tu patrimonio o tus carteras en los últimos 6 meses." />
                        </div>
                        
                        <!-- Toggle Chart Mode -->
                        <div class="bg-slate-100 dark:bg-slate-700 p-1 rounded-lg flex text-sm font-medium">
                            <button 
                                @click="chartMode = 'global'"
                                class="px-4 py-1.5 rounded-md transition-all"
                                :class="chartMode === 'global' ? 'bg-white dark:bg-slate-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                            >
                                Patrimonio Total
                            </button>
                            <button 
                                @click="chartMode = 'portfolios'"
                                class="px-4 py-1.5 rounded-md transition-all"
                                :class="chartMode === 'portfolios' ? 'bg-white dark:bg-slate-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                            >
                                Por Cartera
                            </button>
                        </div>
                    </div>
                    <div class="h-[300px] w-full relative">
                        <LineChart :data="netWorthChartData" :options="lineChartOptions" />
                    </div>
                </div>
            </div>

            <!-- 4. ÚLTIMAS TRANSACCIONES (EDITABLES Y FILTRABLES) -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex flex-col md:flex-row justify-between items-center gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Historial de Transacciones</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Haz clic en una transacción para editarla.</p>
                        </div>

                        <!-- Filtros de Transacciones -->
                        <div class="flex space-x-2 overflow-x-auto pb-1 md:pb-0 w-full md:w-auto">
                            <button 
                                v-for="filter in [
                                    { id: 'all', label: 'Todas' },
                                    { id: 'income', label: 'Ingresos' },
                                    { id: 'expense', label: 'Gastos' },
                                    { id: 'investment', label: 'Inversiones' }
                                ]" 
                                :key="filter.id"
                                @click="transactionFilter = filter.id"
                                class="px-3 py-1.5 text-xs font-medium rounded-full border transition-colors whitespace-nowrap"
                                :class="transactionFilter === filter.id 
                                    ? 'bg-slate-800 dark:bg-blue-600 text-white border-slate-800 dark:border-blue-600' 
                                    : 'bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-600 hover:border-slate-300 dark:hover:border-slate-500'"
                            >
                                {{ filter.label }}
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 uppercase font-medium text-xs">
                                <tr>
                                    <th class="px-6 py-3">Fecha</th>
                                    <th class="px-6 py-3">Tipo</th>
                                    <th class="px-6 py-3">Descripción / Activo</th>
                                    <th class="px-6 py-3">Categoría</th>
                                    <th class="px-6 py-3 text-right">Monto</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                <tr 
                                    v-for="transaction in filteredTransactions" 
                                    :key="transaction.id" 
                                    class="hover:bg-blue-50 dark:hover:bg-slate-700/50 cursor-pointer transition-colors group"
                                    @click="editTransaction(transaction)"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-600 dark:text-slate-300">{{ transaction.display_date }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium border flex items-center w-fit gap-1" :class="transactionTypes[transaction.type]?.color || 'bg-gray-100 text-gray-600'">
                                            <span>{{ transactionTypes[transaction.type]?.icon }}</span>
                                            {{ transactionTypes[transaction.type]?.label || transaction.type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-700 dark:text-slate-200 font-medium">
                                        <div class="flex items-center">
                                            <!-- Asset Logo if available -->
                                            <img v-if="transaction.asset_logo" :src="transaction.asset_logo" class="w-6 h-6 rounded-full mr-2 bg-slate-100 dark:bg-slate-700" alt="logo" />
                                            <span>{{ transaction.description || transaction.asset_name || '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ transaction.category || '-' }}</td>
                                    <td class="px-6 py-4 text-right font-bold" :class="['expense', 'buy', 'transfer_out'].includes(transaction.type) ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400'">
                                        {{ ['expense', 'buy', 'transfer_out'].includes(transaction.type) ? '-' : '+' }}{{ formatCurrency(transaction.amount) }}
                                    </td>
                                </tr>
                                <tr v-if="filteredTransactions.length === 0">
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500">
                                        No hay transacciones que coincidan con el filtro.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Transaction Modal -->
            <TransactionModal 
                :show="showModal" 
                :transaction="editingTransaction"
                :portfolios="portfolios"
                @close="showModal = false" 
            />
        </div>
    </AuthenticatedLayout>
</template>
