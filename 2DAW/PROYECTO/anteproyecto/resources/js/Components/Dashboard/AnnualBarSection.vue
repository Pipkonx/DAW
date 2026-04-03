<script setup>
/**
 * AnnualBarSection - Dashboard Component
 * 
 * Este componente muestra el rendimiento anualizado (beneficio neto histórico)
 * en un gráfico de barras vertical. Permite una visión rápida del crecimiento
 * por año y de la rentabilidad absoluta.
 */
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { formatCurrency } from '@/Utils/formatting';
import BarChart from '@/Components/Charts/BarChart.vue';
import InfoTooltip from '@/Components/InfoTooltip.vue';

const props = defineProps({
    data: {
        type: Object,
        required: true // Expected: { labels: ['2023', '2024'], data: [1500, 2800] }
    },
    isPrivacyMode: {
        type: Boolean,
        default: false
    }
});

// Preparación de datos para Chart.js
const chartData = computed(() => {
    return {
        labels: props.data.labels,
        datasets: [
            {
                label: 'Beneficio Neto Anual (€)',
                data: props.data.data,
                backgroundColor: props.data.data.map(val => val >= 0 ? 'rgba(16, 185, 129, 0.8)' : 'rgba(244, 63, 94, 0.8)'),
                borderColor: props.data.data.map(val => val >= 0 ? '#10b981' : '#f43f5e'),
                borderWidth: 1,
                borderRadius: 8,
                hoverBackgroundColor: props.data.data.map(val => val >= 0 ? '#059669' : '#e11d48'),
                barThickness: 32, // Barras más estilizadas
            }
        ]
    };
});

// Opciones premium para el gráfico de barras
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.9)',
            padding: 12,
            cornerRadius: 10,
            callbacks: {
                label: (context) => ` Beneficio: ${formatCurrency(context.parsed.y)}`
            }
        }
    },
    scales: {
        y: {
            grid: { color: 'rgba(148, 163, 184, 0.05)', drawBorder: false },
            ticks: { 
                color: '#94a3b8', 
                font: { size: 10, weight: '500' },
                callback: (value) => formatCurrency(value)
            }
        },
        x: {
            grid: { display: false },
            ticks: { 
                color: '#64748b', 
                font: { size: 11, weight: '700' } 
            }
        }
    }
};
</script>

<template>
    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white leading-none">Rendimiento Histórico</h3>
                <InfoTooltip text="Muestra el beneficio/pérdida neta acumulada al finalizar cada año natural." />
            </div>
            
            <Link 
                :href="route('transactions.performance')" 
                :data="{ view: 'MAX' }"
                class="text-[11px] font-black uppercase tracking-widest text-blue-600 dark:text-blue-400 hover:text-blue-700 underline underline-offset-4 decoration-2 transition-all p-2 -m-2"
            >
                Ver más detalles
            </Link>
        </div>

        <div class="h-[280px] w-full relative" :class="{ 'blur-md select-none pointer-events-none': isPrivacyMode }">
            <BarChart :data="chartData" :options="chartOptions" />
        </div>
    </div>
</template>
