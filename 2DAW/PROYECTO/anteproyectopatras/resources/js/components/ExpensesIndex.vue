<template>
    <div class="py-4 bg-light min-vh-100">
        <div class="container overflow-hidden">
            <!-- Header Section -->
            <header class="mb-5 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
                <div>
                    <h1 class="display-6 fw-black text-dark mb-1">Análisis de Gastos</h1>
                    <p class="text-muted fw-bold small text-uppercase tracking-widest mb-0">Control total de tus flujos de caja y ahorros</p>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <button @click="openNewTransaction" class="btn btn-dark rounded-pill px-4 fw-black shadow-sm d-flex align-items-center gap-2">
                        <i class="bi bi-plus-lg"></i> Operación
                    </button>

                    <div class="d-flex align-items-center gap-2 bg-white p-2 rounded-4 shadow-sm border">
                        <input 
                            type="date" 
                            v-model="dateFilters.start_date" 
                            @change="applyFilters" 
                            :min="minDate"
                            class="form-control form-control-sm border-0 bg-transparent fw-bold text-muted px-2" 
                        />
                        <span class="text-muted fw-black small">-</span>
                        <input 
                            type="date" 
                            v-model="dateFilters.end_date" 
                            @change="applyFilters" 
                            class="form-control form-control-sm border-0 bg-transparent fw-bold text-muted px-2" 
                        />
                    </div>
                </div>
            </header>

            <!-- Summary Cards -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                        <h6 class="text-muted fw-black small text-uppercase tracking-widest mb-3">Ingresos Totales</h6>
                        <div class="h3 fw-black text-dark mb-0">{{ formatCurrency(summary.total_income) }}</div>
                        <div class="mt-2 small text-success fw-bold">Entradas del periodo</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white border-start border-4 border-danger">
                        <h6 class="text-muted fw-black small text-uppercase tracking-widest mb-3">Gastos Totales</h6>
                        <div class="h3 fw-black text-dark mb-0">{{ formatCurrency(summary.total_expense) }}</div>
                        <div class="mt-2 small text-danger fw-bold">Salidas del periodo</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-primary text-white">
                        <h6 class="text-white-50 fw-black small text-uppercase tracking-widest mb-3">Ahorro Neto</h6>
                        <div class="h3 fw-black mb-0">{{ formatCurrency(summary.net_savings) }}</div>
                        <div class="mt-2 small fw-bold" :class="summary.net_savings >= 0 ? 'text-white' : 'text-warning'">
                            Balance del periodo
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section (Simplificado para el demo) -->
            <div class="row g-4 mb-5">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <h5 class="fw-black text-dark mb-4">Tendencia de Balance</h5>
                        <div class="bg-light rounded-4 d-flex align-items-center justify-content-center border border-dashed" style="height: 300px;">
                            <span class="text-muted fw-bold italic small">Gráfico de Evolución Temporal (Chart.js)</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <h5 class="fw-black text-dark mb-4">Distribución</h5>
                        <div class="bg-light rounded-4 d-flex align-items-center justify-content-center border border-dashed mb-4" style="height: 200px;">
                            <span class="text-muted fw-bold italic small">Gráfico de Categorías</span>
                        </div>
                        <div class="space-y-3 mt-auto">
                            <div v-for="(label, index) in charts.categories.labels.slice(0, 5)" :key="index" class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-bold text-muted">{{ label }}</span>
                                <span class="small fw-black text-dark">{{ formatCurrency(charts.categories.data[index]) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Transactions List -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                        <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                            <h5 class="fw-black text-dark mb-0">Historial de Operaciones</h5>
                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" @click="handleExport">
                                <i class="bi bi-download me-1"></i> CSV
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 border-0 text-muted fw-black small uppercase">Fecha</th>
                                        <th class="border-0 text-muted fw-black small uppercase">Categoría</th>
                                        <th class="border-0 text-muted fw-black small uppercase">Descripción</th>
                                        <th class="text-end pe-4 border-0 text-muted fw-black small uppercase">Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="tx in allTransactions" :key="tx.id" class="transition-hover">
                                        <td class="ps-4 py-3 fw-bold text-muted" style="font-size: 0.85rem;">{{ tx.display_date }}</td>
                                        <td>
                                            <span class="badge rounded-pill bg-light text-dark border px-3 py-2 fw-bold text-capitalize" style="font-size: 0.75rem;">
                                                {{ tx.category }}
                                            </span>
                                        </td>
                                        <td class="small text-truncate" style="max-width: 200px;">{{ tx.description }}</td>
                                        <td class="text-end pe-4 fw-black" :class="isExpense(tx.type) ? 'text-danger' : 'text-success'">
                                            {{ isExpense(tx.type) ? '-' : '+' }}{{ formatCurrency(tx.amount) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Infinite Scroll Observer -->
                        <div v-if="hasMore" id="observer-target" class="py-5 text-center text-muted">
                            <div v-if="loadingMore" class="spinner-border spinner-border-sm" role="status"></div>
                            <span v-else class="small fw-bold uppercase tracking-widest opacity-25">Cargando más resultados...</span>
                        </div>
                    </div>
                </div>

                <!-- Top Lists -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                        <h6 class="fw-black text-dark mb-4 uppercase tracking-widest text-danger" style="font-size: 0.75rem;">TOP GASTOS</h6>
                        <div v-for="item in topExpenses" :key="item.category" class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-bold text-dark">{{ item.category }}</span>
                                <span class="small fw-black text-danger">{{ formatCurrency(item.total) }}</span>
                            </div>
                            <div class="progress rounded-pill" style="height: 4px;">
                                <div class="progress-bar bg-danger" :style="{ width: (item.total / summary.total_expense * 100) + '%' }"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <h6 class="fw-black text-dark mb-4 uppercase tracking-widest text-success" style="font-size: 0.75rem;">TOP INGRESOS</h6>
                        <div v-for="item in topIncome" :key="item.category" class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-bold text-dark">{{ item.category }}</span>
                                <span class="small fw-black text-success">{{ formatCurrency(item.total) }}</span>
                            </div>
                            <div class="progress rounded-pill" style="height: 4px;">
                                <div class="progress-bar bg-success" :style="{ width: (item.total / summary.total_income * 100) + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'ExpensesIndex',
    props: {
        filters: Object,
        summary: Object,
        charts: Object,
        transactionsData: Object,
        portfolios: Array,
        categories: Array,
        topExpenses: Array,
        topIncome: Array,
        minDate: String
    },
    data() {
        return {
            dateFilters: {
                start_date: this.filters.start_date,
                end_date: this.filters.end_date,
            },
            allTransactions: [...this.transactionsData.data],
            loadingMore: false,
            hasMore: !!this.transactionsData.next_page_url,
            currentPage: 1,
            observer: null
        }
    },
    mounted() {
        this.setupInfiniteScroll();
    },
    beforeDestroy() {
        if (this.observer) this.observer.disconnect();
    },
    methods: {
        formatCurrency(value) {
            return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value);
        },
        isExpense(type) {
            return ['expense', 'transfer_out'].includes(type);
        },
        applyFilters() {
            const url = new URL(window.location.href);
            url.searchParams.set('start_date', this.dateFilters.start_date);
            url.searchParams.set('end_date', this.dateFilters.end_date);
            window.location.href = url.toString();
        },
        async loadMore() {
            if (this.loadingMore || !this.hasMore) return;
            this.loadingMore = true;
            try {
                const response = await axios.get('/api/expenses/transactions', {
                    params: {
                        page: this.currentPage + 1,
                        start_date: this.dateFilters.start_date,
                        end_date: this.dateFilters.end_date
                    }
                });
                const paginator = response.data;
                this.allTransactions = [...this.allTransactions, ...paginator.data];
                this.currentPage = paginator.current_page;
                this.hasMore = !!paginator.next_page_url;
            } catch (e) {
                console.error('Error loading more:', e);
            } finally {
                this.loadingMore = false;
            }
        },
        setupInfiniteScroll() {
            this.observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting) this.loadMore();
            }, { rootMargin: '200px' });
            
            this.$nextTick(() => {
                const target = document.getElementById('observer-target');
                if (target) this.observer.observe(target);
            });
        },
        openNewTransaction() {
            console.log('Open transaction modal');
            // This would normally open a global modal or redirect
            window.location.href = '/transactions';
        },
        handleExport() {
            window.location.href = `/transactions/export?format=csv&start_date=${this.dateFilters.start_date}&end_date=${this.dateFilters.end_date}`;
        }
    }
}
</script>

<style scoped>
.fw-black { font-weight: 900; }
.rounded-4 { border-radius: 1rem !important; }
.transition-hover:hover {
    background-color: rgba(0,0,0,0.02) !important;
}
.italic { font-style: italic; }
.uppercase { text-transform: uppercase; }
.tracking-widest { letter-spacing: 0.1em; }
</style>
