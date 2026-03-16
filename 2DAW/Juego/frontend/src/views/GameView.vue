<template>
  <div class="h-screen w-screen bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 overflow-hidden flex flex-col font-sans selection:bg-emerald-500/30 selection:text-emerald-700 dark:selection:text-emerald-200 transition-colors duration-300">
    <!-- Grid Background Pattern -->
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px] pointer-events-none"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-white/50 via-slate-100/50 to-white/50 dark:from-slate-950 dark:via-slate-900/50 dark:to-slate-950 pointer-events-none"></div>

    <!-- TOP HEADER -->
    <header class="flex-none h-16 px-4 lg:px-6 flex items-center justify-between z-40 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-white/5 shadow-sm transition-colors duration-300 relative">
        <!-- Left: Logo & Level -->
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/20 text-white shrink-0">
                <span class="text-2xl">🌱</span>
            </div>
            <div class="hidden sm:block">
                <h1 class="text-sm font-bold text-slate-900 dark:text-white leading-tight">Farmer Auto</h1>
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-pulse"></span>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 font-mono uppercase tracking-wider">{{ tutorialStore.activeMission.title }}</span>
                </div>
            </div>
        </div>

        <!-- Center: Resources -->
        <div class="flex-1 flex justify-center px-4 overflow-x-auto no-scrollbar">
            <div class="flex items-center gap-3 sm:gap-6">
                <!-- Money -->
                <div class="flex items-center gap-2 px-3 py-1.5 bg-amber-500/10 border border-amber-500/20 rounded-xl group transition-all hover:bg-amber-500/20" title="Dinero">
                    <span class="text-lg">💰</span>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1">
                            <span class="text-xs font-bold text-amber-500 leading-none">{{ formatNumber(resources.money) }}</span>
                            <span v-if="getPassiveIncome() > 0" class="text-[8px] font-bold text-emerald-500 animate-pulse">+{{ formatNumber(getPassiveIncome()) }}/s</span>
                        </div>
                        <span class="text-[8px] text-amber-500/60 uppercase tracking-tighter">Monedas</span>
                    </div>
                </div>
                
                <!-- Wood -->
                <div class="flex items-center gap-2 px-3 py-1.5 bg-orange-500/10 border border-orange-500/20 rounded-xl group transition-all hover:bg-orange-500/20" title="Madera">
                    <span class="text-lg">🪵</span>
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-orange-500 leading-none">{{ formatNumber(resources.wood) }}</span>
                        <span class="text-[8px] text-orange-500/60 uppercase tracking-tighter">Madera</span>
                    </div>
                </div>

                <!-- Water -->
                <div class="flex items-center gap-2 px-3 py-1.5 bg-blue-500/10 border border-blue-500/20 rounded-xl group transition-all hover:bg-blue-500/20" title="Agua">
                    <span class="text-lg">💧</span>
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-blue-500 leading-none">{{ formatNumber(resources.water) }}</span>
                        <span class="text-[8px] text-blue-500/60 uppercase tracking-tighter">Agua</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Controls -->
        <div class="flex items-center gap-2">
             <!-- Ranking -->
             <button @click="showRanking = true; play('click')" 
                     class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/10 transition-colors text-amber-500 dark:text-amber-400 hover:text-amber-600 dark:hover:text-amber-300 relative group"
                     title="Ranking Global"
                     @mouseenter="play('hover')">
               <span class="text-xl">🏆</span>
               <span class="hidden group-hover:block absolute top-full right-0 mt-1 text-xs bg-slate-800 text-white px-2 py-1 rounded shadow-lg whitespace-nowrap z-50">Ranking</span>
             </button>

             <!-- Tutorial Replay -->
             <button @click="showOnboarding = true; play('click')" 
                     class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/10 transition-colors text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white relative group"
                     title="Ayuda / Tutorial"
                     @mouseenter="play('hover')">
               <span class="text-xl">❓</span>
               <span class="hidden group-hover:block absolute top-full right-0 mt-1 text-xs bg-slate-800 text-white px-2 py-1 rounded shadow-lg whitespace-nowrap z-50">Guía</span>
             </button>

             <!-- Tech Tree -->
             <button @click="showTechTree = true; play('click')" 
                     class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/10 transition-colors text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300 relative group"
                     title="Árbol de Tecnologías"
                     @mouseenter="play('hover')">
               <span class="text-xl">🧬</span>
               <span class="hidden group-hover:block absolute top-full right-0 mt-1 text-xs bg-slate-800 text-white px-2 py-1 rounded shadow-lg whitespace-nowrap z-50">Tecnologías</span>
             </button>

             <div class="w-px h-6 bg-slate-200 dark:bg-white/10 mx-1"></div>

             <!-- User Profile / Auth -->
             <button @click="user ? logout() : showAuth = true; play('click')" 
                     class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/10 transition-colors relative group"
                     :class="user ? 'text-emerald-500 hover:text-emerald-600' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'"
                     :title="user ? `Sesión iniciada como ${user.name}` : 'Iniciar Sesión'"
                     @mouseenter="play('hover')">
               <span v-if="user" class="text-xl">👤</span>
               <span v-else class="text-xl">🔑</span>
               <span class="hidden group-hover:block absolute top-full right-0 mt-1 text-xs bg-slate-800 text-white px-2 py-1 rounded shadow-lg whitespace-nowrap z-50">
                 {{ user ? 'Cerrar Sesión' : 'Iniciar Sesión' }}
               </span>
             </button>

             <div class="w-px h-6 bg-slate-200 dark:bg-white/10 mx-1"></div>
 
             <!-- Sound Toggle -->
             <button @click="toggleMute" 
                     class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/10 transition-colors text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
                     :title="isMuted ? 'Activar Sonido' : 'Silenciar'">
               <svg v-if="!isMuted" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
               </svg>
               <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
               </svg>
             </button>
        </div>
    </header>

    <!-- MAIN CONTENT (Centered Command Center) -->
    <main class="flex-1 w-full max-w-[1600px] mx-auto p-4 lg:p-6 flex flex-col lg:flex-row gap-6 overflow-hidden relative z-10">
      
      <!-- LEFT PANEL: Game View -->
      <section class="flex-1 flex flex-col min-h-[40vh] bg-slate-100 dark:bg-slate-900/50 rounded-3xl overflow-hidden border border-slate-200 dark:border-white/10 shadow-2xl relative group transition-all duration-300 hover:shadow-emerald-500/10 hover:border-emerald-500/20">
        
        <!-- Panel Header with Mission Info -->
        <div class="h-14 bg-slate-900 border-b border-white/5 flex items-center justify-between px-4 shrink-0 relative z-20">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[10px] font-mono font-bold text-emerald-400 tracking-widest uppercase">Vista del Dron</span>
                </div>
                
                <!-- Mission Summary (Compact) -->
                <div class="hidden md:flex items-center gap-2 text-xs text-slate-400 border-l border-white/10 pl-3 overflow-hidden">
                    <span class="text-blue-400 font-bold uppercase tracking-wider whitespace-nowrap">Misión:</span>
                    <span class="whitespace-nowrap overflow-x-auto no-scrollbar max-w-[400px]">{{ tutorialStore.activeMission.description }}</span>
                </div>
            </div>

            <!-- Hint Button (Toggle) -->
            <button 
                @click="showHint = !showHint"
                class="p-1.5 rounded-lg hover:bg-white/10 text-amber-500 transition-colors relative"
                title="Ver Pista"
            >
                <span class="text-lg">💡</span>
                <span v-if="!showHint" class="absolute top-0 right-0 w-2 h-2 bg-amber-500 rounded-full animate-ping"></span>
            </button>
        </div>

        <!-- Expanded Mission/Hint Panel -->
        <Transition name="slide-down">
            <div v-if="showHint" class="bg-amber-500/5 border-b border-amber-500/10 p-4 relative z-10">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-sm font-bold text-amber-400 mb-1">Consejo de Misión</h3>
                        <p class="text-xs text-slate-300">{{ currentHint }}</p>
                    </div>
                    <button @click="showHint = false" class="text-slate-500 hover:text-white">✕</button>
                </div>
            </div>
        </Transition>

        <!-- Canvas Container -->
        <div ref="canvasContainer" 
             class="flex-1 relative w-full h-full bg-slate-100 dark:bg-slate-950 transition-colors duration-300 flex items-center justify-center overflow-hidden"
             :class="{'shadow-[inset_0_0_50px_rgba(16,185,129,0.1)]': controller.isRunning.value}"
        >
            <!-- Background Grid Pattern -->
            <div class="absolute inset-0 opacity-20 pointer-events-none" 
                 style="background-image: radial-gradient(#475569 1px, transparent 1px); background-size: 24px 24px;">
            </div>

            <!-- Loading State -->
            <div v-if="!canvasContainer" class="absolute inset-0 flex items-center justify-center bg-slate-100 dark:bg-slate-900 text-slate-900 dark:text-white z-10">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-primary"></div>
            </div>

            <!-- Event Notification -->
            <Transition name="slide-fade">
                <div v-if="eventStore.activeEvent" 
                     class="absolute top-16 right-4 px-6 py-4 rounded-xl shadow-2xl z-30 backdrop-blur-xl bg-white/90 dark:bg-slate-900/90 border border-slate-200 dark:border-white/10 w-96 max-w-[90vw]"
                     :class="getEventClass(eventStore.activeEvent)"
                >
                    <div class="flex items-start gap-4">
                        <span class="text-2xl animate-bounce mt-1">⚠️</span>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-sm uppercase tracking-wider text-white mb-1">{{ eventStore.activeEvent.name }}</h4>
                            <p class="text-xs font-medium text-white/90 leading-relaxed">{{ eventStore.activeEvent.description }}</p>
                        </div>
                    </div>
                    <!-- Progress Bar -->
                    <div class="w-full bg-black/20 h-1.5 mt-3 rounded-full overflow-hidden backdrop-blur-sm">
                         <div class="h-full bg-white/80 transition-all duration-1000 ease-linear" :style="{ width: eventProgress + '%' }"></div>
                    </div>
                </div>
            </Transition>
        </div>
      </section>

      <!-- RIGHT PANEL: Code Editor -->
      <section class="flex-none w-full lg:w-[500px] xl:w-[600px] flex flex-col h-[50vh] lg:h-auto bg-slate-900 rounded-3xl overflow-hidden border border-slate-700 shadow-2xl relative">
        <!-- Mac-style Terminal Header -->
        <div class="h-9 bg-slate-950 flex items-center px-4 border-b border-white/5 shrink-0 select-none">
             <div class="flex gap-1.5 group">
                 <div class="w-3 h-3 rounded-full bg-red-500/20 group-hover:bg-red-500 transition-colors border border-red-500/30"></div>
                 <div class="w-3 h-3 rounded-full bg-yellow-500/20 group-hover:bg-yellow-500 transition-colors border border-yellow-500/30"></div>
                 <div class="w-3 h-3 rounded-full bg-green-500/20 group-hover:bg-green-500 transition-colors border border-green-500/30"></div>
             </div>
             <div class="flex-1 text-center flex justify-center items-center gap-2 opacity-60">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                 </svg>
                 <span class="text-[10px] font-mono font-bold text-slate-300 tracking-widest uppercase">Terminal de Comandos</span>
             </div>
             <div class="w-10"></div> <!-- Spacer -->
        </div>

        <Sidebar 
            class="flex-1"
            :isRunning="controller.isRunning.value"  
            :isPaused="controller.isPaused.value"
            :logs="controller.logs.value"
            :errorMarker="controller.errorMarker.value"
            :executionLine="controller.executionLine.value"
            :breakpoints="controller.breakpoints.value"
            @run="controller.runCode"
            @reset="controller.reset"
            @pause="controller.pauseGame"
            @resume="controller.resumeGame"
            @step="controller.stepGame"
            @clear-logs="controller.logs.value = []"
            @update:breakpoints="controller.breakpoints.value = $event"
            @analyze="controller.handleAnalyze"
            @log-message="controller.addLog($event.message, $event.type)"
        />
      </section>

    </main>

    <!-- Modals -->
    <TechTree 
        v-if="showTechTree"
        :resources="resources"
        @close="showTechTree = false"
        @purchase="controller.handlePurchase"
    />

    <Ranking 
        v-if="showRanking"
        @close="showRanking = false"
    />

    <!-- Auth Modal -->
    <AuthModal 
        v-if="showAuth"
        @close="showAuth = false"
        @success="fetchUser"
    />

    <!-- Onboarding Tutorial -->
    <Onboarding 
        v-if="showOnboarding"
        :show="showOnboarding"
        @complete="showOnboarding = false"
        @close="showOnboarding = false"
    />

    <!-- Level Complete Modal -->
    <Transition name="bounce">
        <div v-if="controller.showLevelComplete.value" class="absolute inset-0 bg-slate-900/80 backdrop-blur-md flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 p-8 rounded-3xl shadow-2xl text-center max-w-md w-full relative overflow-hidden group">
                <!-- Background glow -->
                <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-emerald-500/5 opacity-50 pointer-events-none"></div>
                
                <div class="relative z-10">
                    <div class="w-24 h-24 bg-gradient-to-br from-green-400 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-green-500/30 rotate-3 group-hover:rotate-6 transition-transform duration-500 text-white">
                        <span class="text-5xl drop-shadow-md">🏆</span>
                    </div>
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-2 tracking-tight">¡Nivel Completado!</h2>
                    <p class="text-slate-600 dark:text-slate-400 mb-8 text-lg font-medium">Tu algoritmo ha funcionado perfectamente.</p>
                    
                    <div class="flex flex-col gap-3">
                        <button 
                            @click="controller.nextLevel"
                            class="w-full bg-gradient-to-r from-green-600 to-emerald-500 hover:from-green-500 hover:to-emerald-400 text-white font-bold py-3.5 px-6 rounded-xl transition-all hover:scale-105 shadow-lg shadow-green-500/25 border border-green-400/20"
                        >
                            Siguiente Nivel
                        </button>
                        <button 
                            @click="controller.showLevelComplete.value = false"
                            class="w-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-medium py-3.5 px-6 rounded-xl transition-colors border border-slate-200 dark:border-white/5"
                        >
                            Quedarse aquí
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>

    <Toast ref="toastRef" />

  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useGameController } from '../composables/useGameController';
