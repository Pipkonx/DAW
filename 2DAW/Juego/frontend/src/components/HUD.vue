<template>
  <div class="flex gap-4 items-center pointer-events-none select-none">
    <!-- Resources Group -->
    <div class="flex gap-3 pointer-events-auto">
        <!-- Money -->
        <div class="glass px-4 py-2 rounded-full flex items-center gap-3 shadow-lg shadow-black/20 hover:scale-105 transition-transform cursor-help" title="Fondos disponibles">
            <div class="bg-accent/20 p-1.5 rounded-full">
                <span class="text-accent text-lg">💰</span>
            </div>
            <div class="flex flex-col">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider leading-none">Dinero</span>
                <span class="text-lg font-bold text-slate-100 font-mono leading-tight">${{ resources.money }}</span>
            </div>
        </div>

        <!-- Wood -->
        <div class="glass px-4 py-2 rounded-full flex items-center gap-3 shadow-lg shadow-black/20 hover:scale-105 transition-transform cursor-help" title="Recursos de construcción">
            <div class="bg-amber-700/30 p-1.5 rounded-full">
                <span class="text-amber-500 text-lg">🪵</span>
            </div>
            <div class="flex flex-col">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider leading-none">Madera</span>
                <span class="text-lg font-bold text-slate-100 font-mono leading-tight">{{ resources.wood }}</span>
            </div>
        </div>

        <!-- Water -->
        <div class="glass px-4 py-2 rounded-full flex items-center gap-3 shadow-lg shadow-black/20 hover:scale-105 transition-transform cursor-help" title="Recursos hídricos">
            <div class="bg-secondary/20 p-1.5 rounded-full">
                <span class="text-secondary text-lg">💧</span>
            </div>
            <div class="flex flex-col">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider leading-none">Agua</span>
                <span class="text-lg font-bold text-slate-100 font-mono leading-tight">{{ resources.water }}</span>
            </div>
        </div>
    </div>

    <!-- Actions Group -->
    <div class="flex gap-3 ml-4 pointer-events-auto border-l border-white/10 pl-4">
        <!-- Ranking Button -->
        <button 
            @click="$emit('toggle-ranking')" 
            class="group relative w-12 h-12 glass flex items-center justify-center rounded-xl shadow-lg hover:bg-accent hover:border-accent transition-all duration-300 hover:scale-110 active:scale-95 hover:shadow-neon-accent"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-accent group-hover:text-slate-900 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <div class="absolute -bottom-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-surface-dark text-white text-[10px] rounded opacity-0 group-hover:opacity-100 transition-all transform translate-y-2 group-hover:translate-y-0 shadow-xl border border-white/10 whitespace-nowrap z-50 pointer-events-none font-bold tracking-wide">
                RANKING
            </div>
        </button>

        <!-- Tech Tree Button -->
        <button 
            @click="$emit('toggle-tech')" 
            class="group relative w-12 h-12 glass flex items-center justify-center rounded-xl shadow-lg hover:bg-primary hover:border-primary transition-all duration-300 hover:scale-110 active:scale-95 hover:shadow-neon-primary"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary group-hover:text-slate-900 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
            <div class="absolute -bottom-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-surface-dark text-white text-[10px] rounded opacity-0 group-hover:opacity-100 transition-all transform translate-y-2 group-hover:translate-y-0 shadow-xl border border-white/10 whitespace-nowrap z-50 pointer-events-none font-bold tracking-wide">
                TECNOLOGÍA
            </div>
        </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
    resources: {
        type: Object,
        default: () => ({ money: 0, wood: 0, water: 0 })
    }
})

defineEmits(['toggle-tech', 'toggle-ranking'])
</script>
