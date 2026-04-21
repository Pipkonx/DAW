<template>
    <div class="py-4 bg-light min-vh-100">
        <div class="container container-narrow">
            <!-- Asset Header -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-0">
                        <div class="asset-logo me-4 shadow-sm rounded-circle bg-white d-flex align-items-center justify-content-center overflow-hidden" style="width: 70px; height: 70px; min-width: 70px;">
                            <img :src="marketAsset.logo_url" class="w-75 h-75 object-fit-contain" @error="marketAsset.logo_url = 'https://ui-avatars.com/api/?name='+marketAsset.ticker">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-baseline mb-1">
                                <h2 class="fw-black text-dark h3 mb-0 me-3">{{ marketAsset.name }}</h2>
                                <span class="badge bg-dark rounded-pill px-3">{{ marketAsset.ticker }}</span>
                            </div>
                            <div class="text-muted small fw-bold text-uppercase tracking-widest">{{ marketAsset.type_label }} • {{ marketAsset.currency }}</div>
                        </div>
                        <div class="text-end ms-4">
                            <div class="display-6 fw-black text-dark mb-0">${{ marketAsset.current_price.toLocaleString() }}</div>
                            <div :class="marketAsset.change_percent >= 0 ? 'text-success' : 'text-danger'" class="fw-bold fs-5">
                                {{ marketAsset.change_percent >= 0 ? '+' : '' }}{{ marketAsset.change_percent }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Grid -->
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <!-- Chart Card Placeholder -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="fw-black text-dark text-uppercase small tracking-widest mb-0">Rendimiento Histórico</h6>
                            <div class="btn-group btn-group-sm bg-light p-1 rounded-pill">
                                <button v-for="r in ['1W', '1M', '1Y', 'MAX']" :key="r"
                                        class="btn btn-sm rounded-pill px-3 fw-bold border-0"
                                        :class="activeRange === r ? 'btn-primary' : 'text-muted'"
                                        @click="activeRange = r">
                                    {{ r }}
                                </button>
                            </div>
                        </div>
                        <div class="chart-container d-flex align-items-center justify-content-center bg-light rounded-4" style="height: 350px;">
                             <!-- Simplified Chart Placeholder -->
                             <div class="text-center opacity-25">
                                 <i class="bi bi-activity display-1"></i>
                                 <p class="small fw-bold uppercase">Gráfico de precios asíncrono</p>
                             </div>
                        </div>
                    </div>

                    <!-- Metrics Grid -->
                    <div class="row g-3">
                        <div class="col-md-3" v-for="m in metrics" :key="m.label">
                            <div class="card border-0 shadow-sm rounded-4 p-3 text-center h-100">
                                <div class="text-muted small fw-bold uppercase tracking-tighter mb-1">{{ m.label }}</div>
                                <div class="fw-black text-dark">{{ m.value }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- User Position -->
                    <div v-if="userPosition" class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-primary text-white overflow-hidden position-relative">
                        <div class="position-absolute top-0 end-0 p-4 opacity-10">
                            <i class="bi bi-cash-stack display-1"></i>
                        </div>
                        <h6 class="fw-bold mb-4 opacity-75">TU POSICIÓN</h6>
                        <div class="mb-4">
                            <div class="h5 mb-1">{{ userPosition.quantity }} {{ marketAsset.ticker }}</div>
                            <div class="h2 fw-black mb-0">${{ userPosition.current_value.toLocaleString() }}</div>
                        </div>
                        <div class="row g-2 pt-3 border-top border-white border-opacity-10">
                            <div class="col-6">
                                <div class="small opacity-75 mb-0">Rendimiento</div>
                                <div class="fw-bold" :class="userPosition.profit_loss >= 0 ? 'text-white' : 'text-warning'">
                                    {{ userPosition.profit_loss >= 0 ? '+' : '' }}{{ userPosition.profit_loss.toLocaleString() }}
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="small opacity-75 mb-0">Promedio</div>
                                <div class="fw-bold">${{ userPosition.avg_buy_price.toFixed(2) }}</div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="card border-0 shadow-sm rounded-4 p-4 mb-4 text-center border-dashed border-2">
                        <div class="mb-3 text-muted opacity-50"><i class="bi bi-wallet2 fs-2"></i></div>
                        <p class="small fw-bold text-muted mb-3">No tienes unidades de este activo.</p>
                        <button class="btn btn-outline-primary btn-sm rounded-pill px-4">Añadir compra</button>
                    </div>

                    <!-- Description Card -->
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h6 class="fw-black text-dark text-uppercase small tracking-widest mb-3">Sobre {{ marketAsset.name }}</h6>
                        <p class="small text-muted mb-0" style="font-size: 13px; line-height: 1.6;">
                            {{ marketAsset.description || 'Este activo financiero está siendo rastreado por el motor de datos de Pipkonx.' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Transactions / Debate Tabs -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                <div class="card-header bg-white border-0 p-0">
                    <ul class="nav nav-tabs nav-fill border-0">
                        <li class="nav-item">
                            <button class="nav-link border-0 text-dark fw-bold py-3 active border-bottom border-primary border-4 rounded-0">Transacciones</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link border-0 text-muted fw-bold py-3">Comunidad</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-0">
                            <thead class="bg-light fs-xs text-muted fw-bold uppercase">
                                <tr>
                                    <th class="border-0 rounded-start">Fecha</th>
                                    <th class="border-0">Tipo</th>
                                    <th class="border-0 text-center">Cantidad</th>
                                    <th class="border-0 text-end">Precio</th>
                                    <th class="border-0 text-end rounded-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="tx in latestTransactions" :key="tx.id">
                                    <td class="small fw-bold">{{ tx.date }}</td>
                                    <td>
                                        <span :class="tx.type === 'buy' ? 'bg-success-subtle text-success border-success-subtle' : 'bg-danger-subtle text-danger border-danger-subtle'" 
                                              class="badge border rounded-pill px-3 py-1 text-uppercase fw-black" style="font-size: 9px;">
                                            {{ tx.type === 'buy' ? 'COMPRA' : 'VENTA' }}
                                        </span>
                                    </td>
                                    <td class="text-center fw-bold">{{ tx.quantity }}</td>
                                    <td class="text-end small">${{ tx.price_per_unit }}</td>
                                    <td class="text-end fw-black">${{ (tx.quantity * tx.price_per_unit).toLocaleString() }}</td>
                                </tr>
                                <tr v-if="latestTransactions.length === 0">
                                    <td colspan="5" class="text-center py-4 text-muted small italic">No hay historial de operaciones</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'AssetShow',
    props: {
        marketAsset: Object,
        chartData: Array,
        userPosition: Object,
        latestTransactions: Array,
        posts: Object
    },
    data() {
        return {
            activeRange: '1Y'
        }
    },
    computed: {
        metrics() {
            return [
                { label: 'Sector', value: this.marketAsset.sector || 'N/A' },
                { label: 'Industria', value: this.marketAsset.industry || 'N/A' },
                { label: 'Capt. Mercado', value: 'High' },
                { label: 'País', value: this.marketAsset.country || 'USA' }
            ]
        }
    }
}
</script>

<style scoped>
.fw-black { font-weight: 900; }
.rounded-4 { border-radius: 1rem !important; }
.container-narrow { max-width: 1000px; }
.bg-success-subtle { background-color: #d1e7dd; }
.bg-danger-subtle { background-color: #f8d7da; }
.border-success-subtle { border-color: #badbcc !important; }
.border-danger-subtle { border-color: #f5c2c7 !important; }
.fs-xs { font-size: 0.65rem; }
</style>
