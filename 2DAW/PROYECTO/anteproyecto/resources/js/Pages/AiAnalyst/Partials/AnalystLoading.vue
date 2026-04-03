<script setup>
defineProps({
    currentStep: Number,
    loadingSteps: Array
});
</script>

<template>
    <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-xl p-12 text-center mb-8 border border-slate-100 dark:border-slate-700">
        <div class="flex flex-col items-center">
            <!-- Spinner Animado con Icono Central -->
            <div class="relative w-24 h-24 mb-8">
                <div class="absolute inset-0 rounded-full border-4 border-indigo-50 dark:border-slate-700"></div>
                <div class="absolute inset-0 rounded-full border-4 border-indigo-600 border-t-transparent animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center text-indigo-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                </div>
            </div>

            <!-- Paso de Procesamiento Actual -->
            <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2 transition-all duration-500">
                {{ loadingSteps[currentStep] }}
            </h3>
            <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto font-medium">
                Analizando tu patrimonio global mediante algoritmos avanzados de IA...
            </p>
        </div>
    </div>
</template>
