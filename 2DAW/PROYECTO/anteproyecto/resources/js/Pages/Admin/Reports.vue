<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import ModalConfirm from '@/Components/ModalConfirm.vue';

const props = defineProps({
    reports: Object, // Paginado
});

const form = useForm({});

const confirmModal = ref({
    show: false,
    title: '',
    message: '',
    type: 'danger',
    onConfirm: () => {}
});

const dismissReport = (id) => {
    confirmModal.value = {
        show: true,
        title: '¿Descartar Reporte?',
        message: 'Esta acción ignorará las denuncias actuales. El contenido se mantendrá visible en la plataforma.',
        type: 'info',
        onConfirm: () => {
            form.delete(route('admin.reports.dismiss', id), {
                preserveScroll: true,
                onSuccess: () => confirmModal.value.show = false
            });
        }
    };
};

const takeAction = (id) => {
    confirmModal.value = {
        show: true,
        title: '¿Eliminar Contenido?',
        message: 'Se procederá a borrar definitivamente el post/comentario reportado y se cerrará el caso. Esta acción es irreversible.',
        type: 'danger',
        onConfirm: () => {
            form.delete(route('admin.reports.action', id), {
                preserveScroll: true,
                onSuccess: () => confirmModal.value.show = false
            });
        }
    };
};

const getReportableTypeLabel = (type) => {
    if (type.includes('Post')) return 'Publicación';
    if (type.includes('Comment')) return 'Comentario';
    return 'Contenido';
};

</script>