import Sidebar from '../components/Sidebar.vue';
import TechTree from '../components/TechTree.vue';
import Ranking from '../components/Ranking.vue';
import AuthModal from '../components/AuthModal.vue';
import Toast from '../components/Toast.vue';
import Onboarding from '../components/Onboarding.vue';

import { useTutorialStore } from '../stores/tutorial';
import { useTechStore } from '../stores/techTree';
import { formatNumber } from '../utils/formatters';
import { useRandomEventStore } from '../stores/randomEvents';
import { useSound } from '../composables/useSound';
import { useTheme } from '../composables/useTheme';
import { AuthService } from '../services/AuthService';

const router = useRouter();
const canvasContainer = ref(null);
const toastRef = ref(null);
const showTechTree = ref(false);
const showRanking = ref(false);
const showAuth = ref(false);
const showHint = ref(false);
const showOnboarding = ref(false);

const user = ref(null);

const tutorialStore = useTutorialStore();
const eventStore = useRandomEventStore();
const { play, isMuted, toggleMute } = useSound();
const { isDark } = useTheme();

const controller = useGameController(canvasContainer, toastRef);

// Auth logic
const fetchUser = async () => {
  try {
    user.value = await AuthService.getUser();
    if (user.value) {
      if (toastRef.value) toastRef.value.show('Sesión iniciada', `Bienvenido de nuevo, ${user.value.name}`, 'success');
      // Al iniciar sesión, cargamos el progreso guardado
      await controller.loadUserProgress();
    }
  } catch (e) {
    user.value = null;
  }
};

