<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TransactionModal from '@/Components/Features/Transactions/TransactionModal.vue';
import TransactionHistory from '@/Components/Features/Transactions/TransactionHistory.vue';
import ImportReviewModal from '@/Components/Features/Expenses/ImportReviewModal.vue';
import PrimaryButton from '@/Components/BaseUI/PrimaryButton.vue';
import TextInput from '@/Components/BaseUI/TextInput.vue';
import { usePrivacy } from '@/Composables/usePrivacy';
import { useToast } from '@/Composables/useToast';
import axios from 'axios';

// Componenes de UI refactorizados
import SummaryCards from '@/Components/Features/Expenses/SummaryCards.vue';
import ExpenseChartsSection from '@/Components/Features/Expenses/ExpenseChartsSection.vue';
import TopCategoriesList from '@/Components/Features/Expenses/TopCategoriesList.vue';

const { isPrivacyMode } = usePrivacy();
const { showToast } = useToast();

const props = defineProps({
    filters: Object,
    summary: Object,
    charts: Object,
    transactions: Object,
    portfolios: Array,
    categories: Array,
    topExpenses: Array,
    topIncome: Array,
    min_date: String,
});

// --- ESTADO Y CARGA INFINITA ---
const allTransactions = ref([...props.transactions.data]);
const loadingMore = ref(false);
const hasMore = ref(!!props.transactions.next_page_url);
const currentPage = ref(1);
const observerTarget = ref(null);
let observer = null;

const loadMoreTransactions = async () => {
    if (loadingMore.value || !hasMore.value) return;
    loadingMore.value = true;
    try {
        const response = await axios.get(route('expenses.transactions'), {
            params: { 
                page: currentPage.value + 1,
                start_date: dateFilters.value.start_date,
                end_date: dateFilters.value.end_date
            }
        });
        const paginator = response.data;
        allTransactions.value = [...allTransactions.value, ...paginator.data];
        currentPage.value = paginator.current_page;
        hasMore.value = !!paginator.next_page_url;
    } catch (error) {
        console.error('Error al cargar más:', error);
    } finally {
        loadingMore.value = false;
    }
};

watch(() => props.transactions, (newVal) => {
    allTransactions.value = [...newVal.data];
    currentPage.value = 1;
    hasMore.value = !!newVal.next_page_url;
}, { deep: true });

onMounted(() => {
    observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) loadMoreTransactions();
    }, { rootMargin: '200px' });
    if (observerTarget.value) observer.observe(observerTarget.value);

    // Recuperar filtros persistidos
    const queryParams = new URLSearchParams(window.location.search);
    if (!queryParams.has('start_date') && !queryParams.has('end_date')) {
        const savedStart = localStorage.getItem('expenses_filter_start');
        const savedEnd = localStorage.getItem('expenses_filter_end');
        if (savedStart || savedEnd) {
            dateFilters.value.start_date = savedStart || dateFilters.value.start_date;
            dateFilters.value.end_date = savedEnd || dateFilters.value.end_date;
        }
    }
});

onUnmounted(() => { if (observer) observer.disconnect(); });

// --- FILTROS ---
const dateFilters = ref({
    start_date: props.filters.start_date,
    end_date: props.filters.end_date,
});

watch(() => dateFilters.value, (newFilters) => {
    localStorage.setItem('expenses_filter_start', newFilters.start_date || '');
    localStorage.setItem('expenses_filter_end', newFilters.end_date || '');
}, { deep: true });

const applyFilters = () => {
    // Evitar fechas anteriores a la primera transacción
    if (props.min_date && dateFilters.value.start_date < props.min_date) {
        dateFilters.value.start_date = props.min_date;
    }

    router.get(route('expenses.index'), {
        start_date: dateFilters.value.start_date,
        end_date: dateFilters.value.end_date,
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['summary', 'charts', 'topExpenses', 'topIncome', 'transactions', 'filters'],
    });
};

// --- MODALES Y ACCIONES ---
const showModal = ref(false);
const editingTransaction = ref(null);
const fileInput = ref(null);

const openTransactionModal = () => {
    editingTransaction.value = { type: 'expense' };
    showModal.value = true;
};

const editTransaction = (transaction) => {
    editingTransaction.value = transaction;
    showModal.value = true;
};

