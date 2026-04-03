<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import TransactionModal from '@/Components/TransactionModal.vue';
import { formatCurrency, formatPercent, formatDate } from '@/Utils/formatting';
import { usePrivacy } from '@/Composables/usePrivacy';
import axios from 'axios';

const props = defineProps({
    marketAsset: Object,
    chartData: Array,
    userPosition: Object,
    latestTransactions: Array,
    posts: Object,
    filters: Object
});

const { isPrivacyMode } = usePrivacy();
const activeTab = ref('overview');
const chartRange = ref('1Y');
const localChartData = ref(props.chartData);
const isLoadingChart = ref(false);
const showTransactionModal = ref(false);

const tabs = [
    { id: 'overview', label: 'Visión General', icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' },
    { id: 'portfolio', label: 'Cartera de Inversiones', icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' },
    { id: 'transactions', label: 'Últimas Operaciones', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' },
    { id: 'debate', label: 'Debate Community', icon: 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z' }
];

// Price formatting
const currentPrice = computed(() => props.marketAsset.current_price || 0);
const priceChange = computed(() => 1.25); // Simulated or from API

// Chart logic
const formattedChartData = computed(() => {
    return {
        labels: localChartData.value.map(d => d.date),
        datasets: [{
            label: 'Precio',
            data: localChartData.value.map(d => d.close),
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

const updateRange = async (range) => {
    chartRange.value = range;
    isLoadingChart.value = true;
    
    let days = 365;
    if (range === '1W') days = 7;
    if (range === '1M') days = 30;
    if (range === '1Y') days = 365;
    if (range === 'YTD') {
        const startOfYear = new Date(new Date().getFullYear(), 0, 1);
        days = Math.floor((new Date() - startOfYear) / (1000 * 60 * 60 * 24));
    }
    if (range === 'MAX') days = 1825;

    try {
        const response = await axios.get(route('assets.chart-data', { 
            ticker: props.marketAsset.ticker,
            days: days
        }));
        localChartData.value = response.data;
    } catch (e) {
        console.error("Error loading chart", e);
    } finally {
        isLoadingChart.value = false;
    }
};

// Social logic
const postForm = useForm({
    market_asset_id: props.marketAsset.id,
    content: ''
});

const submitPost = () => {
    postForm.post(route('social.post'), {
        onSuccess: () => postForm.reset('content')
    });
};

const toggleLike = (id, type) => {
    router.post(route('social.like'), {
        likeable_id: id,
        likeable_type: type
    }, { preserveScroll: true });
};

const toggleBookmark = (postId) => {
    router.post(route('social.bookmark', postId), {}, { preserveScroll: true });
};

const repost = (postId) => {
    router.post(route('social.repost', postId), {}, { preserveScroll: true });
};

const report = (id, type) => {
    const reason = prompt('Indica el motivo del reporte:');
    if (reason) {
        router.post(route('social.report'), {
            reportable_id: id,
            reportable_type: type,
            reason: reason
        }, { preserveScroll: true });
    }
};

const openComments = (postId) => {
    // Lógica para abrir hilo de comentarios o scroll
    activeTab.value = 'debate';
};

</script>

<template>
    <Head :title="`${marketAsset.name} (${marketAsset.ticker})`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-2 flex items-center justify-center overflow-hidden shrink-0">
                        <img v-if="marketAsset.logo_url" :src="marketAsset.logo_url" class="w-full h-full object-contain" :alt="marketAsset.ticker">
                        <span v-else class="text-2xl font-black text-indigo-600">{{ marketAsset.ticker.substring(0, 2) }}</span>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-3xl font-black text-slate-800 dark:text-white leading-tight">{{ marketAsset.name }}</h1>
                            <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg text-sm font-black border border-indigo-100 dark:border-indigo-800/30">{{ marketAsset.ticker }}</span>
                        </div>
                        <div class="flex items-center gap-4 mt-2 text-sm text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">
                            <span>{{ marketAsset.type_label }}</span>
                            <span class="w-1.5 h-1.5 bg-slate-300 dark:bg-slate-600 rounded-full"></span>
                            <span>ISIN: {{ marketAsset.isin || 'Sin definir' }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col items-end">
                    <div class="text-4xl font-black text-slate-800 dark:text-white tracking-tight">
                        {{ formatCurrency(currentPrice) }}
                    </div>
                    <div class="flex items-center gap-2 mt-1 px-3 py-1 rounded-full text-xs font-black" :class="priceChange >= 0 ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400' : 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400'">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path v-if="priceChange >= 0" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 15l7-7 7 7" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M19 9l-7 7-7-7" />
                        </svg>
                        <span>{{ formatPercent(priceChange) }} (Hoy)</span>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-8 bg-slate-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Chart & Ranges -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                    <div class="p-6 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/20 border-b border-slate-100 dark:border-slate-700">
                        <div class="flex gap-1 bg-slate-200/50 dark:bg-slate-900/50 p-1 rounded-xl">
                            <button 
                                v-for="r in ['1D', '1W', '1M', 'YTD', '1Y', 'MAX']" :key="r"
                                @click="updateRange(r)"
                                class="px-4 py-1.5 rounded-lg text-xs font-black transition-all"
                                :class="chartRange === r ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                            >
                                {{ r }}
                            </button>
                        </div>
                        <div class="flex items-center gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Vivo: fintechPro Data
                        </div>
                    </div>
                    <div class="h-[400px] p-6 relative">
                        <div v-if="isLoadingChart" class="absolute inset-0 z-10 bg-white/60 dark:bg-slate-800/60 flex items-center justify-center backdrop-blur-[2px]">
                            <div class="animate-spin rounded-full h-8 w-8 border-4 border-indigo-500 border-t-transparent"></div>
                        </div>
                        <LineChart :data="formattedChartData" :options="chartOptions" />
                    </div>
                </div>

                <!-- Tabs Navigation -->
                <div class="flex border-b border-slate-200 dark:border-slate-700">
                    <button 
                        v-for="tab in tabs" :key="tab.id"
                        @click="activeTab = tab.id"
                        class="px-6 py-4 text-sm font-bold transition-all border-b-2 flex items-center gap-2"
                        :class="activeTab === tab.id ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400 translate-y-[1px]' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="tab.icon" />
                        </svg>
                        {{ tab.label }}
                    </button>
                </div>

                <!-- Tabs Content -->
                <div class="mt-6">
                    <!-- Visión General Tab -->
                    <div v-if="activeTab === 'overview'" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="md:col-span-2 space-y-6">
                                <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
                                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        Descripción del Activo
                                    </h3>
                                    <p class="text-sm text-slate-600 dark:text-slate-300 font-medium leading-relaxed">
                                        {{ marketAsset.description || 'No hay una descripción detallada disponible para este activo actualmente.' }}
                                    </p>
                                    
                                    <div class="my-8 border-t border-slate-50 dark:border-slate-700/50"></div>

                                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-8 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                        Datos Clave del Activo
                                    </h3>
                                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-y-10 gap-x-8">
                                        <div v-for="item in [
                                            { label: 'Sector', value: marketAsset.sector || 'N/A' },
                                            { label: 'Industria', value: marketAsset.industry || 'N/A' },
                                            { label: 'Capitalización', value: marketAsset.market_cap ? formatCurrency(marketAsset.market_cap) : 'N/A' },
                                            { label: 'ISIN', value: marketAsset.isin || 'N/A' },
                                            { label: 'Tipo', value: marketAsset.type_label || 'Otros' },
                                            { label: 'TER (Coste)', value: marketAsset.ter ? formatPercent(marketAsset.ter) : 'N/A' }
                                        ]" :key="item.label" class="group">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 group-hover:text-indigo-500 transition-colors">{{ item.label }}</p>
                                            <p class="text-lg font-black text-slate-800 dark:text-slate-100 truncate" :title="item.value">{{ item.value }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div class="bg-indigo-600 rounded-3xl p-8 text-white flex flex-col justify-between shadow-xl shadow-indigo-500/20 relative overflow-hidden group">
                                    <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                                    <div class="relative z-10">
                                        <h3 class="font-black text-[10px] uppercase tracking-[0.2em] opacity-80 mb-4 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                            Análisis Fundamental
                                        </h3>
                                        <p class="text-2xl font-black">Rating: Estable</p>
                                        <p class="text-sm opacity-80 mt-4 leading-relaxed font-medium">
                                            Basado en los últimos datos de mercado y sentimiento del debate. La volatilidad se mantiene en niveles históricos bajos.
                                        </p>
                                    </div>
                                    <button class="relative z-10 mt-10 w-full bg-white text-indigo-600 font-black py-4 rounded-2xl shadow-lg hover:bg-slate-50 transition-all text-sm active:scale-[0.98]">
                                        Ver Análisis IA Completo
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Gráficos Adicionales -->
                        <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
                            <div class="flex justify-between items-center mb-10">
                                <div>
                                    <h3 class="text-lg font-black text-slate-800 dark:text-white">Mapa de Calor y Distribución</h3>
                                    <p class="text-sm text-slate-500 font-medium">Distribución geográfica y por sectores</p>
                                </div>
                                <div class="text-[10px] font-black text-slate-300 uppercase tracking-widest flex items-center gap-2">
                                    GRÁFICO POR <span class="text-indigo-500">pipkonx</span>
                                </div>
                            </div>
                            <div v-if="marketAsset.sectorWeightings || marketAsset.countryWeightings" class="grid grid-cols-1 md:grid-cols-2 gap-12">
                                <!-- Sectores -->
                                <div v-if="marketAsset.sectorWeightings" class="space-y-6">
                                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-indigo-500"></div>
                                        Ponderación por Sector
                                    </h4>
                                    <div class="space-y-4">
                                        <div v-for="sw in marketAsset.sectorWeightings" :key="sw.sector" class="space-y-2">
                                            <div class="flex justify-between items-center text-xs font-bold text-slate-600 dark:text-slate-300">
                                                <span>{{ sw.sector }}</span>
                                                <span class="text-indigo-600 dark:text-indigo-400 font-black">{{ sw.weightPercentage.toFixed(2) }}%</span>
                                            </div>
                                            <div class="h-2 bg-slate-100 dark:bg-slate-900/50 rounded-full overflow-hidden">
                                                <div class="h-full bg-indigo-500 rounded-full transition-all duration-1000" :style="{ width: sw.weightPercentage + '%' }"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Países -->
                                <div v-if="marketAsset.countryWeightings" class="space-y-6">
                                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                        Distribución Geográfica
                                    </h4>
                                    <div class="space-y-4">
                                        <div v-for="cw in marketAsset.countryWeightings" :key="cw.country" class="space-y-2">
                                            <div class="flex justify-between items-center text-xs font-bold text-slate-600 dark:text-slate-300">
                                                <span>{{ cw.country }}</span>
                                                <span class="text-emerald-600 dark:text-emerald-400 font-black">{{ cw.weightPercentage.toFixed(2) }}%</span>
                                            </div>
                                            <div class="h-2 bg-slate-100 dark:bg-slate-900/50 rounded-full overflow-hidden">
                                                <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000" :style="{ width: cw.weightPercentage + '%' }"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="h-64 bg-slate-50 dark:bg-slate-900/50 rounded-2xl flex items-center justify-center border-2 border-dashed border-slate-100 dark:border-slate-800/50">
                                <div class="text-center">
                                    <svg class="w-12 h-12 text-slate-300 dark:text-slate-700 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                                    <p class="text-slate-400 font-black text-xs uppercase tracking-widest">Información de distribución no disponible para este tipo de activo</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cartera de Inversiones Tab -->
                    <div v-if="activeTab === 'portfolio'" class="space-y-8">
                        <div v-if="userPosition" class="space-y-8">
                            <!-- Retorno Detallado (8 Cards) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div v-for="(item, key) in [
                                    { label: 'Total', value: formatCurrency(userPosition.current_value), sub: userPosition.quantity + ' uds', color: 'text-slate-800 dark:text-white' },
                                    { label: 'Invertido', value: formatCurrency(userPosition.total_invested), sub: 'Capital inicial', color: 'text-slate-600 dark:text-slate-300' },
                                    { label: 'Ganancia de precio', value: formatCurrency(userPosition.profit_loss), sub: formatPercent(userPosition.profit_loss_percentage), color: userPosition.profit_loss >= 0 ? 'text-emerald-500' : 'text-rose-500' },
                                    { label: 'Ganancia realizada', value: formatCurrency(userPosition.realized_gain || 0), sub: 'Ventas cerradas', color: 'text-slate-600 dark:text-slate-300' },
                                    { label: 'Retorno total', value: formatCurrency(userPosition.total_return || userPosition.profit_loss), sub: 'Neto (inc. costes)', color: 'text-indigo-600 dark:text-indigo-400' },
                                    { label: 'Comprar en', value: formatCurrency(userPosition.avg_buy_price), sub: 'Precio medio', color: 'text-slate-800 dark:text-white' },
                                    { label: 'Impuestos', value: formatCurrency(userPosition.total_tax || 0), sub: 'Deducidos', color: 'text-rose-400' },
                                    { label: 'Costos', value: formatCurrency(userPosition.total_fees || 0), sub: 'Comisiones', color: 'text-slate-400' }
                                ]" :key="key" class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm group hover:border-indigo-200 dark:hover:border-indigo-900/50 transition-all">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 group-hover:text-indigo-500 transition-colors">{{ item.label }}</p>
                                    <p class="text-xl font-black truncate" :class="item.color">{{ isPrivacyMode ? '****' : item.value }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-tighter">{{ isPrivacyMode ? '****' : item.sub }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="bg-slate-100 dark:bg-slate-800/50 p-16 rounded-3xl text-center border-2 border-dashed border-slate-200 dark:border-slate-700">
                            <div class="w-20 h-20 bg-white dark:bg-slate-800 rounded-3xl mx-auto flex items-center justify-center text-slate-300 mb-6 shadow-sm border border-slate-100 dark:border-slate-700">
                                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <h4 class="text-2xl font-black text-slate-800 dark:text-white">No tienes posiciones en el activo</h4>
                            <p class="text-slate-500 text-sm mt-2 max-w-sm mx-auto font-medium leading-relaxed">Comienza a construir tu patrimonio añadiendo una operación de compra para este activo.</p>
                            <button @click="showTransactionModal = true" class="mt-8 bg-indigo-600 text-white font-black py-4 px-10 rounded-2xl shadow-xl shadow-indigo-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                                Registrar Operación
                            </button>
                        </div>
                    </div>

                    <!-- Últimas Operaciones Tab -->
                    <div v-if="activeTab === 'transactions'" class="space-y-6">
                        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
                            <div class="p-6 border-b border-slate-50 dark:border-slate-700/50 flex justify-between items-center">
                                <h3 class="font-black text-slate-800 dark:text-white uppercase tracking-widest text-xs">Historial de Transacciones</h3>
                                <div class="px-3 py-1 bg-slate-50 dark:bg-slate-900 rounded-lg text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    {{ latestTransactions.length }} Movimientos
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="bg-slate-50/50 dark:bg-slate-900/50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        <tr>
                                            <th class="px-6 py-4">Fecha</th>
                                            <th class="px-6 py-4">Tipo</th>
                                            <th class="px-6 py-4 text-right">Cantidad</th>
                                            <th class="px-6 py-4 text-right">Precio</th>
                                            <th class="px-6 py-4 text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                                        <tr v-for="tx in latestTransactions" :key="tx.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <p class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ formatDate(tx.date) }}</p>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest" :class="tx.type === 'buy' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20' : 'bg-rose-50 text-rose-600 dark:bg-rose-900/20'">
                                                    {{ tx.type === 'buy' ? 'Compra' : 'Venta' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right font-black text-slate-600 dark:text-slate-300">{{ tx.quantity }}</td>
                                            <td class="px-6 py-4 text-right font-black text-slate-600 dark:text-slate-300">{{ formatCurrency(tx.price_per_unit) }}</td>
                                            <td class="px-6 py-4 text-right font-black text-slate-800 dark:text-white">{{ formatCurrency(tx.amount) }}</td>
                                        </tr>
                                        <tr v-if="latestTransactions.length === 0">
                                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 font-bold">No se han registrado operaciones todavía</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Debate Tab -->
                    <div v-if="activeTab === 'debate'" class="max-w-3xl mx-auto space-y-8">
                        <!-- Post Box -->
                        <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700">
                            <h3 class="font-black text-slate-800 dark:text-white mb-6 uppercase tracking-widest text-xs">Debate sobre {{ marketAsset.name }}</h3>
                            <div class="flex gap-5">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center font-black text-indigo-600 dark:text-indigo-400 shrink-0 shadow-inner">
                                    {{ $page.props.auth.user.name.charAt(0) }}
                                </div>
                                <div class="flex-1">
                                    <textarea 
                                        v-model="postForm.content"
                                        placeholder="¿Qué opinas sobre este activo?"
                                        class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500 min-h-[120px] p-5 dark:text-white font-medium resize-none placeholder:text-slate-400"
                                    ></textarea>
                                    <div class="flex justify-between items-center mt-6">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Puestos {{ posts.total }}</p>
                                        </div>
                                        <button 
                                            @click="submitPost"
                                            :disabled="postForm.processing || !postForm.content"
                                            class="bg-indigo-600 text-white font-black px-10 py-3 rounded-2xl text-sm disabled:opacity-50 shadow-lg shadow-indigo-500/20 active:scale-95 transition-all"
                                        >
                                            Publicar en {{ marketAsset.ticker }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Feed -->
                        <div class="space-y-6">
                            <div v-for="post in posts.data" :key="post.id" class="bg-white dark:bg-slate-800 p-8 rounded-3xl shadow-sm border border-slate-50 dark:border-slate-700/50 group transition-all hover:border-indigo-100 dark:hover:border-indigo-900/30">
                                <div class="flex gap-5">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-900/50 flex items-center justify-center font-black text-slate-400 shrink-0 border border-slate-100 dark:border-slate-700">
                                        {{ post.user.name.charAt(0) }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex items-center gap-3">
                                                <span class="font-black text-slate-800 dark:text-white">{{ post.user.name }}</span>
                                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50 dark:bg-slate-900 px-2 py-0.5 rounded-md">• {{ formatDate(post.created_at) }}</span>
                                            </div>
                                            <button class="text-slate-300 hover:text-slate-500 transition-colors">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                                            </button>
                                        </div>
                                        <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed font-medium mb-6">{{ post.content }}</p>
                                        
                                        <!-- Actions -->
                                        <div class="flex items-center justify-between pt-4 border-t border-slate-50 dark:border-slate-700/50">
                                            <div class="flex items-center gap-1 md:gap-4">
                                                <button @click="toggleLike(post.id, 'post')" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/20 group/btn transition-all">
                                                    <svg class="w-4 h-4 text-slate-400 group-hover/btn:text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                    </svg>
                                                    <span class="text-xs font-black text-slate-400 group-hover/btn:text-emerald-500">{{ post.likes_count || 0 }}</span>
                                                </button>

                                                <button @click="openComments(post.id)" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-900/50 group/btn transition-all">
                                                    <svg class="w-4 h-4 text-slate-400 group-hover/btn:text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                    </svg>
                                                    <span class="text-xs font-black text-slate-400 group-hover/btn:text-indigo-500">{{ post.comments_count || 0 }}</span>
                                                </button>

                                                <button @click="repost(post.id)" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 group/btn transition-all">
                                                    <svg class="w-4 h-4 text-slate-400 group-hover/btn:text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                                    </svg>
                                                    <span class="text-xs font-black text-slate-400 group-hover/btn:text-blue-500">{{ post.reposts_count || 0 }}</span>
                                                </button>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <button @click="toggleBookmark(post.id)" class="p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-900 transition-all text-slate-300 hover:text-indigo-500" title="Marcar">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                                                </button>
                                                <button @click="report(post.id, 'post')" class="p-2 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-all text-slate-300 hover:text-rose-500" title="Informar">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div v-if="posts.data.length === 0" class="text-center py-24 bg-white/50 dark:bg-slate-800/30 rounded-[3rem] border-4 border-dashed border-slate-100 dark:border-slate-700/50">
                                <p class="text-slate-400 font-black uppercase tracking-[0.2em] text-xs">Sé el primero en opinar sobre {{ marketAsset.ticker }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <TransactionModal 
            v-if="showTransactionModal" 
            :show="showTransactionModal" 
            @close="showTransactionModal = false"
            :initial-asset="marketAsset"
        />
    </AuthenticatedLayout>
</template>

<style scoped>
.group:hover .group-hover\:block { display: block; }
button:disabled { cursor: not-allowed; }
</style>
