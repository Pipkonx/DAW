<script setup>
import { ref, computed } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PortfolioHeader from '@/Components/Transactions/PortfolioHeader.vue';
import DoughnutChart from '@/Components/Charts/DoughnutChart.vue';
import { formatCurrency, formatPercent } from '@/Utils/formatting';
import { usePrivacy } from '@/Composables/usePrivacy';

const props = defineProps({
    portfolios: Array,
    selectedPortfolioId: [String, Number],
    assets: Array
});

const { isPrivacyMode } = usePrivacy();

// Local State
const hoveredItem = ref(null);
const allocationType = ref('asset'); // asset is "posiciones"
const activeClasses = ref(['Stock', 'Crypto', 'ETF', 'Mutual Fund']); 

const allocationLabels = {
    type: 'Tipo de Activo',
    asset: 'Posiciones',
    region: 'Región',
    sector: 'Sector',
    industry: 'Industria',
    country: 'País',
    currency_code: 'Divisa'
};

const toggleClass = (id) => {
    if (activeClasses.value.includes(id)) {
        activeClasses.value = activeClasses.value.filter(c => c !== id);
    } else {
        activeClasses.value.push(id);
    }
};

const classFilters = [
    { id: 'Stock', label: 'Acciones' },
    { id: 'Crypto', label: 'Criptomonedas' },
    { id: 'ETF', label: 'ETFs' },
    { id: 'Mutual Fund', label: 'Fondos' }
];

const modernPalette = [
    '#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', 
    '#ef4444', '#0ea5e9', '#f97316', '#6366f1',
    '#ec4899', '#14b8a6', '#84cc16', '#a855f7',
    '#06b6d4', '#eab308', '#f43f5e', '#64748b'
];

// Navigation
const switchPortfolio = (id) => {
    router.get(route('transactions.allocation'), { 
        portfolio_id: id 
    }, { preserveState: true, preserveScroll: true });
};

// Frontend live group calculations
const filteredAssets = computed(() => {
    return props.assets.filter(a => {
        if (!a.type) return false;
        const typeLower = a.type.toLowerCase();
        
        let isValid = false;
        if (activeClasses.value.includes('Stock') && typeLower.includes('stock')) isValid = true;
        if (activeClasses.value.includes('Crypto') && typeLower.includes('crypto')) isValid = true;
        if (activeClasses.value.includes('ETF') && typeLower.includes('etf')) isValid = true;
        if (activeClasses.value.includes('Mutual Fund') && (typeLower.includes('fund') || typeLower.includes('mutual'))) isValid = true;
        
        return isValid;
    });
});

const groupedAllocations = computed(() => {
    const groups = {};
    let totalValue = 0;

    filteredAssets.value.forEach(asset => {
        let key = 'Desconocido/Liquidez';
        if (allocationType.value === 'asset') {
            key = asset.name || asset.ticker || 'Sin nombre';
        } else {
            key = asset[allocationType.value] || 'Desconocido';
        }

        const val = Number(asset.current_value) || 0;
        const pl = Number(asset.total_pl) || 0;

        if (!groups[key]) {
            groups[key] = { 
                label: key, 
                value: 0, 
                pl: 0,
                ticker: asset.ticker || asset.isin // Añadido para el enlace de navegación
            };
        }
        groups[key].value += val;
        groups[key].pl += pl;
        totalValue += val;
    });

    let sorted = Object.values(groups).sort((a,b) => b.value - a.value);
    
    sorted = sorted.map((item, idx) => ({
        ...item,
        percentage: totalValue > 0 ? (item.value / totalValue) * 100 : 0,
        color: modernPalette[idx % modernPalette.length],
    }));

    return {
        data: sorted,
        total: totalValue
    };
});

const allocationChartData = computed(() => {
    return {
        labels: groupedAllocations.value.data.map(d => d.label),
        datasets: [{
            data: groupedAllocations.value.data.map(d => d.value),
            backgroundColor: groupedAllocations.value.data.map(d => d.color),
            borderWidth: 0,
            hoverOffset: 15,
            borderRadius: 6,
            spacing: 3
        }]
    };
});

const allocationChartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    layout: { padding: 30 },
    cutout: '75%',
    onHover: (event, elements) => {
        if (elements && elements.length > 0) {
            const index = elements[0].index;
            hoveredItem.value = groupedAllocations.value.data[index];
        } else {
            hoveredItem.value = null;
        }
    },
    plugins: {
         legend: { display: false },
         tooltip: { enabled: false }
     }
}));

</script>

