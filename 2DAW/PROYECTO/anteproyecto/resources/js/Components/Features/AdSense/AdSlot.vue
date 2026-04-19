<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

/**
 * Componente para renderizar espacios publicitarios de Google AdSense.
 * Incluye lógica para ocultar los anuncios si el usuario es "Premium".
 */
const props = defineProps({
    slotId: {
        type: String,
        required: true
    },
    adFormat: {
        type: String,
        default: 'auto' // auto, fluid, rectangle, vertical, horizontal
    },
    layout: {
        type: String,
        default: 'horizontal' // horizontal, square, sidebar
    },
    fullWidthResponsive: {
        type: Boolean,
        default: true
    },
    class: {
        type: String,
        default: ''
    }
});

const page = usePage();

/**
 * Comprobar si el usuario tiene una suscripción activa.
 */
const isPremium = computed(() => {
    return page.props.auth?.user?.is_premium || false;
});

// ID de cliente de AdSense (en un entorno real esto vendría de .env o config)
const adsenseClientId = 'ca-pub-XXXXXXXXXXXXXXXX'; 

</script>

<template>
    <!-- Solo mostramos el anuncio si el usuario NO es premium -->
    <div v-if="!isPremium" :class="['ad-container my-6 overflow-hidden flex justify-center', props.class]">
        <div class="w-full h-full bg-slate-100 dark:bg-slate-800/50 rounded-xl p-2 border border-dashed border-slate-300 dark:border-slate-700 transition-all hover:border-slate-400 dark:hover:border-slate-600">
            <p class="text-[10px] text-center text-slate-400 uppercase tracking-widest mb-1 italic">Publicidad Controlada</p>
            
            <!-- Marcado estándar de Google AdSense -->
            <ins class="adsbygoogle"
                 style="display:block"
                 :data-ad-client="adsenseClientId"
                 :data-ad-slot="slotId"
                 :data-ad-format="adFormat"
                 :data-full-width-responsive="fullWidthResponsive ? 'true' : 'false'"></ins>
            
            <!-- Placeholder con variedad visual -->
            <div :class="{
                'h-24 md:h-32': layout === 'horizontal',
                'aspect-square max-w-[300px] mx-auto': layout === 'square',
                'h-[400px]': layout === 'sidebar'
            }" class="flex flex-col items-center justify-center text-slate-300 dark:text-slate-600 border border-slate-200 dark:border-slate-700/50 rounded-lg bg-white dark:bg-slate-900/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-2 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-tighter opacity-40">Anuncio AdSense</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
.ad-container {
    min-width: 120px;
}
</style>
