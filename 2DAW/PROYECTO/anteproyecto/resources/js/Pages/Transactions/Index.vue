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
    selectedAssetId: [String, Number],
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
    const newAssetId = props.selectedAssetId == asset.id ? null : asset.id;
    router.get(route('transactions.index'), { 
        portfolio_id: props.selectedPortfolioId,
        asset_id: newAssetId,
        timeframe: props.filters.timeframe 
    }, { preserveState: true, preserveScroll: true });
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
                :portfolios="portfolios"
                :selected-portfolio-id="selectedPortfolioId"
                @switch="switchPortfolio"
                @create="openCreatePortfolioModal"
                @settings="openSettings"
            />
        </template>

        <div class="py-8 bg-slate-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                
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
                />

                <!-- Transaction History -->
                <TransactionHistory 
                    :transactions="transactions"
                    @edit="openEditTransaction"
                    @export="openExportModal"
                />

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
