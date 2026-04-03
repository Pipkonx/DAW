<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TransactionModal from '@/Components/TransactionModal.vue';
import { usePrivacy } from '@/Composables/usePrivacy';
import AssetHeader from './Partials/AssetHeader.vue';
import AssetPriceChart from './Partials/AssetPriceChart.vue';
import AssetMetricsGrid from './Partials/AssetMetricsGrid.vue';
import AssetTransactionTable from './Partials/AssetTransactionTable.vue';
import AssetDebateFeed from './Partials/AssetDebateFeed.vue';
import axios from 'axios';

const props = defineProps({
    marketAsset: Object,
    chartData: Array,
    userPosition: Object,
    latestTransactions: Array,
    posts: Object
});

const { isPrivacyMode } = usePrivacy();
const activeTab = ref('overview');
const chartRange = ref('1Y');
const localChartData = ref(props.chartData);
const isLoadingChart = ref(false);
const showTransactionModal = ref(false);

const tabs = [
    { id: 'overview', label: 'Visión General', icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' },
    { id: 'portfolio', label: 'Posiciones', icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8m0 0V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' },
    { id: 'debate', label: 'Comunidad', icon: 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z' }
];

/**
 * Actualiza el rango del gráfico realizando una petición asíncrona al backend.
 */
const updateRange = async (range) => {
    chartRange.value = range;
    isLoadingChart.value = true;
    
    let days = 365;
    if (range === '1W') days = 7;
    if (range === '1M') days = 30;
    if (range === 'YTD') days = Math.floor((new Date() - new Date(new Date().getFullYear(), 0, 1)) / 86400000);
    if (range === 'MAX') days = 1825;

    try {
        const response = await axios.get(route('assets.chart-data', { ticker: props.marketAsset.ticker, days }));
        localChartData.value = response.data;
    } catch (e) {
        console.error("Error cargando histórico", e);
    } finally {
        isLoadingChart.value = false;
    }
};
</script>

<template>
    <Head :title="`${marketAsset.name} (${marketAsset.ticker})`" />

    <AuthenticatedLayout>
        <template #header>
            <!-- Cabecera Modular -->
            <AssetHeader 
                :market-asset="marketAsset" 
                :current-price="marketAsset.current_price || 0" 
            />
        </template>

        <div class="py-8 bg-slate-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Gráfico de Precios Principal -->
                <AssetPriceChart 
                    :local-chart-data="localChartData" 
                    :chart-range="chartRange" 
                    :is-loading-chart="isLoadingChart"
                    @update-range="updateRange"
                />

                <!-- Navegación por Pestañas (Tabs) -->
                <nav class="flex border-b border-slate-200 dark:border-slate-700">
                    <button 
                        v-for="tab in tabs" :key="tab.id"
                        @click="activeTab = tab.id"
                        class="px-6 py-4 text-sm font-bold transition-all border-b-2 flex items-center gap-2"
                        :class="activeTab === tab.id ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400 translate-y-[1px]' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="tab.icon" /></svg>
                        {{ tab.label }}
                    </button>
                </nav>

                <!-- Contenido Dinámico según Pestaña -->
                <div class="mt-6">
                    <div v-if="activeTab === 'overview'">
                        <AssetMetricsGrid :market-asset="marketAsset" />
                    </div>

                    <div v-if="activeTab === 'portfolio'">
                        <AssetTransactionTable 
                            :user-position="userPosition" 
                            :latest-transactions="latestTransactions" 
                            :is-privacy-mode="isPrivacyMode"
                            @open-modal="showTransactionModal = true"
                        />
                    </div>

                    <div v-if="activeTab === 'debate'">
                        <AssetDebateFeed :market-asset="marketAsset" :posts="posts" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Global para Registro de Operaciones -->
        <TransactionModal 
            v-if="showTransactionModal" 
            :show="showTransactionModal" 
            @close="showTransactionModal = false"
            :initial-asset="marketAsset"
        />
    </AuthenticatedLayout>
</template>
