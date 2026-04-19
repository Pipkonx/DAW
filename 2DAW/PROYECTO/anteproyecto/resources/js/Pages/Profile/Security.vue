<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { 
    ShieldCheckIcon, 
    DevicePhoneMobileIcon, 
    KeyIcon,
    ExclamationTriangleIcon
} from '@heroicons/vue/24/outline';
import { ref } from 'vue';

// Componentes Refactorizados
import TwoFactorSetupModal from '@/Components/Features/Security/TwoFactorSetupModal.vue';
import ConnectionHistoryTable from '@/Components/Features/Security/ConnectionHistoryTable.vue';

const props = defineProps({
    activities: Array,
    twoFactorEnabled: Boolean,
    currentSessionId: String,
});

const showSetupModal = ref(false);
const showDisableModal = ref(false);
const disableForm = useForm({});

const handle2faSuccess = () => {
    showSetupModal.value = false;
    // La página se refrescará automáticamente vía Inertia si es necesario
};

const executeDisable = () => {
    disableForm.post(route('profile.security.disable2fa'), {
        preserveScroll: true,
        onSuccess: () => {
            showDisableModal.value = false;
        }
    });
};
</script>

<template>
    <Head title="Seguridad" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-black text-slate-800 dark:text-white leading-tight uppercase tracking-tight italic">
                Seguridad y Privacidad
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <!-- Card: Autenticación de Doble Factor -->
                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-xl overflow-hidden border border-slate-100 dark:border-slate-700 relative">
                    <div class="absolute top-0 right-0 p-4 opacity-5">
                        <ShieldCheckIcon class="w-32 h-32" />
                    </div>

                    <div class="p-8 md:p-12 flex flex-col md:flex-row items-center gap-8 relative z-10">
                        <div class="w-20 h-20 bg-indigo-100 dark:bg-indigo-900/40 rounded-3xl flex items-center justify-center text-indigo-600 shrink-0 shadow-inner">
                            <DevicePhoneMobileIcon class="w-10 h-10" />
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <div class="flex items-center gap-3 justify-center md:justify-start mb-2">
                                <h3 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight leading-none italic">
                                    Doble Factor (TOTP)
                                </h3>
                                <span v-if="twoFactorEnabled" class="px-2 py-0.5 bg-emerald-500 text-[10px] text-white font-black rounded-full animate-pulse uppercase tracking-wider">
                                    Activo
                                </span>
                            </div>
                            <p class="text-slate-500 dark:text-slate-400 text-sm max-w-xl font-medium">
                                Protege tu capital con el estándar de la industria. Al iniciar sesión, solicita un código dinámico desde apps como Google Authenticator o Authy.
                            </p>
                        </div>
                        <div class="shrink-0">
                            <button v-if="!twoFactorEnabled" @click="showSetupModal = true" 
                                class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black transition-all active:scale-95 shadow-xl shadow-indigo-500/30 uppercase tracking-widest text-xs">
                                Configurar Protección
                            </button>
                            <button v-else @click="showDisableModal = true" 
                                class="px-8 py-4 bg-emerald-50 text-emerald-700 border-2 border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30 rounded-2xl font-black transition-all active:scale-95 flex items-center gap-2 uppercase tracking-widest text-xs group">
                                <ShieldCheckIcon class="w-5 h-5 group-hover:rotate-12 transition-transform" />
                                Gestionar Protección
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Historial (Componente Refactorizado) -->
                <ConnectionHistoryTable 
                    :activities="activities" 
                    :currentSessionId="currentSessionId" 
                />

                <!-- Card: Recordatorio Contraseña -->
                <div class="bg-indigo-600 rounded-[2.5rem] p-8 text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-2xl shadow-indigo-500/20">
                    <div class="flex items-center gap-6">
                        <div class="p-4 bg-white/10 rounded-2xl">
                            <KeyIcon class="w-8 h-8" />
                        </div>
                        <div>
                            <h4 class="text-xl font-black uppercase tracking-tight italic">¿Seguridad comprometida?</h4>
                            <p class="opacity-70 text-sm font-medium text-balance">Si detectas sesiones sospechosas, cambia tu contraseña inmediatamente.</p>
                        </div>
                    </div>
                    <Link :href="route('profile.edit')" class="px-8 py-4 bg-white text-indigo-600 font-black rounded-2xl hover:bg-slate-100 transition shadow-lg uppercase tracking-widest text-xs whitespace-nowrap">
                        CAMBIAR CONTRASEÑA
                    </Link>
                </div>

            </div>
        </div>

        <!-- Modales Refactorizados -->
        <TwoFactorSetupModal 
            :show="showSetupModal" 
            @close="showSetupModal = false" 
            @success="handle2faSuccess"
        />

        <!-- Modal de Desactivación (Simple, se mantiene aquí) -->
        <div v-if="showDisableModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
            <div class="bg-white dark:bg-slate-900 rounded-[3rem] p-8 md:p-12 max-w-lg w-full shadow-2xl relative border border-white/10 text-center">
                <div class="w-20 h-20 bg-rose-50 dark:bg-rose-900/30 rounded-[2rem] flex items-center justify-center mx-auto mb-8 text-rose-600">
                    <ExclamationTriangleIcon class="w-10 h-10" />
                </div>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white uppercase mb-4 italic">¿Desactivar Protección?</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-8 font-medium">
                    Tu cuenta quedará expuesta. Te recomendamos mantener el 2FA activo para proteger tus datos financieros.
                </p>
                <div class="flex flex-col gap-4">
                    <button @click="executeDisable" class="w-full py-5 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-2xl transition shadow-xl uppercase tracking-widest text-xs">
                        SÍ, DESACTIVAR PROTECCIÓN
                    </button>
                    <button @click="showDisableModal = false" class="w-full py-5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-black rounded-2xl transition uppercase tracking-widest text-xs">
                        MANTENER PROTEGIDO
                    </button>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