const handleExport = (format) => {
    window.location.href = route('transactions.export', { format, ...dateFilters.value });
};

// --- IMPORTACIÓN ---
const showImportModal = ref(false);
const importPreviewTransactions = ref([]);
const isImporting = ref(false);

const handleFileUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;
    const formData = new FormData();
    formData.append('file', file);
    try {
        isImporting.value = true;
        const response = await axios.post(route('transactions.preview-import'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        importPreviewTransactions.value = response.data.transactions;
        showImportModal.value = true;
    } catch (error) {
        showToast('Error al procesar el archivo.', 'error');
    } finally {
        isImporting.value = false;
        if (fileInput.value) fileInput.value.value = null;
    }
};

const confirmImport = () => {
    if (importPreviewTransactions.value.length === 0) return;
    isImporting.value = true;
    router.post(route('transactions.bulk-store'), {
        transactions: importPreviewTransactions.value
    }, {
        onSuccess: () => {
            showImportModal.value = false;
            importPreviewTransactions.value = [];
            applyFilters();
        },
        onFinish: () => {
            isImporting.value = false;
        }
    });
};
</script>

<template>
    <Head title="Análisis de Gastos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">Análisis de Gastos</h2>
                
                <div class="flex items-center gap-4">
                    <PrimaryButton @click="openTransactionModal" class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Agregar Operación</span>
                    </PrimaryButton>

                    <div class="flex items-center gap-2 bg-white dark:bg-slate-800 p-1.5 px-3 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-500 transition-colors group">
                        <div class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <input 
                                type="date" 
                                v-model="dateFilters.start_date" 
                                @change="applyFilters" 
                                :min="min_date"
                                class="text-xs border-none bg-transparent dark:bg-transparent text-slate-600 dark:text-slate-200 focus:ring-0 p-0 py-1 transition-colors [color-scheme:dark]" 
                            />
                        </div>
                        <span class="text-slate-300 dark:text-slate-600 font-bold ml-1">-</span>
                        <input 
                            type="date" 
                            v-model="dateFilters.end_date" 
                            @change="applyFilters" 
                            class="text-xs border-none bg-transparent dark:bg-transparent text-slate-600 dark:text-slate-200 focus:ring-0 p-0 py-1 transition-colors [color-scheme:dark]" 
                        />
                    </div>
                </div>
            </div>
        </template>

        <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <SummaryCards :summary="summary" :is-privacy-mode="isPrivacyMode" />
            <ExpenseChartsSection :charts="charts" :summary="summary" :is-privacy-mode="isPrivacyMode" />

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <div class="lg:col-span-3 space-y-4">
                    <TransactionHistory 
                        :transactions="allTransactions" 
                        :loading="loadingMore"
                        :has-more="hasMore"
                        filter-mode="expenses" 
                        @edit="editTransaction"
                        @export="handleExport"
                        @import="fileInput.click()"
                    />
                    <div ref="observerTarget" class="h-4"></div>
                </div>

                <div class="lg:col-span-1 space-y-6">
                    <TopCategoriesList title="Top Gastos" :items="topExpenses" :total-amount="summary.total_expense" color-class="bg-rose-500" :is-privacy-mode="isPrivacyMode" />
                    <TopCategoriesList title="Top Ingresos" :items="topIncome" :total-amount="summary.total_income" color-class="bg-emerald-500" :is-privacy-mode="isPrivacyMode" />
                </div>
            </div>
        </div>

        <input type="file" ref="fileInput" @change="handleFileUpload" class="hidden" accept=".csv" />

        <TransactionModal 
            :show="showModal" 
            :transaction="editingTransaction" 
            :portfolios="portfolios" 
            :categories="categories" 
            :allowed-types="['income', 'expense']" 
            @close="showModal = false" 
        />

        <ImportReviewModal 
            :show="showImportModal" 
            :transactions="importPreviewTransactions" 
            :categories="categories" 
            :portfolios="portfolios"
            :is-importing="isImporting"
            @close="showImportModal = false" 
            @confirm="confirmImport"
            @remove="(idx) => importPreviewTransactions.splice(idx, 1)"
        />
    </AuthenticatedLayout>
</template>
