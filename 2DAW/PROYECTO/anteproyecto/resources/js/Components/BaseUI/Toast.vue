<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    message: String,
    type: {
        type: String,
        default: 'success'
    },
    duration: {
        type: Number,
        default: 3000
    }
});

const visible = ref(true);
const emit = defineEmits(['close']);

onMounted(() => {
    setTimeout(() => {
        visible.value = false;
        setTimeout(() => emit('close'), 500);
    }, props.duration);
});
</script>

<template>
    <Transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="visible" class="fixed top-5 right-5 z-[200] max-w-sm w-full bg-white dark:bg-slate-800 shadow-2xl rounded-2xl pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden border border-slate-100 dark:border-slate-700">
            <div class="p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <!-- Success Icon -->
                        <svg v-if="type === 'success'" class="h-6 w-6 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <!-- Error Icon -->
                        <svg v-else-if="type === 'error'" class="h-6 w-6 text-rose-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-xs font-black uppercase tracking-widest text-slate-900 dark:text-white">
                            {{ type === 'success' ? 'Éxito' : 'Aviso' }}
                        </p>
                        <p class="mt-0.5 text-sm font-medium text-slate-500 dark:text-slate-400">
                            {{ message }}
                        </p>
                    </div>
                </div>
            </div>
            <!-- Progress Bar -->
            <div class="h-1 bg-slate-100 dark:bg-slate-700 w-full">
                <div class="h-full bg-blue-500 transition-all duration-[3000ms] ease-linear" :style="{ width: visible ? '0%' : '100%' }"></div>
            </div>
        </div>
    </Transition>
</template>
