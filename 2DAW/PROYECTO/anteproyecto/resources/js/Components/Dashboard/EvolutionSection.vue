<script setup>
/**
 * EvolutionSection - Dashboard Component
 * 
 * Este componente encapsula el gráfico de líneas de evolución financiera.
 * Permite alternar entre 'Patrimonio Total' y 'Por Cartera', 
 * así como entre 'Valor (€)' y 'Rendimiento (%)'.
 */
import { computed } from 'vue';
import { formatCurrency } from '@/Utils/formatting';
import LineChart from '@/Components/Charts/LineChart.vue';
import InfoTooltip from '@/Components/InfoTooltip.vue';

const props = defineProps({
    charts: {
        type: Object,
        required: true // { netWorthLabels, netWorthData, netWorthYields, portfolioHistory }
    },
    portfolios: {
        type: Array,
        required: true
    },
    chartMode: {
        type: String,
        default: 'global' // 'global' | 'portfolios'
    },
    displayMode: {
        type: String,
        default: 'value' // 'value' | 'percent'
    },
    isPrivacyMode: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:chartMode', 'update:displayMode']);

// Procesamiento de datos para el gráfico de líneas (Área con Gradiente suave)
const chartData = computed(() => {
    const isGlobal = props.chartMode === 'global';
    const isPercent = props.displayMode === 'percent';
    
    if (isGlobal) {
        // Modo Global: Una sola línea de patrimonio total (Estilo Área)
        const data = isPercent 
            ? props.charts.netWorthYields || props.charts.netWorthData.map(() => 0) 
            : props.charts.netWorthData;

        return {
            labels: props.charts.netWorthLabels,
            datasets: [
                {
                    label: isPercent ? 'Rendimiento (%)' : 'Patrimonio Neto (€)',
                    backgroundColor: isPercent ? 'rgba(14, 165, 233, 0.15)' : 'rgba(99, 102, 241, 0.15)', // Sky para %
                    borderColor: isPercent ? '#0ea5e9' : '#6366f1',
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: isPercent ? '#0ea5e9' : '#6366f1',
                    pointBorderWidth: 2,
                    pointRadius: 0, // Ocultar puntos por defecto para limpieza
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: isPercent ? '#0ea5e9' : '#6366f1',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 2,
                    fill: true,
                    tension: 0.5, // Curva más suave
                    data: data
                }
            ]
        };
    } else {
        // Modo Por Cartera: Múltiples líneas
        const datasets = [];
        const colors = [
            '#6366f1', // Indigo 500
            '#3b82f6', // Blue 500
            '#0ea5e9', // Sky 500
            '#06b6d4', // Cyan 500
            '#818cf8', // Indigo 400
            '#60a5fa', // Blue 400
            '#38bdf8'  // Sky 400
        ];

        
        let i = 0;
        for (const [id, history] of Object.entries(props.charts.portfolioHistory || {})) {
            const portfolio = props.portfolios.find(p => p.id == id) || { name: 'Sin Cartera' };
            const data = isPercent ? history.yields : history.values;
            const color = colors[i % colors.length];
            
            datasets.push({
                label: portfolio.name + (isPercent ? ' (%)' : ' (€)'),
                borderColor: color,
                backgroundColor: 'rgba(255, 255, 255, 0)',
                borderWidth: 2,
                pointRadius: 0,
                pointHoverRadius: 5,
                pointBackgroundColor: color,
                pointBorderColor: '#ffffff',
                tension: 0.45,
                data: data
            });
            i++;
        }
        return {
            labels: props.charts.netWorthLabels,
            datasets: datasets
        };
    }
});

// Opciones del gráfico de líneas optimizadas
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    layout: {
        padding: {
            top: 20,
            bottom: 20,
            left: 10,
            right: 15
        }
    },
    interaction: {
        mode: 'index',
        intersect: false,
    },
    plugins: {
        legend: { 
            display: props.chartMode === 'portfolios', 
            position: 'top', 
            align: 'end',
            labels: {
                usePointStyle: true,
                pointStyle: 'circle',
                padding: 15,
                font: { size: 12, weight: '500' },
                color: '#64748b'
            }
        },
        tooltip: {
            enabled: true,
            backgroundColor: 'rgba(15, 23, 42, 0.9)', // Slate 900 con transparencia
            titleColor: '#f8fafc',
            bodyColor: '#f8fafc',
            padding: 12,
            cornerRadius: 10,
            displayColors: true,
            usePointStyle: true,
            borderColor: 'rgba(255, 255, 255, 0.1)',
            borderWidth: 1,
            callbacks: {
                label: (context) => {
                    const label = context.dataset.label || '';
                    const value = context.parsed.y;
                    if (props.displayMode === 'percent') {
                        return ` ${label}: ${value >= 0 ? '+' : ''}${value.toFixed(2)}%`;
                    }
                    return ` ${label}: ${formatCurrency(value)}`;
                }
            }
        }
    },
    scales: {
        y: {
            grid: { 
                color: 'rgba(241, 245, 249, 0.5)',
                drawBorder: false 
            },
            ticks: {
                callback: (value) => {
                    if (props.displayMode === 'percent') {
                        return (value >= 0 ? '+' : '') + value.toFixed(1) + '%';
                    }
                    // Usar abreviaciones para limpieza
                    if (value >= 1000) return (value / 1000).toFixed(1) + 'k€';
                    return value + '€';
                },
                font: { size: 10, weight: '500' },
                color: '#94a3b8',
                maxTicksLimit: 6
            },
            border: { display: false }
        },
        x: {
            grid: { display: false },
            ticks: { 
                font: { size: 10, weight: '500' }, 
                color: '#94a3b8',
                maxRotation: 0
            },
            border: { display: false }
        }
    }
};


// Emisión de cambios de estado
const updateMode = (val) => emit('update:chartMode', val);
const updateDisplay = (val) => emit('update:displayMode', val);
</script>

<template>
    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <div class="flex items-center">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Evolución Financiera</h3>
                <InfoTooltip text="Visualiza cómo ha crecido tu patrimonio o tus carteras en los últimos 6 meses." />
            </div>
            
            <!-- Toggles y Controles -->
            <div class="flex flex-wrap gap-2">
                <!-- Modo: Global vs Carteras -->
                <div class="bg-slate-100 dark:bg-slate-700 p-1 rounded-lg flex text-sm font-medium">
                    <button 
                        @click="updateMode('global')"
                        class="px-4 py-1.5 rounded-md transition-all"
                        :class="chartMode === 'global' ? 'bg-white dark:bg-slate-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                    >
                        Total
                    </button>
                    <button 
                        @click="updateMode('portfolios')"
                        class="px-4 py-1.5 rounded-md transition-all"
                        :class="chartMode === 'portfolios' ? 'bg-white dark:bg-slate-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                    >
                        Carteras
                    </button>
                </div>

                <!-- Modo: Valor vs Rendimiento -->
                <div class="bg-slate-100 dark:bg-slate-700 p-1 rounded-lg flex text-sm font-medium ml-0 sm:ml-4">
                    <button 
                        @click="updateDisplay('value')"
                        class="px-4 py-1.5 rounded-md transition-all"
                        :class="displayMode === 'value' ? 'bg-white dark:bg-slate-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                    >
                        Valor
                    </button>
                    <button 
                        @click="updateDisplay('percent')"
                        class="px-4 py-1.5 rounded-md transition-all"
                        :class="displayMode === 'percent' ? 'bg-white dark:bg-slate-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                    >
                        Rendimiento
                    </button>
                </div>
            </div>
        </div>

        <div class="h-[300px] w-full relative" :class="{ 'blur-sm select-none': isPrivacyMode }">
            <LineChart :data="chartData" :options="chartOptions" />
        </div>
    </div>
</template>
