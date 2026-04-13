<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { 
    ShieldCheckIcon, 
    GlobeAltIcon, 
    DevicePhoneMobileIcon, 
    KeyIcon,
    XMarkIcon,
    ExclamationTriangleIcon,
    ArrowPathIcon
} from '@heroicons/vue/24/outline';
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    activities: Array,
    twoFactorEnabled: Boolean,
    currentSessionId: String,
});

const showSetupModal = ref(false);
const showDisableModal = ref(false);
const qrCodeUrl = ref('');
const secret = ref('');
const step = ref(1); // 1: Info, 2: QR & Code, 3: Success

const setupForm = useForm({
    code: '',
    secret: '',
});

const disableForm = useForm({});

const startSetup = async () => {
    try {
        const response = await axios.post(route('profile.security.setup2fa'));
        qrCodeUrl.value = response.data.qrCodeUrl;
        secret.value = response.data.secret;
        setupForm.secret = response.data.secret;
        step.value = 2;
        showSetupModal.value = true;
    } catch (error) {
        console.error('Error al iniciar setup 2FA:', error);
    }
};

const confirmSetup = () => {
    setupForm.post(route('profile.security.activate2fa'), {
        preserveScroll: true,
        onSuccess: () => {
            step.value = 3;
            setupForm.reset();
        },
    });
};

