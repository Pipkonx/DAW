<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    user_name: String,
    analyses: Array, // Array of {id, report, date, created_at}
    has_investments: Boolean,
});

const allAnalyses = ref([...props.analyses]);
const loading = ref(false);
const error = ref(null);
const currentStep = ref(0);
const loadingSteps = [
    "Leyendo tus posiciones actuales...",
    "Consultando datos de mercado en tiempo real...",
    "Analizando diversificación y nivel de riesgo...",
    "Evaluando tendencias de los últimos índices...",
    "Buscando referencias de mercado globales...",
    "Generando informe estratégico personalizado con IA..."
];

let stepInterval = null;

const startLoadingSteps = () => {
    currentStep.value = 0;
    stepInterval = setInterval(() => {
        if (currentStep.value < loadingSteps.length - 1) {
            currentStep.value++;
        }
    }, 4000);
};

const stopLoadingSteps = () => {
    if (stepInterval) {
        clearInterval(stepInterval);
        stepInterval = null;
    }
};

const fetchTodayReport = async () => {
    // Usar fecha local YYYY-MM-DD para evitar problemas con UTC/Zonas horarias
    const nowLocal = new Date();
    const today = nowLocal.getFullYear() + '-' + 
                 String(nowLocal.getMonth() + 1).padStart(2, '0') + '-' + 
                 String(nowLocal.getDate()).padStart(2, '0');
                 
    const hasToday = allAnalyses.value.some(a => a.date === today);
    
    if (hasToday) {
        return;
    }

    loading.value = true;
    error.value = null;
    startLoadingSteps();

    try {
        const response = await axios.get(route('ai-analyst.report'));
        if (response.data.report) {
            const newAnalysis = {
                id: Date.now(),
                report: response.data.report,
                date: today,
                created_at: new Date().toISOString()
            };
            allAnalyses.value.unshift(newAnalysis);
        } else if (response.data.error) {
            error.value = response.data.error;
        }
    } catch (e) {
        if (e.response && e.response.data && e.response.data.error) {
            error.value = e.response.data.error;
        } else if (e.response && e.response.status === 429) {
            error.value = "Límite de cuota alcanzado. El modelo está saturado, por favor espera 1 minuto.";
        } else if (e.message) {
            error.value = "Error de conexión: " + e.message;
        } else {
            error.value = 'No se pudo generar el informe diario. Por favor, intenta de nuevo.';
        }
        console.error(e);
    } finally {
        loading.value = false;
        stopLoadingSteps();
    }
};

onMounted(() => {
    if (props.has_investments) {
        fetchTodayReport();
    }
});

const formatDate = (dateStr) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateStr).toLocaleDateString('es-ES', options);
};

