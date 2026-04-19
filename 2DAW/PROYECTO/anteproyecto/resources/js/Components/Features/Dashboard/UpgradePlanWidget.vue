<script setup>
import { Link } from '@inertiajs/vue3';

/**
 * Widget publicitario interno para incentivar la compra de planes Premium.
 */
defineProps({
    compact: Boolean
});
</script>

<template>
    <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 to-blue-700 rounded-2xl p-6 shadow-xl shadow-indigo-500/20 text-white group">
        <!-- Elementos decorativos -->
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-indigo-400/20 rounded-full blur-xl"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="font-bold tracking-tight uppercase text-xs text-indigo-100">Plan Pro</span>
            </div>

            <h3 class="text-xl font-black mb-2 leading-tight">
                Desbloquea todo <br v-if="!compact" /> el poder de tu cartera
            </h3>
            
            <p class="text-sm text-indigo-100/80 mb-6 max-w-[200px]">
                Elimina anuncios y accede al **Analista IA** avanzado hoy mismo.
            </p>

            <Link 
                :href="route('subscription.index')"
                class="inline-flex items-center justify-center px-6 py-3 bg-white text-indigo-600 font-bold rounded-xl shadow-lg hover:bg-indigo-50 hover:scale-105 active:scale-95 transition-all duration-300 w-full sm:w-auto"
            >
                Mejorar Plan
                <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </Link>
        </div>

        <!-- Marca de agua de fondo -->
        <div class="absolute right-[-10%] bottom-[-10%] opacity-10 rotate-12">
            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 2.18l7 3.12v4.7c0 4.41-3 8.54-7 9.8-4-1.26-7-5.39-7-9.8V6.3l7-3.12z"/>
            </svg>
        </div>
    </div>
</template>
