<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch, computed, onMounted } from 'vue';
import TransactionModal from '@/Components/TransactionModal.vue';
import TransactionHistory from '@/Components/Transactions/TransactionHistory.vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import BarChart from '@/Components/Charts/BarChart.vue';
import DoughnutChart from '@/Components/Charts/DoughnutChart.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InfoTooltip from '@/Components/InfoTooltip.vue';
import { formatCurrency } from '@/Utils/formatting';
import { usePrivacy } from '@/Composables/usePrivacy';

const { isPrivacyMode } = usePrivacy();

const props = defineProps({
    filters: Object,
    summary: Object,
    charts: Object,
    transactions: Object,
    portfolios: Array,
    categories: Array,
    availableYears: Array,
    selectedYear: Number,
    yearStats: Object,
    topExpenses: Array,
    topIncome: Array,
});

// Estado para filtros
const dateFilters = ref({
    start_date: props.filters.start_date,
    end_date: props.filters.end_date,
});

// Paginación "Load More" para listas Top
const displayedTopExpenses = ref([]);
const displayedTopIncome = ref([]);
const topExpensesPage = ref(1);
const topIncomePage = ref(1);
const pageSize = 20;

const loadMoreTopExpenses = () => {
    if (!props.topExpenses) return;
    const start = (topExpensesPage.value - 1) * pageSize;
    const end = start + pageSize;
    if (start < props.topExpenses.length) {
        displayedTopExpenses.value.push(...props.topExpenses.slice(start, end));
        topExpensesPage.value++;
    }
};

const loadMoreTopIncome = () => {
    if (!props.topIncome) return;
    const start = (topIncomePage.value - 1) * pageSize;
    const end = start + pageSize;
    if (start < props.topIncome.length) {
        displayedTopIncome.value.push(...props.topIncome.slice(start, end));
        topIncomePage.value++;
    }
};

// Intersection Observers for infinite scroll in lists
const topExpensesObserverTarget = ref(null);
const topIncomeObserverTarget = ref(null);

onMounted(() => {
    // Configurar observers
    const options = { root: null, rootMargin: '50px', threshold: 0.1 };
    
    const expensesObserver = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) {
            loadMoreTopExpenses();
        }
    }, options);
    
    if (topExpensesObserverTarget.value) expensesObserver.observe(topExpensesObserverTarget.value);
    
    const incomeObserver = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) {
            loadMoreTopIncome();
        }
    }, options);
    
    if (topIncomeObserverTarget.value) incomeObserver.observe(topIncomeObserverTarget.value);
});

// Watch for prop changes to reset lists
watch(() => props.topExpenses, () => {
    displayedTopExpenses.value = [];
    topExpensesPage.value = 1;
    loadMoreTopExpenses();
}, { immediate: true });

watch(() => props.topIncome, () => {
    displayedTopIncome.value = [];
    topIncomePage.value = 1;
    loadMoreTopIncome();
}, { immediate: true });



// Estado para Modal
const showModal = ref(false);
const editingTransaction = ref(null);
const selectedTransactions = ref([]);

// Aplicar filtros automáticamente al cambiar fechas
const applyFilters = () => {
    router.get(route('expenses.index'), dateFilters.value, {
        preserveState: true,
        replace: true,
        only: ['summary', 'charts', 'transactions', 'filters', 'yearStats', 'topExpenses', 'selectedYear']
    });
};