const executeDisable = () => {
    disableForm.post(route('profile.security.disable2fa'), {
        preserveScroll: true,
        onSuccess: () => {
            showDisableModal.value = false;
        }
    });
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
    <Head title="Seguridad" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-black text-slate-800 dark:text-white leading-tight uppercase tracking-tight italic">
                Seguridad y Privacidad
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <!-- Card: Autenticación de Doble Factor (Real) -->
                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-xl overflow-hidden border border-slate-100 dark:border-slate-700 relative">
                    <!-- Decoración -->
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
                            <button v-if="!twoFactorEnabled" @click="showSetupModal = true; step = 1" 
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

                <!-- Card: Historial de Actividad (Con Sesión Actual) -->
                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-xl overflow-hidden border border-slate-100 dark:border-slate-700">
                    <div class="p-8 border-b border-slate-50 dark:border-slate-700 flex justify-between items-center">
                        <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-widest flex items-center gap-3 italic">
                            <GlobeAltIcon class="w-5 h-5 text-indigo-500" />
                            Historial de Conexiones
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 dark:bg-slate-900/50">
                                <tr>
                                    <th class="px-8 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Evento</th>
                                    <th class="px-8 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Dirección IP</th>
                                    <th class="px-8 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Estado / Sesión</th>
                                    <th class="px-8 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 text-right">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                <tr v-for="activity in activities" :key="activity.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors group">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-3">
                                            <div :class="[
                                                'w-2 h-2 rounded-full',
                                                activity.type === 'login' ? 'bg-indigo-500' : 'bg-amber-500'
                                            ]"></div>
                                            <span class="font-bold text-slate-700 dark:text-slate-200 capitalize text-sm">{{ activity.type.replace('_', ' ') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 font-mono text-xs text-slate-500 dark:text-slate-400">
                                        {{ activity.ip_address || 'Localhost' }}
                                    </td>
                                    <td class="px-8 py-5">
                                        <span v-if="activity.session_id === currentSessionId" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-black bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 uppercase tracking-widest animate-pulse">
                                            Sesión Actual
                                        </span>
                                        <span v-else class="text-[10px] text-slate-400 font-bold uppercase tracking-tight opacity-50">
                                            Finalizada
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-right text-[11px] font-bold text-slate-500">
                                        {{ formatDate(activity.created_at) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recordatorio Contraseña -->
                <div class="bg-indigo-600 rounded-[2.5rem] p-8 text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-2xl shadow-indigo-500/20">
                    <div class="flex items-center gap-6">
                        <div class="p-4 bg-white/10 rounded-2xl">
                            <KeyIcon class="w-8 h-8" />
                        </div>
                        <div>
                            <h4 class="text-xl font-black uppercase tracking-tight italic">¿Seguridad comprometida?</h4>
                            <p class="opacity-70 text-sm font-medium">Si detectas sesiones sospechosas, cambia tu contraseña inmediatamente.</p>
                        </div>
                    </div>
                    <Link :href="route('profile.edit')" class="px-8 py-4 bg-white text-indigo-600 font-black rounded-2xl hover:bg-slate-100 transition shadow-lg uppercase tracking-widest text-xs">
                        CAMBIAR CONTRASEÑA
                    </Link>
                </div>

            </div>
        </div>

        <!-- Modal de Configuración 2FA -->
        <div v-if="showSetupModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
            <div class="bg-white dark:bg-slate-900 rounded-[3rem] p-8 md:p-12 max-w-lg w-full shadow-2xl relative border border-white/10">
                <button @click="showSetupModal = false" class="absolute top-8 right-8 text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                    <XMarkIcon class="w-8 h-8" />
                </button>

                <!-- Paso 1: Introducción -->
                <div v-if="step === 1" class="text-center">
                    <div class="w-20 h-20 bg-indigo-50 dark:bg-indigo-900/30 rounded-[2rem] flex items-center justify-center mx-auto mb-8 text-indigo-600 shadow-inner">
                        <ShieldCheckIcon class="w-10 h-10" />
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white uppercase mb-4 italic">Refuerza tu cuenta</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mb-8 leading-relaxed font-medium">
                        Necesitarás una aplicación como Google Authenticator instalada en tu smartphone para escanear un código de seguridad.
                    </p>
                    <button @click="startSetup" class="w-full py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition shadow-xl shadow-indigo-500/20 uppercase tracking-widest text-xs">
                        Comenzar Configuración
                    </button>
                </div>

                <!-- Paso 2: QR y Verificación -->
                <div v-if="step === 2" class="text-center">
                    <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase mb-6 italic">Escanea el Código</h3>
                    
                    <div class="bg-white p-6 rounded-[2.5rem] inline-block mb-8 shadow-2xl border border-slate-100 mx-auto">
                        <div v-html="qrCodeUrl" class="qr-container"></div>
                    </div>

                    <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4">Introduce el código de 6 dígitos</p>
                    
                    <form @submit.prevent="confirmSetup" class="space-y-6">
                        <input 
                            v-model="setupForm.code"
                            type="text" 
                            maxlength="6"
                            placeholder="000 000"
                            class="w-full text-center text-4xl font-black tracking-[0.5em] bg-slate-50 dark:bg-slate-800/50 border-none rounded-2xl py-6 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 shadow-inner"
                            required
                        />
                        <div v-if="setupForm.errors.code" class="text-rose-500 text-[10px] font-black uppercase tracking-widest">{{ setupForm.errors.code }}</div>
                        
                        <button 
                            type="submit" 
                            :disabled="setupForm.processing"
                            class="w-full py-5 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-2xl transition shadow-xl shadow-emerald-500/20 uppercase tracking-widest text-xs active:scale-95"
                        >
                            Verificar y Activar
                        </button>
                    </form>
                </div>

                <!-- Paso 3: Éxito -->
                <div v-if="step === 3" class="text-center">
                    <div class="w-20 h-20 bg-emerald-50 dark:bg-emerald-900/30 rounded-[2rem] flex items-center justify-center mx-auto mb-8 text-emerald-600">
                        <ShieldCheckIcon class="w-10 h-10" />
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white uppercase mb-4 italic">¡Protegido con éxito!</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mb-8 font-medium">
                        Tu cuenta ahora requiere autenticación de doble factor para cada inicio de sesión.
                    </p>
                    <button @click="showSetupModal = false" class="w-full py-4 bg-slate-800 dark:bg-slate-700 text-white font-black rounded-2xl hover:bg-slate-900 dark:hover:bg-slate-600 transition uppercase tracking-widest text-xs">
                        Regresar a Seguridad
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal de Desactivación -->
        <div v-if="showDisableModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
            <div class="bg-white dark:bg-slate-900 rounded-[3rem] p-8 md:p-12 max-w-lg w-full shadow-2xl relative border border-white/10">
                <div class="text-center">
                    <div class="w-20 h-20 bg-rose-50 dark:bg-rose-900/30 rounded-[2rem] flex items-center justify-center mx-auto mb-8 text-rose-600">
                        <ExclamationTriangleIcon class="w-10 h-10" />
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white uppercase mb-4 italic">¿Desactivar Protección?</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mb-8 leading-relaxed font-medium">
                        Tu cuenta quedará expuesta. Te recomendamos mantener el 2FA activo para proteger tus datos financieros y patrimoniales.
                    </p>
                    
                    <div class="flex flex-col gap-4">
                        <button 
                            @click="executeDisable"
                            :disabled="disableForm.processing"
                            class="w-full py-5 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-2xl transition shadow-xl shadow-rose-500/20 uppercase tracking-widest text-xs active:scale-95"
                        >
                            {{ disableForm.processing ? 'DESACTIVANDO...' : 'SÍ, DESACTIVAR PROTECCIÓN' }}
                        </button>
                        <button 
                            @click="showDisableModal = false"
                            class="w-full py-5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-black rounded-2xl hover:bg-slate-200 dark:hover:bg-slate-700 transition uppercase tracking-widest text-xs"
                        >
                            MANTENER PROTEGIDO
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.qr-container :deep(svg) {
    width: 200px;
    height: 200px;
    display: block;
    margin: 0 auto;
}
</style>
