<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Doughnut } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement, CategoryScale } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale);

const props = defineProps({
    profile: Object,
    holdings: Array,
    history: Array,
    stats: Object,
    isFollowing: Boolean,
});

const activeTab = ref('cartera'); // 'cartera' | 'actividad'
const isSyncing = ref(false);
const lastUpdateSeconds = ref(0);
const liveReturns = ref({}); // Para la simulación de mercado en vivo

let syncInterval = null;

onMounted(() => {
    // Inicializar retornos en vivo con los valores "reales" iniciales
    props.holdings.forEach(h => {
        liveReturns.value[h.symbol] = getBaseReturn(h.symbol);
    });

    syncInterval = setInterval(() => {
        lastUpdateSeconds.value++;
        
        // Simular fluctuación de mercado cada 5 segundos
        if (lastUpdateSeconds.value % 5 === 0) {
            simulateMarketFluctuation();
        }

        if (lastUpdateSeconds.value % 30 === 0) {
            triggerSyncPulse();
        }
    }, 1000);
});

onUnmounted(() => {
    if (syncInterval) clearInterval(syncInterval);
});

const triggerSyncPulse = () => {
    isSyncing.value = true;
    setTimeout(() => {
        isSyncing.value = false;
        lastUpdateSeconds.value = 0;
    }, 2000);
};

const simulateMarketFluctuation = () => {
    Object.keys(liveReturns.value).forEach(symbol => {
        const change = (Math.random() * 0.4 - 0.2).toFixed(2); // Variación de +/- 0.2%
        liveReturns.value[symbol] = (parseFloat(liveReturns.value[symbol]) + parseFloat(change)).toFixed(2);
    });
};

const formatNumber = (num) => {
    if (!num) return '0';
    if (num >= 1e9) return (num / 1e9).toFixed(2) + 'B';
    if (num >= 1e6) return (num / 1e6).toFixed(2) + 'M';
    if (num >= 1e3) return (num / 1e3).toFixed(2) + 'K';
    return Number(num).toLocaleString();
};

const toggleFollow = () => {
    router.post(route('famous-portfolios.follow', props.profile.slug), {}, {
        preserveScroll: true
    });
};

const getBaseReturn = (symbol) => {
    const seed = symbol.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0);
    const isPositive = seed % 3 !== 0;
    const value = (seed % 25 + (seed % 10) / 10).toFixed(1);
    return isPositive ? value : -value;
};

// Lógica de Gráfico de Tarta (Doughnut)
const chartData = computed(() => {
    // Tomar todos los activos para el desglose del 100%
    const sorted = [...props.holdings].sort((a, b) => b.weight - a.weight);
    
    const labels = sorted.map(h => h.symbol);
    const data = sorted.map(h => h.weight);

    return {
        labels: labels,
        datasets: [{
            data: data,
            backgroundColor: [
                '#4f46e5', '#10b981', '#f59e0b', '#ef4444', 
                '#6366f1', '#06b6d4', '#8b5cf6', '#94a3b8',
                '#ec4899', '#84cc16', '#06b6d4', '#475569'
            ],
            borderWidth: 0,
            hoverOffset: 20
        }]
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false
        },
        tooltip: {
            backgroundColor: '#0f172a',
            titleFont: { size: 14, weight: 'bold' },
            bodyFont: { size: 12 },
            padding: 12,
            cornerRadius: 12,
            displayColors: true,
            callbacks: {
                label: (context) => ` ${context.label}: ${Number(context.raw || 0).toFixed(2)}%`
            }
        }
    },
    cutout: '75%'
};

const getTransactionTypeColor = (type) => {
    if (!type) return 'text-slate-500 bg-slate-500/10 border-slate-500/20';
    const t = type.toLowerCase();
    if (t.includes('new') || t.includes('nueva')) return 'text-emerald-500 bg-emerald-500/10 border-emerald-500/20';
    if (t.includes('increased') || t.includes('incrementada')) return 'text-blue-500 bg-blue-500/10 border-blue-500/20';
    if (t.includes('sold') || t.includes('reduced') || t.includes('vendida') || t.includes('reducida')) return 'text-rose-500 bg-rose-500/10 border-rose-500/20';
    return 'text-slate-500 bg-slate-500/10 border-slate-500/20';
};

