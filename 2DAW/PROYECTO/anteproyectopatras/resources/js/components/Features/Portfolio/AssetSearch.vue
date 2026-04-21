<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const query = ref('');
const results = ref([]);
const isSearching = ref(false);
const showResults = ref(false);
const searchContainer = ref(null);

const handleSearch = async () => {
    if (query.value.length < 2) {
        results.value = [];
        return;
    }

    isSearching.value = true;
    try {
        const response = await axios.get(route('market.search'), {
            params: { query: query.value }
        });
        // Filter to prefer Stocks as requested by user
        results.value = response.data.filter(item => item.type === 'stock').slice(0, 8);
    } catch (e) {
        console.error('Search error', e);
    } finally {
        isSearching.value = false;
    }
};

let timeout = null;
watch(query, () => {
    clearTimeout(timeout);
    timeout = setTimeout(handleSearch, 300);
});

const selectAsset = (asset) => {
    query.value = '';
    showResults.value = false;
    router.get(route('assets.show', asset.ticker));
};

const handleClickOutside = (e) => {
    if (searchContainer.value && !searchContainer.value.contains(e.target)) {
        showResults.value = false;
    }
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => document.removeEventListener('click', handleClickOutside));

</script>

<template>
    <div ref="searchContainer" class="relative w-full max-w-md mx-4">
        <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg v-if="!isSearching" class="h-4 w-4 text-slate-400 group-focus-within:text-blue-500 transition-colors" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <svg v-else class="animate-spin h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>
            <input
                v-model="query"
                type="text"
                @focus="showResults = true"
                class="block w-full pl-10 pr-3 py-2 border-none rounded-2xl bg-slate-100 dark:bg-slate-700/50 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500/50 sm:text-sm transition-all shadow-inner"
                placeholder="Buscar acciones... (AAPL, NVDA, TSLA)"
            />
        </div>

        <!-- Resultados -->
        <div 
            v-if="showResults && (results.length > 0 || isSearching)" 
            class="absolute mt-2 w-full bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden z-[100] animate-in fade-in zoom-in-95 duration-100"
        >
            <div v-if="results.length > 0" class="py-2">
                <button
                    v-for="asset in results"
                    :key="asset.ticker"
                    @click="selectAsset(asset)"
                    class="w-full flex items-center gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors text-left"
                >
                    <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center overflow-hidden border border-slate-200 dark:border-slate-600">
                        <img :src="`https://financialmodelingprep.com/image-stock/${asset.ticker}.png`" @error="(e) => e.target.src = 'https://ui-avatars.com/api/?name='+asset.ticker" class="w-7 h-7 object-contain" />
                    </div>
                    <div class="flex-grow">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900 dark:text-white">{{ asset.ticker }}</span>
                            <span class="text-[10px] font-black px-1.5 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded uppercase tracking-tighter">{{ asset.type }}</span>
                        </div>
                        <div class="text-xs text-slate-500 truncate max-w-[200px]">{{ asset.name }}</div>
                    </div>
                </button>
            </div>
            <div v-else-if="!isSearching" class="p-8 text-center">
                <svg class="h-12 w-12 text-slate-200 dark:text-slate-700 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-slate-500">No encontramos ningún activo.</p>
            </div>
        </div>
    </div>
</template>
