<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Components
import PortfolioHeader from '@/Components/Transactions/PortfolioHeader.vue';
import EvolutionChart from '@/Components/Transactions/EvolutionChart.vue';
import AllocationChart from '@/Components/Transactions/AllocationChart.vue';
import AssetsTable from '@/Components/Transactions/AssetsTable.vue';
import TransactionHistory from '@/Components/Transactions/TransactionHistory.vue';
import ExportModal from '@/Components/Transactions/ExportModal.vue';

// Legacy Modals
import TransactionModal from '@/Components/TransactionModal.vue';
import PortfolioModal from '@/Components/PortfolioModal.vue';
import SettingsModal from '@/Components/SettingsModal.vue';

const props = defineProps({
    portfolios: Array,
    selectedPortfolioId: [String, Number],
    selectedAssetId: [String, Number, Array],
    summary: Object,
    assets: Array,
    transactions: Object,
    chart: Object,
    allocations: Object,
    filters: Object,
    minDate: String
});

// Modal State
const showPortfolioModal = ref(false);
const editingPortfolio = ref(null);
const showTransactionModal = ref(false);
const editingTransaction = ref(null);
const showSettingsModal = ref(false);
const showExportModal = ref(false);
const exportFormat = ref('pdf');

// Navigation Actions
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

const filterByAsset = (asset) => {
    let currentIds = [];
    
    // Normalizar selectedAssetId a array
    if (props.selectedAssetId) {
        if (Array.isArray(props.selectedAssetId)) {
            currentIds = [...props.selectedAssetId];
        } else if (typeof props.selectedAssetId === 'string' && props.selectedAssetId.includes(',')) {
            currentIds = props.selectedAssetId.split(',').map(id => id.trim());
        } else {
            currentIds = [String(props.selectedAssetId)];
        }
    }
    
    const assetId = String(asset.id);
    const index = currentIds.indexOf(assetId);
    
    if (index > -1) {
        // Si ya está seleccionado, lo quitamos (toggle off)
        currentIds.splice(index, 1);
    } else {
        // Si no está, lo añadimos (toggle on)
        currentIds.push(assetId);
    }
    
    // Convertir a string separado por comas para la URL o null si está vacío
    const newAssetIdParam = currentIds.length > 0 ? currentIds.join(',') : null;
    
    router.get(route('transactions.index'), { 
        portfolio_id: props.selectedPortfolioId,
        asset_id: newAssetIdParam,
        timeframe: props.filters.timeframe 
    }, { preserveState: true, preserveScroll: true });
};

const deleteAsset = (asset) => {
    router.delete(route('assets.destroy', asset.id), {
        onSuccess: () => {
             // Si el activo borrado estaba en la selección, quitarlo y actualizar la vista
             if (props.selectedAssetId) {
                 let currentIds = [];
                 if (Array.isArray(props.selectedAssetId)) {
                     currentIds = [...props.selectedAssetId];
                 } else if (typeof props.selectedAssetId === 'string' && props.selectedAssetId.includes(',')) {
                     currentIds = props.selectedAssetId.split(',').map(id => id.trim());
                 } else {
                     currentIds = [String(props.selectedAssetId)];
                 }
                 
                 const index = currentIds.indexOf(String(asset.id));
                 if (index > -1) {
                     currentIds.splice(index, 1);
                     
                     const newAssetIdParam = currentIds.length > 0 ? currentIds.join(',') : null;
                     
                     router.get(route('transactions.index'), { 
                        portfolio_id: props.selectedPortfolioId,
                        asset_id: newAssetIdParam,
                        timeframe: props.filters.timeframe 
                    }, { preserveState: true, preserveScroll: true, replace: true });
                 }
             }
        },
        preserveScroll: true
    });
};

// Modal Actions
const openCreatePortfolioModal = () => {
    editingPortfolio.value = null;
    showPortfolioModal.value = true;
};

const openSettings = () => {
    showSettingsModal.value = true;
};

const openNewTransaction = () => {
    editingTransaction.value = null;
    showTransactionModal.value = true;
};

const openEditTransaction = (transaction) => {
    editingTransaction.value = transaction;
    showTransactionModal.value = true;
};

const openExportModal = (format) => {
    exportFormat.value = format;
    showExportModal.value = true;
};

const confirmExport = ({ format, start_date, end_date }) => {
    const params = new URLSearchParams({
        format: format,
        portfolio_id: props.selectedPortfolioId !== 'aggregated' ? props.selectedPortfolioId : 'aggregated',
        asset_id: props.selectedAssetId || '',
        start_date: start_date,
        end_date: end_date
    });
    
    window.location.href = `${route('transactions.export')}?${params.toString()}`;
    showExportModal.value = false;
};
</script>

<template>
    <Head title="Patrimonio Neto" />

    <AuthenticatedLayout>
        <template #header>
            <PortfolioHeader 
                v-if="portfolios.length > 0"
                :portfolios="portfolios"
                :selected-portfolio-id="selectedPortfolioId"
                @switch="switchPortfolio"
                @create="openCreatePortfolioModal"
                @settings="openSettings"
            />
            <h2 v-else class="font-semibold text-xl text-slate-800 dark:text-white leading-tight">
                Bienvenido a tu Patrimonio
            </h2>
        </template>

        <div class="py-8 bg-slate-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- EMPTY STATE -->
                <div v-if="portfolios.length === 0" class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 max-w-md w-full">
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">Comienza tu viaje</h3>
                        <p class="text-slate-500 dark:text-slate-400 mb-8">
                            Para empezar a gestionar tus inversiones y ver tu patrimonio neto, necesitas crear tu primera cartera.
                        </p>
                        <button 
                            @click="openCreatePortfolioModal"
                            class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl shadow-lg shadow-blue-500/30 transition-all transform hover:scale-[1.02]"
                        >
                            Crear mi primera cartera
                        </button>
                    </div>
                </div>

                <div v-else class="space-y-8">
                    <!-- Charts Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2">
                            <EvolutionChart 
                                :summary="summary"
                                :chart="chart"
                                :filters="filters"
                                @update:timeframe="switchTimeframe"
                            />
                        </div>
                        <div>
                            <AllocationChart 
                                :allocations="allocations"
                            />
                        </div>
                    </div>

                    <!-- Assets Table -->
                    <AssetsTable 
                        :assets="assets"
                        :selected-asset-id="selectedAssetId"
                        @filter-asset="filterByAsset"
                        @add-transaction="openNewTransaction"
                        @delete-asset="deleteAsset"
                    />

                    <!-- Transaction History -->
                    <TransactionHistory 
                        :transactions="transactions"
                        @edit="openEditTransaction"
                        @export="openExportModal"
                    />
                </div>

            </div>
        </div>

        <!-- Modals -->
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

        <ExportModal 
            :show="showExportModal"
            :format="exportFormat"
            :min-date="minDate"
            @close="showExportModal = false"
            @confirm="confirmExport"
        />

    </AuthenticatedLayout>
</template>
