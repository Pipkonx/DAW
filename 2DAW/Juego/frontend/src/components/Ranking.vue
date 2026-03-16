<template>
    <div class="fixed inset-0 bg-slate-900/80 z-50 flex items-center justify-center p-4 backdrop-blur-sm animate-fade-in">
        <div class="w-full max-w-2xl bg-slate-800/90 rounded-xl border border-slate-700 shadow-2xl flex flex-col max-h-[80vh] animate-scale-in">
            <!-- Header -->
            <div class="p-4 border-b border-slate-700 flex justify-between items-center bg-slate-800/50 rounded-t-xl">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <span class="text-amber-500">🏆</span> Clasificación Global
                </h2>
                <button 
                    @click="$emit('close'); play('click')" 
                    @mouseenter="play('hover')"
                    class="text-slate-400 hover:text-white transition-colors p-1 rounded-full hover:bg-slate-700/50"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Tabs -->
            <div class="flex border-b border-slate-700">
                <button 
                    v-for="tab in ['Diario', 'Semanal', 'Total']" 
                    :key="tab"
                    @click="activeTab = tab; play('click')"
                    @mouseenter="play('hover')"
                    class="flex-1 py-3 text-sm font-medium transition-colors"
                    :class="activeTab === tab ? 'bg-slate-800 text-white border-b-2 border-amber-500' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200'"
                >
                    {{ tab }}
                </button>
            </div>
            
            <!-- Content -->
            <div class="flex-1 overflow-auto p-4 custom-scroll bg-slate-950">
                <div v-if="rankingStore.loading" class="flex justify-center items-center h-40">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-500"></div>
                </div>
                
                <div v-else-if="rankingStore.error" class="text-red-400 text-center p-4">
                    {{ rankingStore.error }}
                </div>
                
                <table v-else class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-slate-500 text-xs uppercase tracking-wider border-b border-slate-700">
                            <th class="p-3 w-16 text-center">Rango</th>
                            <th class="p-3">Jugador</th>
                            <th class="p-3 text-right">Eficiencia</th>
                            <th class="p-3 text-right">Puntuación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr 
                            v-for="(entry, index) in rankingStore.rankings" 
                            :key="entry.id"
                            class="border-b border-slate-800 hover:bg-slate-800/50 transition-colors group"
                        >
                            <td class="p-3 text-center font-bold">
                                <span v-if="index === 0" class="text-yellow-400 text-xl">🥇</span>
                                <span v-else-if="index === 1" class="text-slate-300 text-xl">🥈</span>
                                <span v-else-if="index === 2" class="text-amber-700 text-xl">🥉</span>
                                <span v-else class="text-slate-500">#{{ index + 1 }}</span>
                            </td>
                            <td class="p-3 font-medium text-slate-200 group-hover:text-white">{{ entry.username }}</td>
                            <td class="p-3 text-right text-cyan-400 font-mono">{{ entry.efficiency }}</td>
                            <td class="p-3 text-right text-amber-400 font-mono font-bold">{{ entry.score }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- User Rank Footer -->
            <div class="p-4 bg-slate-800 border-t border-slate-700 flex justify-between items-center text-sm rounded-b-xl" v-if="rankingStore.userRank">
                <div class="text-slate-400">Tu Rango: <span class="text-white font-bold">#{{ rankingStore.userRank.rank }}</span></div>
                <div class="flex gap-6">
                    <div class="text-slate-400">Eficiencia: <span class="text-cyan-400">{{ rankingStore.userRank.efficiency }}</span></div>
                    <div class="text-slate-400">Puntos: <span class="text-amber-400">{{ rankingStore.userRank.score }}</span></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRankingStore } from '../stores/ranking';
import { useSound } from '../composables/useSound';

defineEmits(['close']);

const { play } = useSound();
const rankingStore = useRankingStore();
const activeTab = ref('Semanal');

const loadData = () => {
    // Map Spanish tabs to API values if needed, or update store to handle Spanish
    const tabMap = {
        'Diario': 'daily',
        'Semanal': 'weekly',
        'Total': 'all_time'
    };
    rankingStore.fetchRankings(tabMap[activeTab.value] || 'weekly');
};

onMounted(() => {
    loadData();
});

watch(activeTab, () => {
    loadData();
});
</script>
