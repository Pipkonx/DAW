<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue';

const props = defineProps({
    show: Boolean,
    title: {
        type: String,
        default: '¿Estás seguro?'
    },
    message: String,
    confirmText: {
        type: String,
        default: 'Confirmar'
    },
    cancelText: {
        type: String,
        default: 'Cancelar'
    },
    type: {
        type: String,
        default: 'danger' // danger, warning, info
    }
});

const emit = defineEmits(['confirm', 'cancel']);
const dialog = ref();
const showSlot = ref(props.show);

watch(
    () => props.show,
    () => {
        if (props.show) {
            document.body.style.overflow = 'hidden';
            showSlot.value = true;
            dialog.value?.showModal();
        } else {
            document.body.style.overflow = '';
            setTimeout(() => {
                dialog.value?.close();
                showSlot.value = false;
            }, 200);
        }
    },
);

const close = () => {
    emit('cancel');
};

const closeOnEscape = (e) => {
    if (e.key === 'Escape' && props.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    document.body.style.overflow = '';
});

const typeClasses = {
    danger: 'bg-rose-600 hover:bg-rose-700 shadow-rose-500/20 text-white',
    warning: 'bg-amber-500 hover:bg-amber-600 shadow-amber-500/20 text-white',
    info: 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-500/20 text-white'
};

const iconColors = {
    danger: 'text-rose-600 bg-rose-50 dark:bg-rose-900/20',
    warning: 'text-amber-500 bg-amber-50 dark:bg-amber-900/20',
    info: 'text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20'
};

</script>

<template>
    <Teleport to="body">
        <dialog
            class="z-[100] m-0 min-h-full min-w-full overflow-y-auto bg-transparent backdrop:bg-transparent"
            ref="dialog"
        >
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" @click.self="close">
                
                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 scale-95 translate-y-4"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                    leave-active-class="transition ease-in duration-200"
                    leave-from-class="opacity-100 scale-100 translate-y-0"
                    leave-to-class="opacity-0 scale-95 translate-y-4"
                >
                    <div v-if="showSlot" class="bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-2xl border border-slate-100 dark:border-slate-700 max-w-md w-full overflow-hidden">
                        <div class="p-8 text-center">
                            
                            <!-- Icono Dinámico -->
                            <div :class="[iconColors[type], 'w-20 h-20 rounded-3xl mx-auto mb-6 flex items-center justify-center rotate-3 scale-110 shadow-inner']">
                                <svg v-if="type === 'danger'" xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <svg v-else-if="type === 'warning'" xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>

                            <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-2 leading-tight">
                                {{ title }}
                            </h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed px-4">
                                {{ message }}
                            </p>
                        </div>

                        <div class="p-6 bg-slate-50 dark:bg-slate-900/50 flex gap-3">
                            <button 
                                @click="emit('cancel')"
                                class="flex-1 px-6 py-4 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-bold rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-all active:scale-95 text-sm uppercase tracking-widest border border-slate-200 dark:border-slate-700"
                            >
                                {{ cancelText }}
                            </button>
                            <button 
                                @click="emit('confirm')"
                                :class="[typeClasses[type], 'flex-1 px-6 py-4 font-black rounded-2xl transition-all active:scale-95 text-sm uppercase tracking-widest shadow-lg']"
                            >
                                {{ confirmText }}
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </dialog>
    </Teleport>
</template>
