<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

/**
 * ManageSubscription - Panel de estado de membresía del usuario.
 * Muestra el plan actual con contador de días y opción de cancelación.
 */
const props = defineProps({
    subscription: { type: Object, default: null },
});

const showCancelConfirm = ref(false);
const isCanceling = ref(false);

const tierConfig = {
    premium: { label: 'Premium', icon: '⭐', gradient: 'from-purple-700 to-purple-500' },
    pro:     { label: 'Pro',     icon: '⚡', gradient: 'from-indigo-700 to-indigo-500' },
    basic:   { label: 'Basic',   icon: '✅', gradient: 'from-blue-700 to-blue-500' },
};

const currentTier = props.subscription
    ? (tierConfig[props.subscription.tier] ?? tierConfig.basic)
    : null;

const daysColor = (days) => {
    if (days === null) return 'text-emerald-300';
    if (days <= 5)     return 'text-rose-300';
    if (days <= 15)    return 'text-amber-300';
    return 'text-emerald-300';
};

const barColor = (days) => {
    if (days === null) return 'bg-emerald-400';
    if (days <= 5)     return 'bg-rose-400';
    if (days <= 15)    return 'bg-amber-400';
    return 'bg-emerald-400';
};

const handleCancel = () => {
    isCanceling.value = true;
    router.post(route('profile.subscription.cancel'), {}, {
        preserveScroll: true,
        onFinish: () => { isCanceling.value = false; showCancelConfirm.value = false; },
    });
};
</script>

<template>
    <section>
        <header class="mb-6">
            <h2 class="text-lg font-black text-slate-900 dark:text-white">Gestión de Membresía</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Consulta el estado de tu plan activo y gestiona tu suscripción.
            </p>
        </header>

        <!-- Sin membresía -->
        <div v-if="!subscription"
            class="rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700 p-10 text-center">
            <div class="text-5xl mb-3">🔒</div>
            <h3 class="text-base font-black text-slate-700 dark:text-slate-300 mb-1">Sin plan activo</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Contacta con el administrador para activar un plan.
            </p>
        </div>

        <!-- Con membresía -->
        <div v-else class="space-y-5">

            <!-- Tarjeta de plan -->
            <div :class="`bg-gradient-to-br ${currentTier.gradient} rounded-3xl p-6 text-white relative overflow-hidden shadow-2xl`">
                <div class="absolute -right-4 -top-4 text-white/10 text-[110px] leading-none select-none pointer-events-none">
                    {{ currentTier.icon }}
                </div>
                <div class="relative z-10 space-y-4">
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60 mb-0.5">Plan Actual</div>
                        <div class="text-3xl font-black flex items-center gap-2">
                            {{ currentTier.icon }} {{ currentTier.label }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <!-- Días restantes -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4">
                            <div class="text-[9px] font-black uppercase tracking-widest opacity-60 mb-1">Días restantes</div>
                            <div :class="['text-4xl font-black tabular-nums', daysColor(subscription.days_left)]">
                                {{ subscription.days_left !== null ? subscription.days_left : '∞' }}
                            </div>
                            <div class="text-[10px] opacity-50 mt-1">
                                Vence: <span class="font-bold">{{ subscription.ends_at ?? 'Sin límite' }}</span>
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 flex flex-col justify-between">
                            <div class="text-[9px] font-black uppercase tracking-widest opacity-60 mb-2">Estado</div>
                            <div class="inline-flex items-center gap-1.5 bg-white/20 self-start px-3 py-1.5 rounded-full text-xs font-black">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                                Activa
                            </div>
                            <div class="text-[10px] opacity-50 mt-2 leading-tight">{{ subscription.status }}</div>
                        </div>
                    </div>

                    <!-- Barra de progreso -->
                    <div v-if="subscription.days_left !== null">
                        <div class="h-1.5 bg-white/20 rounded-full overflow-hidden">
                            <div
                                :class="['h-full rounded-full transition-all duration-700', barColor(subscription.days_left)]"
                                :style="`width: ${Math.min(100, Math.max(2, (subscription.days_left / 30) * 100))}%`"
                            ></div>
                        </div>
                        <div class="text-[9px] opacity-40 mt-1 text-right">Base de referencia: 30 días</div>
                    </div>
                </div>
            </div>

            <!-- Cancelación -->
            <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-2xl p-5">
                <h3 class="text-sm font-black text-rose-700 dark:text-rose-400 mb-1">⚠ Cancelar Membresía</h3>
                <p class="text-xs text-rose-600/80 dark:text-rose-400/70 mb-4">
                    Perderás el acceso de forma inmediata. Puedes volver a activarla en cualquier momento contactando al administrador.
                </p>

                <div v-if="!showCancelConfirm">
                    <button @click="showCancelConfirm = true"
                        class="px-5 py-2 text-sm font-black text-rose-600 dark:text-rose-400 bg-white dark:bg-rose-900/40 hover:bg-rose-100 dark:hover:bg-rose-900/60 rounded-xl border border-rose-200 dark:border-rose-700 transition-all">
                        Cancelar suscripción
                    </button>
                </div>

                <div v-else class="space-y-3">
                    <p class="text-sm font-black text-rose-700 dark:text-rose-300">¿Confirmas la cancelación inmediata?</p>
                    <div class="flex gap-3">
                        <button @click="handleCancel" :disabled="isCanceling"
                            class="flex-1 py-2.5 text-sm font-black text-white bg-rose-600 hover:bg-rose-700 disabled:opacity-50 rounded-xl transition-all active:scale-95 flex items-center justify-center gap-2">
                            <svg v-if="isCanceling" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            {{ isCanceling ? 'Cancelando...' : 'Sí, cancelar ahora' }}
                        </button>
                        <button @click="showCancelConfirm = false"
                            class="flex-1 py-2.5 text-sm font-black text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-600 hover:bg-slate-50 transition-all">
                            Volver atrás
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
