<template>
    <div class="py-4">
        <div class="container">
            <!-- Header Section -->
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-dark">Patrimonio Neto</h2>
                    <p class="text-muted small">Gestión de activos y transacciones de inversión.</p>
                </div>
                <div class="d-flex gap-2">
                    <select v-model="localPortfolioId" class="form-select" @change="switchPortfolio">
                        <option value="aggregated">Vista Agregada</option>
                        <option v-for="p in portfolios" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                    <button class="btn btn-primary" @click="openNewTransaction">
                        <i class="bi bi-plus-lg"></i> Transacción
                    </button>
                </div>
            </div>

            <!-- EMPTY STATE -->
            <div v-if="portfolios.length === 0 && assets.length === 0" class="text-center py-5">
                <div class="card border-0 shadow-sm p-5 mx-auto" style="max-width: 400px;">
                    <div class="display-1 text-primary mb-4 opacity-25">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <h4>Comienza tu viaje</h4>
                    <p class="text-muted">Crea tu primera cartera o añade un activo para empezar.</p>
                    <button class="btn btn-primary w-100" @click="openCreatePortfolioModal">
                        Crear mi primera cartera
                    </button>
                </div>
            </div>

            <div v-else class="row g-4">
                <div class="col-lg-8">
                    <!-- Chart Section -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-4">
                                <h5 class="fw-bold">Evolución</h5>
                                <div class="btn-group btn-group-sm">
                                    <button 
                                        v-for="tf in ['1M', '3M', '6M', '1Y', 'MAX']" 
                                        :key="tf"
                                        @click="switchTimeframe(tf)"
                                        class="btn"
                                        :class="filters.timeframe === tf ? 'btn-primary' : 'btn-outline-primary'"
                                    >
                                        {{ tf }}
                                    </button>
                                </div>
                            </div>
                            <!-- Placeholder for Chart -->
                            <div class="bg-light rounded p-5 text-center text-muted" style="height: 300px;">
                                Gráfico de Evolución (Chart.js 3)
                            </div>
                        </div>
                    </div>

                    <!-- Assets Table -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="fw-bold mb-0">Mis Activos</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Activo</th>
                                        <th>Cantidad</th>
                                        <th>Precio Prom.</th>
                                        <th class="text-end pe-4">Valor Actual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="asset in assets" :key="asset.id" @click="filterByAsset(asset)" style="cursor: pointer;">
                                        <td class="ps-4">
                                            <div class="fw-bold">{{ asset.name }}</div>
                                            <div class="small text-muted">{{ asset.ticker }}</div>
                                        </td>
                                        <td>{{ asset.quantity }}</td>
                                        <td class="small">{{ asset.avg_buy_price }}€</td>
                                        <td class="text-end pe-4 fw-bold text-success">{{ asset.current_value }}€</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Transaction History -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">Historial</h5>
                            <button class="btn btn-sm btn-outline-secondary" @click="openExportModal">
                                <i class="bi bi-download"></i> Exportar
                            </button>
                        </div>
                        <div class="table-responsive">
                            <!-- Simplificado -->
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead class="table-light small">
                                    <tr>
                                        <th class="ps-4">Fecha</th>
                                        <th>Tipo</th>
                                        <th>Descripción</th>
                                        <th class="text-end pe-4">Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="tx in transactionsData.data" :key="tx.id">
                                        <td class="ps-4 small text-muted">{{ tx.date }}</td>
                                        <td><span class="badge bg-secondary text-capitalize">{{ tx.type }}</span></td>
                                        <td class="small">{{ tx.description }}</td>
                                        <td class="text-end pe-4 fw-bold" :class="tx.amount >= 0 ? 'text-success' : 'text-danger'">
                                            {{ tx.amount }}€
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Allocation Chart -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Distribución</h5>
                            <div class="bg-light rounded p-5 text-center text-muted" style="height: 250px;">
                                Gráfico de Distribución
                            </div>
                        </div>
                    </div>

                    <!-- Performance Breakdown -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Rendimiento</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Invertido</span>
                                <span class="fw-bold">{{ statusSummary.total_invested }}€</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Plusvalía Total</span>
                                <span :class="statusSummary.total_pl >= 0 ? 'text-success' : 'text-danger'" class="fw-bold">
                                    {{ statusSummary.total_pl }}€
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'TransactionsIndex',
    props: {
        portfolios: Array,
        selectedPortfolioId: [String, Number],
        selectedAssetId: [String, Number, Array],
        statusSummary: Object,
        assets: Array,
        transactionsData: Object,
        chartData: Object,
        allocations: Object,
        filters: Object,
        minDate: String
    },
    data() {
        return {
            localPortfolioId: this.selectedPortfolioId || 'aggregated'
        }
    },
    methods: {
        switchPortfolio() {
            window.location.href = `/transactions?portfolio_id=${this.localPortfolioId}&timeframe=${this.filters.timeframe}`;
        },
        switchTimeframe(tf) {
            window.location.href = `/transactions?portfolio_id=${this.localPortfolioId}&timeframe=${tf}`;
        },
        filterByAsset(asset) {
            // Simplificado para el demo inicial
            window.location.href = `/transactions?portfolio_id=${this.localPortfolioId}&asset_id=${asset.id}&timeframe=${this.filters.timeframe}`;
        },
        openNewTransaction() {
            // TODO: Emitir evento o manejar modal
            console.log('Abrir modal de transacción');
        },
        openCreatePortfolioModal() {
            console.log('Abrir modal de cartera');
        },
        openExportModal() {
            console.log('Abrir modal de exportación');
        }
    }
}
</script>

<style scoped>
.transition-hover:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease;
}
</style>
