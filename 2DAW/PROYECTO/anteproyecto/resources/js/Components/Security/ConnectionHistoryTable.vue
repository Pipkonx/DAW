<script setup>
import { 
    GlobeAltIcon, 
    DevicePhoneMobileIcon,
    ComputerDesktopIcon,
    MapPinIcon
} from '@heroicons/vue/24/outline';

defineProps({
    activities: Array,
    currentSessionId: String
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString('es-ES', {
        day: '2-digit',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getBrowserIcon = (browser) => {
    // Aquí podrías añadir lógica para iconos específicos de Chrome, Firefox, etc.
    return ComputerDesktopIcon;
};
</script>

<template>
    <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-xl overflow-hidden border border-slate-100 dark:border-slate-700">
        <div class="p-8 border-b border-slate-50 dark:border-slate-700 flex justify-between items-center">
            <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-widest flex items-center gap-3 italic">
                <GlobeAltIcon class="w-5 h-5 text-indigo-500" />
                Historial de Conexiones
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-8 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Evento / Dispositivo</th>
                        <th class="px-8 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Ubicación / IP</th>
                        <th class="px-8 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Navegador / SO</th>
                        <th class="px-8 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 text-right">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    <tr v-for="activity in activities" :key="activity.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors group">
                        <!-- Columna: Evento y Dispositivo -->
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div :class="[
                                    'w-10 h-10 rounded-2xl flex items-center justify-center shadow-inner',
                                    activity.type === 'login' ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/40' : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/40'
                                ]">
                                    <component :is="activity.device === 'mobile' ? DevicePhoneMobileIcon : ComputerDesktopIcon" class="w-5 h-5" />
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-700 dark:text-slate-200 capitalize text-sm">
                                            {{ activity.type.replace('_', ' ') }}
                                        </span>
                                        <span v-if="activity.session_id === currentSessionId" class="px-1.5 py-0.5 rounded-full text-[8px] font-black bg-emerald-500 text-white uppercase tracking-tighter">
                                            LIVE
                                        </span>
                                    </div>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">{{ activity.device || 'Escritorio' }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Columna: Ubicación e IP -->
                        <td class="px-8 py-5">
                            <div class="space-y-1">
                                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-600 dark:text-slate-300">
                                    <MapPinIcon class="w-3.5 h-3.5 text-rose-500" />
                                    {{ activity.city || 'Local' }}, {{ activity.country || 'Reserved' }}
                                </div>
                                <div class="text-[10px] font-mono text-slate-400 dark:text-slate-500">
                                    {{ activity.ip_address || '127.0.0.1' }}
                                </div>
                            </div>
                        </td>

                        <!-- Columna: Navegador y SO -->
                        <td class="px-8 py-5">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <!-- Icono dinámico según navegador -->
                                    <component :is="getBrowserIcon(activity.browser)" class="w-4 h-4 text-slate-400" />
                                    <span class="text-xs font-bold text-slate-600 dark:text-slate-300">
                                        {{ activity.browser }} {{ activity.browser_version }}
                                    </span>
                                </div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest pl-6">
                                    {{ activity.os }}
                                </p>
                            </div>
                        </td>

                        <!-- Columna: Fecha -->
                        <td class="px-8 py-5 text-right">
                            <div class="text-[11px] font-bold text-slate-700 dark:text-slate-300">
                                {{ formatDate(activity.created_at) }}
                            </div>
                            <div class="text-[9px] text-slate-400 uppercase font-black tracking-tighter">
                                {{ activity.session_id === currentSessionId ? 'Sesión Actual' : 'Histórico' }}
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
