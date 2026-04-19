<script setup>
import { ref, onUnmounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { 
    ShieldCheckIcon, 
    XMarkIcon,
    ArrowPathIcon,
    RocketLaunchIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    show: Boolean
});

const emit = defineEmits(['close', 'success']);

const step = ref(1); // 1: Info, 2: Setup, 3: Success
const hasScannedQR = ref(false);
const qrCodeUrl = ref('');
const secret = ref('');

// Temporizador y Código Dinámico
const secondsRemaining = ref(30);
let timerInterval = null;

const setupForm = useForm({
    code: '',
    secret: '',
});

const fetchCurrentCode = async () => {
    if (!secret.value) return;
    try {
        const response = await axios.post(route('profile.security.current-code'), { secret: secret.value });
        secondsRemaining.value = response.data.secondsRemaining;
    } catch (error) {
        console.error('Error al obtener código OTP:', error);
    }
};

const startTimer = () => {
    if (timerInterval) clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        if (secondsRemaining.value > 0) {
            secondsRemaining.value--;
        } else {
            fetchCurrentCode();
        }
    }, 1000);
};

const stopTimer = () => {
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }
};

const startSetup = async () => {
    try {
        const response = await axios.post(route('profile.security.setup2fa'));
        qrCodeUrl.value = response.data.qrCodeUrl;
        secret.value = response.data.secret;
        setupForm.secret = response.data.secret;
        hasScannedQR.value = false;
        step.value = 2;
        
        await fetchCurrentCode();
        startTimer();
    } catch (error) {
        console.error('Error al iniciar setup 2FA:', error);
    }
};

const confirmQRScanned = () => {
    hasScannedQR.value = true;
};

const confirmSetup = () => {
    setupForm.post(route('profile.security.activate2fa'), {
        preserveScroll: true,
        onSuccess: () => {
            step.value = 3;
            setupForm.reset();
            stopTimer();
            emit('success');
        },
    });
};

const close = () => {
    stopTimer();
    emit('close');
};

onUnmounted(() => stopTimer());
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div class="bg-white dark:bg-slate-900 rounded-[3rem] p-8 md:p-12 max-w-lg w-full shadow-2xl relative border border-white/10">
            <button @click="close" class="absolute top-8 right-8 text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                <XMarkIcon class="w-8 h-8" />
            </button>

            <!-- Paso 1: Introducción -->
            <div v-if="step === 1" class="text-center">
                <div class="w-20 h-20 bg-indigo-50 dark:bg-indigo-900/30 rounded-[2rem] flex items-center justify-center mx-auto mb-8 text-indigo-600 shadow-inner">
                    <ShieldCheckIcon class="w-10 h-10" />
                </div>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white uppercase mb-4 italic text-balance">Refuerza tu cuenta</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-8 leading-relaxed font-medium">
                    Necesitarás una aplicación como Google Authenticator instalada en tu smartphone para escanear un código de seguridad.
                </p>
                <button @click="startSetup" class="w-full py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition shadow-xl shadow-indigo-500/20 uppercase tracking-widest text-xs">
                    Comenzar Configuración
                </button>
            </div>

            <!-- Paso 2: Configuración (QR -> Verificación) -->
            <div v-if="step === 2" class="text-center">
                
                <!-- Sub-paso 2.1: Escaneo de QR -->
                <div v-if="!hasScannedQR" class="animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase mb-6 italic">1. Escanea el Código</h3>
                    
                    <div class="bg-white p-6 rounded-[2.5rem] inline-block shadow-2xl border border-slate-100 mb-8">
                        <div v-html="qrCodeUrl" class="qr-container"></div>
                    </div>

                    <p class="text-slate-500 dark:text-slate-400 text-xs mb-8 max-w-xs mx-auto font-medium">
                        Abre tu aplicación de autenticación y escanea este código para vincular tu cuenta.
                    </p>

                    <button @click="confirmQRScanned" class="w-full py-4 bg-slate-800 dark:bg-slate-700 text-white font-black rounded-2xl hover:bg-slate-950 transition shadow-xl uppercase tracking-widest text-xs flex items-center justify-center gap-2 group">
                        Ya he escaneado el código 
                        <ShieldCheckIcon class="w-4 h-4 group-hover:scale-110 transition-transform" />
                    </button>
                </div>

                <!-- Sub-paso 2.2: Verificación con Código -->
                <div v-else class="animate-in zoom-in-95 duration-500">
                    <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase mb-6 italic">2. Verifica la Conexión</h3>
                    
                    <!-- Ayuda Visual: Temporizador de Sincronización -->
                    <div class="bg-slate-50 dark:bg-slate-800/50 px-8 py-4 rounded-3xl border border-slate-100 dark:border-slate-700 inline-flex flex-col items-center gap-2 mb-8">
                        <div class="relative w-12 h-12 flex items-center justify-center">
                            <svg class="w-full h-full -rotate-90">
                                <circle cx="24" cy="24" r="22" stroke="currentColor" stroke-width="4" fill="transparent" class="text-slate-200 dark:text-slate-700" />
                                <circle cx="24" cy="24" r="22" stroke="currentColor" stroke-width="4" fill="transparent" 
                                    class="text-indigo-500 transition-all duration-1000" 
                                    :stroke-dasharray="138" 
                                    :stroke-dashoffset="138 * (1 - secondsRemaining / 30)" 
                                />
                            </svg>
                            <span class="absolute text-sm font-black text-slate-800 dark:text-white">{{ secondsRemaining }}</span>
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Sincronización</p>
                    </div>

                    <form @submit.prevent="confirmSetup" class="space-y-6">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4">Introduce el código generado</p>
                            <input 
                                v-model="setupForm.code"
                                type="text" 
                                maxlength="6"
                                placeholder="000 000"
                                class="w-full text-center text-4xl font-black tracking-[0.5em] bg-slate-50 dark:bg-slate-800/50 border-none rounded-2xl py-6 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 shadow-inner"
                                required
                                autofocus
                            />
                            <div v-if="setupForm.errors.code" class="text-rose-500 text-[10px] font-black uppercase tracking-widest mt-2">{{ setupForm.errors.code }}</div>
                        </div>
                        
                        <button 
                            type="submit" 
                            :disabled="setupForm.processing"
                            class="w-full py-5 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-2xl transition shadow-xl shadow-emerald-500/20 uppercase tracking-widest text-xs active:scale-95"
                        >
                            <span v-if="!setupForm.processing">ACTIVAR PROTECCIÓN</span>
                            <span v-else class="flex items-center justify-center gap-2">
                                <ArrowPathIcon class="w-4 h-4 animate-spin" />
                                PROCESANDO...
                            </span>
                        </button>

                        <button @click="hasScannedQR = false" type="button" class="text-[10px] font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest">
                            Volver a ver el QR
                        </button>
                    </form>
                </div>
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
                <button @click="close" class="w-full py-4 bg-slate-800 dark:bg-slate-700 text-white font-black rounded-2xl hover:bg-slate-900 dark:hover:bg-slate-600 transition uppercase tracking-widest text-xs">
                    Regresar a Seguridad
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.qr-container :deep(svg) {
    width: 200px;
    height: 200px;
    display: block;
    margin: 0 auto;
}
</style>
