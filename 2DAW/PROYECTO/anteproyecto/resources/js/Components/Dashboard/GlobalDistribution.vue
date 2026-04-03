<script setup>
/**
 * GlobalDistribution - Dashboard Component
 * 
 * Muestra el gráfico doughnut de distribución de patrimonio (Invertido vs Líquido).
 * Encapsula la lógica de visualización de la tasa de inversión.
 */
import { computed } from 'vue';
import { formatCurrency } from '@/Utils/formatting';
import DoughnutChart from '@/Components/Charts/DoughnutChart.vue';
import InfoTooltip from '@/Components/InfoTooltip.vue';

const props = defineProps({
    allocation: {
        type: Object,
        required: true // Espera { labels, values }
    },
    summary: {
        type: Object,
        required: true
    },
    isPrivacyMode: {
        type: Boolean,
        default: false
    }
});

// Configuración de los datos del gráfico ordenados
const chartData = computed(() => {
    const rawData = [
        { label: 'Invertido', value: props.allocation?.values[0] || 0, color: '#3b82f6' }, // Blue
        { label: 'Líquido', value: props.allocation?.values[1] || 0, color: '#10b981' }, // Emerald
        { label: 'Otros', value: props.allocation?.values[2] || 0, color: '#8b5cf6' }  // Violet
    ];

    // Ordenar por valor descendente
    const sortedData = [...rawData].sort((a, b) => b.value - a.value);
    
    return {
        labels: sortedData.map(d => d.label),
        datasets: [{
            data: sortedData.map(d => d.value),
            backgroundColor: sortedData.map(d => d.color),
            borderWidth: 0,
            hoverOffset: 15,
            borderRadius: 10,
            spacing: 5,
            cutout: '78%'
        }]
    };
});


// Opciones del gráfico doughnut optimizadas
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    layout: {
        padding: 20
    },
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.9)',
            padding: 12,
            cornerRadius: 10,
            displayColors: true,
            usePointStyle: true,
            callbacks: {
                label: (context) => {
                    const value = context.parsed;
                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                    return ` ${context.label}: ${formatCurrency(value)} (${percentage})`;
                }
            }
        }
    }
};


// Cálculo de la tasa de inversión
const investmentRate = computed(() => {
    const cash = Number(props.summary.cash) || 0;
    const total = Number(props.summary.investmentsTotal) || 0;
    
    const sum = total + cash;
    if (sum <= 0) return '0.0';
    
    return ((total / sum) * 100).toFixed(1);
});
</script>

<template>
    <div class="space-y-6 h-full flex flex-col">
        <div class="flex items-center justify-between flex-shrink-0">
            <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center">
                <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 p-2 rounded-lg mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                </span>
                Distribución Global
            </h3>
            <InfoTooltip text="Porcentaje de tu patrimonio invertido vs líquido." />
        </div>
        
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex-grow flex flex-col justify-center">
            <!-- Gráfico y Leyenda -->
            <div class="flex flex-col items-center justify-center">
                <!-- Contenedor del gráfico -->
                <div class="relative w-72 h-72 flex-shrink-0" :class="{ 'blur-sm select-none': isPrivacyMode }">
                    <DoughnutChart :data="chartData" :options="chartOptions" />
                    
                    <!-- Texto Central: Tasa de Inversión -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <p class="text-xs text-slate-400 uppercase tracking-widest font-bold text-center leading-tight">Invertido</p>
                        <p class="text-3xl font-black text-slate-800 dark:text-white mt-1">
                            {{ isPrivacyMode ? '****' : investmentRate + '%' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
