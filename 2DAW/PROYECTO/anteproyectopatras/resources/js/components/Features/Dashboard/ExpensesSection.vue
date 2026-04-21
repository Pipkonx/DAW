<template>
    <div class="h-100 d-flex flex-column">
        <div class="d-flex align-items-center justify-content-between mb-3 px-1">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <span class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-wallet2" style="font-size: 1rem;"></i>
                </span>
                Gastos {{ rangeLabels[currentRange] }}
            </h5>
            
            <!-- Selector de Rango Temporal -->
            <div class="btn-group btn-group-sm bg-light p-1 rounded-3">
                <button 
                    v-for="r in [{id: 'month', label: 'Mes'}, {id: 'year', label: 'Año'}, {id: 'all', label: 'Todo'}]" 
                    :key="r.id"
                    @click="setRange(r.id)"
                    class="btn btn-sm border-0 rounded-2 px-3 fw-bold"
                    :class="currentRange === r.id ? 'btn-white shadow-sm text-primary' : 'text-muted'"
                >
                    {{ r.label }}
                </button>
            </div>
        </div>

        <div v-if="sortedCategories.length > 0" class="flex-grow-1 d-flex flex-column gap-4">
             <!-- Gráfico de Distribución (Medio Quesito) -->
             <div class="card border-0 shadow-sm rounded-4 p-4 d-flex flex-column align-items-center justify-content-center bg-white position-relative" style="min-height: 250px;">
                <div class="position-relative d-flex align-items-center justify-content-center pt-4" :class="{ 'blur-sm': isPrivacyMode }">
                    <div style="width: 250px; height: 180px;">
                         <canvas ref="expensesChart"></canvas>
                    </div>
                    
                    <!-- Centro del Quesito (Total) -->
                    <div class="position-absolute text-center mt-5 pt-3 pointer-events-none">
                        <span class="extra-small text-uppercase tracking-widest text-muted fw-bold d-block">Total Gastado</span>
                        <span class="h4 fw-black text-primary mt-1">
                            {{ isPrivacyMode ? '****' : formatCurrency(rangeTotal) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Desglose Tabular -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-2 text-muted fw-bold uppercase border-0" style="font-size: 0.65rem;">Categoría</th>
                                <th class="py-2 text-end text-muted fw-bold uppercase border-0" style="font-size: 0.65rem;">Total</th>
                                <th class="pe-4 py-2 text-end text-muted fw-bold uppercase border-0" style="font-size: 0.65rem;">%</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="cat in sortedCategories.slice(0, 5)" :key="cat.category">
                                <td class="ps-4 py-2 fw-bold text-dark">{{ cat.category }}</td>
                                <td class="py-2 text-end text-primary fw-black">{{ isPrivacyMode ? '****' : formatCurrency(cat.total) }}</td>
                                <td class="pe-4 py-2 text-end text-muted fw-bold">
                                    {{ isPrivacyMode ? '****' : (rangeTotal > 0 ? ((cat.total / rangeTotal) * 100).toFixed(1) : 0) }}%
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-light bg-opacity-50 border-0 text-center py-2">
                    <a :href="detailedLink" class="extra-small fw-black text-primary text-uppercase tracking-widest text-decoration-none">
                        Ver análisis detallado <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Estado Vacío -->
        <div v-else class="card border-0 shadow-sm rounded-4 flex-grow-1 p-5 text-center d-flex flex-column align-items-center justify-content-center bg-white">
            <div class="bg-light p-3 rounded-circle mb-3">
                <i class="bi bi-file-earmark-x text-muted" style="font-size: 1.5rem;"></i>
            </div>
            <h6 class="fw-bold text-dark mb-1">Sin gastos registrados</h6>
            <p class="text-muted small mb-0 px-4">Prueba a añadir transacciones en este rango temporal.</p>
        </div>
    </div>
</template>

<script>
import Chart from 'chart.js/auto';
import { formatCurrency } from '@/Utils/formatting';

export default {
    name: 'ExpensesSection',
    props: {
        expenses: Object,
        range: { type: String, default: 'month' },
        isPrivacyMode: Boolean
    },
    data() {
        return {
            currentRange: this.range,
            chart: null,
            rangeLabels: { month: 'del Mes', year: 'del Año', all: 'Total' }
        }
    },
    computed: {
        sortedCategories() {
            const categories = this.expenses.ranges[this.currentRange]?.byCategory || [];
            return [...categories].sort((a, b) => b.total - a.total);
        },
        rangeTotal() {
            return this.expenses.ranges[this.currentRange]?.total || 0;
        },
        detailedLink() {
            const rangeData = this.expenses.ranges[this.currentRange];
            if (!rangeData) return '/expenses';
            return `/expenses?start_date=${rangeData.start}&end_date=${rangeData.end}`;
        }
    },
    mounted() {
        this.renderChart();
    },
    watch: {
        currentRange() { this.renderChart(); },
        expenses: { handler() { this.renderChart(); }, deep: true }
    },
    methods: {
        formatCurrency(val) { return formatCurrency(val); },
        setRange(r) {
            this.currentRange = r;
            this.$emit('update:range', r);
        },
        renderChart() {
            if (!this.$refs.expensesChart) return;
            if (this.chart) this.chart.destroy();
            
            const ctx = this.$refs.expensesChart.getContext('2d');
            const labels = this.sortedCategories.map(c => c.category);
            const data = this.sortedCategories.map(c => c.total);
            
            // Palette
            const colors = ['#f43f5e', '#f97316', '#eab308', '#8b5cf6', '#ec4899', '#0d6efd', '#198754'];

            this.chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors.slice(0, data.length),
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    circumference: 180,
                    rotation: -90,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => {
                                    const percentage = this.rangeTotal > 0 ? ((context.raw / this.rangeTotal) * 100).toFixed(1) : 0;
                                    return ` ${context.label}: ${this.formatCurrency(context.raw)} (${percentage}%)`;
                                }
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
.extra-small { font-size: 0.65rem; }
.btn-white { background-color: #fff; border: 1px solid #eee; }
.blur-sm { filter: blur(5px); }
.uppercase { text-transform: uppercase; }
.pointer-events-none { pointer-events: none; }
</style>
