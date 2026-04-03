<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    stocks: Object,
    etfs: Object,
    crypto: Object,
    funds: Object
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
};

const formatPercent = (value) => {
    return new Intl.NumberFormat('en-US', { style: 'percent', minimumFractionDigits: 2 }).format(value / 100);
};
</script>

<template>
    <Head title="Mercados" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-slate-800 dark:text-white leading-tight">
                Mercados
            </h2>
        </template>

        <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                
                <!-- STOCKS CONTENT -->
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-white border-b border-slate-200 dark:border-slate-700 pb-2">Acciones</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                            <h3 class="text-lg font-bold text-emerald-600 mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                Ganadores
                            </h3>
                            <ul class="space-y-4">
                                <li v-for="stock in stocks.winners" :key="stock.ticker" class="flex justify-between items-center pb-3 border-b border-slate-50 dark:border-slate-700 last:border-0 hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors p-2 rounded-lg group">
                                    <Link :href="route('assets.show', stock.ticker)" class="flex items-center gap-3 flex-grow">
                                        <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700 flex-shrink-0 flex items-center justify-center shadow-sm">
                                            <img v-if="stock.image" :src="stock.image" class="w-full h-full object-contain" @error="stock.image = null" />
                                            <span v-else class="text-xs font-bold text-slate-400">{{ stock.ticker.substring(0,2) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ stock.ticker }}</div>
                                            <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate max-w-[120px]">{{ stock.name }}</div>
                                        </div>
                                    </Link>
                                    <div class="text-right">
                                        <div class="font-medium text-slate-800 dark:text-white text-sm">{{ formatCurrency(stock.price) }}</div>
                                        <div class="text-[10px] font-bold text-emerald-500">+{{ stock.change_percent }}%</div>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Losers -->
                        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                            <h3 class="text-lg font-bold text-rose-600 mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                </svg>
                                Perdedores
                            </h3>
                            <ul class="space-y-4">
                                <li v-for="stock in stocks.losers" :key="stock.ticker" class="flex justify-between items-center pb-3 border-b border-slate-50 dark:border-slate-700 last:border-0 hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors p-2 rounded-lg group">
                                    <Link :href="route('assets.show', stock.ticker)" class="flex items-center gap-3 flex-grow">
                                        <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700 flex-shrink-0 flex items-center justify-center shadow-sm">
                                            <img v-if="stock.image" :src="stock.image" class="w-full h-full object-contain" @error="stock.image = null" />
                                            <span v-else class="text-xs font-bold text-slate-400">{{ stock.ticker.substring(0,2) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ stock.ticker }}</div>
                                            <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate max-w-[120px]">{{ stock.name }}</div>
                                        </div>
                                    </Link>
                                    <div class="text-right">
                                        <div class="font-medium text-slate-800 dark:text-white text-sm">{{ formatCurrency(stock.price) }}</div>
                                        <div class="text-[10px] font-bold text-rose-500">{{ stock.change_percent }}%</div>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Most Searched -->
                        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                            <h3 class="text-lg font-bold text-blue-600 mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Más Buscados
                            </h3>
                            <ul class="space-y-4">
                                <li v-for="stock in stocks.most_searched" :key="stock.ticker" class="flex justify-between items-center pb-3 border-b border-slate-50 dark:border-slate-700 last:border-0 hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors p-2 rounded-lg group">
                                    <Link :href="route('assets.show', stock.ticker)" class="flex items-center gap-3 flex-grow">
                                        <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700 flex-shrink-0 flex items-center justify-center shadow-sm">
                                            <img v-if="stock.image" :src="stock.image" class="w-full h-full object-contain" @error="stock.image = null" />
                                            <span v-else class="text-xs font-bold text-slate-400">{{ stock.ticker.substring(0,2) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ stock.ticker }}</div>
                                            <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate max-w-[120px]">{{ stock.name }}</div>
                                        </div>
                                    </Link>
                                    <div class="text-right">
                                        <div class="font-medium text-slate-800 dark:text-white text-sm">{{ formatCurrency(stock.price) }}</div>
                                        <div :class="stock.change_percent >= 0 ? 'text-emerald-500' : 'text-rose-500'" class="text-[10px] font-bold">
                                            {{ stock.change_percent >= 0 ? '+' : '' }}{{ stock.change_percent }}%
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- ETFS CONTENT -->
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-white border-b border-slate-200 dark:border-slate-700 pb-2">ETFs</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Largest -->
                        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Mayores por Volumen</h3>
                             <ul class="space-y-4">
                                <li v-for="etf in etfs.largest" :key="etf.ticker" class="flex justify-between items-center pb-3 border-b border-slate-50 dark:border-slate-700 last:border-0 hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors p-2 rounded-lg group">
                                    <Link :href="route('assets.show', etf.ticker)" class="flex items-center gap-3 flex-grow">
                                        <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700 flex-shrink-0 flex items-center justify-center shadow-sm">
                                            <img v-if="etf.image" :src="etf.image" class="w-full h-full object-contain" @error="etf.image = null" />
                                            <span v-else class="text-xs font-bold text-slate-400">{{ etf.ticker.substring(0,2) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ etf.ticker }}</div>
                                            <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate max-w-[120px]">{{ etf.name }}</div>
                                        </div>
                                    </Link>
                                    <div class="text-right">
                                        <div class="font-medium text-slate-800 dark:text-white text-sm">{{ formatCurrency(etf.price) }}</div>
                                        <div :class="etf.change_percent >= 0 ? 'text-emerald-500' : 'text-rose-500'" class="text-[10px] font-bold">
                                            {{ etf.change_percent >= 0 ? '+' : '' }}{{ etf.change_percent }}%
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        
                         <!-- Popular -->
                        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Populares en la Comunidad</h3>
                             <ul class="space-y-4">
                                <li v-for="etf in etfs.popular" :key="etf.ticker" class="flex justify-between items-center pb-3 border-b border-slate-50 dark:border-slate-700 last:border-0 hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors p-2 rounded-lg group">
                                    <Link :href="route('assets.show', etf.ticker)" class="flex items-center gap-3 flex-grow">
                                        <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700 flex-shrink-0 flex items-center justify-center shadow-sm">
                                            <img v-if="etf.image" :src="etf.image" class="w-full h-full object-contain" @error="etf.image = null" />
                                            <span v-else class="text-xs font-bold text-slate-400">{{ etf.ticker.substring(0,2) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 dark:text-white group-hover:text-blue-600 dark:hover:text-blue-400 transition-colors">{{ etf.ticker }}</div>
                                            <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate max-w-[120px]">{{ etf.name }}</div>
                                        </div>
                                    </Link>
                                    <div class="text-right">
                                        <div class="font-medium text-slate-800 dark:text-white text-sm">{{ formatCurrency(etf.price) }}</div>
                                        <div :class="etf.change_percent >= 0 ? 'text-emerald-500' : 'text-rose-500'" class="text-[10px] font-bold">
                                            {{ etf.change_percent >= 0 ? '+' : '' }}{{ etf.change_percent }}%
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>

                         <!-- Most Searched -->
                        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Más Buscados</h3>
                             <ul class="space-y-4">
                                <li v-for="etf in etfs.most_searched" :key="etf.ticker" class="flex justify-between items-center pb-3 border-b border-slate-50 dark:border-slate-700 last:border-0 hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors p-2 rounded-lg group">
                                    <Link :href="route('assets.show', etf.ticker)" class="flex items-center gap-3 flex-grow">
                                        <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700 flex-shrink-0 flex items-center justify-center shadow-sm">
                                            <img v-if="etf.image" :src="etf.image" class="w-full h-full object-contain" @error="etf.image = null" />
                                            <span v-else class="text-xs font-bold text-slate-400">{{ etf.ticker.substring(0,2) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ etf.ticker }}</div>
                                            <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate max-w-[120px]">{{ etf.name }}</div>
                                        </div>
                                    </Link>
                                    <div class="text-right">
                                        <div class="font-medium text-slate-800 dark:text-white text-sm">{{ formatCurrency(etf.price) }}</div>
                                        <div :class="etf.change_percent >= 0 ? 'text-emerald-500' : 'text-rose-500'" class="text-[10px] font-bold">
                                            {{ etf.change_percent >= 0 ? '+' : '' }}{{ etf.change_percent }}%
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- CRYPTO CONTENT -->
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-white border-b border-slate-200 dark:border-slate-700 pb-2">Criptomonedas</h2>
                     <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Largest -->
                        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Mayores Criptomonedas</h3>
                             <ul class="space-y-4">
                                <li v-for="coin in crypto.largest" :key="coin.ticker" class="flex justify-between items-center pb-3 border-b border-slate-50 dark:border-slate-700 last:border-0 hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors p-2 rounded-lg group">
                                    <Link :href="route('assets.show', coin.ticker)" class="flex items-center gap-3 flex-grow">
                                        <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700 flex-shrink-0 flex items-center justify-center shadow-sm">
                                            <img v-if="coin.image" :src="coin.image" class="w-full h-full object-contain" @error="coin.image = null" />
                                            <span v-else class="text-xs font-bold text-slate-400">{{ coin.ticker.substring(0,2) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ coin.ticker }}</div>
                                            <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate max-w-[120px]">{{ coin.name }}</div>
                                        </div>
                                    </Link>
                                    <div class="text-right">
                                        <div class="font-medium text-slate-800 dark:text-white text-sm">{{ formatCurrency(coin.price) }}</div>
                                        <div :class="coin.change_percent >= 0 ? 'text-emerald-500' : 'text-rose-500'" class="text-[10px] font-bold">
                                            {{ coin.change_percent >= 0 ? '+' : '' }}{{ coin.change_percent }}%
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>

                         <!-- Popular -->
                        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Populares en la Comunidad</h3>
                             <ul class="space-y-4">
                                <li v-for="coin in crypto.popular" :key="coin.ticker" class="flex justify-between items-center pb-3 border-b border-slate-50 dark:border-slate-700 last:border-0 hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors p-2 rounded-lg group">
                                    <Link :href="route('assets.show', coin.ticker)" class="flex items-center gap-3 flex-grow">
                                        <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700 flex-shrink-0 flex items-center justify-center shadow-sm">
                                            <img v-if="coin.image" :src="coin.image" class="w-full h-full object-contain" @error="coin.image = null" />
                                            <span v-else class="text-xs font-bold text-slate-400">{{ coin.ticker.substring(0,2) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ coin.ticker }}</div>
                                            <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate max-w-[120px]">{{ coin.name }}</div>
                                        </div>
                                    </Link>
                                    <div class="text-right">
                                        <div class="font-medium text-slate-800 dark:text-white text-sm">{{ formatCurrency(coin.price) }}</div>
                                        <div :class="coin.change_percent >= 0 ? 'text-emerald-500' : 'text-rose-500'" class="text-[10px] font-bold">
                                            {{ coin.change_percent >= 0 ? '+' : '' }}{{ coin.change_percent }}%
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>

                         <!-- Most Searched -->
                        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Más Buscados</h3>
                             <ul class="space-y-4">
                                <li v-for="coin in crypto.most_searched" :key="coin.ticker" class="flex justify-between items-center pb-3 border-b border-slate-50 dark:border-slate-700 last:border-0 hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors p-2 rounded-lg group">
                                    <Link :href="route('assets.show', coin.ticker)" class="flex items-center gap-3 flex-grow">
                                        <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700 flex-shrink-0 flex items-center justify-center shadow-sm">
                                            <img v-if="coin.image" :src="coin.image" class="w-full h-full object-contain" @error="coin.image = null" />
                                            <span v-else class="text-xs font-bold text-slate-400">{{ coin.ticker.substring(0,2) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ coin.ticker }}</div>
                                            <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate max-w-[120px]">{{ coin.name }}</div>
                                        </div>
                                    </Link>
                                    <div class="text-right">
                                        <div class="font-medium text-slate-800 dark:text-white text-sm">{{ formatCurrency(coin.price) }}</div>
                                        <div :class="coin.change_percent >= 0 ? 'text-emerald-500' : 'text-rose-500'" class="text-[10px] font-bold">
                                            {{ coin.change_percent >= 0 ? '+' : '' }}{{ coin.change_percent }}%
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                 <!-- FUNDS CONTENT -->
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-white border-b border-slate-200 dark:border-slate-700 pb-2">Fondos de Inversión</h2>
                     <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Popular -->
                        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Fondos Populares</h3>
                             <ul class="space-y-4">
                                <li v-for="fund in funds.popular" :key="fund.ticker" class="flex justify-between items-center pb-3 border-b border-slate-50 dark:border-slate-700 last:border-0 hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors p-2 rounded-lg group">
                                    <Link :href="route('assets.show', fund.ticker)" class="flex items-center gap-3 flex-grow">
                                        <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700 flex-shrink-0 flex items-center justify-center shadow-sm">
                                            <img v-if="fund.image" :src="fund.image" class="w-full h-full object-contain" @error="fund.image = null" />
                                            <span v-else class="text-xs font-bold text-slate-400">{{ fund.ticker.substring(0,2) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ fund.ticker }}</div>
                                            <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate max-w-[120px]">{{ fund.name }}</div>
                                        </div>
                                    </Link>
                                    <div class="text-right">
                                        <div class="font-medium text-slate-800 dark:text-white text-sm">{{ formatCurrency(fund.price) }}</div>
                                        <div :class="fund.change_percent >= 0 ? 'text-emerald-500' : 'text-rose-500'" class="text-[10px] font-bold">
                                            {{ fund.change_percent >= 0 ? '+' : '' }}{{ fund.change_percent }}%
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
