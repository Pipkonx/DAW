<script setup>
import { useForm } from '@inertiajs/vue3';

defineProps({
    backups: Array,
});

const form = useForm({});
const importForm = useForm({
    backup_file: null,
});

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

const triggerFileInput = () => {
    document.getElementById('backup-upload').click();
};

const triggerDirectRestoreInput = () => {
    document.getElementById('backup-direct-restore').click();
};
</script>

<template>
    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl overflow-hidden border border-slate-100 dark:border-slate-700 mt-8">
        <div class="px-8 py-6 border-b border-slate-50 dark:border-slate-700 lg:flex lg:items-center lg:justify-between gap-4">
            <div class="mb-4 lg:mb-0">
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-1">Copias de Seguridad (Backup)</h3>
                <p class="text-slate-500 dark:text-slate-400 text-xs italic font-medium">Gestión de snapshots. Los backups preventivos se crean automáticamente antes de restaurar.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <!-- Inputs encapsulados -->
                <input type="file" id="backup-upload" class="hidden" @change="handleImport" accept=".sqlite">
                <input type="file" id="backup-direct-restore" class="hidden" @change="handleDirectRestore" accept=".sqlite">
                
                <button 
                    @click="triggerFileInput"
                    :disabled="importForm.processing"
                    class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all active:scale-95"
                    title="Subir archivo a la lista de backups"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                    </svg>
                    Subir
                </button>

                <button 
                    @click="triggerDirectRestoreInput"
                    class="flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-lg shadow-emerald-500/10 active:scale-95"
                    title="Subir y aplicar base de datos inmediatamente"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Restaurar
                </button>

                <button 
                    @click="generateBackup"
                    :disabled="form.processing"
                    class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 disabled:bg-slate-400 text-white px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-lg shadow-blue-500/10 active:scale-95"
                >
                    <svg v-if="!form.processing" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg v-else class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Snapshot
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
</template>
