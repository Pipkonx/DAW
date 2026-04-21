<template>
    <div class="h-100 d-flex flex-column">
        <div class="d-flex align-items-center justify-content-between mb-3 px-1">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <span class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-pie-chart-fill" style="font-size: 1rem;"></i>
                </span>
                Distribución Global
            </h5>
            <i class="bi bi-info-circle text-muted" title="Proporción de patrimonio invertido vs liquidez."></i>
        </div>
        
        <div class="card border-0 shadow-sm rounded-4 flex-grow-1 p-4 d-flex flex-column align-items-center justify-content-center bg-white">
            <!-- Gráfico y Texto Central -->
            <div class="position-relative d-flex align-items-center justify-content-center" :class="{ 'blur-sm': isPrivacyMode }">
                <div style="width: 220px; height: 220px;">
                    <canvas ref="distributionChart"></canvas>
                </div>
                
                <!-- Texto Central -->
                <div class="position-absolute text-center mt-2 pointer-events-none">
                    <p class="small text-muted fw-bold text-uppercase tracking-widest mb-0" style="font-size: 0.65rem;">Invertido</p>
                    <p class="h3 fw-black text-dark mb-0">
                        {{ isPrivacyMode ? '****' : investmentRate + '%' }}
                    </p>
                </div>
            </div>

            <!-- Leyenda Simple -->
            <div class="mt-4 w-100 px-2" v-if="!isPrivacyMode">
                <div class="d-flex justify-content-between mb-2 small fw-bold">
                    <span class="text-primary"><i class="bi bi-circle-fill me-1"></i> Invertido</span>
                    <span class="text-dark">{{ formatCurrency(summary.investmentsTotal) }}</span>
                </div>
                <div class="d-flex justify-content-between small fw-bold">
                    <span class="text-success"><i class="bi bi-circle-fill me-1"></i> Líquido</span>
                    <span class="text-dark">{{ formatCurrency(summary.cash) }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Chart from 'chart.js/auto';
import { formatCurrency } from '@/Utils/formatting';

export default {
    name: 'GlobalDistribution',
    props: {
        allocation: Object,
        summary: Object,
        isPrivacyMode: Boolean
    },
    data() {
        return {
            chart: null
        }
    },
    computed: {
        investmentRate() {
            const cash = Number(this.summary.cash) || 0;
            const total = Number(this.summary.investmentsTotal) || 0;
            const sum = total + cash;
            if (sum <= 0) return '0.0';
            return ((total / sum) * 100).toFixed(1);
        }
    },
    mounted() {
        this.renderChart();
    },
    watch: {
        allocation: {
            handler() { this.renderChart(); },
            deep: true
        }
    },
    methods: {
        formatCurrency(val) {
            return formatCurrency(val);
        },
        renderChart() {
            if (this.chart) this.chart.destroy();
            
            const ctx = this.$refs.distributionChart.getContext('2d');
            const values = [
                this.allocation?.values[0] || 0, // Invertido
                this.allocation?.values[1] || 0, // Líquido
                this.allocation?.values[2] || 0  // Otros
            ];

            this.chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Invertido', 'Líquido', 'Otros'],
                    datasets: [{
                        data: values,
                        backgroundColor: ['#0d6efd', '#198754', '#6f42c1'],
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
                                label: (context) => ` ${context.label}: ${this.formatCurrency(context.raw)}`
                            }
                        }
                    },
                    cutout: '75%'
                }
            });
        }
    }
}
</script>

<style scoped>
.rounded-4 { border-radius: 1.25rem !important; }
.fw-black { font-weight: 900; }
.tracking-widest { letter-spacing: 0.1em; }
.blur-sm { filter: blur(5px); }
.pointer-events-none { pointer-events: none; }
</style>
