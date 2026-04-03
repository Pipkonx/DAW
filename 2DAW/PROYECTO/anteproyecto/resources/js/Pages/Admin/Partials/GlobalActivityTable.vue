<script setup>
defineProps({
    global_activity: Array,
});
</script>

<template>
    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl overflow-hidden border border-slate-100 dark:border-slate-700">
        <div class="p-8 border-b border-slate-50 dark:border-slate-700 flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">Actividad Reciente del Sistema</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm italic">Monitoreo de transacciones globales de todos los usuarios.</p>
            </div>
            <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400">Usuario</th>
                        <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400">Activo</th>
                        <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400 text-center">Tipo</th>
                        <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400 text-right">Importe</th>
                        <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400 text-right">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700 text-sm">
                    <tr v-for="activity in global_activity" :key="activity.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-8 py-4">
                            <span class="font-bold text-slate-800 dark:text-white">{{ activity.user }}</span>
                        </td>
                        <td class="px-8 py-4 text-slate-600 dark:text-slate-300 font-medium">{{ activity.asset }}</td>
                        <td class="px-8 py-4 text-center">
                            <span class="px-2 py-1 rounded text-[10px] font-black uppercase tracking-widest" :class="activity.type === 'buy' || activity.type === 'income' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30'">
                                {{ activity.type }}
                            </span>
                        </td>
                        <td class="px-8 py-4 text-right font-bold text-slate-800 dark:text-white">
                            {{ activity.amount.toLocaleString('en-US', { style: 'currency', currency: 'USD' }) }}
                        </td>
                        <td class="px-8 py-4 text-right text-slate-500 dark:text-slate-400 text-xs">{{ activity.date }}</td>
                    </tr>
                    <tr v-if="global_activity.length === 0">
                        <td colspan="5" class="px-8 py-12 text-center text-slate-400 italic">No hay actividad reciente registrada en el sistema.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
