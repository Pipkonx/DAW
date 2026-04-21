<template>
    <div class="py-4 bg-light min-vh-100">
        <div class="container">
            <!-- Back Link -->
            <div class="mb-4">
                <a href="/social" class="btn btn-link text-muted p-0 text-decoration-none fw-bold small">
                    <i class="bi bi-arrow-left me-1"></i> Volver al Muro
                </a>
            </div>

            <!-- Profile Header Card -->
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden mb-5">
                <div class="row g-0">
                    <!-- Identity column -->
                    <div class="col-lg-4 bg-white border-end p-5">
                        <div class="d-flex align-items-center gap-4 mb-4">
                            <div class="position-relative">
                                <img :src="profile.avatar" class="rounded-4 shadow" style="width: 100px; height: 100px; object-fit: cover; border: 4px solid #fff;">
                                <div class="position-absolute bottom-0 end-0 bg-primary rounded-circle border border-white border-3" style="width: 24px; height: 24px;"></div>
                            </div>
                            <div>
                                <h1 class="h3 fw-black text-dark mb-1 tracking-tighter">{{ profile.name }}</h1>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary rounded-pill px-2 py-1 text-uppercase fw-bold" style="font-size: 0.6rem; letter-spacing: 0.1em;">
                                        {{ profile.type || 'Público' }}
                                    </span>
                                    <span class="text-muted small fw-bold">{{ profile.location }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-light p-4 rounded-4 mb-4 border border-white">
                            <p class="small text-muted mb-0 italic leading-relaxed">
                                "{{ profile.description }}"
                            </p>
                        </div>

                        <button 
                            @click="toggleFollow" 
                            class="btn w-100 py-3 rounded-4 fw-black text-uppercase tracking-widest shadow-sm transition-all"
                            :class="isFollowingLocal ? 'btn-outline-danger' : 'btn-primary'"
                        >
                            {{ isFollowingLocal ? 'Siguiendo' : 'Seguidores' }}
                        </button>
                    </div>

                    <!-- Diversification column -->
                    <div class="col-lg-4 p-5 d-flex flex-column align-items-center justify-content-center bg-light bg-opacity-30">
                        <div class="position-absolute text-center">
                            <div class="text-muted fw-black small text-uppercase tracking-widest mb-1">Activos</div>
                            <div class="h2 fw-black mb-0">{{ holdings.length }}</div>
                        </div>
                        <div style="width: 250px; height: 250px;">
                            <canvas ref="holdingsChart"></canvas>
                        </div>
                    </div>

                    <!-- Stats column -->
                    <div class="col-lg-4 p-5 d-flex flex-column justify-content-center border-start">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="p-3 bg-white rounded-4 border shadow-sm h-100">
                                    <div class="text-primary fw-black small text-uppercase tracking-widest mb-1">Seguidores</div>
                                    <div class="h4 fw-black mb-0">{{ stats.seguidores }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-dark text-white rounded-4 border shadow-sm h-100">
                                    <div class="text-white-50 fw-black small text-uppercase tracking-widest mb-1">CIK Legal</div>
                                    <div class="h5 fw-bold mb-0 text-truncate">{{ profile.cik }}</div>
                                </div>
                            </div>
                            <div class="col-12 mt-4">
                                <div class="p-3 bg-white rounded-4 border border-primary border-opacity-10 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-muted fw-black small text-uppercase tracking-widest">Última Actualización</div>
                                        <div class="fw-bold text-dark">{{ stats.last_report }}</div>
                                    </div>
                                    <div class="text-primary h3 mb-0">
                                        <i class="bi bi-clock-history"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mb-4">
                <ul class="nav nav-pills gap-3">
                    <li class="nav-item">
                        <button class="nav-link px-4 py-2 rounded-pill fw-bold text-uppercase tracking-widest shadow-sm"
                                :class="activeTab === 'cartera' ? 'active bg-dark' : 'bg-white text-muted'"
                                @click="activeTab = 'cartera'">
                            Cartera Actual
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link px-4 py-2 rounded-pill fw-bold text-uppercase tracking-widest shadow-sm"
                                :class="activeTab === 'actividad' ? 'active bg-dark' : 'bg-white text-muted'"
                                @click="activeTab = 'actividad'">
                            Historial
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Data Tables -->
            <div class="card border-0 shadow-sm rounded-5 overflow-hidden bg-white">
                <div class="table-responsive" v-if="activeTab === 'cartera'">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-5 py-4 text-muted fw-black small uppercase tracking-wider border-0">Activo</th>
                                <th class="py-4 text-muted fw-black small uppercase tracking-wider border-0">Ticker</th>
                                <th class="py-4 text-muted fw-black small uppercase tracking-wider border-0">Peso (%)</th>
                                <th class="py-4 text-end text-muted fw-black small uppercase tracking-wider border-0">Market Value</th>
                                <th class="pe-5 py-4 text-end text-muted fw-black small uppercase tracking-wider border-0">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="holding in holdings" :key="holding.symbol" class="transition-hover">
                                <td class="ps-5 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 border p-1 bg-white" style="width: 40px; height: 40px;">
                                            <img :src="'https://financialmodelingprep.com/image-stock/' + holding.symbol + '.png'" @error="onImgError" class="w-100 h-100 object-fit-contain">
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ holding.name }}</div>
                                            <div class="small text-muted fw-bold">{{ formatNumber(holding.shares_number) }} SHRS</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-muted border fw-bold font-monospace">
                                        ${{ holding.symbol }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2" style="min-width: 120px;">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar bg-primary rounded-pill" :style="{ width: holding.weight + '%' }"></div>
                                        </div>
                                        <span class="small fw-bold text-dark">{{ holding.weight }}%</span>
                                    </div>
                                </td>
                                <td class="text-end fw-black font-monospace">
                                    ${{ formatNumber(holding.market_value) }}
                                </td>
                                <td class="pe-5 text-end">
                                    <a :href="'/markets/assets/' + holding.symbol" class="btn btn-sm btn-light rounded-pill px-3 fw-bold border">
                                        Analizar
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive" v-if="activeTab === 'actividad'">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-5 py-4 text-muted fw-black small uppercase tracking-wider border-0">Fecha Reporte</th>
                                <th class="py-4 text-muted fw-black small uppercase tracking-wider border-0">Activo</th>
                                <th class="py-4 text-muted fw-black small uppercase tracking-wider border-0">Tipo</th>
                                <th class="py-4 text-end text-muted fw-black small uppercase tracking-wider border-0">Acciones</th>
                                <th class="pe-5 py-4 text-end text-muted fw-black small uppercase tracking-wider border-0">Impacto (%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="trade in history" :key="trade.id" class="transition-hover">
                                <td class="ps-5 py-3">
                                    <span class="fw-bold text-muted font-monospace small uppercase">{{ trade.filling_date }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-2 border p-1 bg-white" style="width: 24px; height: 24px;">
                                            <img :src="'https://financialmodelingprep.com/image-stock/' + trade.symbol + '.png'" @error="onImgError" class="w-100 h-100 object-fit-contain">
                                        </div>
                                        <span class="fw-black text-dark">${{ trade.symbol }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge border fw-bold text-uppercase px-2 py-1" :class="getTradeColor(trade.change_type)" style="font-size: 0.65rem; letter-spacing: 0.05em;">
                                        {{ getTradeName(trade.change_type) }}
                                    </span>
                                </td>
                                <td class="text-end fw-black font-monospace" :class="trade.change_in_shares >= 0 ? 'text-success' : 'text-danger'">
                                    {{ trade.change_in_shares > 0 ? '+' : '' }}{{ formatNumber(trade.change_in_shares) }}
                                </td>
                                <td class="pe-5 text-end fw-black">
                                    {{ trade.percent_of_portfolio }}%
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Chart from 'chart.js/auto';

export default {
    name: 'FamousPortfolioShow',
    props: {
        profile: Object,
        holdings: Array,
        history: Array,
        isFollowing: Boolean,
        stats: Object
    },
    data() {
        return {
            activeTab: 'cartera',
            isFollowingLocal: this.isFollowing,
            chart: null
        }
    },
    mounted() {
        this.renderChart();
    },
    methods: {
        formatNumber(num) {
            if (!num) return '0';
            if (num >= 1e9) return (num / 1e9).toFixed(2) + 'B';
            if (num >= 1e6) return (num / 1e6).toFixed(2) + 'M';
            if (num >= 1e3) return (num / 1e3).toFixed(2) + 'K';
            return Number(num).toLocaleString();
        },
        onImgError(e) {
            e.target.src = 'https://ui-avatars.com/api/?name=?&background=random';
        },
        async toggleFollow() {
            try {
                await axios.post(`/famous-portfolios/${this.profile.slug}/follow`);
                this.isFollowingLocal = !this.isFollowingLocal;
            } catch (e) {
                console.error(e);
            }
        },
        getTradeColor(type) {
            const t = (type || '').toLowerCase();
            if (t.includes('new') || t.includes('compra')) return 'bg-success bg-opacity-10 text-success border-success';
            if (t.includes('sold') || t.includes('reduced') || t.includes('venta')) return 'bg-danger bg-opacity-10 text-danger border-danger';
            return 'bg-info bg-opacity-10 text-info border-info';
        },
        getTradeName(type) {
            const t = (type || '').toLowerCase();
            if (t.includes('new')) return 'COMPRA';
            if (t.includes('increased')) return 'INCREMENTO';
            if (t.includes('sold') || t.includes('reduced')) return 'REDUCCIÓN';
            return (type || 'OPERACIÓN').toUpperCase();
        },
        renderChart() {
            const ctx = this.$refs.holdingsChart.getContext('2d');
            const data = this.holdings.slice(0, 10).map(h => h.weight);
            const labels = this.holdings.slice(0, 10).map(h => h.symbol);
            
            this.chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: [
                            '#4f46e5', '#10b981', '#f59e0b', '#ef4444', 
                            '#6366f1', '#06b6d4', '#8b5cf6', '#94a3b8',
                            '#ec4899', '#84cc16'
                        ],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => ` ${context.label}: ${context.raw}%`
                            }
                        }
                    },
                    cutout: '80%'
                }
            });
        }
    }
}
</script>

<style scoped>
.fw-black { font-weight: 900; }
.tracking-tighter { letter-spacing: -0.05em; }
.tracking-widest { letter-spacing: 0.15em; }
.rounded-5 { border-radius: 1.5rem !important; }
.rounded-4 { border-radius: 1rem !important; }
.bg-opacity-30 { --bs-bg-opacity: 0.3; }
.italic { font-style: italic; }
.uppercase { text-transform: uppercase; }
.transition-hover:hover {
    background-color: rgba(0,0,0,0.02) !important;
}
.font-monospace {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}
</style>
