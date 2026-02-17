<script setup>
import { ref, computed } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TransactionModal from '@/Components/TransactionModal.vue';
import PortfolioModal from '@/Components/PortfolioModal.vue';
import SettingsModal from '@/Components/SettingsModal.vue';
import AssetModal from '@/Components/AssetModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import DoughnutChart from '@/Components/Charts/DoughnutChart.vue';
import InfoTooltip from '@/Components/InfoTooltip.vue';

const props = defineProps({
    portfolios: Array,
    selectedPortfolioId: [String, Number],
    selectedAssetId: [String, Number],
    summary: Object,
    assets: Array,
    transactions: Object,
    chart: Object,
    allocations: Object,
    filters: Object
});

// --- Estado Global ---
const allocationType = ref('type'); // type, sector, industry, region, country, currency, asset
const allocationLabels = {
    type: 'Tipo de Activo',
    sector: 'Sector',
    industry: 'Industria',
    region: 'Región',
    country: 'País',
    currency_code: 'Divisa',
    asset: 'Posición Individual'
};
const showPortfolioModal = ref(false);
const editingPortfolio = ref(null);
const showTransactionModal = ref(false);
const editingTransaction = ref(null);
const showSettingsModal = ref(false);
const showAssetModal = ref(false);
const editingAsset = ref(null);
const chartMode = ref('value'); // 'value' | 'performance'
const assetFilter = ref(''); // Filtro para Posiciones Activas

const openCreatePortfolioModal = () => {
    editingPortfolio.value = null;
    showPortfolioModal.value = true;
};

const openSettings = () => {
    showSettingsModal.value = true;
};

const editAsset = (asset) => {
    // Restringido: Solo permitir editar operaciones, no el activo en sí desde aquí.
    // El usuario pidió: "NO permitir editar el activo, Solo permitir editar o eliminar las operaciones asociadas"
    // Por ahora, eliminamos la acción de editar activo desde la lista de posiciones.
    // Podríamos redirigir al historial filtrado por este activo.
    filterByAsset(asset);
};

const filterByAsset = (asset) => {
    // Toggle filter: if already selected, deselect
    const newAssetId = props.selectedAssetId == asset.id ? null : asset.id;
    
    router.get(route('transactions.index'), { 
        portfolio_id: props.selectedPortfolioId,
        asset_id: newAssetId,
        timeframe: props.filters.timeframe 
    }, { preserveState: true, preserveScroll: true });
};

const filteredAssets = computed(() => {
    if (!assetFilter.value) return props.assets;
    const lower = assetFilter.value.toLowerCase();
    return props.assets.filter(a => 
        a.name.toLowerCase().includes(lower) || 
        a.ticker.toLowerCase().includes(lower)
    );
});

const groupedTransactions = computed(() => {
    if (!props.transactions.data) return [];
    
    const groups = {};
    props.transactions.data.forEach(tx => {
        const date = new Date(tx.date);
        const monthYear = date.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' }).toUpperCase();
        
        if (!groups[monthYear]) {
            groups[monthYear] = [];
        }
        groups[monthYear].push(tx);
    });
    
    return Object.keys(groups).map(key => ({
        monthYear: key,
        items: groups[key]
    }));
});

const exportHistory = (format) => {
    // Redirigir a la ruta de exportación con los filtros actuales
    const params = new URLSearchParams({
        format: format,
        portfolio_id: props.selectedPortfolioId !== 'aggregated' ? props.selectedPortfolioId : 'aggregated',
        asset_id: props.selectedAssetId || '',
        timeframe: props.filters.timeframe || '1M'
    });
    
    window.location.href = `${route('transactions.export')}?${params.toString()}`;
};


const editPortfolio = (portfolio) => {
    editingPortfolio.value = portfolio;
    showPortfolioModal.value = true;
};

