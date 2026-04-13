<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { PlusIcon, ChatBubbleLeftRightIcon, CheckCircleIcon, ClockIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    tickets: Array,
});

const showModal = ref(false);

const form = useForm({
    subject: '',
    message: '',
    priority: 'medium',
});

const submit = () => {
    form.post(route('support.store'), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    });
};

const getStatusClass = (status) => {
    return {
        'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': status === 'open',
        'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400': status === 'answered',
        'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400': status === 'closed',
    };
};

const getStatusLabel = (status) => {
    const labels = { open: 'Abierto', answered: 'Respondido', closed: 'Cerrado' };
    return labels[status] || status;
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
};
</script>

<template>
    <Head title="Soporte Técnico" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-black text-slate-800 dark:text-white leading-tight uppercase tracking-tight">
                    Centro de Ayuda y Soporte
                </h2>
                <button @click="showModal = true" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-xl shadow-lg shadow-indigo-500/30 transition-all flex items-center gap-2 active:scale-95">
                    <PlusIcon class="w-5 h-5" />
                    NUEVA CONSULTA
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div v-if="tickets.length === 0" class="bg-white dark:bg-slate-800 rounded-[3rem] p-20 text-center border-2 border-dashed border-slate-200 dark:border-slate-700">
                    <div class="w-24 h-24 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center text-slate-300 mx-auto mb-6">
                        <ChatBubbleLeftRightIcon class="w-12 h-12" />
                    </div>
                    <h3 class="text-2xl font-black text-slate-700 dark:text-white mb-2 uppercase">¿En qué podemos ayudarte?</h3>
                    <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-8">
                        Nuestro equipo de soporte está listo para resolver tus dudas técnicas o financieras.
                    </p>
                    <button @click="showModal = true" class="text-indigo-600 font-black hover:underline uppercase tracking-widest text-sm">Crear mi primer ticket de soporte</button>
                </div>

                <div v-else class="grid gap-6">
                    <Link v-for="ticket in tickets" :key="ticket.id" :href="route('support.show', ticket.id)" 
                        class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-xl border border-transparent hover:border-indigo-500/30 transition-all flex flex-col md:flex-row md:items-center justify-between gap-6 group">
                        
                        <div class="flex items-center gap-6">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0" :class="getStatusClass(ticket.status)">
                                <ClockIcon v-if="ticket.status === 'open'" class="w-7 h-7" />
                                <ChatBubbleLeftRightIcon v-if="ticket.status === 'answered'" class="w-7 h-7" />
                                <CheckCircleIcon v-if="ticket.status === 'closed'" class="w-7 h-7" />
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-slate-900 dark:text-white group-hover:text-indigo-500 transition-colors uppercase leading-tight">{{ ticket.subject }}</h4>
                                <div class="flex items-center gap-4 mt-1">
                                    <span class="text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full" :class="getStatusClass(ticket.status)">
                                        {{ getStatusLabel(ticket.status) }}
                                    </span>
                                    <span class="text-xs text-slate-400 font-bold tracking-tight">Abierto el {{ formatDate(ticket.created_at) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div v-if="ticket.messages.length > 0" class="text-right hidden md:block">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 text-right">Último Mensaje</p>
                                <p class="text-xs text-slate-600 dark:text-slate-300 italic truncate max-w-xs">"{{ ticket.messages[0].message }}"</p>
                            </div>
                            <div class="p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Modal Nueva Consulta -->
        <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-800 rounded-[3rem] p-10 max-w-2xl w-full shadow-2xl border border-slate-100 dark:border-slate-700 animate-in zoom-in-95 duration-200">
                <h3 class="text-3xl font-black text-slate-900 dark:text-white mb-2 uppercase tracking-tight">Nueva Consulta</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-8">Cuéntanos detalladamente tu problema o sugerencia.</p>
                
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 ms-1">Asunto de la consulta</label>
                        <input v-model="form.subject" type="text" placeholder="Ej: Error al subir PDF de BBVA" class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-2xl py-4 px-6 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 shadow-sm" required>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 ms-1">Prioridad</label>
                            <select v-model="form.priority" class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-2xl py-4 px-6 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 shadow-sm font-bold">
                                <option value="low">Baja (General)</option>
                                <option value="medium">Media (Funcionalidad)</option>
                                <option value="high">Alta (Financiero/Cuenta)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 ms-1">Mensaje Detallado</label>
                        <textarea v-model="form.message" rows="5" placeholder="Escribe aquí tu consulta..." class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-2xl py-4 px-6 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 shadow-sm" required></textarea>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="button" @click="showModal = false" class="flex-1 py-4 font-black text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-2xl transition-all">CANCELAR</button>
                        <button type="submit" :disabled="form.processing" class="flex-2 px-12 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-xl shadow-indigo-500/30 transition-all disabled:opacity-50">ENVIAR TICKET</button>
                    </div>
                </form>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
