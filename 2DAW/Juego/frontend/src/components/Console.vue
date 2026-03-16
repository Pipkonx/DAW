<template>
  <div class="flex flex-col h-full bg-transparent font-mono text-sm relative">
    <div class="flex items-center justify-between px-4 py-2 bg-white/5 border-b border-white/5 select-none">
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Salida</span>
            <span v-if="logs.length > 0" class="px-1.5 py-0.5 bg-slate-800/50 rounded-full text-[10px] text-primary">{{ logs.length }}</span>
        </div>
        <div class="flex items-center gap-2">
            <button 
                @click="autoScroll = !autoScroll" 
                class="hover:text-white transition-colors p-1 rounded hover:bg-white/10"
                :class="{ 'text-primary': autoScroll, 'text-slate-500': !autoScroll }"
                title="Auto-scroll"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" transform="rotate(180 10 10)" />
                </svg>
            </button>
            <button @click="$emit('clear')" class="text-slate-500 hover:text-red-400 transition-colors p-1 rounded hover:bg-white/10" title="Limpiar Consola">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Logs Area -->
    <div ref="scrollContainer" class="flex-1 overflow-y-auto p-4 space-y-1 custom-scroll">
        <div v-if="logs.length === 0" class="text-slate-600 italic px-2 py-1 text-center mt-4 opacity-50">
            <span class="block mb-2 text-2xl">⚡</span>
            Listo para ejecutar...
        </div>
        <div 
            v-for="(log, i) in logs" 
            :key="i" 
            class="flex gap-3 break-words hover:bg-white/5 px-2 py-1.5 rounded transition-colors border-l-2 border-transparent hover:border-slate-600"
            :class="getLogBorderClass(log.type)"
        >
            <!-- Timestamp -->
            <span class="text-slate-600 flex-shrink-0 select-none w-16 text-right font-mono text-[10px] pt-1 opacity-60">
                {{ formatTime(log.timestamp) }}
            </span>

            <!-- Content -->
            <span :class="getLogClass(log.type)" class="font-mono flex-1 leading-relaxed">
                <span v-if="log.count > 1" class="bg-slate-700 text-slate-300 px-1.5 rounded-full text-[10px] mr-1.5 inline-block border border-slate-600">{{ log.count }}</span>
                
                <template v-if="log.type === 'error' && log.message.includes('en línea')">
                    <div class="flex flex-col">
                        <span class="text-red-400 font-bold flex items-center gap-1">
                            <span class="text-xs">❌</span>
                            {{ log.message.split(': ')[0] }}
                        </span>
                        <span class="text-red-300/80 text-xs mt-0.5 ml-5 italic">
                            {{ log.message.split(': ').slice(1).join(': ') }}
                        </span>
                    </div>
                </template>
                <template v-else>
                    {{ log.message }}
                </template>
            </span>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, nextTick, onMounted } from 'vue';

const props = defineProps({
  logs: {
    type: Array,
    default: () => []
  }
});

defineEmits(['clear']);

const scrollContainer = ref(null);
const autoScroll = ref(true);

const formatTime = (timestamp) => {
  return new Date(timestamp).toLocaleTimeString([], { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit', fractionalSecondDigits: 3 });
};

const getLogClass = (type) => {
  switch (type) {
    case 'error': return 'text-red-400 font-bold';
    case 'success': return 'text-emerald-400 font-bold';
    case 'warning': return 'text-amber-400';
    case 'info': return 'text-cyan-400';
    default: return 'text-slate-300';
  }
};

const getLogBorderClass = (type) => {
    switch (type) {
        case 'error': return 'border-red-500/50 bg-red-500/5';
        case 'success': return 'border-emerald-500/50 bg-emerald-500/5';
        case 'warning': return 'border-amber-500/50 bg-amber-500/5';
        default: return 'border-transparent';
    }
};

const scrollToBottom = () => {
  if (scrollContainer.value && autoScroll.value) {
    scrollContainer.value.scrollTop = scrollContainer.value.scrollHeight;
  }
};

watch(() => props.logs, () => {
  nextTick(scrollToBottom);
}, { deep: true });

onMounted(() => {
  scrollToBottom();
});
</script>

<style scoped>
.custom-scroll::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
.custom-scroll::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.1);
}
.custom-scroll::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 3px;
}
.custom-scroll::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.2);
}
</style>
