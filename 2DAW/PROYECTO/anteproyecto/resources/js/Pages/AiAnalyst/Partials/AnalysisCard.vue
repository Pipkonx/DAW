<script setup>
import { formatDate } from '@/Utils/formatting';

defineProps({
    analysis: Object
});

/**
 * Motor de renderizado simplificado para transformar Markdown básico en HTML con clases Tailwind.
 * 
 * @param {string} text Texto en formato Markdown (GGFM)
 * @returns {string} HTML renderizado con estilos premium
 */
const renderMarkdown = (text) => {
    if (!text) return '';
    
    return text
        .replace(/^### (.*$)/gim, '<h3 class="text-xl font-black mt-6 mb-3 text-slate-800 dark:text-slate-100">$1</h3>')
        .replace(/^## (.*$)/gim, '<h2 class="text-2xl font-black mt-8 mb-4 border-b pb-2 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white">$1</h2>')
        .replace(/^# (.*$)/gim, '<h1 class="text-3xl font-black mt-10 mb-6 text-slate-900 dark:text-white">$1</h1>')
        .replace(/\*\*(.*)\*\*/gim, '<strong class="font-bold text-slate-900 dark:text-white">$1</strong>')
        .replace(/\*(.*)\*/gim, '<em class="italic">$1</em>')
        .replace(/^\- (.*$)/gim, '<li class="ml-4 list-disc mb-1 text-slate-600 dark:text-slate-400">$1</li>')
        .replace(/\n\n/gim, '<br/><br/>')
        .replace(/\n/gim, '<br/>');
};
</script>

<template>
    <div class="relative group">
        <!-- Separador de Fecha con Estilo de Línea de Tiempo -->
        <div class="flex items-center gap-4 mb-8 sticky top-0 bg-slate-50 dark:bg-slate-900/95 py-3 z-10">
            <div class="h-[1px] flex-grow bg-slate-200 dark:bg-slate-800"></div>
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 whitespace-nowrap">
                {{ formatDate(analysis.date) }}
            </span>
            <div class="h-[1px] flex-grow bg-slate-200 dark:bg-slate-800"></div>
        </div>

        <!-- Cuerpo del Análisis Profesional -->
        <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-xl sm:rounded-3xl border border-slate-100 dark:border-slate-700 transition-all duration-500 hover:shadow-2xl hover:border-indigo-200 dark:hover:border-indigo-900/30">
            <div class="p-8 sm:p-12">
                <div class="mb-10 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-xl shadow-indigo-500/30">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900 dark:text-white leading-tight">Análisis Estratégico</h3>
                            <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest mt-1">fintechPro AI Intelligence</p>
                        </div>
                    </div>
                </div>

                <!-- Contenido Renderizado -->
                <div 
                    class="prose prose-slate dark:prose-invert max-w-none text-slate-700 dark:text-slate-300 leading-relaxed font-medium"
                    v-html="renderMarkdown(analysis.report)"
                >
                </div>

                <!-- Pie de página informativo -->
                <div class="mt-10 pt-8 border-t border-slate-50 dark:border-slate-700/50 flex justify-between items-center">
                    <p class="text-[10px] text-slate-400 italic max-w-xs font-bold uppercase tracking-tighter">
                        * Basado en posiciones consolidadas a tiempo real.
                    </p>
                    <div class="text-[10px] font-black text-slate-300 uppercase tracking-widest">
                        Ref: {{ analysis.id }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
:deep(li) {
    margin-left: 1.5rem;
    list-style-type: disc;
    margin-bottom: 0.5rem;
}
</style>
