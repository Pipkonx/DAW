<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    plans: Array,
    intent: Object // SetupIntent de Stripe
});

const selectedPlan = ref(null);
const processing = ref(false);

const form = useForm({
    plan_id: '',
    payment_method: ''
});

const handleSubscription = (planId) => {
    selectedPlan.value = planId;
    
    // Stripe Checkout: el controlador nos redirigirá asíncronamente
    form.plan_id = planId;
    form.post(route('subscription.subscribe'), {
        onStart: () => processing.value = true,
        onFinish: () => processing.value = false
    });
};
</script>

<template>
    <Head title="Planes de Membresía" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                Elige tu Plan Premium
            </h2>
        </template>

        <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center mb-16">
                    <h1 class="text-4xl font-extrabold text-slate-900 dark:text-white mb-4">
                        Potencia tus Inversiones al Siguiente Nivel
                    </h1>
                    <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                        Suscríbete a uno de nuestros planes para eliminar los anuncios y desbloquear herramientas avanzadas de análisis financiero.
                    </p>
                </div>

                <!-- Rejilla de Planes -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                    <div v-for="plan in plans" :key="plan.id" 
                        class="relative bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-xl border border-slate-100 dark:border-slate-700 transition-all hover:scale-105"
                        :class="{'ring-2 ring-blue-500 scale-105': plan.popular}"
                    >
                        <!-- Etiqueta de Popular -->
                        <div v-if="plan.popular" class="absolute -top-4 left-1/2 -translate-x-1/2 bg-blue-500 text-white px-4 py-1 rounded-full text-xs font-bold uppercase tracking-widest shadow-lg">
                            Más Popular
                        </div>

                        <div class="mb-8">
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">{{ plan.name }}</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ plan.description }}</p>
                        </div>

                        <div class="mb-8">
                            <span class="text-5xl font-black text-slate-900 dark:text-white">{{ plan.price }}€</span>
                            <span class="text-slate-400">/ mes</span>
                        </div>

                        <!-- Lista de Características -->
                        <ul class="space-y-4 mb-8">
                            <li v-for="feature in plan.features" :key="feature" class="flex items-center gap-3 text-slate-600 dark:text-slate-300">
                                <div class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-sm">{{ feature }}</span>
                            </li>
                        </ul>

                        <button 
                            @click="handleSubscription(plan.id)"
                            :disabled="processing"
                            class="w-full py-4 rounded-xl font-bold transition-all shadow-lg active:scale-95"
                            :class="plan.popular ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-white'"
                        >
                            {{ processing && selectedPlan === plan.id ? 'Procesando...' : 'Elegir Plan' }}
                        </button>
                    </div>
                </div>

                <div class="text-center">
                    <p class="text-sm text-slate-400">
                        Pagos seguros procesados por **Stripe**. Puedes cancelar en cualquier momento.
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
