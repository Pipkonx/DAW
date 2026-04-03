<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    backups: Array,
    users: Array,
    stats: Object,
    api_health: Object,
    api_consumption: Object,
    global_activity: Array,
});

const form = useForm({});
const importForm = useForm({
    backup_file: null,
});

const systemLogs = ref('');
const loadingLogs = ref(false);

const generateBackup = () => {
    form.post(route('admin.backup.generate'), { preserveScroll: true });
};

const restoreBackup = (filename) => {
    if (confirm('¿ESTÁS SEGURO? Esta acción sobrescribirá la base de datos actual con los datos de esta copia. El sistema realizará una copia preventiva automática.')) {
        form.post(route('admin.backup.restore', filename), { preserveScroll: true });
    }
};

const deleteBackup = (filename) => {
    if (confirm('¿Estás seguro de eliminar este backup?')) {
        form.delete(route('admin.backup.delete', filename), { preserveScroll: true });
    }
};

const toggleAdmin = (user) => {
    if (confirm(`¿Cambiar el rol de ${user.name}?`)) {
        form.post(route('admin.users.toggle-admin', user.id), { preserveScroll: true });
    }
};

const deleteUser = (user) => {
    if (confirm(`¿ELIMINAR DEFINITIVAMENTE A ${user.name}? Esta acción borrará todos sus datos y transacciones.`)) {
        form.delete(route('admin.users.delete', user.id), { preserveScroll: true });
    }
};

const optimizeDb = () => {
    form.post(route('admin.system.optimize'), { preserveScroll: true });
};

const clearCache = () => {
    form.post(route('admin.system.clear-cache'), { preserveScroll: true });
};

const triggerFileInput = () => {
    document.getElementById('backup-upload').click();
};

const handleImport = (e) => {
    importForm.backup_file = e.target.files[0];
    if (importForm.backup_file) {
        importForm.post(route('admin.backup.import'), {
            preserveScroll: true,
            onSuccess: () => {
                importForm.reset();
            }
        });
    }
};

const handleDirectRestore = (e) => {
    const file = e.target.files[0];
    if (file && confirm('⚠️ ATENCIÓN: Vas a SOBRESCRIBIR TODA LA BASE DE DATOS con este archivo. El sistema se reiniciará con los nuevos datos. ¿Deseas continuar?')) {
        const directForm = useForm({
            backup_file: file,
        });
        directForm.post(route('admin.backup.restore.direct'), {
            preserveScroll: true,
            onSuccess: () => {
                alert('Sistema restaurado con éxito.');
            }
        });
    }
};

const fetchLogs = async () => {
    loadingLogs.value = true;
    try {
        const response = await axios.get(route('admin.system.logs'));
        systemLogs.value = response.data.logs;
    } catch (e) {
        systemLogs.value = "Error recuperando logs...";
    } finally {
        loadingLogs.value = false;
    }
};

</script>

