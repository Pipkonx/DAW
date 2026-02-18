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

const allocationType = ref('asset'); // type, sector, industry, region, country, currency, asset

const allocationLabels = {
    type: 'Tipo de Activo',
    sector: 'Sector',
    industry: 'Industria',
    region: 'Región',
    country: 'País',
    currency_code: 'Divisa',
    asset: 'Posición Individual'
};

// Generate blue monochromatic palette
const bluePalette = [
    '#1e3a8a', // blue-900
    '#1d4ed8', // blue-700
    '#2563eb', // blue-600
    '#3b82f6', // blue-500
    '#60a5fa', // blue-400
    '#93c5fd', // blue-300
    '#bfdbfe', // blue-200
    '#dbeafe', // blue-100
];

const allocationChartData = computed(() => {
    const data = props.allocations[allocationType.value] || [];
    
    return {
        labels: data.map(d => d.label),
        datasets: [{
            data: data.map(d => d.value),
            backgroundColor: data.map((d, index) => bluePalette[index % bluePalette.length]),
            borderWidth: 0,
            hoverOffset: 4
        }]
    };
});

const hoveredItem = ref(null);

const totalAmount = computed(() => {
    const data = props.allocations[allocationType.value] || [];
    return data.reduce((sum, item) => sum + item.value, 0);
});

const allocationChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '75%',
    onHover: (event, elements) => {
        if (elements && elements.length > 0) {
            const index = elements[0].index;
            const data = props.allocations[allocationType.value][index];
            const total = totalAmount.value;
            const percentage = ((data.value / total) * 100).toFixed(2);
            
            hoveredItem.value = {
                label: data.label,
                value: data.value,
                percentage: percentage + '%'
            };

            // Apply transparency to other segments
            const dataset = event.chart.data.datasets[0];
            const originalColors = props.allocations[allocationType.value].map((d, i) => bluePalette[i % bluePalette.length]);
            
            dataset.backgroundColor = originalColors.map((color, i) => {
                return i === index ? color : color + '4D'; // 4D is approx 30% opacity
            });
            event.chart.update();
        } else {
            if (hoveredItem.value !== null) {
                hoveredItem.value = null;
                
                // Reset colors
                const dataset = event.chart.data.datasets[0];
                dataset.backgroundColor = props.allocations[allocationType.value].map((d, i) => bluePalette[i % bluePalette.length]);
                event.chart.update();
            }
        }
    },
    plugins: {
         legend: { 
             display: false 
         },
         tooltip: { enabled: false }
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
            <div v-else class="relative h-full w-full">
                <DoughnutChart :data="allocationChartData" :options="allocationChartOptions" />
                
                <!-- Center Text Overlay -->
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <template v-if="hoveredItem">
                        <span class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1 text-center px-4 truncate max-w-[70%]">
                            {{ hoveredItem.label }}
                        </span>
                        <span class="text-xl font-bold text-slate-800 dark:text-white mb-1">
                            {{ formatCurrency(hoveredItem.value) }}
                        </span>
                        <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                            {{ hoveredItem.percentage }}
                        </span>
                    </template>
                    <template v-else>
                        <span class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1 text-center px-4 truncate max-w-[70%]">
                            Patrimonio Neto Total
                        </span>
                        <span class="text-2xl font-bold text-slate-800 dark:text-white">
                            {{ formatCurrency(totalAmount) }}
                        </span>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
