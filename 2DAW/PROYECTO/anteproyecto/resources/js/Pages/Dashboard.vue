<script setup>
/**
 * Dashboard Principal - FintechPro
 * 
 * Este es el componente orquestador que centraliza la visualización del patrimonio,
 * inversiones, gastos y transacciones recientes. Sigue el patrón de diseño de
 * división por responsabilidades (SRP), delegando la interfaz a subcomponentes.
 */
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, watch } from 'vue';
import axios from 'axios';

// Componentes del Dashboard (Refactorizados)
import KpiSection from '@/Components/Dashboard/KpiSection.vue';
import GlobalDistribution from '@/Components/Dashboard/GlobalDistribution.vue';
import PortfoliosSection from '@/Components/Dashboard/PortfoliosSection.vue';
import ExpensesSection from '@/Components/Dashboard/ExpensesSection.vue';
import EvolutionSection from '@/Components/Dashboard/EvolutionSection.vue';
import RecentTransactions from '@/Components/Dashboard/RecentTransactions.vue';
import UpgradePlanWidget from '@/Components/Dashboard/UpgradePlanWidget.vue';
import FaqAssistant from '@/Components/Dashboard/FaqAssistant.vue';
import WelcomeTour from '@/Components/Dashboard/WelcomeTour.vue';

// Otros Componentes
import TransactionModal from '@/Components/TransactionModal.vue';
import UnlinkedAssetsLog from '@/Components/Dashboard/UnlinkedAssetsLog.vue';
import { usePrivacy } from '@/Composables/usePrivacy';

// Props: Datos inyectados por el controlador de Laravel (Inertia)
const props = defineProps({
    summary: Object,          // Resumen: Patrimonio Neto, Efectivo, Total Inversiones
    portfolios: Array,        // Lista de carteras con sus activos y rendimientos
    expenses: Object,         // Métricas de gastos y rangos (mes, año, todo)
    charts: Object,           // Datos para gráficos de evolución y distribución
    recentTransactions: Array,// Transacciones iniciales para el listado
    allAssetsList: Array,     // Referencia de todos los activos disponibles
    categories: Array,        // Categorías de gastos/ingresos para el modal
    unlinkedAssets: Array,    // Activos detectados pero no vinculados a mercado
    currentFilter: String,   // Filtro actual aplicado en el servidor
    selectedMonths: [String, Number], // Rango de meses actual para el gráfico
    auth: Object,             // Autenticación (inyectado automáticamente por Inertia middleware)
});

// --- ESTADO REACTIVO ---
const { isPrivacyMode } = usePrivacy();
const showModal = ref(false);
const editingTransaction = ref(null);
const isUpdatingChart = ref(false);
const showTour = ref(false);

// Modos de visualización persistentes
const chartMode = ref('global'); // 'global' (patrimonio) | 'portfolios' (por carteras)
const displayMode = ref('value'); // 'value' (€) | 'percent' (%)
const transactionFilter = ref('all'); // Filtro de tabla: all, income, expense, investment
const expenseRange = ref(localStorage.getItem('dashboard_expense_range') || 'month');
const dashboardTimeframe = ref(props.selectedMonths || 'MAX');

// Guardar preferencia de rango de gastos en el navegador
watch(expenseRange, (newRange) => {
    localStorage.setItem('dashboard_expense_range', newRange);
});

// Recargar transacciones al cambiar el filtro
watch(transactionFilter, (newFilter) => {
    router.get(route('dashboard'), { filter: newFilter, months: dashboardTimeframe.value }, {
        preserveState: true,
        preserveScroll: true,
        only: ['recentTransactions', 'currentFilter'],
    });
});

// Recargar gráficos al cambiar el rango de tiempo
watch(dashboardTimeframe, (newTimeframe) => {
    isUpdatingChart.value = true;
    router.get(route('dashboard'), { 
        filter: transactionFilter.value, 
        months: newTimeframe 
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['charts', 'selectedMonths'],
        onFinish: () => {
            isUpdatingChart.value = false;
        }
    });
});

// --- SCROLL INFINITO (TRANSACCIONES) ---
const allTransactions = ref([...props.recentTransactions]);
const loadingMore = ref(false);
const hasMore = ref(true);
const offset = ref(props.recentTransactions.length);
const limit = 20;

/**
 * Carga más transacciones desde el servidor al hacer scroll.
 */
