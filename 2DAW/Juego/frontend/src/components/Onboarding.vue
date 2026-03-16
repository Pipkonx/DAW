<template>
  <Transition name="fade">
    <div v-if="isVisible" class="fixed inset-0 z-[100] pointer-events-none">
      <!-- Backdrop (Clickable only for the current step) -->
      <div class="absolute inset-0 bg-slate-950/40 backdrop-blur-[2px] pointer-events-auto" @click="nextStep"></div>

      <!-- Highlighting the target element -->
      <div 
        ref="highlightBox"
        class="absolute border-2 border-emerald-500 shadow-[0_0_30px_rgba(16,185,129,0.5)] rounded-lg transition-all duration-500 ease-in-out z-10 pointer-events-none"
        :style="highlightStyle"
      >
        <div class="absolute -inset-1 bg-emerald-500/20 animate-pulse rounded-lg"></div>
      </div>

      <!-- Tooltip / Bocadillo -->
      <div 
        class="absolute z-20 transition-all duration-500 ease-in-out pointer-events-auto max-w-xs sm:max-w-sm"
        :style="tooltipStyle"
      >
        <div class="bg-slate-900 border border-emerald-500/30 p-5 rounded-2xl shadow-2xl relative overflow-hidden group">
          <!-- Decoration -->
          <div class="absolute top-0 left-0 w-1 h-full bg-emerald-500"></div>
          
          <div class="flex items-start gap-4">
            <div class="text-3xl mt-1 animate-bounce">{{ currentStepData.icon }}</div>
            <div class="flex-1">
              <h3 class="text-white font-bold text-lg mb-1">{{ currentStepData.title }}</h3>
              <p class="text-slate-400 text-sm leading-relaxed mb-4">{{ currentStepData.content }}</p>
              
              <div class="flex items-center justify-between">
                <span class="text-[10px] font-mono text-slate-500 uppercase tracking-widest">Paso {{ currentStep + 1 }} de {{ steps.length }}</span>
                <button 
                  @click="nextStep"
                  class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold px-4 py-2 rounded-lg transition-all flex items-center gap-2 group/btn shadow-lg shadow-emerald-900/20"
                >
                  {{ currentStep === steps.length - 1 ? '¡Entendido!' : 'Siguiente' }}
                  <span class="group-hover/btn:translate-x-1 transition-transform">➜</span>
                </button>
              </div>
            </div>
          </div>
          
          <!-- Arrow pointing to highlight -->
          <div 
            class="absolute w-4 h-4 bg-slate-900 border-l border-t border-emerald-500/30 transform rotate-45"
            :class="arrowClass"
            :style="arrowStyle"
          ></div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue';

const props = defineProps({
  show: Boolean
});

const emit = defineEmits(['close', 'complete']);

const isVisible = ref(false);
const currentStep = ref(0);
const highlightStyle = ref({});
const tooltipStyle = ref({});
const arrowStyle = ref({});
const arrowClass = ref('');

const steps = [
  {
    target: 'header div.hidden.sm\\:block div.flex.items-center.gap-1\\.5', // Apuntar directamente al indicador de nivel
    title: 'Tu Progreso',
    content: 'Aquí puedes ver tu misión actual. ¡Completa objetivos para ganar monedas!',
    icon: '🌱',
    position: 'bottom'
  },
  {
    target: '.flex-1 .gap-3', // Recursos
    title: 'Recursos de la Granja',
    content: 'Vigila tu dinero, madera y agua. Los necesitarás para comprar mejoras en el árbol de tecnologías.',
    icon: '💰',
    position: 'bottom'
  },
  {
    target: 'button[title="Árbol de Tecnologías"]',
    title: 'Árbol de Tecnologías',
    content: 'Aquí es donde gastas tus recursos para mejorar tu robot, desbloquear sensores y nuevas funciones de Python.',
    icon: '🧬',
    position: 'bottom'
  },
  {
    target: 'section.flex-none.w-full', // Editor y Sidebar
    title: 'Terminal de Control',
    content: 'Este es el cerebro. Aquí escribirás el código Python para automatizar tu robot.',
    icon: '⌨️',
    position: 'left'
  },
  {
    target: 'button[title="Mostrar Explorador"]',
    title: 'Explorador y Misiones',
    content: 'Pulsa aquí para ver tus archivos o, lo más importante, ¡la pestaña de misiones y logros!',
    icon: '📁',
    position: 'right'
  },
  {
    target: 'button.group.flex.items-center.gap-3', // Botón Ejecutar
    title: '¡A Funcionar!',
    content: 'Cuando tengas tu código listo, dale a Ejecutar para que el robot empiece la simulación.',
    icon: '🚀',
    position: 'top'
  }
];

