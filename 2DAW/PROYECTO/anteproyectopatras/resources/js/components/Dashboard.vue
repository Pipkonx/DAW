<template>
    <div class="py-4">
        <!-- Alertas de Activos No Vinculados -->
        <div v-if="unlinkedAssets && unlinkedAssets.length > 0" class="container mb-4">
            <unlinked-assets-log :assets="unlinkedAssets" />
        </div>

        <!-- Cabecera del Dashboard -->
        <div class="container mb-4">
            <div class="row align-items-center">
                <div class="col-md-8 text-center text-md-start">
                    <h2 class="display-6 fw-black text-dark mb-1 tracking-tighter italic uppercase">Mi Dashboard</h2>
                    <p class="text-muted fw-bold small">Resumen detallado de tu patrimonio y flujo de caja.</p>
                </div>
                <div class="col-md-4 text-md-end text-center">
                    <button @click="isPrivacyMode = !isPrivacyMode" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold border-2">
                        <i class="bi" :class="isPrivacyMode ? 'bi-eye-fill' : 'bi-eye-slash-fill'"></i>
                        {{ isPrivacyMode ? 'Mostrar Datos' : 'Modo Privacidad' }}
                    </button>
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
                <div class="col-lg-4">
                    <global-distribution 
                        :allocation="charts.distribution" 
                        :summary="summary" 
                        :is-privacy-mode="isPrivacyMode" 
                    />
                </div>
                <div class="col-lg-4">
                    <expenses-section 
                        :expenses="expenses" 
                        :range="transactionFilter === 'all' ? 'month' : 'all'" 
                        :is-privacy-mode="isPrivacyMode" 
                        @update:range="onRangeUpdate"
                    />
                </div>
                <div class="col-lg-4 d-md-none d-lg-block">
                    <div v-if="!auth.user.is_premium" class="card bg-primary text-white h-100 border-0 shadow-lg rounded-5 p-5 text-center d-flex flex-column justify-content-center overflow-hidden position-relative">
                        <div class="position-absolute top-0 end-0 p-3 opacity-20">
                            <i class="bi bi-star-fill" style="font-size: 5rem;"></i>
                        </div>
                        <h4 class="fw-black mb-3 uppercase italic tracking-widest">Hazte Premium</h4>
                        <p class="small fw-bold text-white-50 mb-4">Desbloquea el análisis de cartera con IA, elimina anuncios y obtén soporte prioritario.</p>
                        <a href="/subscription" class="btn btn-light fw-black rounded-pill py-2 text-primary text-uppercase tracking-widest shadow-sm">Ver planes</a>
                    </div>
                    <div v-else class="card bg-dark text-white h-100 border-0 shadow-lg rounded-5 p-5 text-center d-flex flex-column justify-content-center">
                         <div class="bg-primary bg-opacity-20 rounded-circle p-3 d-inline-block mx-auto mb-3 shadow-sm">
                            <i class="bi bi-gem text-primary h1"></i>
                         </div>
                         <h4 class="fw-black mb-1 uppercase italic tracking-widest">Usuario Pro</h4>
                         <p class="extra-small fw-bold text-muted">Gracias por confiar en FintechPro.</p>
                         <a href="/ai-analyst" class="btn btn-outline-primary rounded-pill mt-3 fw-bold">Ir al Analista IA</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 3: Transacciones Recientes -->
        <div class="container mb-5 pb-5">
            <div class="card border-0 shadow-xl rounded-5 overflow-hidden bg-white">
                <div class="card-header bg-white border-bottom-0 py-4 px-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <h5 class="fw-black text-dark uppercase tracking-widest mb-0 italic">Transacciones Recientes</h5>
                        <select v-model="transactionFilter" class="form-select form-select-sm rounded-pill px-4 fw-bold shadow-sm" style="width: auto; border: 2px solid #eee;">
                            <option value="all">Todas las Transacciones</option>
                            <option value="income">Solo Ingresos</option>
                            <option value="expense">Solo Gastos</option>
                            <option value="investment">Solo Inversiones</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-5 py-3 text-muted fw-black uppercase small border-0" style="font-size: 0.65rem;">Fecha</th>
                                    <th class="py-3 text-muted fw-black uppercase small border-0" style="font-size: 0.65rem;">Categoría</th>
                                    <th class="py-3 text-muted fw-black uppercase small border-0" style="font-size: 0.65rem;">Descripción</th>
                                    <th class="pe-5 py-3 text-end text-muted fw-black uppercase small border-0" style="font-size: 0.65rem;">Monto</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr v-for="tx in allTransactions" :key="tx.id" class="transition-hover">
                                    <td class="ps-5 py-3 small fw-bold font-monospace text-muted">{{ tx.display_date }}</td>
                                    <td class="py-3">
                                        <span class="badge bg-light text-muted border fw-bold px-3 py-1 rounded-pill" style="font-size: 0.7rem;">
                                            {{ tx.category }}
                                        </span>
                                    </td>
                                    <td class="py-3 small fw-bold text-dark">{{ tx.description }}</td>
                                    <td class="pe-5 text-end fw-black font-monospace py-3" :class="tx.amount >= 0 ? 'text-success' : 'text-danger'">
                                        {{ isPrivacyMode ? '****' : formatCurrency(tx.amount) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div v-if="allTransactions.length === 0" class="text-center py-5 text-muted italic">
                        No se encontraron transacciones recientes.
                    </div>
                </div>
                <!-- Ancla para el Observer del Scroll Infinito -->
                <div ref="loadMoreTrigger" class="text-center py-4 border-top bg-light bg-opacity-50" v-if="hasMore">
                    <div v-if="loadingMore" class="spinner-border spinner-border-sm text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import KpiSection from './Features/Dashboard/KpiSection.vue';
import GlobalDistribution from './Features/Dashboard/GlobalDistribution.vue';
import ExpensesSection from './Features/Dashboard/ExpensesSection.vue';
import UnlinkedAssetsLog from './Features/Dashboard/UnlinkedAssetsLog.vue';
import { formatCurrency } from '@/Utils/formatting';

export default {
    name: 'Dashboard',
    components: {
        KpiSection,
        GlobalDistribution,
        ExpensesSection,
        UnlinkedAssetsLog
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
        onRangeUpdate(range) {
            console.log('Range updated to:', range);
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
                this.hasMore = false;
            } finally {
                this.loadingMore = false;
            }
        }
    }
}
</script>

<style scoped>
.fw-black { font-weight: 900; }
.rounded-5 { border-radius: 1.5rem !important; }
.rounded-4 { border-radius: 1rem !important; }
.uppercase { text-transform: uppercase; }
.italic { font-style: italic; }
.tracking-tighter { letter-spacing: -0.05em; }
.tracking-widest { letter-spacing: 0.15em; }
.font-monospace {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}
.transition-hover:hover {
    background-color: rgba(0,0,0,0.02) !important;
}
.blur-sm { filter: blur(6px); transition: filter 0.3s ease; }
.extra-small { font-size: 0.65rem; }
.shadow-xl { box-shadow: 0 1rem 3rem rgba(0,0,0,0.1) !important; }
</style>
