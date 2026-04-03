<script setup>
/**
 * MaintenanceActions - Control de mantenimiento del sistema para administradores.
 * 
 * Permite realizar tareas críticas como limpiar la caché, optimizar la DB y 
 * acceder a reportes y analíticas.
 */
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    loadingLogs: Boolean
});

const emit = defineEmits(['clearCache', 'optimizeDb', 'fetchLogs']);

const clearCache = () => emit('clearCache');
const optimizeDb = () => emit('optimizeDb');
const fetchLogs = () => emit('fetchLogs');

</script>

<template>
    <div class="bg-slate-900 dark:bg-indigo-600 p-6 rounded-3xl shadow-xl flex flex-col justify-center h-full group overflow-hidden relative border border-slate-800 dark:border-indigo-400/30">
        <div class="absolute -right-4 -top-4 opacity-5 group-hover:scale-110 group-hover:-rotate-12 transition-transform duration-700 pointer-events-none">
             <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            </svg>
        </div>
        
        <div class="relative z-10">
            <div class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-indigo-100/70 mb-3 italic flex items-center gap-2">
                <span class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-pulse"></span>
                Panel de Mantenimiento
            </div>
            
            <div class="grid grid-cols-2 gap-2">
                <button @click="clearCache" class="flex items-center justify-center gap-2 px-2 py-2 bg-white/5 hover:bg-white/10 text-white rounded-xl text-[9px] font-bold uppercase tracking-wider border border-white/10 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    Caché
                </button>
                <button @click="optimizeDb" class="flex items-center justify-center gap-2 px-2 py-2 bg-white/5 hover:bg-white/10 text-white rounded-xl text-[9px] font-bold uppercase tracking-wider border border-white/10 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>
                    Optimizar
                </button>
                
                <Link :href="route('admin.reports.index')" class="flex items-center justify-center gap-2 px-2 py-2 bg-rose-500/20 hover:bg-rose-500/30 text-rose-400 rounded-xl text-[9px] font-bold uppercase tracking-wider border border-rose-500/20 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    Reportes
                </Link>
                <button @click="fetchLogs" :disabled="loadingLogs" class="flex items-center justify-center gap-2 px-2 py-2 bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-400 rounded-xl text-[9px] font-bold uppercase tracking-wider border border-indigo-500/20 transition-all disabled:opacity-50">
                    <svg v-if="!loadingLogs" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    <span v-if="loadingLogs" class="animate-spin text-[10px]">🌀</span>
                    <span v-else>Logs</span>
                </button>
            </div>

            <div class="mt-3">
                <Link :href="route('admin.analytics')" class="flex items-center justify-center gap-2 w-full px-2 py-2 bg-emerald-500 text-white rounded-xl text-[9px] font-black uppercase tracking-[0.1em] shadow-lg shadow-emerald-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    Ver Analíticas Globales
                </Link>
            </div>
        </div>
    </div>
</template>
