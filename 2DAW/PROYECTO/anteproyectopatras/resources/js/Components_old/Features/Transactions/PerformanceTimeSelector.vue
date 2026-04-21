<script setup>
/**
 * Componente para seleccionar la temporalidad del análisis (Años o Histórico).
 */
defineProps({
    availableYears: Array,
    viewType: [String, Number]
});

defineEmits(['switch']);
</script>

<template>
    <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 px-2">Temporalidad</h3>
        
        <!-- Botón para ver el historial completo -->
        <button 
            @click="$emit('switch', 'MAX')"
            class="w-full text-left px-4 py-3 rounded-xl mb-2 font-medium transition-colors"
            :class="viewType === 'MAX' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'hover:bg-slate-50 text-slate-700 dark:hover:bg-slate-700/50 dark:text-slate-300'"
        >
            Histórico (MAX)
        </button>

        <div class="border-t border-slate-100 dark:border-slate-700 my-2"></div>

        <!-- Lista de años disponibles -->
        <div class="space-y-1">
            <button 
                v-for="year in availableYears" :key="year"
                @click="$emit('switch', year)"
                class="w-full text-left px-4 py-2 rounded-xl text-sm font-medium transition-colors"
                :class="viewType === String(year) || viewType === Number(year) ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'hover:bg-slate-50 text-slate-600 dark:hover:bg-slate-700/50 dark:text-slate-400'"
            >
                Año {{ year }}
            </button>
        </div>
    </div>
</template>
