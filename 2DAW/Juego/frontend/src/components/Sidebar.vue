<template>
  <div class="flex h-full bg-transparent overflow-hidden font-sans text-slate-700 dark:text-slate-300 relative z-20">
    
    <!-- File Explorer Pane (Collapsible) -->
    <div v-show="isExplorerOpen" class="w-80 flex-shrink-0 border-r border-slate-200 dark:border-white/10 flex flex-col bg-slate-50 dark:bg-slate-900 shadow-xl transition-all duration-300">
        <div class="h-12 px-3 flex justify-between items-center bg-slate-100 dark:bg-slate-800 border-b border-slate-200 dark:border-white/10">
            <div class="flex items-center gap-1 overflow-x-auto no-scrollbar py-1">
                <button 
                    @click="activeExplorerTab = 'docs'"
                    class="text-[10px] font-bold uppercase tracking-wider px-2 py-1.5 rounded transition-all flex items-center gap-1 shrink-0 whitespace-nowrap"
                    :class="activeExplorerTab === 'docs' ? 'text-indigo-500 bg-indigo-500/10 ring-1 ring-indigo-500/20' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                >
                    <span>📖</span> Docs
                </button>
                <button 
                    @click="activeExplorerTab = 'missions'"
                    class="text-[10px] font-bold uppercase tracking-wider px-2 py-1.5 rounded transition-all flex items-center gap-1 shrink-0 whitespace-nowrap"
                    :class="activeExplorerTab === 'missions' ? 'text-emerald-500 bg-emerald-500/10 ring-1 ring-emerald-500/20' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                >
                    <span>🎯</span> Misiones
                </button>
                <button 
                    @click="activeExplorerTab = 'achievements'"
                    class="text-[10px] font-bold uppercase tracking-wider px-2 py-1.5 rounded transition-all flex items-center gap-1 shrink-0 whitespace-nowrap"
                    :class="activeExplorerTab === 'achievements' ? 'text-amber-500 bg-amber-500/10 ring-1 ring-amber-500/20' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                >
                    <span>🏆</span> Logros
                </button>
                <button 
                    @click="activeExplorerTab = 'files'"
                    class="text-[10px] font-bold uppercase tracking-wider px-2 py-1.5 rounded transition-all flex items-center gap-1 shrink-0 whitespace-nowrap"
                    :class="activeExplorerTab === 'files' ? 'text-blue-500 bg-blue-500/10 ring-1 ring-blue-500/20' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                >
                    <span>📂</span> Archivos
                </button>
            </div>
            <button @click="isExplorerOpen = false" class="ml-2 text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M15.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 010 1.414zm-6 0a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 011.414 1.414L5.414 10l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
            
            <div v-if="activeExplorerTab === 'docs'" class="flex-1 overflow-y-auto p-2 space-y-4">
                <div v-for="category in docCategories" :key="category.name" class="space-y-2">
                    <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-1">{{ category.name }}</h3>
                    <div class="space-y-1">
                        <div v-for="fn in category.functions" :key="fn.name" 
                             class="p-2.5 rounded-xl border transition-all relative overflow-hidden group"
                             :class="isFunctionUnlocked(fn.name) ? 'bg-indigo-500/5 border-indigo-500/20' : 'bg-slate-900/50 border-white/5 opacity-50 grayscale'">
                            
                            <div class="flex items-center justify-between mb-1">
                                <code class="text-xs font-mono font-bold" :class="isFunctionUnlocked(fn.name) ? 'text-indigo-400' : 'text-slate-500'">
                                    {{ fn.name }}()
                                </code>
                                <span v-if="!isFunctionUnlocked(fn.name)" class="text-[10px]">🔒</span>
                            </div>
                            <p class="text-[10px] text-slate-400 leading-tight">
                                {{ fn.description }}
                            </p>
                            
                            <!-- Tooltip/Example on hover if unlocked -->
                            <div v-if="isFunctionUnlocked(fn.name)" class="mt-2 pt-2 border-t border-indigo-500/10">
                                <div class="text-[9px] text-indigo-300 font-mono bg-indigo-500/10 p-1.5 rounded">
                                    Ej: {{ fn.example }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else-if="activeExplorerTab === 'missions'" class="flex-1 overflow-y-auto p-2 space-y-1">
                <div 
                    v-for="(mission, index) in tutorialStore.missions" 
                    :key="mission.id"
                    class="group flex flex-col p-3 rounded-xl border transition-all relative overflow-hidden"
                    :class="[
                        tutorialStore.currentMissionIndex === index 
                            ? 'bg-emerald-500/10 border-emerald-500/30 ring-1 ring-emerald-500/20' 
                            : (tutorialStore.completedMissions.includes(mission.id) ? 'bg-white/5 border-white/5 opacity-60' : 'bg-white/5 border-white/5 opacity-40 grayscale')
                    ]"
                >
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-500">
                            {{ tutorialStore.completedMissions.includes(mission.id) ? 'Completada' : (tutorialStore.currentMissionIndex === index ? 'En curso' : 'Bloqueada') }}
                        </span>
                        <span v-if="tutorialStore.completedMissions.includes(mission.id)" class="text-xs">✅</span>
                    </div>
                    <span class="text-xs font-bold truncate text-white">
                        {{ mission.title }}
                    </span>
                    <p class="text-[9px] text-slate-500 line-clamp-2 mt-1 leading-tight group-hover:text-slate-400 transition-colors">
                        {{ mission.description }}
                    </p>
                    <div v-if="!tutorialStore.completedMissions.includes(mission.id)" class="mt-2 text-[8px] font-bold text-emerald-400">
                        Recompensa: +{{ mission.reward }} 💰
                    </div>
                </div>
            </div>
            <div v-else-if="activeExplorerTab === 'achievements'" class="flex-1 overflow-y-auto p-2 space-y-2">
                <!-- Estadísticas Rápidas -->
                <div class="px-3 py-2 bg-white/5 rounded-xl border border-white/5 mb-4">
                    <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Estadísticas</div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex flex-col">
                            <span class="text-[9px] text-slate-500">Plantado</span>
                            <span class="text-xs font-mono text-emerald-400">{{ formatNumber(tutorialStore.totalPlanted) }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[9px] text-slate-500">Cosechado</span>
                            <span class="text-xs font-mono text-orange-400">{{ formatNumber(tutorialStore.totalHarvested) }}</span>
                        </div>
                    </div>
                </div>

                <div 
                    v-for="ach in tutorialStore.achievements" 
                    :key="ach.id"
                    class="group p-3 rounded-xl border transition-all flex items-center gap-4 relative overflow-hidden"
                    :class="ach.unlocked 
                        ? 'bg-amber-500/10 border-amber-500/30' 
                        : 'bg-slate-900/50 border-white/5 opacity-50 grayscale'"
                >
                    <!-- Status Icon -->
                    <div class="w-10 h-10 shrink-0 rounded-lg flex items-center justify-center text-2xl bg-white/5 shadow-inner"
                         :class="ach.unlocked ? 'animate-pulse' : ''">
                        {{ ach.unlocked ? ach.icon : '🔒' }}
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider" :class="ach.unlocked ? 'text-amber-500' : 'text-slate-500'">
                                {{ ach.title }}
                            </span>
                            <span v-if="ach.unlocked" class="text-[8px] bg-amber-500/20 text-amber-500 px-1.5 py-0.5 rounded uppercase font-bold tracking-tighter">Desbloqueado</span>
                        </div>
                        <p class="text-[9px] text-slate-400 leading-snug mt-0.5 line-clamp-2">
                            {{ ach.description }}
                        </p>
                    </div>

                    <!-- Shine effect on unlocked -->
                    <div v-if="ach.unlocked" class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                </div>
            </div>
            <div v-else class="flex-1 overflow-y-auto">
                <FileExplorer />
            </div>
        </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 relative bg-slate-50/30 dark:bg-slate-900/30">
        
        <!-- File Tabs & Toolbar -->
        <div class="flex flex-col z-10 shadow-lg shadow-black/5 dark:shadow-black/20">
            <!-- File Tabs -->
            <div class="flex items-center bg-slate-100/90 dark:bg-slate-900/90 border-b border-slate-200 dark:border-white/5 h-10">
                <!-- Toggle Explorer -->
                <button 
                    v-if="!isExplorerOpen"
                    @click="isExplorerOpen = true" 
                    class="h-full px-3 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-white/5 border-r border-slate-200 dark:border-white/5 transition-colors flex items-center justify-center"
                    title="Mostrar Explorador"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Tabs -->
                <div class="flex overflow-x-auto no-scrollbar flex-1 h-full items-end pt-1 px-1 gap-1">
                    <div 
                        v-for="(file, path) in fsStore.files" 
                        :key="path"
                        v-show="file.isOpen"
                        @click="fsStore.openFile(path)"
                        class="group flex items-center gap-2 px-3 py-1.5 text-xs font-medium cursor-pointer min-w-[100px] max-w-[180px] select-none transition-all relative rounded-t-md border-t border-x border-transparent"
                        :class="fsStore.activeFile === path ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-primary-light border-slate-200 dark:border-white/10 shadow-[0_-2px_10px_rgba(0,0,0,0.1)] dark:shadow-[0_-2px_10px_rgba(0,0,0,0.2)]' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 hover:bg-slate-200 dark:hover:bg-white/5'"
                    >
                        <div v-if="fsStore.activeFile === path" class="absolute top-0 left-0 right-0 h-[2px] bg-emerald-500 dark:bg-primary shadow-[0_0_8px_rgba(16,185,129,0.6)] rounded-full"></div>
                        
                        <!-- File Icon (Simple) -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-70" :class="fsStore.activeFile === path ? 'text-emerald-600 dark:text-primary' : 'text-slate-400 dark:text-slate-500'" viewBox="0 0 20 20" fill="currentColor">
                             <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                        </svg>

                        <span class="truncate">{{ file.name }}</span>
                        
                        <button 
                            @click.stop="fsStore.closeFile(path)" 
                            class="opacity-0 group-hover:opacity-100 p-0.5 hover:bg-red-500/20 hover:text-red-500 dark:hover:text-red-400 rounded-full transition-all ml-auto"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Toolbar Actions -->
            <div class="flex items-center justify-between px-3 py-2 bg-slate-100/60 dark:bg-slate-900/60 backdrop-blur-md border-b border-slate-200 dark:border-white/5">
                 <div class="flex gap-1">
                     <!-- Save Button -->
                     <button 
                        @click="handleSave"
                        class="p-1.5 hover:bg-slate-200 dark:hover:bg-white/10 rounded-md text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-primary transition-all active:scale-95 group relative" 
                        title="Guardar (Ctrl+S)"
                     >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                     </button>
                     <!-- Reload Button -->
                     <button 
                        @click="handleReload"
                        class="p-1.5 hover:bg-slate-200 dark:hover:bg-white/10 rounded-md text-slate-500 dark:text-slate-400 hover:text-cyan-600 dark:hover:text-secondary transition-all active:scale-95" 
                        title="Recargar"
                     >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                     </button>
                 </div>
                 
                 <div class="flex items-center gap-2">
                    <!-- Tools Group -->
                    <div class="flex items-center bg-slate-200/50 dark:bg-slate-800/50 rounded-lg p-0.5 border border-slate-300 dark:border-white/5">
                        <button 
                            @click="$emit('analyze')"
                            class="p-1.5 text-slate-500 dark:text-slate-400 hover:text-cyan-600 dark:hover:text-secondary hover:bg-white/50 dark:hover:bg-white/5 rounded-md transition-all"
                            title="Analizar Código"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </button>
                        <div class="w-px h-4 bg-slate-400/20 dark:bg-white/10 mx-0.5"></div>
                        <button 
                            @click="handleRunTests"
                            class="p-1.5 text-slate-500 dark:text-slate-400 hover:text-amber-600 dark:hover:text-accent hover:bg-white/50 dark:hover:bg-white/5 rounded-md transition-all"
                            title="Ejecutar Tests"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </div>

                    <div class="h-6 w-px bg-slate-300 dark:bg-white/10 mx-1"></div>
                    
                    <!-- Execution Controls -->
                    <div class="flex items-center gap-2">
                        <button 
                            @click="$emit('reset')"
                            class="p-2 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-white/10 rounded-lg transition-all active:scale-95"
                            title="Reiniciar Simulación"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                        
                        <button 
                            v-if="isRunning"
                            @click="$emit(isPaused ? 'resume' : 'pause')"
                            class="p-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-amber-600 dark:text-amber-400 border border-amber-500/30 rounded-lg shadow-lg shadow-amber-900/10 transition-all active:scale-95"
                            :title="isPaused ? 'Reanudar' : 'Pausar'"
                        >
                            <svg v-if="isPaused" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <button 
                            v-if="isRunning && isPaused"
                            @click="$emit('step')"
                            class="p-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-cyan-600 dark:text-secondary border border-cyan-500/30 dark:border-secondary/30 rounded-lg shadow-lg shadow-cyan-900/10 dark:shadow-secondary/10 transition-all active:scale-95"
                            title="Paso a Paso"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                 <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <!-- Primary Run Button (Enhanced) -->
                        <button 
                            @click="handleRun(); play('click')"
                            @mouseenter="!isRunning && play('hover')"
                            :disabled="isRunning && !isPaused"
                            class="relative group flex items-center gap-3 px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 disabled:from-slate-300 disabled:to-slate-300 disabled:text-slate-500 dark:disabled:from-slate-800 dark:disabled:to-slate-800 dark:disabled:text-slate-600 disabled:border-slate-400 dark:disabled:border-slate-700 disabled:shadow-none text-white font-bold rounded-xl shadow-[0_0_20px_rgba(16,185,129,0.3)] hover:shadow-[0_0_30px_rgba(16,185,129,0.5)] border border-emerald-400/30 transition-all duration-300 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 overflow-hidden"
                        >
                            <!-- Shiny effect -->
                            <div class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12"></div>
                            
                            <!-- Icon -->
                            <div class="relative flex items-center justify-center bg-black/20 rounded-full p-1 group-hover:bg-black/10 transition-colors">
                                <svg v-if="!isRunning" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white drop-shadow-md" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                </svg>
                                <svg v-else class="h-5 w-5 text-white drop-shadow-md animate-spin-slow" viewBox="0 0 20 20" fill="currentColor">
                                     <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2H4zm4 5a1 1 0 011-1h6a1 1 0 110 2H9a1 1 0 01-1-1zm0 4a1 1 0 011-1h6a1 1 0 110 2H9a1 1 0 01-1-1zm-1 5a1 1 0 011-1h2a1 1 0 110 2H8a1 1 0 01-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            
                            <span class="relative tracking-wider text-sm text-shadow uppercase font-extrabold">{{ isRunning ? 'Reiniciar' : 'Ejecutar' }}</span>
                        </button>
                    </div>
                 </div>
            </div>
        </div>

        <!-- Editor Area -->
        <div class="flex-1 relative overflow-hidden bg-white dark:bg-[#0d1117] flex flex-col border-t border-slate-200 dark:border-white/10">
            <!-- Explicit Label for User Clarity -->
            <div 
                v-if="currentFileContent !== null" 
                @click="focusEditor"
                class="bg-slate-50 dark:bg-[#161b22] px-4 py-2 border-b border-slate-200 dark:border-white/5 flex items-center gap-3 shadow-sm cursor-pointer hover:bg-slate-100 dark:hover:bg-[#1c2128] transition-colors group"
                title="Click para escribir código"
            >
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 group-hover:text-emerald-500 dark:group-hover:text-emerald-300 uppercase tracking-wider transition-colors">Editor Activo</span>
                </div>
                <div class="h-4 w-px bg-slate-300 dark:bg-white/10"></div>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-mono flex items-center gap-2 group-hover:text-slate-700 dark:group-hover:text-slate-300">
                    {{ fsStore.activeFile }}
                    <span class="text-[10px] text-slate-400 dark:text-slate-600 uppercase tracking-wider ml-2 opacity-60">Modo Inserción</span>
                </span>
            </div>

            <!-- Inner shadow for depth -->
            <div class="absolute inset-0 shadow-[inset_0_4px_6px_-1px_rgba(0,0,0,0.05)] dark:shadow-[inset_0_4px_6px_-1px_rgba(0,0,0,0.3)] pointer-events-none z-10"></div>
            
            <CodeEditor 
                ref="codeEditorRef"
                v-if="currentFileContent !== null"
                class="flex-1"
                :modelValue="currentFileContent" 
                :errorMarker="errorMarker"
                :executionLine="executionLine"
                :breakpoints="breakpoints"
                @update:modelValue="updateContent"
                @update:breakpoints="$emit('update:breakpoints', $event)"
            />
            <div v-else class="flex flex-col items-center justify-center h-full text-slate-400 dark:text-slate-500">
                <div class="w-20 h-20 mb-6 rounded-full bg-slate-100 dark:bg-slate-800/50 flex items-center justify-center border border-slate-200 dark:border-white/5 shadow-lg animate-pulse-slow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 opacity-50 text-emerald-500 dark:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <p class="text-xl font-medium text-slate-600 dark:text-slate-300 mb-2">Editor Vacío</p>
                <p class="text-sm opacity-60">Selecciona un archivo del explorador</p>
            </div>
        </div>

        <!-- Console/Output -->
        <div class="h-64 border-t border-slate-200 dark:border-white/5 bg-slate-50/90 dark:bg-slate-950/80 backdrop-blur-md flex flex-col relative z-20">
            <div class="px-4 py-2 bg-slate-100/90 dark:bg-slate-900/90 border-b border-slate-200 dark:border-white/5 flex justify-between items-center shadow-sm">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-cyan-600 dark:text-secondary" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm3.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414L7.586 10 5.293 7.707a1 1 0 010-1.414zM11 12a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Terminal</span>
                </div>
                <button @click="$emit('clear-logs')" class="text-[10px] px-2 py-0.5 rounded border border-slate-300 dark:border-slate-700 text-slate-500 dark:text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors uppercase font-medium">Limpiar</button>
            </div>
            <Console :logs="logs" @clear="$emit('clear-logs')" class="flex-1" />
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useFileSystemStore } from '../stores/filesystem';
import { useTutorialStore } from '../stores/tutorial';
import { useTechStore } from '../stores/techTree';
import { runTest } from '../utils/testRunner';
import { useSound } from '../composables/useSound';
import CodeEditor from './CodeEditor.vue';
import FileExplorer from './FileExplorer.vue';
import Console from './Console.vue';