const loadMoreTransactions = async () => {
    if (loadingMore.value || !hasMore.value) return;

    loadingMore.value = true;
    try {
        const response = await axios.get(route('dashboard.transactions'), {
            params: { 
                offset: offset.value, 
                limit: limit,
                filter: transactionFilter.value // Pasar el filtro actual al API
            }
        });

        const newTransactions = response.data;
        if (newTransactions.length < limit) {
            hasMore.value = false;
        }

        allTransactions.value = [...allTransactions.value, ...newTransactions];
        offset.value += newTransactions.length;
    } catch (error) {
        console.error('Error al cargar transacciones adicionales:', error);
    } finally {
        loadingMore.value = false;
    }
};

// Intersection Observer para detectar el final de la lista
const loadMoreTrigger = ref(null);
let observer = null;

onMounted(() => {
    // Verificar si se debe mostrar el tour de bienvenida
    if (!props.auth.user.onboarding_completed_at) {
        showTour.value = true;
    }

    observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) {
            loadMoreTransactions();
        }
    }, { threshold: 0.1 });

    if (loadMoreTrigger.value) {
        observer.observe(loadMoreTrigger.value);
    }
});

onUnmounted(() => {
    if (observer) observer.disconnect();
});

// Sincronizar si las transacciones iniciales cambian (ej: tras una acción en el modal)
watch(() => props.recentTransactions, (newVal) => {
    allTransactions.value = [...newVal];
    offset.value = newVal.length;
    hasMore.value = true;
}, { deep: true });

// --- GESTIÓN DE MODAL ---
const editTransaction = (transaction) => {
    editingTransaction.value = transaction;
    showModal.value = true;
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <!-- Cabecera del Dashboard -->
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold leading-tight text-slate-800 dark:text-white">
                        Mi Dashboard
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Resumen detallado de tu patrimonio y flujo de caja.
                    </p>
                </div>
            </div>
        </template>

        <div class="py-8 space-y-8">
            
            <!-- Alertas de Activos No Vinculados -->
            <div v-if="unlinkedAssets && unlinkedAssets.length > 0" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <UnlinkedAssetsLog :assets="unlinkedAssets" />
            </div>

            <!-- Sección 1: Indicadores Clave (KPIs) -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <KpiSection 
                    :summary="summary" 
                    :expenses="expenses" 
                    :portfolios-count="portfolios.length" 
                    :is-privacy-mode="isPrivacyMode" 
                />
            </div>

            <!-- Sección 2: Distribución y Gastos (Fila Superior) -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 items-stretch">
                    <!-- 2.1. Distribución Global (Invertido vs Líquido) -->
                    <GlobalDistribution 
                        :allocation="charts.allocation" 
                        :summary="summary"
                        :is-privacy-mode="isPrivacyMode"
                        class="h-full"
                    />

                    <!-- 2.2. Análisis de Gastos por Rango -->
                    <ExpensesSection 
                        :expenses="expenses" 
                        v-model:range="expenseRange"
                        :is-privacy-mode="isPrivacyMode"
                        class="h-full"
                    />

                    <!-- 2.3. Upgrade Widget (Solo si no es premium) -->
                    <div v-if="!$page.props.auth.user.is_premium" class="xl:col-span-1 md:col-span-2 xl:block">
                        <UpgradePlanWidget class="h-full" />
                    </div>
                </div>
            </div>

            <!-- Sección 3: Mi Evolución (Gráfico de Líneas) -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <EvolutionSection 
                    :charts="charts" 
                    :portfolios="portfolios"
                    v-model:chart-mode="chartMode"
                    v-model:display-mode="displayMode"
                    v-model:timeframe="dashboardTimeframe"
                    :loading="isUpdatingChart"
                    :is-privacy-mode="isPrivacyMode"
                />
            </div>

            <!-- Sección 4: Mis Inversiones (Ancho Completo) -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <PortfoliosSection 
                    :portfolios="portfolios" 
                    :is-privacy-mode="isPrivacyMode"
                />
            </div>

            <!-- Sección 4: Listado de Transacciones con Filtros y Scroll Infinito -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <RecentTransactions 
                    :transactions="allTransactions" 
                    v-model:filter="transactionFilter"
                    :loading-more="loadingMore"
                    :is-privacy-mode="isPrivacyMode"
                    @edit="editTransaction"
                />
                
                <!-- Ancla para el Observer del Scroll Infinito -->
                <div ref="loadMoreTrigger" class="h-4"></div>
            </div>

        </div>

        <!-- Tour de Bienvenida -->
        <WelcomeTour :show="showTour" @close="showTour = false" />

        <!-- Asistente de FAQ (Antiguo accionador flotante) -->
        <FaqAssistant />

        <!-- Modal para Crear/Editar Transacciones -->
        <TransactionModal 
            v-if="showModal" 
            :show="showModal" 
            :transaction="editingTransaction"
            :portfolios="portfolios" 
            :categories="categories"
            @close="showModal = false" 
        />
    </AuthenticatedLayout>
</template>
