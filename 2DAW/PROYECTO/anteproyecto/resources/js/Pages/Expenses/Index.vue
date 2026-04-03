<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import TransactionModal from '@/Components/TransactionModal.vue';
import TransactionHistory from '@/Components/Transactions/TransactionHistory.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { usePrivacy } from '@/Composables/usePrivacy';

// Components refactorizados
import SummaryCards from '@/Components/Expenses/SummaryCards.vue';
import ExpenseChartsSection from '@/Components/Expenses/ExpenseChartsSection.vue';
import TopCategoriesList from '@/Components/Expenses/TopCategoriesList.vue';

const { isPrivacyMode } = usePrivacy();

const props = defineProps({
    filters: Object,
    summary: Object,
    charts: Object,
    transactions: Object, // Paginado
    portfolios: Array,
    categories: Array,
    availableYears: Array,
    selectedYear: Number,
    yearStats: Object,
    topExpenses: Array, // Lista completa para cliente
    topIncome: Array,   // Lista completa para cliente
});

// Estado para filtros
const dateFilters = ref({
    start_date: props.filters.start_date,
    end_date: props.filters.end_date,
});

// Sincronizar estado si los props cambian (ej: navegación con nuevos parámetros)
watch(() => props.filters, (newFilters) => {
    dateFilters.value.start_date = newFilters.start_date;
    dateFilters.value.end_date = newFilters.end_date;
}, { deep: true });

// Cargar filtros persistidos al montar
onMounted(() => {
    const queryParams = new URLSearchParams(window.location.search);
    const hasUrlFilters = queryParams.has('start_date') || queryParams.has('end_date');
    
    const savedStart = localStorage.getItem('expenses_filter_start');
    const savedEnd = localStorage.getItem('expenses_filter_end');
    
    // Si no hay filtros en la URL pero sí en localStorage, aplicarlos
    if (!hasUrlFilters && (savedStart || savedEnd)) {
        dateFilters.value.start_date = savedStart || dateFilters.value.start_date;
        dateFilters.value.end_date = savedEnd || dateFilters.value.end_date;
        applyFilters();
    }
});

// Peristir cambios en los filtros
watch(() => dateFilters.value, (newFilters) => {
    if (newFilters.start_date) localStorage.setItem('expenses_filter_start', newFilters.start_date);
    if (newFilters.end_date) localStorage.setItem('expenses_filter_end', newFilters.end_date);
}, { deep: true });

const applyFilters = () => {
    router.get(route('expenses.index'), {
        start_date: dateFilters.value.start_date,
        end_date: dateFilters.value.end_date,
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['summary', 'charts', 'topExpenses', 'topIncome', 'transactions', 'filters'],
    });
};

// Modal Actions
const showModal = ref(false);
const editingTransaction = ref(null);

const openTransactionModal = () => {
    editingTransaction.value = { type: 'expense' }; // Por defecto gasto
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

// CSV Import/Export Logic
const fileInput = ref(null);

const triggerFileInput = () => {
    if (fileInput.value) fileInput.value.click();
};

const handleExport = (format) => {
    const params = {
        format: format,
        ...dateFilters.value
    };
    window.location.href = route('transactions.export', params);
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
                applyFilters(); // Recargar datos
                alert('Transacciones importadas correctamente.');
            },
            onError: (errors) => {
                console.error(errors);
                alert('Hubo un error al importar el archivo.');
            },
            onFinish: () => {
                if (fileInput.value) fileInput.value.value = null;
            }
        });
    } else {
        event.target.value = null;
    }
};
</script>

<template>
    <Head title="Análisis de Gastos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                    Análisis de Gastos
                </h2>
                
                <div class="flex items-center gap-4">
                    <!-- Botón Agregar Manual -->
                    <PrimaryButton @click="openTransactionModal" class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="hidden sm:inline">Agregar Operación</span>
                        <span class="sm:hidden">Agregar</span>
                    </PrimaryButton>

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
            </div>
        </template>

        <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- 1. TARJETAS DE RESUMEN (KPIs) -->
            <SummaryCards :summary="summary" :is-privacy-mode="isPrivacyMode" />

            <!-- 2. GRÁFICOS -->
            <ExpenseChartsSection :charts="charts" :summary="summary" :is-privacy-mode="isPrivacyMode" />

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
                    <TopCategoriesList 
                        title="Top Gastos" 
                        :items="topExpenses" 
                        :total-amount="summary.total_expense"
                        color-class="bg-rose-500"
                        empty-message="No hay gastos registrados."
                        :is-privacy-mode="isPrivacyMode"
                    />

                    <!-- Top Ingresos -->
                    <TopCategoriesList 
                        title="Top Ingresos" 
                        :items="topIncome" 
                        :total-amount="summary.total_income"
                        color-class="bg-emerald-500"
                        empty-message="No hay ingresos registrados."
                        :is-privacy-mode="isPrivacyMode"
                    />
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
