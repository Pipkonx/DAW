<script setup>
/**
 * SystemLogsViewer - Visor de registros del sistema para administradores.
 * 
 * Muestra las últimas líneas de los logs de Laravel en una interfaz 
 * de tipo terminal.
 */
defineProps({
    logs: String
});

const emit = defineEmits(['close']);
</script>

<template>
    <div v-if="logs" class="bg-slate-900 rounded-3xl shadow-2xl overflow-hidden border border-slate-800 animate-in fade-in slide-in-from-bottom-4 duration-500">
        <div class="px-8 py-4 bg-slate-800/50 flex justify-between items-center border-b border-slate-700">
            <div class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                <span class="text-xs font-black uppercase tracking-widest text-slate-300">Laravel System Logs (Últimas 50 líneas)</span>
            </div>
            <button @click="emit('close')" class="text-slate-500 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        <div class="p-6">
            <pre class="text-[10px] sm:text-xs font-mono text-emerald-400 bg-black/30 p-4 rounded-xl overflow-x-auto max-h-96 scrollbar-thin scrollbar-thumb-slate-700 leading-relaxed">{{ logs }}</pre>
        </div>
    </div>
</template>