const logout = async () => {
  try {
    await AuthService.logout();
    localStorage.removeItem('token');
    user.value = null;
    if (toastRef.value) toastRef.value.show('Sesión cerrada', 'Has cerrado sesión correctamente', 'info');
    router.push('/');
  } catch (e) {
    console.error('Logout error', e);
  }
};

onMounted(() => {
  fetchUser();
  
  // Check if first time
  const completed = localStorage.getItem('onboarding_completed');
  if (!completed) {
    showOnboarding.value = true;
  }
});

// Computed for current Mission Index
const currentMission = computed(() => tutorialStore.currentMissionIndex + 1);

// Feedback Watchers
watch(controller.errorMarker, (newVal) => {
    if (newVal && toastRef.value) {
        toastRef.value.show('Error de Código', newVal.message, 'error');
    }
});

watch(controller.showLevelComplete, (newVal) => {
    if (newVal && toastRef.value) {
        toastRef.value.show('¡Nivel Completado!', 'Algoritmo ejecutado con éxito.', 'success');
        play('success');
    }
});

const resources = controller.resources;

// Computed for UI
const eventProgress = computed(() => {
    if (!eventStore.activeEvent) return 0;
    const elapsed = controller.currentTime.value - eventStore.activeEvent.startTime;
    return Math.max(0, 100 - (elapsed / eventStore.activeEvent.duration) * 100);
});