const deletePortfolio = (portfolio) => {
    if (confirm(`¿Estás seguro de que deseas eliminar la cartera "${portfolio.name}"? Esta acción no se puede deshacer.`)) {
        router.delete(route('portfolios.destroy', portfolio.id), {
             preserveScroll: true,
             onSuccess: () => {
                 if (props.selectedPortfolioId == portfolio.id) {
                     switchPortfolio('aggregated');
                 }
             }
        });
    }
};

// --- Acciones de Navegación ---
const switchPortfolio = (id) => {
    router.get(route('transactions.index'), { 
        portfolio_id: id,
        timeframe: props.filters.timeframe 
    }, { preserveState: true, preserveScroll: true });
};

const switchTimeframe = (tf) => {
    router.get(route('transactions.index'), { 
        portfolio_id: props.selectedPortfolioId,
        timeframe: tf 
    }, { preserveState: true, preserveScroll: true });
};

// --- Helpers de Formato ---
const formatCurrency = (value) => {
    if (value === undefined || value === null || isNaN(value)) return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(0);
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value);
};

const formatPercent = (value) => {
    if (value === undefined || value === null || isNaN(value)) return '0,00%';
    return new Intl.NumberFormat('es-ES', { style: 'percent', minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value / 100);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-ES', { year: 'numeric', month: 'short', day: 'numeric' });
};

// --- Datos Gráficos ---

// 1. Gráfico de Rendimiento (Line)
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

// 2. Gráfico de Distribución (Doughnut)
const allocationChartData = computed(() => {
    const data = props.allocations[allocationType.value] || [];
    return {
        labels: data.map(d => d.label),
        datasets: [{
            data: data.map(d => d.value),
            backgroundColor: data.map(d => d.color || '#cbd5e1'),
            borderWidth: 0
        }]
    };
});

const allocationChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 12, usePointStyle: true, padding: 15 } },
        tooltip: {
            callbacks: {
                label: (context) => {
                    const val = context.parsed;
                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                    const pct = ((val / total) * 100).toFixed(1) + '%';
                    return `${context.label}: ${formatCurrency(val)} (${pct})`;
                }
            }
        }
    }
};

const liquidVsInvestedOptions = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '70%',
    plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, padding: 15 } },
        tooltip: {
            callbacks: {
                label: (context) => {
                    const val = context.parsed;
                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                    const pct = ((val / total) * 100).toFixed(1) + '%';
                    return `${context.label}: ${formatCurrency(val)} (${pct})`;
                }
            }
        }
    }
};

// --- Modal de Transacciones ---
const openNewTransaction = () => {
    editingTransaction.value = null;
    showTransactionModal.value = true;
};
</script>

