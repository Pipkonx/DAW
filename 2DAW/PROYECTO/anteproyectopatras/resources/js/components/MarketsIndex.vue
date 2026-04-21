<template>
    <div class="py-5 bg-light min-vh-100">
        <div class="container pb-5">
            <header class="mb-5">
                <h1 class="display-5 fw-black text-dark mb-2">Explorador de Mercados</h1>
                <p class="text-muted fw-bold small text-uppercase tracking-widest">Datos en tiempo real y tendencias globales</p>
            </header>

            <!-- Categoría: Acciones -->
            <section class="mb-5">
                <div class="d-flex align-items-baseline mb-4 border-bottom pb-2">
                    <h2 class="fw-black h3 text-dark me-3">Acciones</h2>
                    <span class="badge bg-dark text-white rounded-pill small">Wall Street</span>
                </div>
                
                <div class="row g-4">
                    <!-- Ganadores -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                                <h5 class="fw-bold text-success d-flex align-items-center mb-0">
                                    <i class="bi bi-graph-up-arrow me-2"></i> Ganadores
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div v-for="stock in stocks.winners" :key="stock.ticker" class="market-row d-flex align-items-center p-2 rounded-3 mb-2 transition-hover">
                                    <div class="asset-icon me-3 shadow-sm rounded-circle bg-white d-flex align-items-center justify-content-center overflow-hidden" style="width: 40px; height: 40px;">
                                        <img v-if="stock.image" :src="stock.image" class="w-75 h-75 object-fit-contain">
                                        <span v-else class="fw-bold small text-muted">{{ stock.ticker.substring(0,2) }}</span>
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <a :href="'/assets/' + stock.ticker" class="text-dark fw-bold text-decoration-none small d-block text-truncate">{{ stock.ticker }}</a>
                                        <div class="text-muted small text-truncate" style="font-size: 10px;">{{ stock.name }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold small">{{ formatCurrency(stock.price) }}</div>
                                        <div class="text-success fw-bold" style="font-size: 10px;">+{{ stock.change_percent }}%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Perdedores -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                                <h5 class="fw-bold text-danger d-flex align-items-center mb-0">
                                    <i class="bi bi-graph-down-arrow me-2"></i> Perdedores
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div v-for="stock in stocks.losers" :key="stock.ticker" class="market-row d-flex align-items-center p-2 rounded-3 mb-2 transition-hover">
                                    <div class="asset-icon me-3 shadow-sm rounded-circle bg-white d-flex align-items-center justify-content-center overflow-hidden" style="width: 40px; height: 40px;">
                                        <img v-if="stock.image" :src="stock.image" class="w-75 h-75 object-fit-contain">
                                        <span v-else class="fw-bold small text-muted">{{ stock.ticker.substring(0,2) }}</span>
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <a :href="'/assets/' + stock.ticker" class="text-dark fw-bold text-decoration-none small d-block text-truncate">{{ stock.ticker }}</a>
                                        <div class="text-muted small text-truncate" style="font-size: 10px;">{{ stock.name }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold small">{{ formatCurrency(stock.price) }}</div>
                                        <div class="text-danger fw-bold" style="font-size: 10px;">{{ stock.change_percent }}%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Más buscados -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                                <h5 class="fw-bold text-primary d-flex align-items-center mb-0">
                                    <i class="bi bi-fire me-2"></i> Hot Trends
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div v-for="stock in stocks.most_searched" :key="stock.ticker" class="market-row d-flex align-items-center p-2 rounded-3 mb-2 transition-hover">
                                    <div class="asset-icon me-3 shadow-sm rounded-circle bg-white d-flex align-items-center justify-content-center overflow-hidden" style="width: 40px; height: 40px;">
                                        <img v-if="stock.image" :src="stock.image" class="w-75 h-75 object-fit-contain">
                                        <span v-else class="fw-bold small text-muted">{{ stock.ticker.substring(0,2) }}</span>
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <a :href="'/assets/' + stock.ticker" class="text-dark fw-bold text-decoration-none small d-block text-truncate">{{ stock.ticker }}</a>
                                        <div class="text-muted small text-truncate" style="font-size: 10px;">{{ stock.name }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold small">{{ formatCurrency(stock.price) }}</div>
                                        <div :class="stock.change_percent >= 0 ? 'text-success' : 'text-danger'" class="fw-bold" style="font-size: 10px;">
                                            {{ stock.change_percent >= 0 ? '+' : '' }}{{ stock.change_percent }}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Categoría: Crypto -->
            <section class="mb-5">
                <div class="d-flex align-items-baseline mb-4 border-bottom pb-2">
                    <h2 class="fw-black h3 text-dark me-3">Criptomonedas</h2>
                    <span class="badge bg-warning text-dark rounded-pill small">Blockchain</span>
                </div>
                
                <div class="row g-4">
                    <div v-for="(group, key) in crypto" :key="key" class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                                <h5 class="fw-bold text-dark text-capitalize mb-0">{{ key === 'largest' ? 'Top Capitalización' : (key === 'popular' ? 'Viral' : 'Recientes') }}</h5>
                            </div>
                            <div class="card-body p-4">
                                <div v-for="coin in group" :key="coin.ticker" class="market-row d-flex align-items-center p-2 rounded-3 mb-2 transition-hover">
                                    <img :src="coin.image" class="rounded-circle me-3 shadow-sm bg-white" width="32" height="32">
                                    <div class="flex-grow-1 min-width-0">
                                        <a :href="'/assets/' + coin.ticker" class="text-dark fw-bold text-decoration-none small d-block text-truncate">{{ coin.ticker }}</a>
                                        <div class="text-muted small text-truncate" style="font-size: 10px;">{{ coin.name }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold small">{{ formatCurrency(coin.price) }}</div>
                                        <div :class="coin.change_percent >= 0 ? 'text-success' : 'text-danger'" class="fw-bold" style="font-size: 10px;">
                                            {{ coin.change_percent >= 0 ? '+' : '' }}{{ coin.change_percent.toFixed(2) }}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
export default {
    name: 'MarketsIndex',
    props: {
        stocks: Object,
        crypto: Object,
        etfs: Object,
        funds: Object
    },
    methods: {
        formatCurrency(value) {
            return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
        }
    }
}
</script>

<style scoped>
.fw-black { font-weight: 900; }
.rounded-4 { border-radius: 1rem !important; }
.transition-hover {
    transition: all 0.2s ease;
}
.market-row:hover {
    background-color: #f8fafc !important;
    transform: translateX(4px);
}
.asset-icon {
    border: 1px solid #f1f5f9;
}
</style>