const currentHint = computed(() => {
    return tutorialStore.activeMission.hint || "Revisa tu lógica paso a paso.";
});

const techStore = useTechStore();

const getPassiveIncome = () => {
  let income = 0;
  Object.values(techStore.technologies).forEach(tech => {
    if (tech.unlocked && tech.modifiers?.passiveIncome) {
      income += tech.modifiers.passiveIncome;
    }
  });
  return income;
};

const getEventClass = (event) => {
    // Map event types to Tailwind classes
    const classes = {
        'drought': 'bg-orange-600/90 text-white',
        'rain': 'bg-blue-600/90 text-white',
        'pest': 'bg-red-600/90 text-white',
        'market_boom': 'bg-green-600/90 text-white'
    };
    // Fallback based on stored colors if available, or default
    return classes[event.id] || event.bg || 'bg-slate-700';
};
</script>

<style scoped>
/* Transitions */
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.3s ease-out;
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateX(-50%) translateY(-20px);
  opacity: 0;
}

.bounce-enter-active {
  animation: bounce-in 0.5s;
}
.bounce-leave-active {
  animation: bounce-in 0.5s reverse;
}
@keyframes bounce-in {
  0% { transform: scale(0); }
  50% { transform: scale(1.05); }
  100% { transform: scale(1); }
}

/* Hide Scrollbar for Header Resources */
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>