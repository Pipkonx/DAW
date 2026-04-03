<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    profile: Object,
    holdings: Array,
    history: Array,
    stats: Object,
});

const showFullDescription = ref(false);

const formatNumber = (num) => {
    if (!num) return '0';
    if (num >= 1e9) return (num / 1e9).toFixed(2) + 'B';
    if (num >= 1e6) return (num / 1e6).toFixed(2) + 'M';
    return num.toLocaleString();
};

const getTransactionTypeColor = (type) => {
    if (!type) return 'text-slate-500 bg-slate-50 dark:bg-slate-700/50';
    const t = type.toLowerCase();
    if (t.includes('new') || t.includes('nueva')) return 'text-emerald-500 bg-emerald-50 dark:bg-emerald-900/20';
    if (t.includes('increased') || t.includes('incrementada')) return 'text-blue-500 bg-blue-50 dark:bg-blue-900/20';
    if (t.includes('sold') || t.includes('reduced') || t.includes('vendida') || t.includes('reducida')) return 'text-rose-500 bg-rose-50 dark:bg-rose-900/20';
    return 'text-slate-500 bg-slate-50 dark:bg-slate-700/50';
};

const getTransactionTypeName = (type) => {
    if (!type) return 'Transacción';
    const t = type.toLowerCase();
    if (t.includes('new')) return 'Nueva Posición';
    if (t.includes('increased')) return 'Incrementada';
    if (t.includes('sold') || t.includes('reduced')) return 'Reducida';
    if (t.includes('no_change')) return 'Sin Cambios';
    return type;
};

</script>

