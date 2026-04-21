<template>
    <div class="py-5 bg-light min-vh-100">
        <div class="container overflow-hidden pb-5">
            <header class="mb-5 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
                <div>
                    <h1 class="display-5 fw-black text-dark mb-2">Analíticas del Ecosistema</h1>
                    <p class="text-muted fw-bold small text-uppercase tracking-widest">Métricas de Crecimiento y Actividad Social</p>
                </div>
                <div class="px-4 py-3 bg-white border shadow-sm rounded-4 d-flex align-items-center gap-3">
                    <div class="pulse-emerald"></div>
                    <span class="text-emerald-600 fw-black small text-uppercase tracking-tighter">Tiempo Real</span>
                </div>
            </header>

            <!-- KPIs Grid -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white overflow-hidden position-relative">
                        <div class="bg-primary position-absolute top-0 end-0 opacity-10 rounded-circle" style="width: 100px; height: 100px; margin-top: -30px; margin-right: -30px; filter: blur(30px);"></div>
                        <div class="position-relative">
                            <h6 class="text-primary fw-black small text-uppercase tracking-widest mb-3">Usuarios Activos (24h)</h6>
                            <div class="h1 fw-black text-dark mb-0">{{ metrics.totals.active_users_24h }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white overflow-hidden position-relative border-start border-4 border-success">
                        <div class="bg-success position-absolute top-0 end-0 opacity-10 rounded-circle" style="width: 100px; height: 100px; margin-top: -30px; margin-right: -30px; filter: blur(30px);"></div>
                        <div class="position-relative">
                            <h6 class="text-success fw-black small text-uppercase tracking-widest mb-3">Nuevos Posts (24h)</h6>
                            <div class="h1 fw-black text-dark mb-0">{{ metrics.totals.new_posts_24h }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white overflow-hidden position-relative border-start border-4 border-warning">
                        <div class="bg-warning position-absolute top-0 end-0 opacity-10 rounded-circle" style="width: 100px; height: 100px; margin-top: -30px; margin-right: -30px; filter: blur(30px);"></div>
                        <div class="position-relative">
                            <h6 class="text-warning fw-black small text-uppercase tracking-widest mb-3">Volumen Total</h6>
                            <div class="h1 fw-black text-dark mb-0">{{ formatNumber(metrics.totals.total_volume) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <!-- User Growth -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="fw-black text-dark h5 mb-0">Crecimiento de Usuarios</h4>
                                <p class="text-muted small mb-0 fw-bold">Nuevos registros últimos 30 días</p>
                            </div>
                        </div>
                        <div class="chart-container d-flex align-items-end gap-1 mb-3" style="height: 180px;">
                            <div v-for="day in metrics.user_growth" :key="day.date" 
                                 class="flex-grow-1 bg-primary rounded-top transition-all bar-hover"
                                 :style="{ height: getScale(day.count, maxUserGrowth) }"
                                 v-tooltip="day.count + ' usuarios'">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between text-muted fw-bold small text-uppercase tracking-wider fs-xs">
                            <span>Hace 30 días</span>
                            <span>Hoy</span>
                        </div>
                    </div>
                </div>

                <!-- Popular Assets -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <h4 class="fw-black text-dark h5 mb-4">Activos más mencionados</h4>
                        <div class="space-y-4">
                            <div v-for="asset in metrics.popular_assets" :key="asset.ticker" class="asset-row mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small fw-black text-dark">${{ asset.ticker }} <span class="text-muted fw-bold ms-2">{{ asset.name }}</span></span>
                                    <span class="small fw-black text-primary">{{ asset.mentions }}</span>
                                </div>
                                <div class="progress rounded-pill" style="height: 6px;">
                                    <div class="progress-bar bg-primary" :style="{ width: (asset.mentions / metrics.popular_assets[0].mentions * 100) + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Operations Distribution -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <h4 class="fw-black text-dark h5 mb-4">Tipos de Operaciones</h4>
                        <div class="row g-3">
                            <div v-for="tx in metrics.tx_distribution" :key="tx.type" class="col-6">
                                <div class="p-3 bg-light rounded-4 border">
                                    <span class="text-muted small fw-black text-uppercase tracking-widest block mb-1" style="font-size: 8px;">{{ tx.type }}</span>
                                    <div class="h4 fw-black text-dark mb-0">{{ tx.count }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Summary -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-lg rounded-4 p-4 bg-primary text-white h-100 overflow-hidden position-relative">
                        <div class="position-absolute bottom-0 end-0 opacity-10" style="margin-right: -40px; margin-bottom: -40px;">
                            <i class="bi bi-graph-up-arrow display-1" style="font-size: 8rem;"></i>
                        </div>
                        <div class="position-relative">
                            <h4 class="fw-black h5 mb-5 italic">Snapshot de Actividad</h4>
                            <div class="row">
                                <div class="col-6">
                                    <span class="small fw-black text-uppercase text-white-50 tracking-widest text-xs">Posts Totales</span>
                                    <div class="display-5 fw-black">{{ formatNumber(totalPosts) }}</div>
                                </div>
                                <div class="col-6 border-start border-white border-opacity-25 ps-4">
                                    <span class="small fw-black text-uppercase text-white-50 tracking-widest text-xs">Comentarios</span>
                                    <div class="display-5 fw-black">{{ formatNumber(totalComments) }}</div>
                                </div>
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
    name: 'AdminAnalytics',
    props: {
        metrics: Object
    },
    computed: {
        maxUserGrowth() {
            return Math.max(...this.metrics.user_growth.map(d => d.count), 1);
        },
        totalPosts() {
            return this.metrics.post_activity.reduce((a, b) => a + b.count, 0);
        },
        totalComments() {
            return this.metrics.comment_activity.reduce((a, b) => a + b.count, 0);
        }
    },
    methods: {
        formatNumber(num) {
            return new Intl.NumberFormat('es-ES').format(num);
        },
        getScale(value, max) {
            return (value / max * 100) + '%';
        }
    }
}
</script>

<style scoped>
.fw-black { font-weight: 900; }
.rounded-4 { border-radius: 1.25rem !important; }
.text-emerald-600 { color: #10b981 !important; }
.pulse-emerald {
    width: 8px;
    height: 8px;
    background-color: #10b981;
    border-radius: 50%;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}
.bar-hover { opacity: 0.2; }
.bar-hover:hover { opacity: 1; filter: brightness(1.2); }
.italic { font-style: italic; }
.fs-xs { font-size: 0.65rem; }
</style>
