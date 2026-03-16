<template>
  <Transition name="toast">
    <div v-if="visible" 
         class="fixed top-24 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-xl shadow-2xl backdrop-blur-md border border-white/10"
         :class="typeClasses">
      
      <!-- Icon -->
      <div class="flex-shrink-0">
        <svg v-if="type === 'success'" class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <svg v-else-if="type === 'error'" class="w-6 h-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <svg v-else class="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>

      <!-- Content -->
      <div class="flex flex-col">
        <span class="font-bold text-sm text-white">{{ title }}</span>
        <span class="text-xs text-slate-300">{{ message }}</span>
      </div>

      <!-- Close -->
      <button @click="hide" class="ml-2 text-slate-400 hover:text-white">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  duration: { type: Number, default: 3000 }
});

const visible = ref(false);
const title = ref('');
const message = ref('');
const type = ref('info'); // success, error, info
let timer = null;

const typeClasses = computed(() => {
  switch (type.value) {
    case 'success': return 'bg-emerald-900/80 border-emerald-500/30 shadow-[0_0_15px_rgba(16,185,129,0.2)]';
    case 'error': return 'bg-red-900/80 border-red-500/30 shadow-[0_0_15px_rgba(239,68,68,0.2)]';
    default: return 'bg-slate-800/80 border-slate-500/30 shadow-[0_0_15px_rgba(148,163,184,0.2)]';
  }
});

const show = (newTitle, newMessage, newType = 'info') => {
  title.value = newTitle;
  message.value = newMessage;
  type.value = newType;
  visible.value = true;

  if (timer) clearTimeout(timer);
  timer = setTimeout(() => {
    visible.value = false;
  }, props.duration);
};

const hide = () => {
  visible.value = false;
};

defineExpose({ show });
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.toast-enter-from,
.toast-leave-to {
  transform: translateX(20px) scale(0.95);
  opacity: 0;
}
</style>