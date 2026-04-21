<template>
    <div class="py-5 bg-light min-vh-100">
        <div class="container overflow-hidden">
            <div class="max-w-700 mx-auto">
                <header class="mb-5 text-center">
                    <h1 class="display-5 fw-black text-dark mb-2">Analista de Estrategia IA</h1>
                    <p class="text-muted fw-bold small text-uppercase tracking-widest">Inteligencia Artificial aplicada a tu patrimonio</p>
                </header>

                <!-- Status Info -->
                <div v-if="hasInvestments" class="alert bg-white border-0 shadow-sm rounded-4 p-3 mb-5 d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-info-circle"></i>
                    </div>
                    <div class="small fw-bold text-muted">
                        El Analista IA genera un informe dinámico basado en tu patrimonio actual cada 24 horas.
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="card border-0 shadow-sm rounded-4 p-5 mb-5 text-center">
                    <div class="spinner-border text-primary mb-4" role="status" style="width: 3rem; height: 3rem;"></div>
                    <h5 class="fw-bold mb-2">Analizando tu cartera...</h5>
                    <div class="text-muted small font-italic pulse">
                        {{ loadingSteps[currentStep] }}
                    </div>
                </div>

                <!-- Error State -->
                <div v-if="error" class="alert alert-danger rounded-4 p-4 mb-5 border-0 shadow-sm">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                        <h5 class="mb-0 fw-bold">Atención</h5>
                    </div>
                    <p class="mb-3 small">{{ error }}</p>
                    <button class="btn btn-danger btn-sm rounded-pill px-4 fw-bold" @click="fetchTodayReport">Reintentar</button>
                </div>

                <!-- Empty State (No Investments) -->
                <div v-if="!hasInvestments" class="card border-0 shadow-sm rounded-4 p-5 mb-5 text-center bg-white">
                    <div class="display-3 text-muted opacity-25 mb-4"><i class="bi bi-graph-up-arrow"></i></div>
                    <h4 class="fw-bold mb-3">Cartera sin activos</h4>
                    <p class="text-muted mb-4 px-lg-5">El Analista IA necesita datos de tus inversiones para generar recomendaciones estratégicas. Añade activos a tu patrimonio para comenzar.</p>
                    <a href="/transactions" class="btn btn-primary rounded-pill px-5 fw-bold py-3 shadow-sm">Gestionar Patrimonio</a>
                </div>

                <!-- Analyses List -->
                <div v-if="allAnalyses.length > 0" class="space-y-12 mt-4">
                    <div v-for="(an, index) in allAnalyses" :key="an.id" class="position-relative">
                        <div v-if="index === 0" class="badge bg-primary text-white mb-3 shadow-sm rounded-pill px-3 py-2">
                             MÁS RECIENTE
                        </div>
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                            <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light p-2 rounded-3 me-3">
                                        <i class="bi bi-calendar-event text-primary fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-black text-dark text-capitalize">{{ formatDate(an.date) }}</h6>
                                        <small class="text-muted">Informe Estratégico</small>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                        <i class="bi bi-share"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-4 pt-4">
                                <div class="ai-report-content" v-html="formatReport(an.report)"></div>
                            </div>
                            <div class="card-footer bg-light border-0 py-3 px-4 text-center">
                                <small class="text-muted fw-bold text-uppercase tracking-tighter">Pipkonx Intelligence Engine v2.0</small>
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
    name: 'AiAnalystIndex',
    props: {
        analyses: Array,
        hasInvestments: Boolean,
        userName: String
    },
    data() {
        return {
            allAnalyses: [...this.analyses],
            loading: false,
            error: null,
            currentStep: 0,
            loadingSteps: [
                "Leyendo tus posiciones actuales...",
                "Consultando datos de mercado en tiempo real...",
                "Analizando diversificación y nivel de riesgo...",
                "Evaluando tendencias de los últimos índices...",
                "Buscando referencias de mercado globales...",
                "Generando informe estratégico personalizado con IA..."
            ],
            stepInterval: null
        }
    },
    mounted() {
        if (this.hasInvestments) this.fetchTodayReport();
    },
    methods: {
        formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' });
        },
        formatReport(report) {
            if (!report) return '';
            // Basic markdown-like replacement
            return report
                .replace(/\*\*(.*?)\*\*/g, '<strong class="text-primary">$1</strong>')
                .replace(/\*(.*?)\*/g, '<em class="text-muted">$1</em>')
                .replace(/### (.*?)$/gm, '<h5 class="fw-bold text-dark mt-4 mb-3 border-bottom pb-2">$1</h5>')
                .replace(/## (.*?)$/gm, '<h4 class="fw-black text-indigo mt-5 mb-4 border-start border-4 border-indigo ps-3">$1</h4>')
                .replace(/• (.*?)$/gm, '<div class="d-flex mb-2"><i class="bi bi-lightning-fill text-warning me-2"></i><span>$1</span></div>')
                .replace(/\n\n/g, '<p class="mb-3"></p>');
        },
        startLoadingSteps() {
            this.currentStep = 0;
            this.stepInterval = setInterval(() => {
                if (this.currentStep < this.loadingSteps.length - 1) {
                    this.currentStep++;
                }
            }, 3000);
        },
        stopLoadingSteps() {
            if (this.stepInterval) clearInterval(this.stepInterval);
            this.stepInterval = null;
        },
        async fetchTodayReport() {
            const now = new Date();
            const today = now.toISOString().split('T')[0];
            
            if (this.allAnalyses.some(a => a.date === today)) return;

            this.loading = true;
            this.error = null;
            this.startLoadingSteps();

            try {
                const response = await axios.get('/ai-analyst/report');
                if (response.data.report) {
                    this.allAnalyses.unshift({
                        id: Date.now(),
                        report: response.data.report,
                        date: today,
                        created_at: new Date().toISOString()
                    });
                } else if (response.data.error) {
                    this.error = response.data.error;
                }
            } catch (e) {
                this.error = e.response?.data?.error || "Error de conexión con el motor de IA. Por favor, reintenta.";
            } finally {
                this.loading = false;
                this.stopLoadingSteps();
            }
        }
    }
}
</script>

<style scoped>
.fw-black { font-weight: 900; }
.rounded-4 { border-radius: 1rem !important; }
.max-w-700 { max-width: 700px; }
.text-indigo { color: #6366f1; }
.border-indigo { border-color: #6366f1 !important; }
.pulse { animation: pulse 2s infinite; }
@keyframes pulse {
    0% { opacity: 0.6; }
    50% { opacity: 1; }
    100% { opacity: 0.6; }
}
.ai-report-content {
    line-height: 1.7;
    font-size: 0.95rem;
}
</style>
