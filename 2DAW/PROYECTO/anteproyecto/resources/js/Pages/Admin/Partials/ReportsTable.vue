<script setup>
defineProps({
    reports: Array,
});
</script>

<template>
    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl overflow-hidden border border-slate-100 dark:border-slate-700 mt-8">
        <div class="p-8 border-b border-slate-50 dark:border-slate-700 flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">Moderación de Comunidad</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm italic">Postes y comentarios denunciados que requieren revisión.</p>
            </div>
            <div class="p-4 bg-rose-50 dark:bg-rose-900/20 rounded-2xl">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                 </svg>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400">Denunciante</th>
                        <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400">Razón</th>
                        <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400 text-center">Tipo</th>
                        <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400 text-right">Referencia ID</th>
                        <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400 text-right">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700 text-sm">
                    <tr v-for="report in reports" :key="report.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-8 py-4">
                            <span class="font-bold text-slate-800 dark:text-white">{{ report.user_name }}</span>
                        </td>
                        <td class="px-8 py-4 text-slate-600 dark:text-slate-300 font-medium max-w-sm truncate">{{ report.reason }}</td>
                        <td class="px-8 py-4 text-center">
                            <span class="px-2 py-1 rounded text-[10px] font-black uppercase tracking-widest bg-amber-100 text-amber-700 dark:bg-amber-900/30">
                                {{ report.type }}
                            </span>
                        </td>
                        <td class="px-8 py-4 text-right font-bold text-slate-800 dark:text-white">
                            #{{ report.ref_id }}
                        </td>
                        <td class="px-8 py-4 text-right text-slate-500 dark:text-slate-400 text-xs">{{ report.date }}</td>
                    </tr>
                    <tr v-if="reports.length === 0">
                        <td colspan="5" class="px-8 py-12 text-center text-slate-400 italic">No hay reportes pendientes en la plataforma.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template
