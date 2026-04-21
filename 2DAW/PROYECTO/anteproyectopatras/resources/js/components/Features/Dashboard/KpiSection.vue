<template>
    <div class="row g-4">
        <!-- Tarjeta: Patrimonio -->
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-4 position-relative overflow-hidden">
                <div class="position-absolute h-100 w-100 top-0 end-0 opacity-10 pointer-events-none" style="z-index: 0;">
                    <svg class="position-absolute top-0 end-0 m-4" style="width: 80px; height: 80px; color: #0d6efd;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.95V5h-2.93v1.74c-1.81.44-2.43 1.41-2.43 2.51 0 1.91 1.66 2.52 3.97 3.06 1.77.42 2.34 1.05 2.34 1.81 0 .93-.93 1.54-2.34 1.54-1.47 0-2.09-.73-2.14-1.8h-1.8c.06 1.64 1.13 2.76 2.8 3.08v1.78h2.93v-1.77c1.9-.45 2.51-1.47 2.51-2.67 0-1.99-1.72-2.56-4.03-3.08z"/></svg>
                </div>
                <div class="position-relative" style="z-index: 1;">
                    <div class="d-flex align-items-center mb-2">
                        <p class="text-uppercase tracking-wider text-muted small mb-0 fw-bold">Patrimonio</p>
                    </div>
                    <h3 class="display-6 fw-bold text-dark mb-2">{{ isPrivacyMode ? '****' : formatCurrency(summary.netWorth) }}</h3>
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <div class="fw-bold" :class="(summary.investmentsTotal - summary.investmentsCost) >= 0 ? 'text-success' : 'text-danger'">
                            <span v-if="!isPrivacyMode">
                                {{ (summary.investmentsTotal - summary.investmentsCost) >= 0 ? '▲' : '▼' }} 
                                {{ formatCurrency(Math.abs(summary.investmentsTotal - summary.investmentsCost)) }}
                            </span>
                            <span v-else>****</span>
                        </div>
                        <div v-if="!isPrivacyMode" class="badge rounded-pill fw-bold" :class="(summary.investmentsYield || 0) >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'">
                            {{ (summary.investmentsYield || 0).toFixed(2) }}%
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-top small d-flex align-items-center">
                        <span class="text-muted">Ahorros: </span>
                        <span class="fw-bold ms-1 text-dark">{{ isPrivacyMode ? '****' : formatCurrency(summary.cash) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta: Total Inversiones -->
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-4 position-relative overflow-hidden">
                <div class="position-absolute h-100 w-100 top-0 end-0 opacity-10 pointer-events-none" style="z-index: 0;">
                    <svg class="position-absolute top-0 end-0 m-4" style="width: 80px; height: 80px; color: #198754;" fill="currentColor" viewBox="0 0 24 24"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
                </div>
                <div class="position-relative" style="z-index: 1;">
                    <div class="d-flex align-items-center mb-2">
                        <p class="text-uppercase tracking-wider text-muted small mb-0 fw-bold">Inversiones</p>
                    </div>
                    <h3 class="display-6 fw-bold text-success mb-2">{{ isPrivacyMode ? '****' : formatCurrency(summary.investmentsTotal) }}</h3>
                    <div class="text-muted small">Valor total de mercado</div>
                    <div class="mt-4 pt-3 border-top small d-flex align-items-center">
                        <span class="fw-bold text-dark">{{ portfoliosCount }} carteras activas</span>
                        <span class="text-muted ms-1">gestionadas</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta: Gastos Mensuales -->
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-4 position-relative overflow-hidden">
                <div class="position-absolute h-100 w-100 top-0 end-0 opacity-10 pointer-events-none" style="z-index: 0;">
                    <svg class="position-absolute top-0 end-0 m-4" style="width: 80px; height: 80px; color: #dc3545;" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 9c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm6 12H6v-1c0-2 4-3.1 6-3.1s6 1.1 6 3.1v1z"/></svg>
                </div>
                <div class="position-relative" style="z-index: 1;">
                    <div class="d-flex align-items-center mb-2">
                        <p class="text-uppercase tracking-wider text-muted small mb-0 fw-bold">Gastos del Mes</p>
                    </div>
                    <h3 class="display-6 fw-bold text-danger mb-2">{{ isPrivacyMode ? '****' : '-' + formatCurrency(expenses.monthlyTotal) }}</h3>
                    <div class="text-muted small">Flujo de salida este mes</div>
                    <div class="mt-4 pt-3 border-top small d-flex align-items-center">
                        <span class="text-muted">Ingresos: </span>
                        <span class="fw-bold text-success ms-1">{{ isPrivacyMode ? '****' : '+' + formatCurrency(expenses.monthlyIncome) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { formatCurrency } from '@/Utils/formatting';

export default {
    name: 'KpiSection',
    props: {
        summary: {
            type: Object,
            required: true
        },
        expenses: {
            type: Object,
            required: true
        },
        portfoliosCount: {
            type: Number,
            default: 0
        },
        isPrivacyMode: {
            type: Boolean,
            default: false
        }
    },
    methods: {
        formatCurrency(value) {
            return formatCurrency(value);
        }
    }
}
</script>

<style scoped>
.rounded-4 {
    border-radius: 1rem !important;
}
.bg-success-subtle {
    background-color: #d1e7dd !important;
}
.bg-danger-subtle {
    background-color: #f8d7da !important;
}
</style>