<template>
    <Head title="Panel de Administración" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-slate-800 dark:text-white leading-tight">
                    Centro de Mando Administrativo
                </h2>
                <div class="flex gap-4">
                    <div v-for="(status, api) in api_health" :key="api" class="flex items-center gap-2 px-3 py-1 bg-white dark:bg-slate-800 rounded-full border border-slate-100 dark:border-slate-700 shadow-sm">
                        <div class="w-2 h-2 rounded-full" :class="status ? 'bg-emerald-500' : 'bg-rose-500'"></div>
                        <span class="text-[10px] font-black uppercase text-slate-500">{{ api }}</span>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Telemetría y Mantenimiento -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                        <div class="text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">Usuarios Totales</div>
                        <div class="text-3xl font-bold text-slate-800 dark:text-white">{{ stats.users }}</div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                        <div class="text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">Peso DB (SQLite)</div>
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ stats.db_size }}</div>
                    </div>
                    <div class="md:col-span-2 bg-slate-800 dark:bg-blue-600 p-6 rounded-2xl shadow-lg flex items-center justify-between group overflow-hidden relative">
                        <div class="relative z-10">
                            <div class="text-xs font-black uppercase tracking-widest text-blue-200 mb-2 italic">Mantenimiento Global</div>
                            <div class="flex gap-3">
                                <button @click="clearCache" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-sm font-bold border border-white/20 transition-all">Limpiar Caché</button>
                                <button @click="optimizeDb" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-sm font-bold border border-white/20 transition-all">Optimizar DB</button>
                                <button @click="fetchLogs" class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-500/20 transition-all ml-2">Ver Logs</button>
                            </div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 absolute -right-4 -bottom-4 text-white/10 group-hover:rotate-12 transition-transform duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Consumo de Recursos Externos y Configuración de Tareas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Monitor de APIs -->
                    <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl shadow-xl border border-slate-100 dark:border-slate-700">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h3 class="text-xl font-bold text-slate-800 dark:text-white">Telemetría de Servicios</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Cuotas y límites de consumo en tiempo real.</p>
                            </div>
                            <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <div v-for="(data, api) in api_consumption" :key="api" class="space-y-3 group">
                                <div class="flex justify-between items-end">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full" :class="`bg-${data.status}-500`"></div>
                                        <span class="text-xs font-black uppercase text-slate-600 dark:text-slate-400 tracking-wider">{{ api }}</span>
                                    </div>
                                    <span :class="[`text-${data.status}-500`, 'text-[10px]', 'font-black', 'text-right']">
                                        {{ data.used }} / {{ data.limit }}
                                    </span>
                                </div>
                                <div class="h-2 bg-slate-100 dark:bg-slate-700/50 rounded-full overflow-hidden shadow-inner">
                                    <div 
                                        class="h-full transition-all duration-700 ease-out"
                                        :class="`bg-${data.status}-500 shadow-[0_0_10px_rgba(var(--tw-color-${data.status}-500),0.4)]`"
                                        :style="{ width: `${data.percentage}%` }"
                                    ></div>
                                </div>
                                <p v-if="data.warning" class="text-[9px] text-rose-500 font-black animate-pulse">
                                    ⚠️ CUOTA CRÍTICA ({{ api }})
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Estado del Planificador -->
                    <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl shadow-xl border border-slate-100 dark:border-slate-700 flex flex-col justify-center relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                            </svg>
                        </div>
                        <div class="text-center space-y-4">
                            <div class="inline-flex p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-3xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-800 dark:text-white">Automatización de Backups</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 max-w-xs mx-auto leading-relaxed">
                                    Instantáneas diarias del sistema programadas a las <span class="font-bold text-slate-800 dark:text-white">03:00 AM</span>. 
                                    Se mantienen las últimas 30 copias.
                                </p>
                            </div>
                            <div class="pt-4 flex items-center justify-center gap-2">
                                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-ping"></div>
                                <span class="text-[10px] font-black uppercase text-emerald-600 dark:text-emerald-400 tracking-widest">Servicio de Sincronización Activo</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visor de Logs del Sistema (Condicional) -->
                <div v-if="systemLogs" class="bg-slate-900 rounded-3xl shadow-2xl overflow-hidden border border-slate-800 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div class="px-8 py-4 bg-slate-800/50 flex justify-between items-center border-b border-slate-700">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                            <span class="text-xs font-black uppercase tracking-widest text-slate-300">Laravel System Logs (Últimas 50 líneas)</span>
                        </div>
                        <button @click="systemLogs = ''" class="text-slate-500 hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    <div class="p-6">
                        <pre class="text-[10px] sm:text-xs font-mono text-emerald-400 bg-black/30 p-4 rounded-xl overflow-x-auto max-h-96 scrollbar-thin scrollbar-thumb-slate-700 leading-relaxed">{{ systemLogs }}</pre>
                    </div>
                </div>

                <!-- Actividad Global Reciente -->
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

                <!-- Centro de Control de Backups -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl overflow-hidden border border-slate-100 dark:border-slate-700">
                    <div class="p-8 border-b border-slate-50 dark:border-slate-700 md:flex md:items-center md:justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">Copias de Seguridad (Backup)</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm italic">Gestión de snapshots. Los backups preventivos se crean automáticamente antes de restaurar.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 mt-4 md:mt-0">
                            <!-- Input para importar -->
                            <input type="file" id="backup-upload" class="hidden" @change="handleImport" accept=".sqlite">
                            <input type="file" id="backup-direct-restore" class="hidden" @change="handleDirectRestore" accept=".sqlite">
                            
                            <button 
                                @click="triggerFileInput"
                                :disabled="importForm.processing"
                                class="flex items-center gap-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-lg text-sm font-bold transition-all active:scale-95"
                                title="Subir archivo a la lista de backups"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                                </svg>
                                Solo Subir
                            </button>

                            <button 
                                @click="() => document.getElementById('backup-direct-restore').click()"
                                class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-all shadow-lg shadow-emerald-500/10 active:scale-95"
                                title="Subir y aplicar base de datos inmediatamente"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                Restauración Directa
                            </button>

                            <button 
                                @click="generateBackup"
                                :disabled="form.processing"
                                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 disabled:bg-slate-400 text-white px-4 py-2 rounded-lg text-sm font-bold transition-all shadow-lg shadow-blue-500/10 active:scale-95"
                            >
                                <svg v-if="!form.processing" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <svg v-else class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Generar Snapshot
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 dark:bg-slate-900/50">
                                <tr>
                                    <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400">Archivo</th>
                                    <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400 text-center">Tamaño</th>
                                    <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400">Fecha</th>
                                    <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-700 text-sm">
                                <tr v-for="backup in backups" :key="backup.name" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors">
                                    <td class="px-8 py-4 font-mono text-slate-600 dark:text-slate-300">{{ backup.name }}</td>
                                    <td class="px-8 py-4 text-center">
                                        <span class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-[10px] font-bold text-slate-500">{{ backup.size }}</span>
                                    </td>
                                    <td class="px-8 py-4 text-slate-500 dark:text-slate-400">{{ backup.created_at }}</td>
                                    <td class="px-8 py-4 text-right flex justify-end gap-2">
                                        <button 
                                            @click="restoreBackup(backup.name)"
                                            class="p-2 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-colors"
                                            title="Restaurar este backup"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                            </svg>
                                        </button>
                                        <a 
                                            :href="route('admin.backup.download', backup.name)"
                                            class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                                            target="_blank"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                            </svg>
                                        </a>
                                        <button 
                                            @click="deleteBackup(backup.name)"
                                            class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-colors"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Gestión de Usuarios -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl overflow-hidden border border-slate-100 dark:border-slate-700">
                    <div class="p-8 border-b border-slate-50 dark:border-slate-700">
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">Gestión de Usuarios</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm italic">Control de accesos y roles administrativos.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 dark:bg-slate-900/50">
                                <tr>
                                    <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400">Usuario</th>
                                    <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400">Email</th>
                                    <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400">Rol</th>
                                    <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-700 text-sm">
                                <tr v-for="user in users" :key="user.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors">
                                    <td class="px-8 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs">
                                                {{ user.name.substring(0,2).toUpperCase() }}
                                            </div>
                                            <span class="font-bold text-slate-800 dark:text-white">{{ user.name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-4 text-slate-500 dark:text-slate-400">{{ user.email }}</td>
                                    <td class="px-8 py-4">
                                        <span v-if="user.is_admin" class="px-3 py-1 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase rounded-full">Administrador</span>
                                        <span v-else class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-500 text-[10px] font-black uppercase rounded-full">Usuario</span>
                                    </td>
                                    <td class="px-8 py-4 text-right flex justify-end gap-2">
                                        <button 
                                            @click="toggleAdmin(user)"
                                            class="p-2 text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors"
                                            title="Cambiar Rol"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                            </svg>
                                        </button>
                                        <button 
                                            @click="deleteUser(user)"
                                            class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-colors"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6 4.125 2.25 2.25m0 0 2.25 2.25m-2.25-2.25-2.25 2.25m2.25-2.25-2.25-2.25M3.75 7.5l.625-10.632A2.25 2.25 0 0 1 6.622 3h10.756a2.25 2.25 0 0 1 2.247 2.118L20.25 7.5" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