const getTransactionTypeName = (type) => {
    if (!type) return 'TRANSACCIÓN';
    const t = type.toLowerCase();
    if (t.includes('new')) return 'COMPRA';
    if (t.includes('increased')) return 'INCREMENTO';
    if (t.includes('sold') || t.includes('reduced')) return 'REDUCCIÓN';
    return type.toUpperCase();
};

</script>

<template>
    <Head :title="`Perfil Maestro: ${profile.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center gap-4">
                    <Link :href="route('social.feed')" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </Link>
                    <h2 class="font-black text-xl text-slate-800 dark:text-white leading-tight uppercase tracking-widest">
                        Analítica <span class="text-indigo-600">Guru</span>
                    </h2>
                </div>
                
                <div class="flex items-center gap-3 bg-white dark:bg-slate-900 px-4 py-2 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800">
                    <div class="relative flex h-3 w-3">
                        <span v-if="!isSyncing" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span :class="isSyncing ? 'bg-indigo-500 animate-spin' : 'bg-emerald-500'" class="relative inline-flex rounded-full h-3 w-3"></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black uppercase tracking-widest leading-none" :class="isSyncing ? 'text-indigo-500' : 'text-emerald-500'">
                            {{ isSyncing ? 'Sincronizando...' : 'Mercado en Vivo' }}
                        </span>
                        <span class="text-[8px] text-slate-400 font-bold uppercase mt-0.5 tracking-tighter">Últ: hace {{ lastUpdateSeconds }}s</span>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-8 bg-slate-50 dark:bg-slate-950 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- PERFIL MAESTRO SUPERIOR -->
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl border border-slate-100 dark:border-slate-800 overflow-hidden mb-8">
                    <div class="flex flex-col lg:flex-row divide-y lg:divide-y-0 lg:divide-x divide-slate-100 dark:divide-slate-800">
                        <!-- Identidad y Bio -->
                        <div class="lg:w-1/3 p-10 bg-slate-50/30 dark:bg-slate-900/40">
                            <div class="flex items-center gap-6 mb-8 group">
                                <div class="relative">
                                    <div class="absolute -inset-2 bg-gradient-to-tr from-indigo-600 to-purple-600 rounded-[1.8rem] blur-xl opacity-20 group-hover:opacity-40 transition duration-500"></div>
                                    <img :src="profile.avatar" class="w-24 h-24 rounded-[1.5rem] object-cover border-4 border-white dark:border-slate-800 shadow-2xl relative z-10" />
                                </div>
                                <div>
                                    <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tighter leading-none mb-2">
                                        {{ profile.name }}
                                    </h1>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-indigo-600 text-white text-[8px] font-black rounded-md uppercase tracking-[0.2em]">
                                            {{ profile.type || 'Público' }}
                                        </span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ profile.location }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-white dark:bg-slate-800/40 p-5 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm mb-6">
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed italic">
                                    "{{ profile.description }}"
                                </p>
                            </div>

                            <button 
                                @click="toggleFollow" 
                                class="w-full py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.25em] shadow-xl transition-all active:scale-95 flex items-center justify-center gap-3 border border-transparent shadow-indigo-500/20"
                                :class="isFollowing ? 'bg-slate-100 dark:bg-slate-800 text-rose-500 border-slate-200 dark:border-slate-700' : 'bg-indigo-600 hover:bg-indigo-700 text-white'"
                            >
                                {{ isFollowing ? 'Siguiendo' : 'Seguir' }}
                            </button>
                        </div>

                        <!-- Gráfico de Diversificación -->
                        <div class="lg:w-1/3 p-10 flex flex-col items-center justify-center relative">
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Concentración</span>
                                <span class="text-3xl font-black text-slate-900 dark:text-white font-mono">{{ holdings.length }}</span>
                                <span class="text-[8px] font-bold text-slate-400 uppercase">Activos</span>
                            </div>
                            <div class="w-full h-48 relative z-10">
                                <Doughnut :data="chartData" :options="chartOptions" />
                            </div>
                        </div>

                        <!-- KPIs de Desempeño -->
                        <div class="lg:w-1/3 p-10 flex flex-col justify-center gap-6">
                            <div class="grid grid-cols-2 lg:grid-cols-2 gap-4">
                                <div class="bg-indigo-50 dark:bg-indigo-900/10 p-4 rounded-2xl border border-indigo-100/50 dark:border-indigo-800/20">
                                    <div class="text-[8px] text-indigo-500 dark:text-indigo-400 font-black uppercase tracking-widest mb-1">Followers</div>
                                    <div class="text-xl font-black text-slate-900 dark:text-white font-mono tracking-tight">{{ formatNumber(stats.seguidores) }}</div>
                                </div>
                                <div class="bg-slate-900 dark:bg-black p-5 rounded-2xl border border-slate-700 dark:border-slate-800 shadow-xl overflow-hidden group">
                                    <div class="flex justify-between items-center relative z-10">
                                        <div>
                                            <div class="text-[8px] text-indigo-400 font-black uppercase tracking-widest mb-1">Registro Legal</div>
                                            <div class="text-xs font-black font-mono text-white">CIK: {{ profile.cik }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABS INTEGRADOS -->
                <div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-8 border-b border-slate-200 dark:border-slate-800 pb-2">
                    <div class="flex gap-10">
                        <button @click="activeTab = 'cartera'" class="pb-3 text-[10px] font-black uppercase tracking-widest transition-all relative" :class="activeTab === 'cartera' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 hover:text-slate-600'">
                            Análisis de Cartera
                            <div v-if="activeTab === 'cartera'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-indigo-600 dark:bg-indigo-400 rounded-full"></div>
                        </button>
                        <button @click="activeTab = 'actividad'" class="pb-3 text-[10px] font-black uppercase tracking-widest transition-all relative" :class="activeTab === 'actividad' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 hover:text-slate-600'">
                            Historia Comercial
                            <div v-if="activeTab === 'actividad'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-indigo-600 dark:bg-indigo-400 rounded-full"></div>
                        </button>
                    </div>
                </div>

                <!-- CONTENEDOR DE DATOS -->
                <div class="grid grid-cols-1">
                    
                    <!-- VISTA CARTERA -->
                    <div v-if="activeTab === 'cartera'" class="animate-in fade-in slide-in-from-bottom-2 duration-300">
                        <div class="bg-white dark:bg-slate-900 rounded-[2rem] shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-slate-100 dark:border-slate-800">
                                        <tr>
                                            <th class="px-8 py-6">Activo</th>
                                            <th class="px-8 py-6">Ticker</th>
                                            <th class="px-8 py-6">Exposición (%)</th>
                                            <th class="px-8 py-6 text-right">Rentabilidad (Día)</th>
                                            <th class="px-8 py-6 text-right">Market Value</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                                        <tr v-for="holding in holdings" :key="holding.symbol" class="hover:bg-slate-50 dark:hover:bg-indigo-900/5 transition-all group">
                                            <td class="px-8 py-5">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-center p-1.5 shrink-0 group-hover:scale-110 transition duration-300">
                                                        <img :src="`https://financialmodelingprep.com/image-stock/${holding.symbol}.png`" @error="(e) => e.target.src = 'https://ui-avatars.com/api/?name='+holding.symbol" class="w-full h-full object-contain" />
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="text-xs font-black text-slate-800 dark:text-white uppercase truncate max-w-[200px]">{{ holding.name }}</div>
                                                        <div class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">{{ formatNumber(holding.shares_number) }} SHRS</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-5">
                                                <span class="inline-flex px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-[10px] font-black text-slate-500 rounded border border-slate-200 dark:border-slate-700 tracking-tighter">
                                                    ${{ holding.symbol }}
                                                </span>
                                            </td>
                                            <td class="px-8 py-5">
                                                <div class="flex items-center gap-4 min-w-[150px]">
                                                    <div class="flex-1 h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden border border-slate-200/50 dark:border-slate-700/50">
                                                        <div class="h-full bg-indigo-500 rounded-full transition-all duration-1000" :style="{ width: Number(holding.weight || 0) + '%' }"></div>
                                                    </div>
                                                    <span class="text-[10px] font-mono font-black text-slate-600 dark:text-slate-300 w-12 text-right">
                                                        {{ Number(holding.weight || 0).toFixed(2) }}%
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-8 py-5 text-right">
                                                <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg font-black text-[10px] tabular-nums"
                                                    :class="liveReturns[holding.symbol] >= 0 
                                                        ? 'bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-800/30' 
                                                        : 'bg-rose-50 text-rose-600 border border-rose-100 dark:bg-rose-950/30 dark:text-rose-400 dark:border-rose-800/30'">
                                                    {{ liveReturns[holding.symbol] > 0 ? '+' : '' }}{{ liveReturns[holding.symbol] }}%
                                                </div>
                                            </td>
                                            <td class="px-8 py-5 text-right font-mono">
                                                <div class="text-xs font-black text-slate-900 dark:text-white">${{ formatNumber(holding.market_value) }}</div>
                                                <div class="text-[8px] text-slate-400 font-bold uppercase tracking-widest">EST. VAL</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- VISTA HISTORIAL (TABLA COMPACTA) -->
                    <div v-if="activeTab === 'actividad'" class="animate-in fade-in slide-in-from-bottom-2 duration-300">
                        <div class="bg-white dark:bg-slate-900 rounded-[2rem] shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-slate-100 dark:border-slate-800">
                                        <tr>
                                            <th class="px-8 py-6">Fecha Reporte</th>
                                            <th class="px-8 py-6">Activo</th>
                                            <th class="px-8 py-6">Acción</th>
                                            <th class="px-8 py-6 text-right">Cantidad Transaccionada</th>
                                            <th class="px-8 py-6 text-right">Impacto</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                                        <tr v-for="trade in history" :key="trade.id" class="hover:bg-slate-50 dark:hover:bg-indigo-950/10 transition-all group">
                                            <td class="px-8 py-5">
                                                <span class="text-[10px] font-mono font-black text-slate-500 dark:text-slate-400 tracking-wider group-hover:text-indigo-500 transition-colors uppercase">
                                                    {{ trade.filling_date }}
                                                </span>
                                            </td>
                                            <td class="px-8 py-5">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-7 h-7 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 flex items-center justify-center p-1.5 shrink-0 shadow-sm transition group-hover:border-indigo-500">
                                                        <img :src="`https://financialmodelingprep.com/image-stock/${trade.symbol}.png`" class="w-full h-full object-contain" />
                                                    </div>
                                                    <span class="text-xs font-black text-slate-800 dark:text-white group-hover:text-indigo-600 transition-colors">${{ trade.symbol }}</span>
                                                </div>
                                            </td>
                                            <td class="px-8 py-5">
                                                <span class="inline-flex px-3 py-1 rounded-md text-[8px] font-black tracking-widest border" :class="getTransactionTypeColor(trade.change_type)">
                                                    {{ getTransactionTypeName(trade.change_type) }}
                                                </span>
                                            </td>
                                            <td class="px-8 py-5 text-right font-mono text-xs font-black" :class="trade.change_in_shares >= 0 ? 'text-emerald-500' : 'text-rose-500'">
                                                {{ trade.change_in_shares > 0 ? '+' : '' }}{{ formatNumber(trade.change_in_shares) }}
                                            </td>
                                            <td class="px-8 py-5 text-right">
                                                <span class="text-xs font-black text-slate-900 dark:text-white">{{ Number(trade.percent_of_portfolio || 0).toFixed(2) }}%</span>
                                                <div class="text-[8px] text-slate-400 font-bold uppercase tracking-widest">PORTFOLIO IMPACT</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.font-mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
</style>