<template>
    <Head title="Distribución de la Cartera" />

    <AuthenticatedLayout>
        <template #header>
            <PortfolioHeader 
                :portfolios="portfolios"
                :selected-portfolio-id="selectedPortfolioId"
                @switch="switchPortfolio"
            />
        </template>

        <div class="py-8 bg-slate-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Header / Filters -->
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" /></svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Análisis de Distribución</h2>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Audita tus ponderaciones en tiempo real</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <button 
                                v-for="cf in classFilters" :key="cf.id"
                                @click="toggleClass(cf.id)"
                                class="px-3 py-1 rounded-full text-xs font-bold transition-all flex items-center gap-1.5 border"
                                :class="activeClasses.includes(cf.id) ? 'bg-indigo-50 border-indigo-200 text-indigo-700 shadow-sm dark:bg-indigo-900/30 dark:border-indigo-700 dark:text-indigo-300' : 'bg-transparent border-slate-200 text-slate-400 dark:border-slate-700 dark:text-slate-500 hover:border-slate-300'"
                            >
                                <svg v-if="activeClasses.includes(cf.id)" class="w-3 h-3 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                <span>{{ cf.label }}</span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="w-full lg:w-56 shrink-0 bg-slate-50 dark:bg-slate-900/50 p-2 rounded-xl">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider pl-1 mb-1">Agrupar por</label>
                        <select 
                            v-model="allocationType"
                            class="w-full text-sm font-semibold border-slate-200 rounded-lg text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 py-2 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200 shadow-sm"
                        >
                            <option v-for="(label, key) in allocationLabels" :key="key" :value="key">
                                {{ label }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Main Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Left: Chart -->
                    <div class="lg:col-span-1 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 flex flex-col justify-center min-h-[500px]">
                        <div v-if="groupedAllocations.data.length === 0" class="flex flex-col items-center justify-center text-slate-400 h-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="text-sm">No hay activos en esta categoría.</span>
                        </div>
                        <div v-else class="relative w-full h-[400px]">
                            <DoughnutChart :data="allocationChartData" :options="allocationChartOptions" />
                            
                            <!-- Center Info -->
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none transition-all duration-300">
                                <template v-if="hoveredItem">
                                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1 text-center px-6 truncate max-w-full block">
                                        {{ hoveredItem.label }}
                                    </span>
                                    <span class="text-3xl font-black text-slate-800 dark:text-white my-1">
                                        {{ isPrivacyMode ? '****' : formatCurrency(hoveredItem.value) }}
                                    </span>
                                    <span class="text-sm font-bold bg-opacity-20 px-3 py-1 rounded-full mt-1" :style="{ color: hoveredItem.color, backgroundColor: hoveredItem.color + '20' }">
                                        {{ hoveredItem.percentage.toFixed(2) }}%
                                    </span>
                                </template>
                                <template v-else>
                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">
                                        Total Filtrado
                                    </span>
                                    <span class="text-3xl font-black text-slate-800 dark:text-white">
                                        {{ isPrivacyMode ? '****' : formatCurrency(groupedAllocations.total) }}
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Vertical Breakdown -->
                    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 flex flex-col max-h-[600px] overflow-hidden">
                        <div class="flex justify-between items-center mb-6 border-b border-slate-100 dark:border-slate-700 pb-4">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Desglose de Pesos</h3>
                            <span class="text-sm font-semibold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 py-1 px-3 rounded-lg">{{ groupedAllocations.data.length }} elementos</span>
                        </div>
                        
                        <div class="flex-1 overflow-y-auto pr-3 space-y-3 custom-scrollbar pb-2">
                            <div v-for="item in groupedAllocations.data" :key="item.label" class="group transition-opacity" :class="{'opacity-40': hoveredItem && hoveredItem.label !== item.label}">
                                
                                <div class="flex justify-between items-end mb-1">
                                    <Link :href="route('assets.show', { ticker: item.ticker || item.label })" class="text-xs font-bold text-slate-700 dark:text-slate-200 truncate pr-4 max-w-[60%] hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                        {{ item.label }}
                                    </Link>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 mt-0.5">{{ isPrivacyMode ? '****' : formatCurrency(item.value) }}</span>
                                        <span class="text-xs font-black text-slate-800 dark:text-white" :style="{ color: item.color }">{{ item.percentage.toFixed(1) }}%</span>
                                    </div>
                                </div>
                                
                                <div class="w-full bg-slate-100 dark:bg-slate-700/50 rounded-full h-1.5 mb-1 flex shadow-inner overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-700 ease-out relative overflow-hidden" 
                                         :style="{ width: item.percentage + '%', backgroundColor: item.color }">
                                    </div>
                                </div>
                                
                                <div class="flex justify-between items-center text-[10px] font-medium tracking-wide text-slate-400">
                                    <span>Rendimiento Real:</span>
                                    <span class="font-bold" :class="item.pl > 0 ? 'text-emerald-500' : (item.pl < 0 ? 'text-rose-500' : 'text-slate-400')">
                                        <span v-if="!isPrivacyMode">
                                            {{ item.pl > 0 ? '+' : '' }}{{ formatCurrency(item.pl) }} 
                                        </span>
                                        <span v-else>****</span>
                                    </span>
                                </div>
                            </div>
                            
                            <div v-if="groupedAllocations.data.length === 0" class="text-center text-slate-400 py-12">
                                Selecciona una categoría con activos para visualizar el desglose.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #475569; }

@keyframes shine {
  100% { transform: translateX(250%); }
}
</style>
