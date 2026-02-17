<script setup>
import { ref, computed } from 'vue';
import DoughnutChart from '@/Components/Charts/DoughnutChart.vue';
import { formatCurrency } from '@/Utils/formatting';

const props = defineProps({
    allocations: {
        type: Object,
        required: true
    }
});

const allocationType = ref('type'); // type, sector, industry, region, country, currency, asset

const allocationLabels = {
    type: 'Tipo de Activo',
    sector: 'Sector',
    industry: 'Industria',
    region: 'Región',
    country: 'País',
    currency_code: 'Divisa',
    asset: 'Posición Individual'
};

const allocationChartData = computed(() => {
    const data = props.allocations[allocationType.value] || [];
    return {
        labels: data.map(d => d.label),
        datasets: [{
            data: data.map(d => d.value),
            backgroundColor: data.map(d => d.color || '#cbd5e1'),
            borderWidth: 0
        }]
    };
});

const allocationChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 12, usePointStyle: true, padding: 15 } },
        tooltip: {
            callbacks: {
                label: (context) => {
                    const val = context.parsed;
                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                    const pct = ((val / total) * 100).toFixed(1) + '%';
                    return `${context.label}: ${formatCurrency(val)} (${pct})`;
                }
            }
        }
    }
};
</script>

<template>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col h-[450px] dark:bg-slate-800 dark:border-slate-700">
        <div class="flex flex-col mb-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Distribución</h3>
                
                <select 
                    v-model="allocationType"
                    class="text-xs border-slate-200 rounded-lg text-slate-600 focus:border-blue-500 focus:ring-blue-500 py-1 pl-2 pr-8 dark:bg-slate-700 dark:border-slate-600 dark:text-slate-300"
                >
                    <option v-for="(label, key) in allocationLabels" :key="key" :value="key">
                        {{ label }}
                    </option>
                </select>
            </div>
        </div>

        <div class="flex-grow relative w-full">
            <div v-if="!allocations[allocationType] || allocations[allocationType].length === 0" class="h-full flex items-center justify-center text-slate-400 text-sm">
                No hay datos disponibles
            </div>
            <DoughnutChart v-else :data="allocationChartData" :options="allocationChartOptions" />
        </div>
    </div>
</template>
