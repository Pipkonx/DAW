<template>
    <div class="fixed inset-0 bg-slate-50/90 dark:bg-slate-950/80 z-50 flex items-center justify-center p-4 sm:p-8 backdrop-blur-md transition-all duration-300">
        <div class="relative w-full max-w-6xl h-[85vh] bg-white/50 dark:bg-slate-900/50 glass-strong rounded-2xl overflow-hidden shadow-2xl shadow-slate-200/50 dark:shadow-black/50 flex flex-col border border-slate-200 dark:border-white/10">
            <!-- Header -->
            <div class="p-6 border-b border-slate-200 dark:border-white/10 flex justify-between items-center z-10 shrink-0 bg-white/80 dark:bg-slate-900/50 backdrop-blur-md transition-colors duration-300">
                <div class="flex items-center gap-4">
                    <div class="p-2 bg-emerald-100 dark:bg-primary/20 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600 dark:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Árbol de Tecnología</h2>
                        <p class="text-slate-500 dark:text-slate-400 text-sm">Desbloquea mejoras para optimizar tu granja</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-6">
                    <div class="flex flex-col items-end">
                        <span class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider font-bold">Fondos Disponibles</span>
                        <span class="text-emerald-600 dark:text-accent font-mono text-xl font-bold text-shadow-sm">${{ resources.money }}</span>
                    </div>
                    <button @click="$emit('close'); play('click')" @mouseenter="play('hover')" class="p-2 hover:bg-slate-200 dark:hover:bg-white/10 rounded-full text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all transform hover:rotate-90">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Canvas Area -->
            <div 
                ref="canvasContainer"
                class="relative flex-1 bg-slate-50 dark:bg-slate-950 overflow-hidden group cursor-grab active:cursor-grabbing transition-colors duration-300"
                @mousedown="startDrag"
                @wheel.prevent="handleWheel"
            >
                <!-- Transform Container -->
                <div class="absolute inset-0 origin-top-left will-change-transform" :style="transformStyle">
                    
                    <!-- Infinite Grid Pattern -->
                    <div class="absolute -inset-[5000px] bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:40px_40px] pointer-events-none opacity-20"></div>
                    <div class="absolute -inset-[5000px] bg-gradient-to-br from-white/50 via-slate-100/50 to-white/50 dark:from-slate-900/50 dark:via-slate-900/50 dark:to-slate-800/50 pointer-events-none transition-colors duration-300"></div>

                    <!-- Content Area (Large enough to hold nodes) -->
                    <div class="relative w-[3000px] h-[3000px]">
                        <!-- Connections (SVG) -->
                        <svg class="absolute top-0 left-0 w-full h-full pointer-events-none z-0 overflow-visible">
                            <defs>
                                <linearGradient id="line-gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" stop-color="#334155" />
                                    <stop offset="50%" stop-color="#475569" />
                                    <stop offset="100%" stop-color="#334155" />
                                </linearGradient>
                                <filter id="glow-line">
                                    <feGaussianBlur stdDeviation="2" result="coloredBlur"/>
                                    <feMerge>
                                        <feMergeNode in="coloredBlur"/>
                                        <feMergeNode in="SourceGraphic"/>
                                    </feMerge>
                                </filter>
                            </defs>
                            <!-- Base Lines -->
                            <path 
                                v-for="link in links" 
                                :key="link.id"
                                :d="getCurvedPath(link.x1, link.y1, link.x2, link.y2)"
                                :stroke="isDark ? '#334155' : '#cbd5e1'" 
                                stroke-width="4"
                                fill="none"
                                stroke-linecap="round"
                                class="transition-colors duration-300"
                            />
                            <!-- Animated Flow Lines for Unlocked/Available -->
                            <path 
                                v-for="link in activeLinks" 
                                :key="'active-' + link.id"
                                :d="getCurvedPath(link.x1, link.y1, link.x2, link.y2)"
                                :stroke="link.color" 
                                stroke-width="2"
                                fill="none"
                                stroke-linecap="round"
                                stroke-dasharray="10,10"
                                class="animate-dash"
                            />
                        </svg>
                        
                        <!-- Nodes -->
                        <div 
                            v-for="tech in techStore.technologies" 
                            :key="tech.id" 
                            class="absolute w-80 h-36 p-0 rounded-xl transition-all duration-300 z-10 cursor-pointer group/node"
                            :style="{ left: tech.position.x + 'px', top: tech.position.y + 'px' }"
                            :class="getTechWrapperClass(tech)"
                            @mousedown.stop
                            @click="selectTech(tech); play('click')"
                            @mouseenter="play('hover')"
                        >
                            <div class="relative w-full h-full p-4 flex items-center gap-4 overflow-hidden rounded-xl border transition-all duration-300 backdrop-blur-md"
                                :class="getTechInnerClass(tech)"
                            >
                                <!-- Glow Effect -->
                                <div v-if="tech.unlocked" class="absolute inset-0 bg-emerald-500/10 dark:bg-primary/10 blur-xl opacity-50 group-hover/node:opacity-100 transition-opacity"></div>
                                <div v-else-if="canUnlock(tech)" class="absolute inset-0 bg-amber-500/10 dark:bg-accent/10 blur-xl opacity-0 group-hover/node:opacity-100 transition-opacity"></div>

                                <!-- Icon -->
                                <div class="relative z-10 w-16 h-16 flex items-center justify-center rounded-lg text-4xl shadow-inner transition-transform group-hover/node:scale-110"
                                    :class="getIconClass(tech)"
                                >
                                    {{ tech.icon }}
                                </div>
                                
                                <!-- Info -->
                                <div class="relative z-10 flex-1 min-w-0">
                                    <div class="font-bold text-lg leading-tight truncate text-slate-800 dark:text-white mb-1">{{ tech.name }}</div>
                                    <div v-if="tech.unlocked" class="text-xs font-bold text-emerald-600 dark:text-primary uppercase tracking-wider flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 dark:bg-primary animate-pulse"></span>
                                        Desbloqueado
                                    </div>
                                    <div v-else class="text-sm font-mono text-slate-500 dark:text-slate-400 group-hover/node:text-amber-600 dark:group-hover/node:text-accent transition-colors">
                                        ${{ tech.cost }}
                                    </div>
                                </div>

                                <!-- Status Indicator (Corner) -->
                                <div class="absolute top-2 right-2">
                                    <div v-if="tech.unlocked" class="text-emerald-500 dark:text-primary opacity-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div v-else-if="!canUnlock(tech)" class="text-slate-400 dark:text-slate-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Zoom Controls -->
                <div class="absolute bottom-6 right-6 flex flex-col gap-2 z-20">
                    <button @click.stop="zoomIn" class="p-3 bg-white dark:bg-slate-800 text-slate-700 dark:text-white rounded-lg shadow-lg hover:bg-slate-50 dark:hover:bg-slate-700 hover:scale-105 transition-all active:scale-95 border border-slate-200 dark:border-white/10" title="Zoom In">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                    <button @click.stop="resetView" class="p-3 bg-white dark:bg-slate-800 text-slate-700 dark:text-white rounded-lg shadow-lg hover:bg-slate-50 dark:hover:bg-slate-700 hover:scale-105 transition-all active:scale-95 border border-slate-200 dark:border-white/10" title="Reset View">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                        </svg>
                    </button>
                    <button @click.stop="zoomOut" class="p-3 bg-white dark:bg-slate-800 text-slate-700 dark:text-white rounded-lg shadow-lg hover:bg-slate-50 dark:hover:bg-slate-700 hover:scale-105 transition-all active:scale-95 border border-slate-200 dark:border-white/10" title="Zoom Out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Detail Panel (Overlay) -->
            <transition name="slide-right">
                <div v-if="selectedTech" class="absolute right-0 top-[88px] bottom-0 w-96 bg-white/95 dark:bg-slate-900/95 border-l border-slate-200 dark:border-white/10 p-8 shadow-2xl z-20 backdrop-blur-xl flex flex-col transition-colors duration-300">
                    <button @click="selectedTech = null" class="absolute top-4 right-4 p-1 hover:bg-slate-200 dark:hover:bg-white/10 rounded-full text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    
                    <div class="flex-1 overflow-y-auto custom-scroll pr-2">
                        <div class="flex justify-center mb-8">
                            <div class="w-24 h-24 rounded-2xl flex items-center justify-center text-5xl shadow-2xl relative overflow-hidden"
                                :class="selectedTech.unlocked ? 'bg-emerald-100 dark:bg-primary/20 text-emerald-600 dark:text-primary ring-2 ring-emerald-500/50 dark:ring-primary/50' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 ring-1 ring-slate-300 dark:ring-white/10'"
                            >
                                <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent pointer-events-none"></div>
                                {{ selectedTech.icon }}
                            </div>
                        </div>
                        
                        <h3 class="text-3xl font-bold text-slate-800 dark:text-white mb-2 text-center">{{ selectedTech.name }}</h3>
                        <div class="flex justify-center mb-8">
                            <span class="px-3 py-1 rounded-full text-sm font-bold border"
                                :class="selectedTech.unlocked ? 'bg-emerald-100 dark:bg-primary/10 border-emerald-300 dark:border-primary/30 text-emerald-600 dark:text-primary' : 'bg-slate-100 dark:bg-slate-800 border-slate-300 dark:border-slate-700 text-slate-500 dark:text-slate-400'"
                            >
                                {{ selectedTech.unlocked ? 'TECNOLOGÍA ACTIVA' : 'BLOQUEADO' }}
                            </span>
                        </div>
                        
                        <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-4 mb-6 border border-slate-200 dark:border-white/5">
                            <h4 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Descripción</h4>
                            <p class="text-slate-700 dark:text-slate-300 leading-relaxed">
                                {{ selectedTech.description }}
                            </p>
                        </div>
                        
                        <div v-if="!selectedTech.unlocked" class="bg-slate-100 dark:bg-slate-950/50 rounded-xl p-4 mb-6 border border-slate-200 dark:border-white/5">
                            <h4 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Requisitos</h4>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-slate-600 dark:text-slate-400">Costo</span>
                                <span class="font-mono font-bold" :class="canPurchase ? 'text-amber-600 dark:text-accent' : 'text-red-500 dark:text-red-400'">${{ selectedTech.cost }}</span>
                            </div>
                            <div v-if="selectedTech.dependencies.length > 0" class="mt-2 pt-2 border-t border-slate-200 dark:border-white/5">
                                <span class="text-slate-500 dark:text-slate-400 text-sm">Dependencias:</span>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span v-for="depId in selectedTech.dependencies" :key="depId" 
                                        class="text-xs px-2 py-1 rounded bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700"
                                        :class="techStore.technologies[depId].unlocked ? 'border-emerald-300 dark:border-primary/30 text-emerald-600 dark:text-primary' : 'opacity-50'"
                                    >
                                        {{ techStore.technologies[depId].name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-slate-200 dark:border-white/10">
                        <button 
                            @click="handleAction(); play('click')"
                            @mouseenter="!isActionDisabled && play('hover')"
                            :disabled="isActionDisabled"
                            class="w-full py-4 rounded-xl font-bold text-lg transition-all transform hover:scale-[1.02] active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none shadow-lg flex items-center justify-center gap-2 relative overflow-hidden group"
                            :class="purchaseBtnClass"
                        >
                            <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 pointer-events-none"></div>
                            <span v-if="!selectedTech.unlocked && canPurchase">🔒</span>
                            {{ purchaseBtnText }}
                        </button>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useTechStore } from '../stores/techTree';
import { useSound } from '../composables/useSound';
import { useTheme } from '../composables/useTheme';

const props = defineProps({
    resources: Object
});

const emit = defineEmits(['close', 'purchase']);
const techStore = useTechStore();
const selectedTech = ref(null);
const { play } = useSound();
const { isDark } = useTheme();

// Canvas & Zoom/Pan State
const canvasContainer = ref(null);
const scale = ref(0.8); // Start slightly zoomed out to see more
const translateX = ref(100);
const translateY = ref(100);
const isDragging = ref(false);
const startX = ref(0);
const startY = ref(0);

const transformStyle = computed(() => {
    return {
        transform: `translate(${translateX.value}px, ${translateY.value}px) scale(${scale.value})`
    };
});

const handleWheel = (e) => {
    const zoomFactor = 0.1;
    const delta = e.deltaY > 0 ? -zoomFactor : zoomFactor;
    const newScale = Math.min(Math.max(0.3, scale.value + delta), 3);

    if (newScale !== scale.value) {
        const rect = canvasContainer.value.getBoundingClientRect();
        const mouseX = e.clientX - rect.left;
        const mouseY = e.clientY - rect.top;

        // Calculate world position before zoom
        const worldX = (mouseX - translateX.value) / scale.value;
        const worldY = (mouseY - translateY.value) / scale.value;

        scale.value = newScale;

        // Adjust translate to keep world position under mouse stable
        translateX.value = mouseX - worldX * newScale;
        translateY.value = mouseY - worldY * newScale;
    }
};

const startDrag = (e) => {
    isDragging.value = true;
    startX.value = e.clientX - translateX.value;
    startY.value = e.clientY - translateY.value;
};

const onDrag = (e) => {
    if (isDragging.value) {
        translateX.value = e.clientX - startX.value;
        translateY.value = e.clientY - startY.value;
    }
};

const stopDrag = () => {
    isDragging.value = false;
};

const zoomIn = () => {
    scale.value = Math.min(3, scale.value + 0.2);
};

const zoomOut = () => {
    scale.value = Math.max(0.3, scale.value - 0.2);
};

const resetView = () => {
    scale.value = 0.8;
    translateX.value = 100;
    translateY.value = 100;
};

onMounted(() => {
    window.addEventListener('mousemove', onDrag);
    window.addEventListener('mouseup', stopDrag);
});

onBeforeUnmount(() => {
    window.removeEventListener('mousemove', onDrag);
    window.removeEventListener('mouseup', stopDrag);
});

const links = computed(() => {
    const lines = [];
    Object.values(techStore.technologies).forEach(tech => {
        tech.dependencies.forEach(depId => {
            const dep = techStore.technologies[depId];
            if (dep) {
                // Connect center-right of dependency to center-left of current tech
                // Assumes Left-to-Right flow. Adjust logic if needed.
                // Current Node Size: w-80 (320px), h-36 (144px)
                const nodeWidth = 320;
                const nodeHeight = 144;
                
                lines.push({
                    id: `${dep.id}-${tech.id}`,
                    sourceId: dep.id,
                    targetId: tech.id,
                    x1: dep.position.x + nodeWidth, 
                    y1: dep.position.y + nodeHeight / 2, 
                    x2: tech.position.x,
                    y2: tech.position.y + nodeHeight / 2
                });
            }
        });
    });
    return lines;
});

const activeLinks = computed(() => {
    return links.value.map(link => {
        const sourceTech = techStore.technologies[link.sourceId];
        const targetTech = techStore.technologies[link.targetId];
        
        if (sourceTech.unlocked && targetTech.unlocked) {
            return { ...link, color: '#34D399' }; // Primary
        } else if (sourceTech.unlocked && !targetTech.unlocked) {
             return { ...link, color: '#FBBF24' }; // Accent (Available path)
        }
        return null;
    }).filter(l => l !== null);
});

const getCurvedPath = (x1, y1, x2, y2) => {
    // SVG Path calculation: Cubic Bezier Curve
    const midX = (x1 + x2) / 2;
    // Control points: (midX, y1) and (midX, y2) for a smooth S-curve
    return `M ${x1} ${y1} C ${midX} ${y1}, ${midX} ${y2}, ${x2} ${y2}`;
};

const canUnlock = (tech) => {
    return techStore.canUnlock(tech.id, props.resources.money);
};

const selectTech = (tech) => {
    selectedTech.value = tech;
};

// Styling Helpers
const getTechWrapperClass = (tech) => {
    if (tech.unlocked) return 'hover:scale-105 z-20';
    if (canUnlock(tech)) return 'hover:scale-105 z-10 animate-float';
    return 'opacity-60 hover:opacity-80 grayscale';
};

const getTechInnerClass = (tech) => {
    if (tech.unlocked) return 'bg-slate-800/80 border-primary/50 shadow-[0_0_20px_rgba(52,211,153,0.15)] ring-1 ring-primary/20';
    if (canUnlock(tech)) return 'bg-slate-800/80 border-accent/50 shadow-[0_0_20px_rgba(251,191,36,0.15)] ring-1 ring-accent/20 cursor-pointer';
    return 'bg-slate-900/50 border-slate-700';
};

const getIconClass = (tech) => {
    if (tech.unlocked) return 'bg-primary/20 text-primary';
    if (canUnlock(tech)) return 'bg-accent/20 text-accent';
    return 'bg-slate-800 text-slate-600';
};

// Action Button Logic
const isActiveRobot = computed(() => {
    return selectedTech.value?.unlocksRobot === techStore.activeRobot;
});

const canPurchase = computed(() => {
    return techStore.canUnlock(selectedTech.value?.id, props.resources.money);
});

const isActionDisabled = computed(() => {
    if (selectedTech.value?.unlocked) {
        return isActiveRobot.value || !selectedTech.value?.unlocksRobot;
    }
    return !canPurchase.value;
});

const purchaseBtnText = computed(() => {
    if (selectedTech.value?.unlocked) {
        if (isActiveRobot.value) return 'EQUIPADO';
        if (selectedTech.value?.unlocksRobot) return 'EQUIPAR';
        return 'YA DESBLOQUEADO';
    }
    return `DESBLOQUEAR ($${selectedTech.value?.cost})`;
});

const purchaseBtnClass = computed(() => {
    if (isActiveRobot.value) return 'bg-primary-dark text-white cursor-default shadow-none border border-white/10';
    if (selectedTech.value?.unlocked) {
        if (selectedTech.value?.unlocksRobot) return 'bg-secondary hover:bg-secondary-light text-white shadow-secondary/20';
        return 'bg-slate-700 text-slate-400 cursor-default';
    }
    if (canPurchase.value) return 'bg-accent hover:bg-accent-light text-slate-900 shadow-neon-accent';
    return 'bg-slate-800 text-slate-500 border border-slate-700 cursor-not-allowed';
});

const handleAction = () => {
    if (selectedTech.value?.unlocked) {
        if (selectedTech.value?.unlocksRobot && !isActiveRobot.value) {
            techStore.setActiveRobot(selectedTech.value.unlocksRobot);
        }
        return;
    }
    emit('purchase', selectedTech.value);
};
</script>

<style scoped>
.text-shadow-sm {
    text-shadow: 0 1px 2px rgba(0,0,0,0.5);
}
.animate-dash {
    stroke-dasharray: 10;
    animation: dash 1s linear infinite;
}
@keyframes dash {
    to {
        stroke-dashoffset: -20;
    }
}
.slide-right-enter-active,
.slide-right-leave-active {
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.slide-right-enter-from,
.slide-right-leave-to {
  transform: translateX(100%);
  opacity: 0;
}
</style>
