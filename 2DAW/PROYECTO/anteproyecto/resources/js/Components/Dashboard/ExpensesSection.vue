<script setup>
/**
 * ExpensesSection - Dashboard Component
 * 
 * Especializado en el análisis de flujo de caja y gastos.
 * Permite alternar entre diferentes rangos temporales (Mes, Año, Todo).
 */
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { formatCurrency } from '@/Utils/formatting';
import DoughnutChart from '@/Components/Charts/DoughnutChart.vue';
import InfoTooltip from '@/Components/InfoTooltip.vue';

const props = defineProps({
    expenses: {
        type: Object,
        required: true
    },
    range: {
        type: String,
        required: true
    },
    isPrivacyMode: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:range']);

// Etiquetas legibles para los rangos
const rangeLabels = {
    month: 'del Mes',
    year: 'del Año',
    all: 'Total'
};

// Datos filtrados y ordenados por el rango actual (Mayor a Menor)
const sortedCategories = computed(() => {
    const categories = props.expenses.ranges[props.range]?.byCategory || [];
    return [...categories].sort((a, b) => b.total - a.total);
});

const rangeTotal = computed(() => props.expenses.ranges[props.range]?.total || 0);

// Configuración del gráfico de distribución de gastos (Medio Quesito / Half Doughnut)
const chartData = computed(() => {
    const labels = sortedCategories.value.map(c => c.category);
    const data = sortedCategories.value.map(c => c.total);
    
    // Modern Vibrant Chart Palette (Dark mode friendly)
    const colors = [
        '#f43f5e', '#f97316', '#eab308', '#8b5cf6', '#ec4899', // Hot colors for expenses
        '#3b82f6', '#10b981', '#0ea5e9'
    ];


    return {
        labels: labels,
        datasets: [{
            data: data,
            backgroundColor: colors.slice(0, data.length),
            borderWidth: 0,
            hoverOffset: 15,
            borderRadius: 4,
            spacing: 2,
            cutout: '75%',
        }]
    };
});

// Opciones del gráfico de medio quesito
const doughnutOptions = {
    responsive: true,
    maintainAspectRatio: false,
    layout: {
        padding: 20
    },
    circumference: 180,
    rotation: -90,
    plugins: {
        legend: {
            display: true,
            position: 'bottom',
            labels: {
                usePointStyle: true,
                pointStyle: 'circle',
                padding: 20,
                color: '#64748b',
                font: { size: 12, weight: '500' }
            }
        },
        tooltip: {
            backgroundColor: '#1e293b',
            titleFont: { size: 14 },
            bodyFont: { size: 13 },
            padding: 12,
            cornerRadius: 8,
            callbacks: {
                label: (context) => {
                    const label = context.label || '';
                    const value = context.parsed;
                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                    return ` ${label}: ${formatCurrency(value)} (${percentage}%)`;
                }
            }
        }
    }
};

// Cambio de rango temporal
const setRange = (range) => {
    emit('update:range', range);
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center">
                <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 p-2 rounded-lg mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </span>
                Gastos {{ rangeLabels[range] }}
            </h3>
            
            <!-- Selector de Rango Temporal -->
            <div class="bg-slate-100 dark:bg-slate-700 p-1 rounded-lg flex text-xs font-medium">
                <button 
                    v-for="r in [
                        { id: 'month', label: 'Mes' },
                        { id: 'year', label: 'Año' },
                        { id: 'all', label: 'Todo' }
                    ]" 
                    :key="r.id"
                    @click="setRange(r.id)"
                    class="px-3 py-1 rounded-md transition-all"
                    :class="props.range === r.id ? 'bg-white dark:bg-slate-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                >
                    {{ r.label }}
                </button>
            </div>
        </div>

        <div v-if="sortedCategories.length > 0" class="space-y-6">
             <!-- Gráfico de Distribución (Medio Quesito) -->
             <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 h-96 relative flex flex-col items-center">
                <div class="relative w-full h-full flex items-center justify-center pt-8" :class="{ 'blur-sm select-none': isPrivacyMode }">
                    <DoughnutChart :data="chartData" :options="doughnutOptions" />
                    
                    <!-- Centro del Quesito (Total) -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center pt-24 pointer-events-none">
                        <span class="text-xs uppercase tracking-widest text-slate-400 font-semibold">Total Gastado</span>
                        <span class="text-3xl font-black text-blue-600 dark:text-blue-400 mt-1">
                            {{ isPrivacyMode ? '****' : formatCurrency(rangeTotal) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Desglose Tabular -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 uppercase font-medium text-xs">
                        <tr>
                            <th class="px-4 py-3">Categoría</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3 text-right">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        <tr v-for="cat in sortedCategories.slice(0, 5)" :key="cat.category" class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                            <td class="px-4 py-3 font-medium text-slate-700 dark:text-slate-300">{{ cat.category }}</td>
                            <td class="px-4 py-3 text-right text-blue-600 dark:text-blue-400 font-bold">{{ isPrivacyMode ? '****' : formatCurrency(cat.total) }}</td>
                            <td class="px-4 py-3 text-right text-slate-500 dark:text-slate-400">
                                {{ isPrivacyMode ? '****' : (rangeTotal > 0 ? ((cat.total / rangeTotal) * 100).toFixed(1) : 0) }}%
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="px-4 py-3 bg-slate-50/50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-700 text-center">
                    <Link 
                        :href="route('expenses.index', { 
                            start_date: props.expenses.ranges[props.range].start, 
                            end_date: props.expenses.ranges[props.range].end 
                        })" 
                        class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 uppercase tracking-widest flex items-center justify-center gap-2 transition-colors"
                    >
                        Ver análisis detallado
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </Link>
                </div>
            </div>
        </div>
        
        <!-- Estado Vacío -->
        <div v-else class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 text-center flex flex-col items-center justify-center h-80">
            <div class="bg-slate-50 dark:bg-slate-500/10 p-4 rounded-full mb-4">
                <svg class="w-8 h-8 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h4 class="text-slate-900 dark:text-white font-medium mb-1">Sin gastos registrados</h4>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Prueba a añadir transacciones en este rango temporal.</p>
        </div>
    </div>
</template>
