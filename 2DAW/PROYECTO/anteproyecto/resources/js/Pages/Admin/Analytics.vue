<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    metrics: Object
});

// Formatear números para visualización
const formatNumber = (num) => {
    return new Intl.NumberFormat('es-ES').format(num);
};

// Obtener el máximo para escalas visuales simples
const maxUserGrowth = computed(() => Math.max(...props.metrics.user_growth.map(d => d.count), 1));
const maxActivity = computed(() => {
    const p = props.metrics.post_activity.map(d => d.count);
    const c = props.metrics.comment_activity.map(d => d.count);
    return Math.max(...p, ...c, 1);
});

const getProgressWidth = (value, max) => {
    return (value / max * 100) + '%';
};

</script>

<template>
    <Head title="Analíticas Avanzadas - Admin" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="font-black text-2xl text-slate-800 dark:text-white leading-tight">
                        Analíticas del Ecosistema
                    </h2>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">Métricas de Crecimiento y Actividad Social</p>
                </div>
                
                <div class="flex items-center gap-6 px-6 py-3 bg-slate-100 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                        <span class="text-[10px] font-black uppercase tracking-tighter text-emerald-600">Tiempo Real</span>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- KPIs Superiores -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-xl border border-slate-100 dark:border-slate-700 relative overflow-hidden group hover:scale-[1.02] transition-transform">
                        <div class="absolute -right-4 -top-4 w-32 h-32 bg-blue-500/10 blur-3xl rounded-full"></div>
                        <div class="relative z-10">
                            <span class="text-[10px] font-black uppercase tracking-widest text-blue-500 mb-2 block">Usuarios Activos (24h)</span>
                            <div class="text-4xl font-black text-slate-900 dark:text-white">{{ metrics.totals.active_users_24h }}</div>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-xl border border-slate-100 dark:border-slate-700 relative overflow-hidden group hover:scale-[1.02] transition-transform">
                        <div class="absolute -right-4 -top-4 w-32 h-32 bg-emerald-500/10 blur-3xl rounded-full"></div>
                        <div class="relative z-10">
                            <span class="text-[10px] font-black uppercase tracking-widest text-emerald-500 mb-2 block">Nuevos Posts (24h)</span>
                            <div class="text-4xl font-black text-slate-900 dark:text-white">{{ metrics.totals.new_posts_24h }}</div>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-xl border border-slate-100 dark:border-slate-700 relative overflow-hidden group hover:scale-[1.02] transition-transform">
                        <div class="absolute -right-4 -top-4 w-32 h-32 bg-amber-500/10 blur-3xl rounded-full"></div>
                        <div class="relative z-10">
                            <span class="text-[10px] font-black uppercase tracking-widest text-amber-500 mb-2 block">Volumen Total</span>
                            <div class="text-4xl font-black text-slate-900 dark:text-white">{{ formatNumber(metrics.totals.total_volume) }}</div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    <!-- Gráfico: Crecimiento de Usuarios -->
                    <div class="bg-white dark:bg-slate-800 rounded-[3rem] p-8 shadow-2xl border border-slate-100 dark:border-slate-700">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h4 class="text-lg font-black text-slate-800 dark:text-white">Crecimiento de Usuarios</h4>
                                <p class="text-xs text-slate-400 font-bold italic">Nuevos registros los últimos 30 días</p>
                            </div>
                        </div>
                        
                        <div class="flex items-end gap-1.5 h-48 mb-4">
                            <div v-for="day in metrics.user_growth" :key="day.date" 
                                 class="flex-1 bg-blue-500/20 hover:bg-blue-500 rounded-t-lg transition-all relative group"
                                 :style="{ height: getProgressWidth(day.count, maxUserGrowth) }">
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 bg-slate-900 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-10">
                                    {{ day.count }} users
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between text-[8px] font-bold text-slate-400 uppercase tracking-widest">
                            <span>Hace 30 días</span>
                            <span>Hoy</span>
                        </div>
                    </div>

                    <!-- Activos más populares -->
                    <div class="bg-white dark:bg-slate-800 rounded-[3rem] p-8 shadow-2xl border border-slate-100 dark:border-slate-700">
                        <h4 class="text-lg font-black text-slate-800 dark:text-white mb-8">Activos más mencionados</h4>
                        <div class="space-y-6">
                            <div v-for="asset in metrics.popular_assets" :key="asset.ticker" class="group">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-black text-slate-700 dark:text-slate-300">${{ asset.ticker }} <span class="font-bold text-slate-400 ml-2">- {{ asset.name }}</span></span>
                                    <span class="text-xs font-black text-indigo-500">{{ asset.mentions }} menciones</span>
                                </div>
                                <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-900 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-500 group-hover:bg-indigo-400 transition-all" 
                                         :style="{ width: (asset.mentions / metrics.popular_assets[0].mentions * 100) + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Distribución de Transacciones -->
                    <div class="bg-white dark:bg-slate-800 rounded-[3rem] p-8 shadow-2xl border border-slate-100 dark:border-slate-700">
                        <h4 class="text-lg font-black text-slate-800 dark:text-white mb-8">Tipos de Operaciones</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div v-for="tx in metrics.tx_distribution" :key="tx.type" class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-3xl border border-slate-100 dark:border-slate-700">
                                <span class="text-[10px] font-black uppercase text-slate-400 block mb-1">{{ tx.type }}</span>
                                <div class="text-2xl font-bold text-slate-800 dark:text-white">{{ tx.count }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Actividad Social (Resumen) -->
                    <div class="bg-indigo-600 rounded-[3rem] p-8 shadow-2xl text-white relative overflow-hidden">
                        <div class="absolute right-0 bottom-0 opacity-10">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-64 w-64" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <h4 class="text-lg font-black mb-8 italic">Snapshot de Actividad</h4>
                        <div class="space-y-8 relative z-10">
                            <div class="flex items-center gap-6">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-indigo-200">Posts Totales</span>
                                    <span class="text-5xl font-black">{{ formatNumber(metrics.post_activity.reduce((a, b) => a + b.count, 0)) }}</span>
                                </div>
                                <div class="flex flex-col border-l border-white/20 pl-6">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-indigo-200">Comentarios</span>
                                    <span class="text-5xl font-black">{{ formatNumber(metrics.comment_activity.reduce((a, b) => a + b.count, 0)) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
