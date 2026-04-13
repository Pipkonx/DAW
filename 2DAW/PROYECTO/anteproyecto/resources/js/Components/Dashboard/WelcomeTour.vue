<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { 
    RocketLaunchIcon, 
    CircleStackIcon, 
    ArrowUpTrayIcon, 
    SparklesIcon,
    XMarkIcon 
} from '@heroicons/vue/24/outline';

const props = defineProps({
    show: Boolean,
});

const emit = defineEmits(['close']);

const currentStep = ref(1);
const totalSteps = 4;

const form = useForm({});

const nextStep = () => {
    if (currentStep.value < totalSteps) {
        currentStep.value++;
    } else {
        completeTour();
    }
};

const completeTour = () => {
    form.post(route('onboarding.complete'), {
        onSuccess: () => {
            emit('close');
        }
    });
};

const steps = [
    {
        id: 1,
        title: '¡Bienvenido a fintechPro!',
        description: 'Estamos emocionados de ayudarte a centralizar tu patrimonio. Olvida las hojas de cálculo y toma el control total de tus inversiones hoy mismo.',
        icon: RocketLaunchIcon,
        color: 'text-indigo-600 bg-indigo-100',
    },
    {
        id: 2,
        title: 'Tu Primera Cartera',
        description: 'El primer paso es crear un portafolio. Puede ser para tus acciones, cripto o incluso una cuenta de ahorros. Solo dale un nombre y elige tu moneda base.',
        icon: CircleStackIcon,
        color: 'text-emerald-600 bg-emerald-100',
    },
    {
        id: 3,
        title: 'Importación Inteligente',
        description: '¿Tienes un extracto bancario en PDF? Súbelo y nuestro motor OCR extraerá automáticamente todos tus activos y transacciones por ti.',
        icon: ArrowUpTrayIcon,
        color: 'text-amber-600 bg-amber-100',
    },
    {
        id: 4,
        title: 'Análisis IA Avanzado',
        description: 'Como usuario PRO, puedes pedirle a nuestra IA que analice tu diversificación y te dé consejos personalizados basados en el mercado real.',
        icon: SparklesIcon,
        color: 'text-purple-600 bg-purple-100',
    }
];
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-md">
        <div class="bg-white dark:bg-slate-800 rounded-[3.5rem] p-12 max-w-xl w-full shadow-2xl relative overflow-hidden border border-slate-100 dark:border-slate-700 animate-in zoom-in-95 duration-300">
            
            <!-- Decoración de fondo -->
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl"></div>
            
            <button @click="completeTour" class="absolute top-8 right-8 text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                <XMarkIcon class="w-8 h-8" />
            </button>

            <div class="relative z-10 text-center">
                <!-- Icono Dinámico -->
                <div class="w-24 h-24 rounded-[2.5rem] flex items-center justify-center mx-auto mb-10 transition-all duration-500 transform scale-110" :class="steps[currentStep-1].color">
                    <component :is="steps[currentStep-1].icon" class="w-12 h-12" />
                </div>

                <!-- Título y Descripción -->
                <h3 class="text-3xl font-black text-slate-900 dark:text-white mb-4 uppercase tracking-tight leading-none italic">
                    {{ steps[currentStep-1].title }}
                </h3>
                <p class="text-slate-500 dark:text-slate-400 text-lg leading-relaxed mb-12 min-h-[5rem]">
                    {{ steps[currentStep-1].description }}
                </p>

                <!-- Indicadores de Progreso -->
                <div class="flex justify-center gap-3 mb-12">
                    <div v-for="n in totalSteps" :key="n" 
                        class="h-1.5 rounded-full transition-all duration-500"
                        :class="[n === currentStep ? 'w-10 bg-indigo-600' : 'w-2 bg-slate-200 dark:bg-slate-700']">
                    </div>
                </div>

                <!-- Botones -->
                <button 
                    @click="nextStep"
                    class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-3xl shadow-2xl shadow-indigo-500/30 transition-all active:scale-95 uppercase tracking-widest flex items-center justify-center gap-3"
                >
                    {{ currentStep === totalSteps ? '¡EMPEZAR AHORA!' : 'SIGUIENTE PASO' }}
                    <RocketLaunchIcon v-if="currentStep === totalSteps" class="w-6 h-6 animate-bounce" />
                </button>
            </div>
        </div>
    </div>
</template>
