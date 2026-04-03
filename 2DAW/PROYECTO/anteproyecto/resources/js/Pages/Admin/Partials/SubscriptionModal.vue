<script setup>
/**
 * SubscriptionModal - Modal para la gestión administrativa de suscripciones de usuarios.
 * 
 * Permite cambiar el plan (tier) y la duración de la suscripción de forma 
 * manual por parte de un administrador.
 */
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['close', 'success']);

const form = useForm({
    tier: props.user.tier || 'none',
    days: 30
});

// Sincronizar el formulario con el usuario actual si cambia
watch(() => props.user, (newVal) => {
    form.tier = newVal.tier || 'none';
}, { immediate: true });

const submit = () => {
    form.post(route('admin.users.update-subscription', props.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit('success');
            emit('close');
        }
    });
};

</script>

<template>
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-200">
        <div class="bg-white dark:bg-slate-800 w-full max-w-md rounded-3xl shadow-2xl overflow-hidden border border-slate-100 dark:border-slate-700 animate-in zoom-in-95 duration-200">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Administrar Plan
                </h3>
                <button @click="emit('close')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="p-6 space-y-6">
                <div>
                    <p class="text-sm font-bold text-slate-800 dark:text-white mb-2">Usuario: {{ user.name }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Modifica directamente los datos de membresía en el sistema.</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-2">Nivel de Plan (Tier)</label>
                        <select v-model="form.tier" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm p-3 focus:ring-2 focus:ring-indigo-500 dark:text-white outline-none transition-shadow">
                            <option value="none">Sin plan (Anular)</option>
                            <option value="basic">Básico</option>
                            <option value="pro">Pro</option>
                            <option value="premium">Premium</option>
                        </select>
                    </div>
                    <div v-if="form.tier !== 'none'">
                        <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-2">Días de concesión</label>
                        <input type="number" v-model="form.days" min="1" max="1000" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm p-3 focus:ring-2 focus:ring-indigo-500 dark:text-white outline-none transition-shadow" />
                        <p class="mt-1 text-[10px] text-slate-400 font-bold italic">La suscripción auto-caducará pasada esta cifra de días si no se renueva.</p>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-slate-50 dark:bg-slate-900/50 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-700">
                <button @click="emit('close')" class="px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest text-slate-600 hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-700 transition-colors">
                    Cancelar
                </button>
                <button @click="submit" :disabled="form.processing" class="px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest text-white transition-all active:scale-95 shadow-lg flex items-center justify-center gap-2 min-w-[120px]" :class="form.tier === 'none' ? 'bg-rose-500 shadow-rose-500/20 hover:bg-rose-600' : 'bg-indigo-600 shadow-indigo-500/20 hover:bg-indigo-700'">
                    <span v-if="form.processing" class="animate-spin h-3.5 w-3.5 border-2 border-white border-t-transparent rounded-full"></span>
                    {{ form.tier === 'none' ? 'Anular Plan' : 'Guardar' }}
                </button>
            </div>
        </div>
    </div>
</template>