import { formatNumber } from '../utils/formatters';

defineProps({
    isRunning: Boolean,
    logs: {
        type: Array,
        default: () => []
    },
    errorMarker: Object,
    executionLine: Number,
    isPaused: Boolean,
    breakpoints: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['run', 'reset', 'clear-logs', 'pause', 'resume', 'step', 'update:breakpoints', 'analyze', 'log-message']);
const fsStore = useFileSystemStore();
const tutorialStore = useTutorialStore();
const techStore = useTechStore();
const { play } = useSound();

const codeEditorRef = ref(null);
const focusEditor = () => {
    codeEditorRef.value?.focus();
};

const isExplorerOpen = ref(false);
const activeExplorerTab = ref('docs');

const docCategories = [
    {
        name: 'Movimiento',
        functions: [
            { name: 'move', description: 'Mueve el robot una celda en la dirección indicada.', example: 'move(UP) # Direcciones: UP, DOWN, LEFT, RIGHT' },
            { name: 'get_pos', description: 'Devuelve las coordenadas actuales del robot.', example: 'x, y = get_pos()' }
        ]
    },
    {
        name: 'Agricultura',
        functions: [
            { name: 'plant', description: 'Planta una semilla en la posición actual. El suelo debe estar vacío (SOIL).', example: 'plant()' },
            { name: 'harvest', description: 'Cosecha una planta madura en la posición actual.', example: 'harvest()' },
            { name: 'get_cell', description: 'Analiza el tipo de objeto en una posición específica.', example: 'tipo = get_cell(x, y) # Tipos: SOIL, WATER, PLANT, ROCK' }
        ]
    },
    {
        name: 'Utilidades',
        functions: [
            { name: 'log', description: 'Muestra un mensaje personalizado en la consola del juego.', example: 'log("¡Planta cosechada!")' }
        ]
    }
];

const isFunctionUnlocked = (fnName) => {
    const alwaysUnlocked = ['move', 'get_pos', 'plant', 'harvest', 'log'];
    if (alwaysUnlocked.includes(fnName)) return true;
    
    // Check if unlocked in tech tree
    return Object.values(tutorialStore.techTree || {}).some(tech => 
        tech.unlocked && tech.modifiers?.unlockedFunctions?.includes(fnName)
    ) || Object.values(techStore.technologies).some(tech => 
        tech.unlocked && tech.modifiers?.unlockedFunctions?.includes(fnName)
    );
};

// Ensure main.py is open on mount
onMounted(() => {
    if (!fsStore.activeFile) {
        fsStore.openFile('main.py');
    }
});

const currentFileContent = computed(() => {
    if (fsStore.activeFile && fsStore.files[fsStore.activeFile]) {
        return fsStore.files[fsStore.activeFile].content;
    }
    return null;
});

const updateContent = (val) => {
    if (fsStore.activeFile) {
        fsStore.updateFileContent(fsStore.activeFile, val);
    }
};

const handleRun = () => {
    // Run current file content
    if (currentFileContent.value) {
        emit('run', currentFileContent.value);
    }
};

const handleSave = async () => {
    if (fsStore.activeFile) {
        await fsStore.saveFile(fsStore.activeFile);
        // Visual feedback could be added here
    }
};

const handleReload = async () => {
    if (fsStore.activeFile) {
        await fsStore.reloadFile(fsStore.activeFile);
    }
};

const handleRunTests = async () => {
    if (!fsStore.activeFile || !fsStore.files[fsStore.activeFile]) {
        emit('log-message', { message: "No hay archivo abierto.", type: 'error' });
        return;
    }
    
    const code = fsStore.files[fsStore.activeFile].content;
    const tests = tutorialStore.activeMission.tests || [];
    
    if (tests.length === 0) {
        emit('log-message', { message: "No hay pruebas disponibles para este nivel.", type: 'warning' });
        return;
    }
    
    emit('log-message', { message: "Ejecutando pruebas...", type: 'system' });
    
    let passedCount = 0;
    
    for (const test of tests) {
        emit('log-message', { message: `Prueba: ${test.name}...`, type: 'info' });
        
        const techConfig = {
             battery_bonus: techStore.technologies['battery_1']?.unlocked ? 50 : 0,
             efficiency_bonus: techStore.technologies['efficiency_1']?.unlocked ? 0.1 : 0
        };
        
        const result = await runTest(code, test, techConfig);
        
        if (result.passed) {
            emit('log-message', { message: `✅ ${test.name}: PASADO`, type: 'success' });
            passedCount++;
        } else {
            emit('log-message', { message: `❌ ${test.name}: FALLIDO - ${result.message}`, type: 'error' });
        }
    }
    
    if (passedCount === tests.length) {
        emit('log-message', { message: `¡Todas las ${tests.length} pruebas pasaron!`, type: 'success' });
    } else {
        emit('log-message', { message: `Pruebas completadas: ${passedCount}/${tests.length} pasaron.`, type: 'warning' });
    }
};
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>