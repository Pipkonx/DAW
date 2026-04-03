<script setup>
defineProps({
    api_consumption: Object,
});
</script>

<template>
    <div class="space-y-8">
        <!-- Monitor de Cuotas y Telemetría de Servicios -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-xl border border-slate-100 dark:border-slate-700 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none group-hover:scale-110 transition-transform duration-700">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>

            <div class="flex flex-col md:flex-row items-center justify-between mb-8 gap-4 relative z-10">
                <div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white tracking-tight">Telemetría de Servicios</h3>
                    <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm font-medium italic">Acceso y cuotas de APIs en tiempo real.</p>
                </div>
                <div class="flex items-center gap-3 bg-slate-50 dark:bg-slate-700/50 px-4 py-2 rounded-2xl border border-slate-100 dark:border-slate-600">
                    <div class="flex -space-x-1.5">
                        <div v-for="i in 3" :key="i" class="w-6 h-6 rounded-full bg-emerald-500 border-2 border-white dark:border-slate-800 shadow-sm"></div>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Online</span>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                <div v-for="(data, api) in api_consumption" :key="api" class="space-y-2.5 p-4 rounded-2xl bg-slate-50/50 dark:bg-slate-900/30 border border-slate-100/50 dark:border-white/5 transition-all">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full shadow-[0_0_8px_rgba(var(--tw-color-emerald-500),0.3)]" :class="`bg-${data.status}-500`"></div>
                            <span class="text-[10px] font-black uppercase text-slate-700 dark:text-slate-200 tracking-widest font-mono">{{ api }}</span>
                        </div>
                        <span :class="[`text-${data.status}-500`, 'text-[10px]', 'font-bold', 'font-mono']">
                            {{ data.used }} / {{ data.limit }}
                        </span>
                    </div>
                    <div class="h-2 bg-slate-100 dark:bg-slate-700/50 rounded-full overflow-hidden p-0.5">
                        <div 
                            class="h-full rounded-full transition-all duration-1000 ease-out"
                            :class="`bg-${data.status}-500`"
                            :style="{ width: `${data.percentage}%` }"
                        ></div>
                    </div>
                    <div class="flex justify-between items-center text-[9px] font-bold uppercase tracking-tighter text-slate-400">
                        <span>Carga Global</span>
                        <span :class="`text-${data.status}-500`">{{ data.percentage }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Automatización de Backups -->
        <div class="bg-gradient-to-tr from-slate-900 via-slate-800 to-indigo-900 p-6 rounded-3xl shadow-xl relative overflow-hidden group border border-white/5">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-5 pointer-events-none"></div>
            
            <div class="flex flex-col md:flex-row items-center gap-8 relative z-10">
                <div class="p-5 bg-white/10 backdrop-blur-xl rounded-2xl border border-white/10 group-hover:scale-105 transition-transform duration-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                
                <div class="flex-1 text-center md:text-left">
                    <h3 class="text-xl font-black text-white tracking-tight">Gestión Autónoma de Respaldo</h3>
                    <p class="text-indigo-200/60 mt-1.5 text-sm leading-relaxed">
                        Instantáneas diarias (<span class="text-white font-bold">03:00 AM</span>). 
                        Ventana de retención fija: <span class="text-white font-bold underline decoration-indigo-400">Últimas 5 copias</span>.
                    </p>
                    
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mt-4">
                        <div class="flex items-center gap-2 px-4 py-1.5 bg-emerald-500/20 text-emerald-400 rounded-xl border border-emerald-500/20 text-[10px] font-bold uppercase tracking-widest">
                            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-ping"></div>
                            Sincronizado
                        </div>
                        <div class="px-4 py-1.5 bg-white/5 text-slate-400 rounded-xl border border-white/10 text-[9px] font-black uppercase tracking-[0.2em]">
                            Encrypted
                        </div>
                    </div>
                </div>

                <div class="hidden md:block w-px h-16 bg-white/10"></div>

                <div class="text-center md:px-4">
                    <div class="text-3xl font-black text-white">5/5</div>
                    <div class="text-[9px] font-black uppercase tracking-[0.2em] text-indigo-300/40">Slots</div>
                </div>
            </div>
        </div>
    </div>
</template>
