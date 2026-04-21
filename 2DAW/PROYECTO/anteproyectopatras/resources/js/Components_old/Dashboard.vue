<template>
    <div class="py-4">
        <!-- Alertas de Activos No Vinculados -->
        <div v-if="unlinkedAssets && unlinkedAssets.length > 0" class="container mb-4">
            <!-- UnlinkedAssetsLog componente (pendiente de migración) -->
        </div>

        <!-- Cabecera del Dashboard -->
        <div class="container mb-4">
            <div class="row">
                <div class="col-12">
                    <h2 class="display-6 fw-bold text-dark">Mi Dashboard</h2>
                    <p class="text-muted">Resumen detallado de tu patrimonio y flujo de caja.</p>
                </div>
            </div>
        </div>

        <!-- Sección 1: Indicadores Clave (KPIs) -->
        <div class="container mb-5">
            <kpi-section 
                :summary="summary" 
                :expenses="expenses" 
                :portfolios-count="portfolios.length" 
                :is-privacy-mode="isPrivacyMode" 
            />
        </div>

        <!-- Sección 2: Distribución y Gastos -->
        <div class="container mb-5">
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <!-- GlobalDistribution componente (pendiente de migración) -->
                    <div class="card h-100 border-0 shadow-sm p-4">
                        <h5 class="fw-bold mb-3">Distribución Global</h5>
                        <p class="small text-muted">Próximamente...</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <!-- ExpensesSection componente (pendiente de migración) -->
                    <div class="card h-100 border-0 shadow-sm p-4">
                        <h5 class="fw-bold mb-3">Análisis de Gastos</h5>
                        <p class="small text-muted">Próximamente...</p>
                    </div>
                </div>
                <div class="col-lg-4 d-md-none d-lg-block" v-if="!auth.user.is_premium">
                    <div class="card bg-primary text-white h-100 border-0 shadow-sm p-4 text-center">
                        <h5 class="fw-bold mb-3">Hazte Premium</h5>
                        <p class="small">Desbloquea análisis avanzados y reportes personalizados.</p>
                        <button class="btn btn-light btn-sm mt-auto">Ver planes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 3: Transacciones Recientes -->
        <div class="container mb-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Transacciones Recientes</h5>
                        <select v-model="transactionFilter" class="form-select form-select-sm" style="width: auto;">
                            <option value="all">Todas</option>
                            <option value="income">Ingresos</option>
                            <option value="expense">Gastos</option>
                            <option value="investment">Inversiones</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4">Fecha</th>
                                    <th>Categoría</th>
                                    <th>Descripción</th>
                                    <th class="text-end px-4">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="tx in allTransactions" :key="tx.id">
                                    <td class="px-4 small">{{ tx.display_date }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark fw-normal border">{{ tx.category }}</span>
                                    </td>
                                    <td class="small">{{ tx.description }}</td>
                                    <td class="text-end px-4 fw-bold" :class="tx.amount >= 0 ? 'text-success' : 'text-danger'">
                                        {{ formatCurrency(tx.amount) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Ancla para el Observer del Scroll Infinito -->
                <div ref="loadMoreTrigger" class="text-center py-4" v-if="hasMore">
                    <div v-if="loadingMore" class="spinner-border spinner-border-sm text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import KpiSection from './Features/Dashboard/KpiSection.vue';
import { formatCurrency } from '../../Utils/formatting';

export default {
    name: 'Dashboard',
    components: {
        KpiSection
    },
    props: {
        summary: Object,
        portfolios: Array,
        expenses: Object,
        charts: Object,
        recentTransactions: Array,
        allAssetsList: Array,
        categories: Array,
        unlinkedAssets: Array,
        currentFilter: String,
        selectedMonths: [String, Number],
        auth: Object,
    },
    data() {
        return {
            isPrivacyMode: false,
            transactionFilter: this.currentFilter || 'all',
            allTransactions: [...this.recentTransactions],
            loadingMore: false,
            hasMore: true,
            offset: this.recentTransactions.length,
            limit: 20
        }
    },
    watch: {
        transactionFilter(newFilter) {
            // Nota: En Laravel UI (MPA), esto debería ser una navegación real
            // o una recarga AJAX de toda la sección.
            window.location.href = `/dashboard?filter=${newFilter}`;
        },
        recentTransactions: {
            handler(newVal) {
                this.allTransactions = [...newVal];
                this.offset = newVal.length;
                this.hasMore = true;
            },
            deep: true
        }
    },
    mounted() {
        this.setupInfiniteScroll();
    },
    methods: {
        formatCurrency(value) {
            return formatCurrency(value);
        },
        setupInfiniteScroll() {
            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting && this.hasMore && !this.loadingMore) {
                    this.loadMoreTransactions();
                }
            }, { threshold: 0.1 });

            if (this.$refs.loadMoreTrigger) {
                observer.observe(this.$refs.loadMoreTrigger);
            }
        },
        async loadMoreTransactions() {
            this.loadingMore = true;
            try {
                // Asumimos que route() no está disponible sin Ziggy configurado para Mix
                // Usaremos la URL plana por ahora
                const response = await axios.get('/api/dashboard/transactions', {
                    params: { 
                        offset: this.offset, 
                        limit: this.limit,
                        filter: this.transactionFilter
                    }
                });

                const newTransactions = response.data;
                if (newTransactions.length < this.limit) {
                    this.hasMore = false;
                }

                this.allTransactions = [...this.allTransactions, ...newTransactions];
                this.offset += newTransactions.length;
            } catch (error) {
                console.error('Error al cargar transacciones adicionales:', error);
            } finally {
                this.loadingMore = false;
            }
        }
    }
}
</script>

<style scoped>
.rounded-4 {
    border-radius: 1rem !important;
}
</style>