<template>
    <Head :title="`Cartera de ${profile.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('social.feed')" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <h2 class="font-semibold text-xl text-slate-800 dark:text-white leading-tight">
                    Cartera de Inversión
                </h2>
            </div>
        </template>

        <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Columna Izquierda: Perfil y Bio -->
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm border border-slate-100 dark:border-slate-700 h-full">
                            <div class="flex flex-col items-center text-center">
                                <div class="relative mb-6">
                                    <img :src="profile.avatar" class="w-24 h-24 rounded-3xl object-cover border-4 border-slate-50 dark:border-slate-700 shadow-lg" />
                                    <div class="absolute -bottom-2 -right-2 px-3 py-1 bg-blue-600 text-white text-[10px] font-black rounded-lg uppercase tracking-widest shadow-lg">
                                        {{ profile.type }}
                                    </div>
                                </div>
                                <h1 class="text-2xl font-black text-slate-900 dark:text-white mb-2">{{ profile.name }}</h1>
                                <p class="text-sm text-slate-500 font-bold flex items-center justify-center gap-2 mb-8">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ profile.location }}
                                </p>

                                <div class="grid grid-cols-2 gap-4 w-full mb-8">
                                    <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-700">
                                        <div class="text-2xl font-black text-slate-900 dark:text-white">{{ stats.posiciones }}</div>
                                        <div class="text-[10px] text-slate-500 uppercase font-black tracking-widest mt-1">Posiciones</div>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-700">
                                        <div class="text-xs font-black text-slate-900 dark:text-white">{{ stats.last_report }}</div>
                                        <div class="text-[10px] text-slate-500 uppercase font-black tracking-widest mt-1">Último Reporte</div>
                                    </div>
                                </div>

                                <div class="w-full">
                                    <h3 class="text-xs font-black uppercase text-slate-400 tracking-widest mb-4">Sobre esta cartera</h3>
                                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed transition-all duration-500 overflow-hidden text-center" 
                                       :class="showFullDescription ? 'max-h-[1000px]' : 'max-h-[120px]'">
                                        {{ profile.description }}
                                    </p>
                                    <div class="flex justify-center">
                                        <button 
                                            @click="showFullDescription = !showFullDescription"
                                            class="mt-4 text-blue-600 dark:text-blue-400 text-xs font-black uppercase tracking-widest hover:underline transition-all"
                                        >
                                            {{ showFullDescription ? 'Ver menos' : 'Ver más' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CIK Info Block -->
                        <div class="bg-slate-900 text-white rounded-3xl p-6 shadow-xl relative overflow-hidden group">
                           <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/20 rounded-full blur-2xl group-hover:bg-blue-500/40 transition-all"></div>
                           <div class="relative z-10">
                               <div class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-400 mb-2">Registro Oficial SEC</div>
                               <div class="text-lg font-black font-mono">CIK: {{ profile.cik }}</div>
                               <p class="text-[10px] text-slate-400 mt-2 font-bold leading-tight">
                                   Datos extraídos oficialmente de los reportes trimestrales 13F del regulador estadounidense SEC.
                               </p>
                           </div>
                        </div>
                    </div>

                    <!-- Columna Central/Derecha: Holdings e Historial -->
                    <div class="lg:col-span-2 space-y-8">
                        
                        <!-- Puestos y Cartera -->
                        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                            <div class="p-8 border-b border-slate-50 dark:border-slate-700 flex items-center justify-between">
                                <h3 class="text-lg font-black text-slate-900 dark:text-white">Cartera de Inversiones</h3>
                                <button class="text-xs font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest hover:bg-blue-50 dark:hover:bg-blue-900/20 px-4 py-2 rounded-xl transition-all">
                                    Ver todas las posiciones
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-[10px] font-black uppercase tracking-widest text-slate-400">
                                        <tr>
                                            <th class="px-8 py-4">Activo</th>
                                            <th class="px-8 py-4">Símbolo</th>
                                            <th class="px-8 py-4 text-center">Peso</th>
                                            <th class="px-8 py-4 text-right">Valor de Mercado</th>
                                            <th class="px-8 py-4 text-right">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                        <tr v-for="holding in holdings" :key="holding.ticker" class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group">
                                            <td class="px-8 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center border border-slate-200 dark:border-slate-600 p-1">
                                                        <img :src="`https://financialmodelingprep.com/image-stock/${holding.symbol}.png`" @error="(e) => e.target.src = 'https://ui-avatars.com/api/?name='+holding.symbol" class="w-7 h-7 object-contain" />
                                                    </div>
                                                    <span class="text-sm font-bold text-slate-800 dark:text-white group-hover:text-blue-500 transition-colors">{{ holding.name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-8 py-4">
                                                <Link :href="route('assets.show', holding.symbol)" class="text-xs font-black text-slate-500 hover:text-blue-600 transition-colors">
                                                    ${{ holding.symbol }}
                                                </Link>
                                            </td>
                                            <td class="px-8 py-4">
                                                <div class="flex flex-col items-center gap-1">
                                                    <span class="text-sm font-black text-slate-900 dark:text-white">{{ Number(holding.weight || 0).toFixed(2) }}%</span>
                                                    <div class="w-16 h-1 bg-slate-100 dark:bg-slate-900 rounded-full overflow-hidden">
                                                        <div class="h-full bg-blue-500" :style="{ width: Number(holding.weight || 0) + '%' }"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-4 text-right">
                                                <span class="text-sm font-bold text-slate-800 dark:text-white">${{ formatNumber(holding.market_value) }}</span>
                                            </td>
                                            <td class="px-8 py-4 text-right">
                                                <span class="text-xs font-bold text-slate-500">{{ formatNumber(holding.shares_number) }}</span>
                                            </td>
                                        </tr>
                                        <tr v-if="holdings.length === 0">
                                            <td colspan="5" class="px-8 py-12 text-center">
                                                <div class="text-slate-400 font-bold italic">No hay posiciones reportadas actualmente.</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Historial Comercial -->
                        <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm border border-slate-100 dark:border-slate-700">
                            <h3 class="text-lg font-black text-slate-900 dark:text-white mb-8 flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Historial Comercial
                            </h3>
                            
                            <div class="space-y-8 relative before:absolute before:left-[19px] before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-100 dark:before:bg-slate-700 font-bold italic text-slate-400">
                                <div v-if="history.length === 0" class="pl-12 py-4">
                                    No hay historial comercial reciente reportado.
                                </div>
                                <div v-for="trade in history" :key="trade.id" class="relative pl-12 not-italic">
                                    <div class="absolute left-0 top-1 w-10 h-10 rounded-full border-4 border-white dark:border-slate-800 bg-slate-100 dark:bg-slate-700 z-10 flex items-center justify-center p-1 overflow-hidden">
                                        <img :src="`https://financialmodelingprep.com/image-stock/${trade.symbol}.png`" class="w-full object-contain" />
                                    </div>
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                        <div>
                                            <div class="flex items-center gap-3 mb-1">
                                                <span class="text-sm font-black text-slate-900 dark:text-white">{{ trade.symbol }}</span>
                                                <span class="text-[10px] font-black px-2 py-0.5 rounded uppercase tracking-tighter" :class="getTransactionTypeColor(trade.change_type)">
                                                    {{ getTransactionTypeName(trade.change_type) }}
                                                </span>
                                            </div>
                                            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                                                Reportado el {{ trade.filling_date }}
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-black text-slate-800 dark:text-white">
                                                {{ trade.change_in_shares > 0 ? '+' : '' }}{{ formatNumber(trade.change_in_shares) }} acciones
                                            </div>
                                            <div class="text-[10px] text-slate-500 font-bold">
                                                {{ Number(trade.percent_of_portfolio || 0).toFixed(4) }}% del portfolio
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
