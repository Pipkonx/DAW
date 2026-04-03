<script setup>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AnalystLoading from './Partials/AnalystLoading.vue';
import AnalysisCard from './Partials/AnalysisCard.vue';
import AnalystEmptyState from './Partials/AnalystEmptyState.vue';

const props = defineProps({
    analyses: Array,
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

/**
 * Inicia la rotación de pasos de carga para mejorar la experiencia de usuario (UX).
 */
const startLoadingSteps = () => {
    currentStep.value = 0;
    stepInterval = setInterval(() => {
        if (currentStep.value < loadingSteps.length - 1) currentStep.value++;
    }, 4000);
};

const stopLoadingSteps = () => {
    if (stepInterval) clearInterval(stepInterval);
    stepInterval = null;
};

/**
 * Solicita la generación del informe diario del analista IA.
 */
const fetchTodayReport = async () => {
    const nowLocal = new Date();
    const today = nowLocal.getFullYear() + '-' + String(nowLocal.getMonth() + 1).padStart(2, '0') + '-' + String(nowLocal.getDate()).padStart(2, '0');
    
    // Evitar peticiones duplicadas si ya existe un informe para hoy en el estado local
    if (allAnalyses.value.some(a => a.date === today)) return;

    loading.value = true;
    error.value = null;
    startLoadingSteps();

    try {
        const response = await axios.get(route('ai-analyst.report'));
        if (response.data.report) {
            allAnalyses.value.unshift({
                id: Date.now(),
                report: response.data.report,
                date: today,
                created_at: new Date().toISOString()
            });
        } else if (response.data.error) {
            error.value = response.data.error;
        }
    } catch (e) {
        error.value = e.response?.data?.error || "Error de conexión con el motor de IA. Por favor, reintenta en unos instantes.";
        if (e.response?.status === 429) error.value = "Límite de cuota alcanzado. El modelo está saturado, por favor espera 1 minuto.";
    } finally {
        loading.value = false;
        stopLoadingSteps();
    }
};

onMounted(() => {
    if (props.has_investments) fetchTodayReport();
});
</script>

<template>
    <Head title="Analista IA" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-black text-slate-800 dark:text-slate-200">
                Analista de Estrategia IA
            </h2>
        </template>

        <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Info de Cortesía: Explicación del funcionamiento -->
                <div v-if="has_investments" class="mb-10 flex items-center gap-4 px-6 py-4 bg-white dark:bg-slate-800 border-l-4 border-indigo-500 shadow-sm rounded-r-2xl font-bold text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">
                    <svg class="w-5 h-5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>El Analista IA genera un informe dinámico basado en tu patrimonio actual cada 24 horas.</span>
                </div>

                <!-- Estados de Carga y Vacíos (Modulares) -->
                <AnalystLoading 
                    v-if="loading && allAnalyses.length === 0" 
                    :current-step="currentStep" 
                    :loading-steps="loadingSteps" 
                />

                <AnalystEmptyState 
                    v-if="!has_investments || error || (!loading && allAnalyses.length === 0)" 
                    :has-investments="has_investments" 
                    :error="error"
                    @retry="fetchTodayReport"
                />

                <!-- Feed de Informes Generados (Cronológico) -->
                <div v-if="allAnalyses.length > 0" class="space-y-16">
                    
                    <!-- Indicador de Refresco (Cuando hay historial pero se genera el de hoy) -->
                    <div v-if="loading" class="flex flex-col items-center justify-center p-8 bg-indigo-50/50 dark:bg-indigo-900/10 rounded-[2rem] border border-indigo-100 dark:border-indigo-800/30">
                        <div class="flex items-center text-indigo-600 font-black text-xs tracking-[0.2em] mb-3">
                             <svg class="animate-spin h-4 w-4 mr-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                             ACTUALIZANDO MODELO ESTRATÉGICO...
                        </div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter italic animate-pulse">
                            {{ loadingSteps[currentStep] }}
                        </div>
                    </div>

                    <!-- Lista de Fichas de Análisis -->
                    <AnalysisCard 
                        v-for="an in allAnalyses" 
                        :key="an.id" 
                        :analysis="an" 
                    />
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