// Configuración Gráfico Tendencia (Línea de Saldo Acumulado)
const trendChartData = computed(() => ({
    labels: props.charts.trend.labels,
    datasets: [
        {
            label: 'Saldo Acumulado',
            data: props.charts.trend.balance,
            borderColor: '#3b82f6', // Blue 500
            backgroundColor: (context) => {
                const ctx = context.chart.ctx;
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
                gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');
                return gradient;
            },
            pointBackgroundColor: '#ffffff',
            pointBorderColor: '#3b82f6',
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
        legend: { display: false }, // Ocultar leyenda ya que es una sola línea explicada en el título
        tooltip: {
            callbacks: {
                label: (context) => `Saldo: ${formatCurrency(context.parsed.y)}`
            }
        }
    },
    scales: {
        y: {
            beginAtZero: false, // Permitir valores negativos o rangos dinámicos
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

const deleteTransaction = (transaction) => {
    if (confirm('¿Estás seguro de que quieres eliminar esta transacción?')) {
        router.delete(route('transactions.destroy', transaction.id), {
            preserveScroll: true,
            onSuccess: () => {
                applyFilters();
            }
        });
    }
};

const toggleSelection = (id) => {
    if (selectedTransactions.value.includes(id)) {
        selectedTransactions.value = selectedTransactions.value.filter(txId => txId !== id);
    } else {
        selectedTransactions.value.push(id);
    }
};

const toggleAll = () => {
    if (selectedTransactions.value.length === props.transactions.data.length) {
        selectedTransactions.value = [];
    } else {
        selectedTransactions.value = props.transactions.data.map(tx => tx.id);
    }
};

const deleteSelected = () => {
    if (confirm(`¿Estás seguro de que quieres eliminar ${selectedTransactions.value.length} transacciones?`)) {
        router.delete(route('transactions.bulk-destroy'), {
            data: { ids: selectedTransactions.value },
            preserveScroll: true,
            onSuccess: () => {
                selectedTransactions.value = [];
                applyFilters();
            }
        });
    }
};

// CSV Import Logic
const fileInput = ref(null);

const triggerFileInput = () => {
    if (fileInput.value) {
        fileInput.value.click();
    }
};

const handleExport = (format) => {
    const params = {
        format: format,
        ...dateFilters.value
    };
    
    // Usar window.location para descargar el archivo
    const url = route('transactions.export', params);
    window.location.href = url;
};

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    if (confirm(`¿Deseas importar el archivo "${file.name}"?`)) {
        const formData = new FormData();
        formData.append('file', file);

        router.post(route('transactions.import'), formData, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                applyFilters();
                alert('Transacciones importadas correctamente. Las categorías se han actualizado según el archivo.');
            },
            onError: (errors) => {
                console.error(errors);
                alert('Hubo un error al importar el archivo. Por favor verifica el formato.');
            },
            onFinish: () => {
                // Reset input
                if (fileInput.value) fileInput.value.value = null;
            }
        });
    } else {
        // Reset input if cancelled
        event.target.value = null;
    }
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

                <div class="flex flex-wrap items-center gap-2">
                    <!-- Filtros de Fecha (Opcional) -->
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
            </div>
        </template>

        <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- 1. TARJETAS DE RESUMEN (KPIs) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Ingresos -->
                <div class="bg-white dark:bg-slate-800 p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 border-l-4 border-l-emerald-500 relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide">Ingresos Totales</p>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ isPrivacyMode ? '****' : formatCurrency(summary.total_income) }}</h3>
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
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ isPrivacyMode ? '****' : formatCurrency(summary.total_expense) }}</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">Promedio diario: {{ isPrivacyMode ? '****' : formatCurrency(summary.avg_daily_expense) }}</p>
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
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ isPrivacyMode ? '****' : formatCurrency(summary.net_savings) }}</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">
                            <span v-if="isPrivacyMode">****</span>
                            <span v-else>{{ summary.total_income > 0 ? ((summary.net_savings / summary.total_income) * 100).toFixed(1) + '% de tasa de ahorro' : '-' }}</span>
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

            <!-- 3. TOP GASTOS, INGRESOS & HISTORIAL -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Historial (3/4 ancho, Izquierda) -->
                <div class="lg:col-span-3 space-y-4 order-2 lg:order-1">
                    <TransactionHistory 
                        :transactions="transactions" 
                        filter-mode="expenses" 
                        @edit="editTransaction"
                        @export="handleExport"
                        @import="triggerFileInput"
                    />
                </div>

                <!-- Input para Importar CSV -->
                <input 
                    type="file" 
                    ref="fileInput" 
                    @change="handleFileUpload" 
                    class="hidden" 
                    accept=".csv" 
                />

                <!-- Top Gastos e Ingresos (1/4 ancho, Derecha) -->
                <div class="lg:col-span-1 space-y-6 order-1 lg:order-2">
                    <!-- Top Gastos -->
                    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 max-h-96 overflow-y-auto custom-scrollbar">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 sticky top-0 bg-white dark:bg-slate-800 z-10 py-2">Top Gastos</h3>
                        <div v-if="displayedTopExpenses.length > 0" class="space-y-4">
                            <div v-for="(category, index) in displayedTopExpenses" :key="index" class="relative">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300 truncate w-2/3" :title="category.category_name">{{ category.category_name }}</span>
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">{{ isPrivacyMode ? '****' : formatCurrency(category.total) }}</span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                                    <div class="bg-rose-500 h-2 rounded-full" :style="{ width: Math.min((category.total / (summary.total_expense || 1) * 100), 100) + '%' }"></div>
                                </div>
                            </div>
                            <div ref="topExpensesObserverTarget" class="h-1 w-full"></div>
                        </div>
                        <div v-else class="text-slate-400 text-sm italic text-center py-4">
                            No hay gastos registrados en este periodo.
                        </div>
                    </div>

                    <!-- Top Ingresos -->
                    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 max-h-96 overflow-y-auto custom-scrollbar">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 sticky top-0 bg-white dark:bg-slate-800 z-10 py-2">Top Ingresos</h3>
                        <div v-if="displayedTopIncome.length > 0" class="space-y-4">
                            <div v-for="(category, index) in displayedTopIncome" :key="index" class="relative">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300 truncate w-2/3" :title="category.category_name">{{ category.category_name }}</span>
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">{{ isPrivacyMode ? '****' : formatCurrency(category.total) }}</span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                                    <div class="bg-emerald-500 h-2 rounded-full" :style="{ width: Math.min((category.total / (summary.total_income || 1) * 100), 100) + '%' }"></div>
                                </div>
                            </div>
                            <div ref="topIncomeObserverTarget" class="h-1 w-full"></div>
                        </div>
                        <div v-else class="text-slate-400 text-sm italic text-center py-4">
                            No hay ingresos registrados en este periodo.
                        </div>
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