<template>
    <Head title="Patrimonio Neto" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <h2 class="font-semibold text-xl text-slate-800 dark:text-white leading-tight">Patrimonio Neto</h2>
                
                <div class="flex items-center space-x-2 overflow-x-auto max-w-full pb-2 md:pb-0 no-scrollbar">
                     <!-- Aggregated Button -->
                     <button 
                        @click="switchPortfolio('aggregated')"
                        class="px-4 py-2 rounded-full text-sm font-medium transition-colors whitespace-nowrap"
                        :class="selectedPortfolioId === 'aggregated' ? 'bg-slate-800 text-white shadow-md dark:bg-blue-600' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 dark:hover:bg-slate-700'"
                     >
                        Todo
                     </button>

                     <!-- Portfolio List -->
                     <button 
                        v-for="p in portfolios" 
                        :key="p.id"
                        @click="switchPortfolio(p.id)"
                        class="px-4 py-2 rounded-full text-sm font-medium transition-colors whitespace-nowrap flex items-center space-x-2"
                        :class="selectedPortfolioId == p.id ? 'bg-slate-800 text-white shadow-md dark:bg-blue-600' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 dark:hover:bg-slate-700'"
                     >
                        <span>{{ p.name }}</span>
                     </button>

                     <!-- Add Portfolio Button -->
                     <button 
                        @click="openCreatePortfolioModal"
                        class="p-2 rounded-full bg-blue-600 text-white hover:bg-blue-700 shadow-md transition-colors dark:bg-blue-600 dark:hover:bg-blue-500"
                        title="Nueva Cartera"
                     >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                     </button>

                     <!-- Settings Button -->
                     <button 
                        @click="openSettings"
                        class="p-2 rounded-full bg-white text-slate-400 hover:text-slate-600 hover:bg-slate-50 border border-slate-200 transition-colors dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700 dark:hover:text-slate-200 dark:hover:bg-slate-700"
                        title="Ajustes"
                     >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                     </button>
                </div>
            </div>
        </template>

        <div class="py-8 bg-slate-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- 1. RESUMEN DE PATRIMONIO -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <!-- Valor Total -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between h-32 relative overflow-hidden dark:bg-slate-800 dark:border-slate-700">
                        <div class="absolute right-0 top-0 p-4 opacity-10">
                            <svg class="w-24 h-24 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path></svg>
                        </div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400 z-10">Valor Total</p>
                        <h3 class="text-3xl font-bold text-slate-900 dark:text-white z-10">{{ formatCurrency(summary.current_value) }}</h3>
                    </div>

                    <!-- Retorno Total -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between h-32 dark:bg-slate-800 dark:border-slate-700">
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Retorno Total</p>
                        <div class="flex items-end gap-2">
                            <h3 class="text-3xl font-bold" :class="summary.total_pl >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                                {{ summary.total_pl >= 0 ? '+' : '' }}{{ formatPercent(summary.total_pl_percent) }}
                            </h3>
                            <span class="text-sm font-medium mb-1" :class="summary.total_pl >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                                ({{ summary.total_pl >= 0 ? '+' : '' }}{{ formatCurrency(summary.total_pl) }})
                            </span>
                        </div>
                    </div>

                    <!-- Period PL (Rendimiento del Periodo) -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between h-32 dark:bg-slate-800 dark:border-slate-700">
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Rendimiento ({{ filters.timeframe }})</p>
                        <div class="flex items-end gap-2">
                            <h3 class="text-3xl font-bold" :class="chart.period_pl_value >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                                {{ chart.period_pl_value >= 0 ? '+' : '' }}{{ formatPercent(chart.period_pl_percent) }}
                            </h3>
                             <span class="text-sm font-medium mb-1" :class="chart.period_pl_value >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                                ({{ chart.period_pl_value >= 0 ? '+' : '' }}{{ formatCurrency(chart.period_pl_value) }})
                            </span>
                        </div>
                    </div>

                     <!-- Capital Invertido -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between h-32 dark:bg-slate-800 dark:border-slate-700">
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Capital Invertido</p>
                        <h3 class="text-3xl font-bold text-slate-900 dark:text-white">{{ formatCurrency(summary.total_invested) }}</h3>
                    </div>
                </div>

                <!-- 2. GRÁFICOS PRINCIPALES -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Gráfico de Rendimiento (2/3) -->
                    <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col h-[450px] dark:bg-slate-800 dark:border-slate-700">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Evolución de Cartera</h3>
                            
                            <div class="flex items-center space-x-4">
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
                                        v-for="tf in ['1D', '1W', '1M', '3M', 'YTD', '1Y', 'MAX']" 
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

                        <div class="flex-grow relative w-full">
                            <LineChart :data="performanceChartData" :options="performanceChartOptions" />
                        </div>
                    </div>

                    <!-- Gráfico de Distribución (1/3) -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col h-[450px] dark:bg-slate-800 dark:border-slate-700">
                        <div class="flex flex-col mb-4">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Distribución</h3>
                            </div>
                            
                            <!-- Scroll de Filtros (Reemplaza Grid) -->
                            <div class="flex space-x-2 overflow-x-auto pb-2 no-scrollbar">
                                <button v-for="(label, key) in allocationLabels" :key="key" 
                                    @click="allocationType = key"
                                    class="px-3 py-1 text-xs font-medium rounded-full transition-colors border whitespace-nowrap"
                                    :class="allocationType === key 
                                        ? 'bg-blue-600 text-white border-blue-600 dark:bg-blue-500' 
                                        : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 dark:bg-slate-700 dark:text-slate-300 dark:border-slate-600 dark:hover:bg-slate-600'"
                                >
                                    {{ label }}
                                </button>
                            </div>
                        </div>
                        <div class="flex-grow relative">
                            <DoughnutChart :data="allocationChartData" :options="allocationChartOptions" />
                        </div>
                    </div>
                </div>

                <!-- 3. POSICIONES ACTIVAS -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden dark:bg-slate-800 dark:border-slate-700">
                    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4 dark:border-slate-700">
                        <div>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Posiciones Activas</h3>
                            <p class="text-sm text-slate-500 mt-1 dark:text-slate-400">Desglose detallado de tus activos actuales</p>
                        </div>
                        <div class="flex items-center gap-2">
                             <TextInput
                                v-model="assetFilter"
                                placeholder="Filtrar por nombre o ticker..."
                                class="text-sm w-full sm:w-64"
                            />
                            <PrimaryButton @click="openNewTransaction">
                                + Añadir Transacción
                            </PrimaryButton>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50 dark:bg-slate-700 dark:text-slate-300">
                                <tr>
                                    <th class="px-6 py-4">Activo</th>
                                    <th class="px-6 py-4 text-right">Precio Actual</th>
                                    <th class="px-6 py-4 text-right">Valor Total</th>
                                    <th class="px-6 py-4 text-right">Retorno</th>
                                    <th class="px-6 py-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                <tr v-for="asset in filteredAssets" :key="asset.id" class="hover:bg-slate-50 transition-colors dark:hover:bg-slate-700" :class="{ 'bg-blue-50/50 dark:bg-blue-900/20': selectedAssetId == asset.id }">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white shadow-sm" :style="{ backgroundColor: asset.color }">
                                                {{ asset.ticker ? asset.ticker.substring(0,2) : asset.name.substring(0,2) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 text-base dark:text-white">{{ asset.ticker }}</div>
                                                <div class="text-sm text-slate-500 dark:text-slate-400">{{ asset.name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="font-medium text-slate-900 dark:text-white">{{ formatCurrency(asset.current_price) }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ asset.quantity }} un.</div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="font-bold text-slate-900 text-base dark:text-white">{{ formatCurrency(asset.current_value) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex flex-col items-end">
                                            <span :class="asset.profit_loss >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'" class="font-bold">
                                                {{ asset.profit_loss >= 0 ? '+' : '' }}{{ formatPercent(asset.profit_loss_percent) }}
                                            </span>
                                            <span :class="asset.profit_loss >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'" class="text-xs">
                                                {{ asset.profit_loss >= 0 ? '+' : '' }}{{ formatCurrency(asset.profit_loss) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <button 
                                            @click="filterByAsset(asset)" 
                                            class="p-2 rounded-lg hover:bg-slate-200 transition-colors dark:hover:bg-slate-600"
                                            :class="selectedAssetId == asset.id ? 'text-blue-600 bg-blue-100 dark:bg-blue-900 dark:text-blue-300' : 'text-slate-400 dark:text-slate-500'"
                                            title="Ver Historial de Operaciones"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </button>
                                        <!-- Edit Asset button removed as per request -->
                                    </td>
                                </tr>
                                <tr v-if="filteredAssets.length === 0">

                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                        <div class="flex flex-col items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-300 dark:text-slate-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-lg font-medium text-slate-900 dark:text-white">No hay activos en esta cartera</p>
                                            <p class="text-sm text-slate-500 mt-1 dark:text-slate-400">Añade tu primera transacción para ver tus posiciones</p>
                                            <button @click="openNewTransaction" class="mt-4 text-blue-600 font-medium hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                                + Añadir Transacción
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. HISTORIAL DE TRANSACCIONES -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden dark:bg-slate-800 dark:border-slate-700">
                    <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Historial de Operaciones</h3>
                        <div class="flex gap-2">
                            <button @click="exportHistory('pdf')" class="px-3 py-1 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-900/50 dark:hover:bg-red-900/40">
                                PDF
                            </button>
                            <button @click="exportHistory('excel')" class="px-3 py-1 text-xs font-medium text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors border border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-900/50 dark:hover:bg-emerald-900/40">
                                Excel
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50 dark:bg-slate-700 dark:text-slate-300">
                                <tr>
                                    <th class="px-6 py-3">Fecha</th>
                                    <th class="px-6 py-3">Tipo</th>
                                    <th class="px-6 py-3">Activo</th>
                                    <th class="px-6 py-3 text-right">Cantidad</th>
                                    <th class="px-6 py-3 text-right">Precio</th>
                                    <th class="px-6 py-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                <template v-for="group in groupedTransactions" :key="group.monthYear">
                                    <tr class="bg-slate-50/50 dark:bg-slate-700/30">
                                        <td colspan="6" class="px-6 py-2 text-xs font-bold text-slate-400 dark:text-slate-500 text-center tracking-widest uppercase">
                                            ─ ─ ─ ─ ─ {{ group.monthYear }} ─ ─ ─ ─ ─
                                        </td>
                                    </tr>
                                    <tr v-for="tx in group.items" :key="tx.id" class="hover:bg-slate-50 transition-colors dark:hover:bg-slate-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400">{{ formatDate(tx.date) }}</td>
                                        <td class="px-6 py-4">
                                            <span :class="{
                                                'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300': ['dividend', 'reward', 'gift'].includes(tx.type),
                                                'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300': tx.type === 'buy',
                                                'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300': tx.type === 'sell',
                                                'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300': !['buy', 'sell', 'dividend', 'reward', 'gift'].includes(tx.type)
                                            }" class="px-2 py-1 rounded-full text-xs font-semibold capitalize">
                                                {{ tx.type === 'buy' ? 'Compra' : (tx.type === 'sell' ? 'Venta' : tx.type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">{{ tx.asset ? tx.asset.ticker : '-' }}</td>
                                        <td class="px-6 py-4 text-right dark:text-slate-300">{{ tx.quantity }}</td>
                                        <td class="px-6 py-4 text-right dark:text-slate-300">{{ formatCurrency(tx.price) }}</td>
                                        <td class="px-6 py-4 text-right font-bold text-slate-800 dark:text-white">{{ formatCurrency(tx.total) }}</td>
                                    </tr>
                                </template>
                                <tr v-if="groupedTransactions.length === 0">
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-500 italic dark:text-slate-400">
                                        No hay transacciones registradas.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Paginación Simple -->
                    <div class="p-4 flex justify-center space-x-2" v-if="transactions.links && transactions.links.length > 3">
                        <template v-for="(link, k) in transactions.links" :key="k">
                            <component 
                                :is="link.url ? 'a' : 'span'" 
                                :href="link.url" 
                                v-html="link.label"
                                class="px-3 py-1 text-xs rounded-md border transition-colors"
                                :class="link.active ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 dark:hover:bg-slate-700'"
                            />
                        </template>
                    </div>
                </div>

            </div>
        </div>

        <!-- Modal para Crear/Editar Transacción -->
        <TransactionModal
            :show="showTransactionModal"
            :transaction="editingTransaction"
            :portfolios="portfolios"
            :assets="assets"
            :default-portfolio-id="selectedPortfolioId !== 'aggregated' ? selectedPortfolioId : null"
            :allowed-types="['buy', 'sell', 'dividend', 'reward']"
            @close="showTransactionModal = false"
        />

        <PortfolioModal
            :show="showPortfolioModal"
            :portfolio="editingPortfolio"
            @close="showPortfolioModal = false"
        />

        <SettingsModal
            :show="showSettingsModal"
            :portfolios="portfolios"
            @close="showSettingsModal = false"
        />

        <AssetModal
            :show="showAssetModal"
            :asset="editingAsset"
            @close="showAssetModal = false"
        />
    </AuthenticatedLayout>
</template>