// Simple markdown renderer for basic tags
const renderMarkdown = (text) => {
    if (!text) return '';
    
    let html = text
        .replace(/^### (.*$)/gim, '<h3 class="text-xl font-bold mt-6 mb-3 text-slate-800 dark:text-slate-100">$1</h3>')
        .replace(/^## (.*$)/gim, '<h2 class="text-2xl font-bold mt-8 mb-4 border-b pb-2 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white">$1</h2>')
        .replace(/^# (.*$)/gim, '<h1 class="text-3xl font-extrabold mt-10 mb-6 text-slate-900 dark:text-white">$1</h1>')
        .replace(/\*\*(.*)\*\*/gim, '<strong class="font-bold text-slate-900 dark:text-white">$1</strong>')
        .replace(/\*(.*)\*/gim, '<em class="italic">$1</em>')
        .replace(/^\- (.*$)/gim, '<li class="ml-4 list-disc mb-1">$1</li>')
        .replace(/\n\n/gim, '<br/><br/>')
        .replace(/\n/gim, '<br/>');
        
    return html;
};

</script>

<template>
    <Head title="Analista IA" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-800 dark:text-slate-200">
                Resumen Diario del Analista IA
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Estado sin Inversiones -->
                <div v-if="!has_investments" class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-xl p-12 text-center mb-8 border-2 border-dashed border-slate-200 dark:border-slate-700">
                    <div class="flex flex-col items-center">
                        <div class="w-20 h-20 mb-6 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Requiere Inversiones Activas</h3>
                        <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-8">
                            El Analista IA necesita analizar tus posiciones actuales (Acciones, ETFs, Cripto o Fondos) para generarte un informe estratégico personalizado.
                        </p>
                        <Link :href="route('dashboard')" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold transition-all shadow-lg shadow-blue-500/20 active:scale-95">
                            Añadir mi primera posición
                        </Link>
                    </div>
                </div>

                <!-- Info de Cortesía -->
                <div v-if="has_investments" class="mb-6 flex items-center gap-3 px-4 py-3 bg-blue-50/50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/50 rounded-xl text-blue-600 dark:text-blue-400 text-xs font-medium">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>El Analista IA genera un informe estratégico nuevo cada día al acceder a esta sección, basándose en tus posiciones actuales.</span>
                </div>
                <!-- Estado de Carga (Sólo si no hay nada aún) -->
                <div v-if="loading && allAnalyses.length === 0" class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-xl p-12 text-center mb-8">
                    <div class="flex flex-col items-center">
                        <div class="relative w-20 h-20 mb-6">
                            <div class="absolute inset-0 rounded-full border-4 border-blue-100 dark:border-slate-700"></div>
                            <div class="absolute inset-0 rounded-full border-4 border-blue-600 border-t-transparent animate-spin"></div>
                            <div class="absolute inset-0 flex items-center justify-center text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">{{ loadingSteps[currentStep] }}</h3>
                        <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                            Analizando tu patrimonio con inteligencia avanzada...
                        </p>
                    </div>
                </div>

                <!-- Error (Siempre visible arriba si ocurre) -->
                <div v-if="error" class="mb-8 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-300 text-sm flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <span class="flex-grow">{{ error }}</span>
                    <button @click="fetchTodayReport" class="ml-auto bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-xs font-bold transition-colors">Reintentar</button>
                </div>

                <!-- Feed de Análisis (Orden Descendente) -->
                <div class="space-y-12">
                    <!-- Loading state indicator when generating today's and there is history -->
                    <div v-if="loading && allAnalyses.length > 0" class="flex flex-col items-center justify-center p-6 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800 shadow-inner">
                        <div class="flex items-center text-blue-600 dark:text-blue-400 text-sm font-bold mb-2">
                             <svg class="animate-spin h-4 w-4 mr-3" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            ACTUALIZANDO ANÁLISIS...
                        </div>
                        <div class="text-xs text-blue-500/70 dark:text-blue-400/50 italic animate-pulse">
                            {{ loadingSteps[currentStep] }}
                        </div>
                    </div>

                    <div v-for="(an, index) in allAnalyses" :key="an.id" class="relative">
                        <!-- Separador de Fecha Estético -->
                        <div class="flex items-center gap-4 mb-6 sticky top-0 bg-slate-50 dark:bg-slate-900/90 py-2 z-10">
                            <div class="h-[1px] flex-grow bg-slate-200 dark:border-slate-700"></div>
                            <span class="text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 whitespace-nowrap">
                                {{ formatDate(an.date) }}
                            </span>
                            <div class="h-[1px] flex-grow bg-slate-200 dark:border-slate-700"></div>
                        </div>

                        <!-- Card del Análisis -->
                        <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-xl sm:rounded-2xl border border-slate-100 dark:border-slate-700 transition-all duration-300 hover:shadow-2xl">
                            <div class="p-6 sm:p-10">
                                <div class="mb-6 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Análisis Profesional</h3>
                                            <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest">fintechPro AI Engine</p>
                                        </div>
                                    </div>
                                    <div class="text-[10px] text-slate-400 italic">
                                        ID: {{ an.id }}
                                    </div>
                                </div>

                                <div 
                                    class="prose prose-slate dark:prose-invert max-w-none text-slate-700 dark:text-slate-300 leading-relaxed"
                                    v-html="renderMarkdown(an.report)"
                                >
                                </div>

                                <div class="mt-8 pt-6 border-t border-slate-50 dark:border-slate-700 flex justify-between items-center">
                                    <p class="text-[10px] text-slate-400 italic max-w-xs">
                                        * Basado en tus posiciones a tiempo real en esta fecha.
                                    </p>
                                    <div class="flex items-center gap-1 text-slate-400 group">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 group-hover:text-blue-500 transition-colors">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0-10.628a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Zm0 10.628a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State Complete -->
                <div v-if="allAnalyses.length === 0 && !loading" class="text-center py-20">
                    <div class="bg-slate-100 dark:bg-slate-800 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6 4.125 2.25 2.25m0 0 2.25 2.25m-2.25-2.25-2.25 2.25m2.25-2.25-2.25-2.25M3.75 7.5l.625-10.632A2.25 2.25 0 0 1 6.622 2.25h10.756a2.25 2.25 0 0 1 2.247 2.118L20.25 7.5m-9 3.75h3" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Sin análisis disponibles</h3>
                    <p class="text-slate-500 dark:text-slate-400">Hubo un problema generando el informe de hoy.</p>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Estilos adicionales para que las listas se vean bien si Tailwind no las aplica */
:deep(li) {
    margin-left: 1.5rem;
    list-style-type: disc;
    margin-bottom: 0.5rem;
}
:deep(h1), :deep(h2), :deep(h3) {
    color: inherit;
}
</style>
