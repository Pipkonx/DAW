<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ChatBubbleLeftIcon, ArrowLeftIcon, PaperAirplaneIcon, CheckCircleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    ticket: Object,
    isAdminView: {
        type: Boolean,
        default: false,
    }
});

const form = useForm({
    message: '',
});

const submit = () => {
    const routeName = props.isAdminView ? 'admin.tickets.reply' : 'support.reply';
    form.post(route(routeName, props.ticket.id), {
        onSuccess: () => form.reset(),
    });
};

const closeTicket = () => {
    if (confirm('¿Estás seguro de que deseas cerrar este ticket?')) {
        form.post(route('admin.tickets.close', props.ticket.id));
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString('es-ES', {
        day: '2-digit',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head :title="`Ticket: ${ticket.subject}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <Link :href="isAdminView ? route('admin.tickets.index') : route('support.index')" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                        <ArrowLeftIcon class="w-6 h-6" />
                    </Link>
                    <div class="flex-1">
                        <h2 class="text-xl font-black text-slate-800 dark:text-white uppercase leading-tight tracking-tight">
                            {{ ticket.subject }}
                        </h2>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500">
                                ID: #{{ ticket.id.toString().padStart(4, '0') }}
                            </span>
                            <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full" :class="{
                                'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400': ticket.status === 'open',
                                'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-400': ticket.status === 'answered',
                                'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400': ticket.status === 'closed',
                            }">
                                {{ ticket.status }}
                            </span>
                            <span v-if="isAdminView" class="text-[9px] font-black uppercase tracking-widest text-slate-400">
                                Usuario: {{ ticket.user.name }}
                            </span>
                        </div>
                    </div>
                </div>

                <button 
                    v-if="isAdminView && ticket.status !== 'closed'"
                    @click="closeTicket"
                    class="flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 rounded-xl transition-all font-bold text-xs uppercase tracking-widest"
                >
                    <CheckCircleIcon class="w-4 h-4" />
                    Cerrar Ticket
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Historial de Mensajes -->
                <div class="space-y-6 mb-8">
                    <div v-for="msg in ticket.messages" :key="msg.id" 
                        class="flex flex-col"
                        :class="[msg.user_id === $page.props.auth.user.id ? 'items-end' : 'items-start']">
                        
                        <div class="mb-1 flex items-center gap-2 px-2" :class="[msg.user_id === $page.props.auth.user.id ? 'flex-row-reverse' : 'flex-row']">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ msg.user.name }}</span>
                            <span class="text-[9px] text-slate-400 font-bold opacity-60">{{ formatDate(msg.created_at) }}</span>
                        </div>

                        <div class="p-5 rounded-[2rem] max-w-[85%] text-sm shadow-lg leading-relaxed"
                            :class="[
                                msg.user_id === $page.props.auth.user.id 
                                    ? 'bg-indigo-600 text-white rounded-tr-none shadow-indigo-500/10' 
                                    : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-tl-none border border-slate-100 dark:border-slate-700'
                            ]">
                            {{ msg.message }}
                        </div>
                    </div>
                </div>

                <!-- Caja de Respuesta -->
                <div v-if="ticket.status !== 'closed'" class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-4 shadow-2xl border border-slate-100 dark:border-slate-700 sticky bottom-8">
                    <form @submit.prevent="submit" class="flex items-end gap-4">
                        <div class="flex-1">
                            <textarea 
                                v-model="form.message" 
                                rows="2" 
                                placeholder="Escribe tu respuesta aquí..." 
                                class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-[1.5rem] py-4 px-6 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all resize-none shadow-inner"
                                required
                            ></textarea>
                        </div>
                        <button 
                            type="submit" 
                            :disabled="form.processing || !form.message.trim()"
                            class="mb-1 p-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl shadow-xl shadow-indigo-500/30 transition-all disabled:opacity-50 active:scale-95 group"
                        >
                            <PaperAirplaneIcon class="w-6 h-6 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform" />
                        </button>
                    </form>
                </div>

                <div v-else class="text-center p-8 bg-slate-100 dark:bg-slate-900/50 rounded-3xl border border-dashed border-slate-200 dark:border-slate-700">
                    <p class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Este ticket ha sido marcado como cerrado</p>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
