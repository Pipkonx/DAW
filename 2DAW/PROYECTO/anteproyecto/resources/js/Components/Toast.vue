<script setup>
import { computed, watch, ref, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const flash = computed(() => page.props.flash || {});

const show = ref(false);
const message = ref('');
const type = ref('success');

let timer;

watch(() => flash.value, (newFlash) => {
    if (newFlash.success) {
        showToast(newFlash.success, 'success');
    } else if (newFlash.error) {
        showToast(newFlash.error, 'error');
    }
}, { deep: true });

const showToast = (msg, msgType) => {
    message.value = msg;
    type.value = msgType;
    show.value = true;

    if (timer) clearTimeout(timer);
    
    // Hide toast after a few seconds
    timer = setTimeout(() => {
        close();
    }, 4000);
};

const close = () => {
    show.value = false;
    // Clearing flash variables prevents re-trigger on hot reload if caching applies
    if (page.props.flash) {
        page.props.flash.success = null;
        page.props.flash.error = null;
    }
};

onUnmounted(() => {
    if (timer) clearTimeout(timer);
});
</script>

<template>
    <transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-[-2rem] opacity-0 sm:translate-y-0 sm:translate-x-full"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-show="show" class="fixed top-6 right-6 z-[9999] p-4 max-w-sm w-full bg-white/90 dark:bg-slate-800/90 backdrop-blur-md border shadow-lg rounded-2xl flex items-start gap-4 transition-colors"
             :class="type === 'success' ? 'border-emerald-200 dark:border-emerald-800/40 text-emerald-600 dark:text-emerald-400' : 'border-rose-200 dark:border-rose-800/40 text-rose-600 dark:text-rose-400'">
            <!-- Íconos -->
            <div class="flex-shrink-0 mt-0.5">
                <svg v-if="type === 'success'" class="h-6 w-6 text-emerald-500 shadow-sm rounded-full bg-emerald-50 dark:bg-emerald-900/40 p-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <svg v-else class="h-6 w-6 text-rose-500 shadow-sm rounded-full bg-rose-50 dark:bg-rose-900/40 p-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            
            <div class="flex-1 w-0 pt-0.5">
                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                    {{ type === 'success' ? '¡Hecho!' : 'Operación Fallida' }}
                </p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                    {{ message }}
                </p>
            </div>

            <!-- Botón Cerrar Manual -->
            <button @click="close" class="mt-0.5 flex-shrink-0 bg-transparent rounded-lg p-1.5 text-slate-400 hover:text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 focus:outline-none transition-colors">
                <span class="sr-only">Cerrar</span>
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- ProgressBar -->
            <div class="absolute bottom-0 left-0 h-1 bg-gradient-to-r from-transparent to-current opacity-20 origin-left"
                 :class="{ 'animate-toast-progress': show }"
                 style="width: 100%; border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem;">
            </div>
        </div>
    </transition>
</template>

<style scoped>
@keyframes progress {
    0% { transform: scaleX(1); }
    100% { transform: scaleX(0); }
}

.animate-toast-progress {
    animation: progress 4s linear forwards;
}
</style>