<template>
    <Head title="Gestión de Reportes - Admin" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="font-black text-2xl text-slate-800 dark:text-white leading-tight">
                        Moderación de Reportes
                    </h2>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">Gestión de Seguridad & Protección a la Comunidad</p>
                </div>
                
                <div class="flex items-center gap-6 px-6 py-3 bg-slate-100 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                        <span class="text-[10px] font-black uppercase tracking-tighter text-slate-600">Comunidad Protegida</span>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Cabecera Rediseñada -->
                <div class="relative mb-12 p-8 rounded-[3rem] bg-slate-900 overflow-hidden shadow-2xl border border-slate-800 group">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-rose-500/10 blur-[80px] rounded-full -mr-20 -mt-20 group-hover:bg-rose-500/20 transition-all duration-700"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-500/10 blur-[80px] rounded-full -ml-20 -mb-20"></div>
                    
                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-3 bg-rose-500/20 rounded-2xl border border-rose-500/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-black uppercase tracking-[0.3em] text-rose-400">Seguridad & Moderación</span>
                            </div>
                            <h3 class="text-4xl font-black text-white mb-2 leading-none">Centro de Moderación</h3>
                            <p class="text-slate-400 text-lg max-w-xl font-medium leading-relaxed italic">
                                "Gestiona las denuncias de la comunidad y preserva la integridad del ecosistema."
                            </p>
                        </div>
                        <div class="flex gap-4">
                            <div class="px-6 py-4 bg-white/5 border border-white/10 rounded-3xl backdrop-blur-md">
                                <div class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1">Reportes Pendientes</div>
                                <div class="text-2xl font-bold text-white">{{ reports.total }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Reportes -->
                <div v-if="reports.data.length > 0" class="grid grid-cols-1 gap-6">
                    <div v-for="report in reports.data" :key="report.id" 
                         class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl border border-slate-100 dark:border-slate-700 overflow-hidden transform transition-all hover:shadow-2xl">
                        
                        <div class="p-6 sm:p-8 flex flex-col md:flex-row gap-8">
                            
                            <!-- Columna Izquierda: Info del Reporte -->
                            <div class="flex-1 space-y-4">
                                <div class="flex items-center gap-3">
                                    <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-[10px] font-black uppercase tracking-widest rounded-full">
                                        Pendiente
                                    </span>
                                    <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">
                                        Reportado el {{ new Date(report.created_at).toLocaleDateString() }}
                                    </span>
                                </div>

                                <div>
                                    <h4 class="text-xs font-black uppercase text-slate-400 tracking-widest mb-2">Denunciante</h4>
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-[10px] font-bold">
                                            {{ report.user.name.charAt(0) }}
                                        </div>
                                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ report.user.name }}</span>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-xs font-black uppercase text-slate-400 tracking-widest mb-2">Motivo de la denuncia</h4>
                                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-700 italic">
                                        "{{ report.reason }}"
                                    </p>
                                </div>
                            </div>

                            <!-- Columna Central: Previsualización del Contenido -->
                            <div class="flex-[1.5] space-y-4 border-t md:border-t-0 md:border-l border-slate-100 dark:border-slate-700 md:pl-8 pt-6 md:pt-0">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-xs font-black uppercase text-slate-400 tracking-widest">
                                        Contenido: {{ getReportableTypeLabel(report.reportable_type) }}
                                    </h4>
                                    <span v-if="report.reportable" class="text-[10px] font-bold text-slate-400 italic">ID: #{{ report.reportable.id }}</span>
                                </div>

                                <div v-if="report.reportable" class="space-y-4">
                                    <!-- Autor del contenido -->
                                    <div class="flex items-center gap-2 mb-3">
                                        <img v-if="report.reportable.user.avatar" :src="report.reportable.user.avatar" class="w-8 h-8 rounded-full border border-slate-200">
                                        <div v-else class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs font-bold text-slate-500">
                                            {{ report.reportable.user.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-slate-800 dark:text-white leading-none">{{ report.reportable.user.name }}</div>
                                            <div class="text-[10px] text-slate-500 dark:text-slate-400 italic">@{{ report.reportable.user.username }}</div>
                                        </div>
                                    </div>

                                    <!-- Texto del contenido -->
                                    <div class="text-sm text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800/50 p-4 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-inner">
                                        {{ report.reportable.content }}
                                    </div>

                                    <!-- Imagen si existe (solo para posts) -->
                                    <div v-if="report.reportable.image_path" class="mt-4 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700">
                                        <img :src="'/storage/' + report.reportable.image_path" class="w-full h-48 object-cover">
                                    </div>
                                </div>

                                <div v-else class="p-8 text-center bg-rose-50 dark:bg-rose-900/10 rounded-2xl border border-rose-100 dark:border-rose-900/30">
                                    <p class="text-rose-500 dark:text-rose-400 text-sm font-medium italic">Este contenido ya ha sido eliminado por el autor o por otra acción de moderación.</p>
                                </div>
                            </div>

                            <!-- Columna Derecha: Acciones -->
                            <div class="md:w-48 flex flex-col justify-center gap-3">
                                <button 
                                    @click="dismissReport(report.id)"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-black uppercase tracking-widest transition-all active:scale-95 shadow-sm"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Ignorar
                                </button>
                                
                                <button 
                                    @click="takeAction(report.id)"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all active:scale-95 shadow-lg shadow-rose-500/20"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Eliminar
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <div v-else class="bg-white dark:bg-slate-800 rounded-3xl p-16 text-center shadow-xl border border-slate-100 dark:border-slate-700">
                    <div class="inline-flex p-6 bg-emerald-50 dark:bg-emerald-900/20 rounded-full mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h4 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">Comunidad en calma</h4>
                    <p class="text-slate-500 dark:text-slate-400 italic">No hay reportes pendientes para revisar. ¡Buen trabajo!</p>
                </div>

                <!-- Paginación Simple (Inertia suele proveer links pero aquí haremos un placeholder básico por brevedad si no están definidos) -->
                <div v-if="reports.links && reports.links.length > 3" class="mt-8 flex justify-center">
                    <div class="flex gap-1">
                        <template v-for="link in reports.links" :key="link.label">
                            <div v-if="link.url === null" class="px-4 py-2 text-slate-400 text-xs border border-slate-200 dark:border-slate-700 rounded-lg cursor-not-allowed" v-html="link.label"></div>
                            <Link v-else :href="link.url" 
                                  class="px-4 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg transition-colors"
                                  :class="link.active ? 'bg-indigo-600 text-white border-indigo-700 font-bold' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'"
                                  v-html="link.label" />
                        </template>
                    </div>
                </div>

            </div>
        </div>

        <!-- Modal de Confirmación Premium -->
        <ModalConfirm 
            :show="confirmModal.show"
            :title="confirmModal.title"
            :message="confirmModal.message"
            :type="confirmModal.type"
            @confirm="confirmModal.onConfirm"
            @cancel="confirmModal.show = false"
        />
    </AuthenticatedLayout>
</template>