const currentStepData = computed(() => steps[currentStep.value]);

const updatePosition = () => {
  if (!isVisible.value) return;
  
  const step = currentStepData.value;
  const targetEl = document.querySelector(step.target);
  
  if (!targetEl) {
    // Si no encuentra el target (ej. está colapsado), intenta con un fallback o salta
    console.warn(`Tutorial target not found: ${step.target}`);
    return;
  }

  const rect = targetEl.getBoundingClientRect();
  const padding = 8;

  // Highlight Style
  highlightStyle.value = {
    top: `${rect.top - padding}px`,
    left: `${rect.left - padding}px`,
    width: `${rect.width + padding * 2}px`,
    height: `${rect.height + padding * 2}px`
  };

  // Tooltip Style & Arrow
  const tooltipPadding = 24;
  let top = 0;
  let left = 0;
  let aTop = '';
  let aLeft = '';
  let aBottom = '';
  let aRight = '';

  if (step.position === 'bottom') {
    top = rect.bottom + tooltipPadding;
    left = rect.left + rect.width / 2 - 180;
    aTop = '-8px';
    aLeft = '180px';
    arrowClass.value = 'border-l border-t';
  } else if (step.position === 'top') {
    top = rect.top - 200 - tooltipPadding;
    left = rect.left + rect.width / 2 - 180;
    aBottom = '-8px';
    aLeft = '180px';
    arrowClass.value = 'border-r border-b';
  } else if (step.position === 'left') {
    top = rect.top + rect.height / 2 - 100;
    left = rect.left - 350 - tooltipPadding;
    aRight = '-8px';
    aTop = '50%';
    arrowClass.value = 'border-r border-t';
  } else if (step.position === 'right') {
    top = rect.top + rect.height / 2 - 100;
    left = rect.right + tooltipPadding;
    aLeft = '-8px';
    aTop = '50%';
    arrowClass.value = 'border-l border-b';
  }

  // Prevent off-screen
  left = Math.max(20, Math.min(left, window.innerWidth - 380));
  top = Math.max(20, Math.min(top, window.innerHeight - 250));

  tooltipStyle.value = {
    top: `${top}px`,
    left: `${left}px`
  };

  arrowStyle.value = {
    top: aTop,
    left: aLeft,
    bottom: aBottom,
    right: aRight
  };
};

const nextStep = () => {
  if (currentStep.value < steps.length - 1) {
    currentStep.value++;
    updatePosition();
  } else {
    completeTutorial();
  }
};

const completeTutorial = () => {
  isVisible.value = false;
  localStorage.setItem('onboarding_completed', 'true');
  emit('complete');
  emit('close');
};

watch(() => props.show, (newVal) => {
  if (newVal) {
    isVisible.value = true;
    currentStep.value = 0;
    setTimeout(updatePosition, 100);
  }
});

onMounted(() => {
  window.addEventListener('resize', updatePosition);
  if (props.show) {
    isVisible.value = true;
    setTimeout(updatePosition, 100);
  }
});

onBeforeUnmount(() => {
  window.removeEventListener('resize', updatePosition);
});
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.slide-up-enter-active, .slide-up-leave-active {
  transition: all 0.4s ease-out;
}
.slide-up-enter-from {
  transform: translateY(20px);
  opacity: 0;
}

@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-5px); }
}
</style>
