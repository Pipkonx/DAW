<script setup>
import { ref } from 'vue';

const isOpen = ref(false);
const selectedQuestion = ref(null);

const faqs = [
    {
        q: "¿Cómo añado mis inversiones?",
        a: "Ve a la sección 'Mi Patrimonio' dentro del menú 'Patrimonio'. Allí puedes crear una cartera nueva o añadir transacciones a tus activos existentes usando el icono '+' en la tabla de activos."
    },
    {
        q: "¿Qué es el Patrimonio Neto?",
        a: "Es el valor total de todos tus activos (efectivo, acciones, criptos, inmuebles) menos tus deudas. Es el indicador real de tu riqueza actual."
    },
    {
        q: "¿Cómo se actualizan los precios?",
        a: "Los precios de mercado se sincronizan automáticamente cada vez que abres el Dashboard utilizando datos en tiempo real de mercados globales."
    },
    {
        q: "¿Es segura mi información?",
        a: "Absolutamente. Tus datos están cifrados de extremo a extremo y nunca compartimos tu información financiera con terceros. Solo tú tienes la llave de tus datos."
    },
    {
        q: "¿Cómo veo mis beneficios totales?",
        a: "En el Dashboard principal tienes un resumen de tu 'Beneficio Total'. Para un análisis detallado, ve a 'Mi Patrimonio' y consulta la sección de 'Rendimiento' y los gráficos de evolución."
    }
];

const toggleAssistant = () => {
    isOpen.value = !isOpen.value;
    if (!isOpen.value) selectedQuestion.value = null;
};

const selectQuestion = (index) => {
    selectedQuestion.value = index;
};

const backToList = () => {
    selectedQuestion.value = null;
};
</script>

<template>
    <div class="fixed bottom-8 right-8 z-50 flex flex-col items-end">
        <!-- Ventana de Asistente -->
        <transition
            enter-active-class="transition duration-300 ease-out transform"
            enter-from-class="opacity-0 translate-y-10 scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition duration-200 ease-in transform"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-10 scale-95"
        >
            <div v-if="isOpen" class="mb-4 w-80 md:w-96 bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <!-- Header -->
                <div class="bg-blue-600 p-6 text-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                            <span class="text-xl">🤖</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">Asistente FintechPro</h3>
                            <p class="text-xs text-blue-100">En línea para ayudarte</p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6 h-80 overflow-y-auto">
                    <transition
                        mode="out-in"
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="opacity-0 translate-x-4"
                        enter-to-class="opacity-100 translate-x-0"
                        leave-active-class="transition duration-200 ease-in"
                        leave-from-class="opacity-100 translate-x-0"
                        leave-to-class="opacity-0 -translate-x-4"
                    >
                        <!-- List of Questions -->
                        <div v-if="selectedQuestion === null" :key="'list'" class="space-y-3">
                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">¡Hola! Selecciona una duda frecuente para ayudarte:</p>
                            <button 
                                v-for="(faq, index) in faqs" 
                                :key="index"
                                @click="selectQuestion(index)"
                                class="w-full text-left p-3 rounded-xl border border-slate-100 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all group"
                            >
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-200 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                    {{ faq.q }}
                                </span>
                            </button>
                        </div>

                        <!-- Answer Detail -->
                        <div v-else :key="'answer'" class="space-y-4">
                            <button 
                                @click="backToList"
                                class="inline-flex items-center text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest hover:underline mb-2"
                            >
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                Volver
                            </button>
                            <h4 class="font-bold text-slate-800 dark:text-white">{{ faqs[selectedQuestion].q }}</h4>
                            <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl">
                                {{ faqs[selectedQuestion].a }}
                            </p>
                        </div>
                    </transition>
                </div>
            </div>
        </transition>

        <!-- Botón de Chat (Burbuja) -->
        <button 
            @click="toggleAssistant"
            class="flex items-center justify-center w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg transition-all hover:scale-110 active:scale-95"
            :class="{ 'rotate-90': isOpen }"
        >
            <svg v-if="!isOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
            </svg>
            <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</template>

<style scoped>
/* Estilos adicionales si fueran necesarios, aunque Tailwind cubre casi todo */
::-webkit-scrollbar {
    width: 4px;
}
::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}
.dark ::-webkit-scrollbar-thumb {
    background: #475569;
}
</style>
