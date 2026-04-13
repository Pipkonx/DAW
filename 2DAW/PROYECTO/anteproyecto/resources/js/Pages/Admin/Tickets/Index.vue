<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { 
    ChatBubbleLeftRightIcon, 
    CheckCircleIcon, 
    ClockIcon, 
    ExclamationCircleIcon,
    FunnelIcon 
} from '@heroicons/vue/24/outline';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    tickets: Object,
    filters: Object,
});

const statusFilter = ref(props.filters.status || 'all');
const priorityFilter = ref(props.filters.priority || 'all');

const updateFilters = debounce(() => {
    router.get(route('admin.tickets.index'), {
        status: statusFilter.value,
        priority: priorityFilter.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}, 300);

watch([statusFilter, priorityFilter], updateFilters);

const getStatusClass = (status) => {
    switch (status) {
        case 'open': return 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400';
        case 'answered': return 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-400';
        case 'closed': return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400';
        default: return 'bg-slate-100 text-slate-700 dark:bg-slate-900/40 dark:text-slate-400';
    }
};
</script>

<template>
    <Head title="Gestión de Tickets" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-tight italic">
                        Centro de Soporte Admin
                    </h2>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">
                        Gestión total de consultas técnicas y atención al usuario
                    </p>
                </div>

                <!-- Filtros Rápidos -->
                <div class="flex gap-4">
                    <select v-model="statusFilter" class="bg-white dark:bg-slate-800 border-none rounded-2xl text-xs font-bold uppercase tracking-wider text-slate-500 focus:ring-2 focus:ring-indigo-500 shadow-sm px-6 py-2.5">
                        <option value="all">Todos los Estados</option>
                        <option value="open">Abiertos</option>
                        <option value="answered">Respondidos</option>
                        <option value="closed">Cerrados</option>
                    </select>

                    <select v-model="priorityFilter" class="bg-white dark:bg-slate-800 border-none rounded-2xl text-xs font-bold uppercase tracking-wider text-slate-500 focus:ring-2 focus:ring-indigo-500 shadow-sm px-6 py-2.5">
                        <option value="all">Todas las Prioridades</option>
                        <option value="low">Baja</option>
                        <option value="medium">Media</option>
                        <option value="high">Alta</option>
                    </select>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="bg-white dark:bg-slate-800 rounded-[3rem] shadow-2xl overflow-hidden border border-slate-100 dark:border-slate-700">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700">
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Usuario / ID</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Asunto</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Estado</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Prioridad</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            <tr v-for="ticket in tickets.data" :key="ticket.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center font-black text-indigo-600 dark:text-indigo-400">
                                            {{ ticket.user.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ ticket.user.name }}</p>
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">#{{ ticket.id.toString().padStart(4, '0') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ ticket.subject }}</p>
                                    <p class="text-[10px] text-slate-400 line-clamp-1 italic font-medium">
                                        {{ ticket.messages[0]?.message || 'Sin mensajes' }}
                                    </p>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="text-[9px] font-black uppercase tracking-widest px-3 py-1 rounded-full whitespace-nowrap" :class="getStatusClass(ticket.status)">
                                        {{ ticket.status }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="text-[9px] font-black uppercase tracking-widest" :class="{
                                        'text-slate-400': ticket.priority === 'low',
                                        'text-amber-500': ticket.priority === 'medium',
                                        'text-rose-500': ticket.priority === 'high',
                                    }">
                                        {{ ticket.priority }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <Link 
                                        :href="route('admin.tickets.show', ticket.id)"
                                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-indigo-500/20 active:scale-95"
                                    >
                                        Atender
                                        <ChatBubbleLeftRightIcon class="w-4 h-4" />
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="tickets.data.length === 0">
                                <td colspan="5" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center gap-4 opacity-30">
                                        <ExclamationCircleIcon class="w-16 h-16 text-slate-400" />
                                        <p class="font-black uppercase tracking-widest text-xs text-slate-400">No se encontraron tickets con estos filtros</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Paginación -->
                    <div v-if="tickets.links.length > 3" class="px-8 py-6 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-center gap-2">
                        <Link v-for="link in tickets.links" :key="link.label"
                            :href="link.url || '#'"
                            class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"
                            :class="[
                                link.active ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white dark:bg-slate-800 text-slate-400 hover:bg-indigo-50 dark:hover:bg-slate-700',
                                !link.url && 'opacity-30 cursor-not-allowed'
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
