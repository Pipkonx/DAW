import { ref, watch, onMounted, onBeforeUnmount } from 'vue';
import { GameEngine } from '../game/GameEngine';
import { GameRenderer } from '../game/Renderer';
import { initPyodide, runPython, analyzeCode } from '../utils/pyodide';
import { useFileSystemStore } from '../stores/filesystem';
import { useTutorialStore } from '../stores/tutorial';
import { useTechStore } from '../stores/techTree';
import { useRandomEventStore } from '../stores/randomEvents';
import { useTheme } from './useTheme';
import { GameService } from '../services/GameService';
import { RankingService } from '../services/RankingService';

export function useGameController(canvasContainer, toastRef = ref(null)) {
    // State
    const isRunning = ref(false);
    const isPaused = ref(false);
    const breakpoints = ref([]);
    const logs = ref([]);
    const errorMarker = ref(null);
    const executionLine = ref(null);
    const resources = ref({ money: 100, wood: 0, water: 50 });
    const showLevelComplete = ref(false);
    
    // Core Systems
    let gameEngine = null;
    let renderer = null;
    let eventInterval = null;
    const currentTime = ref(Date.now());

    // Stores
    const fsStore = useFileSystemStore();
    const tutorialStore = useTutorialStore();
    const techStore = useTechStore();
    const eventStore = useRandomEventStore();
    const { isDark } = useTheme();

    // Watch for theme changes
    watch(isDark, (val) => {
        if (renderer) {
            renderer.setTheme(val);
        }
    });

    // Helpers
    const addLog = (message, type = 'system') => {
        logs.value.push({
            message,
            type,
            timestamp: Date.now()
        });
    };

    const getTechConfig = () => {
        const config = {
            robot_type: techStore.activeRobot || 'basic',
            modifiers: {
                maxEnergy: 0,
                energyCostMultiplier: 1.0,
                speedMultiplier: 1.0,
                passiveRecharge: 0,
                viewDistance: 0, // 0 means unlimited or default
                inventorySize: 10, // Base inventory size
                autoSell: false,
                tillCostMultiplier: 1.0,
                growthSpeedMultiplier: 1.0
            },
            unlockedFunctions: []
        };

        // Iterate over all technologies to apply modifiers
        for (const key in techStore.technologies) {
            const tech = techStore.technologies[key];
            if (tech.unlocked) {
                if (tech.modifiers) {
                    if (tech.modifiers.maxEnergy) config.modifiers.maxEnergy += tech.modifiers.maxEnergy;
                    if (tech.modifiers.energyCostMultiplier) config.modifiers.energyCostMultiplier *= tech.modifiers.energyCostMultiplier;
                    if (tech.modifiers.speedMultiplier) config.modifiers.speedMultiplier *= tech.modifiers.speedMultiplier;
                    if (tech.modifiers.passiveRecharge) config.modifiers.passiveRecharge += tech.modifiers.passiveRecharge;
                    if (tech.modifiers.viewDistance) config.modifiers.viewDistance += tech.modifiers.viewDistance;
                    if (tech.modifiers.inventorySize) config.modifiers.inventorySize += tech.modifiers.inventorySize;
                    if (tech.modifiers.autoSell) config.modifiers.autoSell = true;
                    if (tech.modifiers.tillCostMultiplier) config.modifiers.tillCostMultiplier *= tech.modifiers.tillCostMultiplier;
                    if (tech.modifiers.growthSpeedMultiplier) config.modifiers.growthSpeedMultiplier *= tech.modifiers.growthSpeedMultiplier;
                }
                if (tech.unlocksFunction) {
                    config.unlockedFunctions.push(tech.unlocksFunction);
                }
            }
        }

        return config;
    };

    // Core Logic
    const loadLevel = () => {
        // En modo incremental, el nivel es solo la configuración inicial si es la primera vez
        if (!gameEngine) {
            const config = getTechConfig();
            gameEngine = new GameEngine(12, 12, config);
            
            // Colocar algunas plantas iniciales para que el mundo no esté vacío
            for(let i=0; i<10; i++) {
                const rx = Math.floor(Math.random() * 12);
                const ry = Math.floor(Math.random() * 12);
                gameEngine.grid.setCell(rx, ry, 2); // TILE_TYPES.PLANT
            }
        }
        
        if (renderer) {
            renderer.updateGrid(gameEngine.grid);
            renderer.moveRobot(gameEngine.robot.x, gameEngine.robot.y, 0);
        }
        
        fsStore.createFile('main.py', '# Escribe tu código de automatización aquí\n');
        fsStore.openFile('main.py');
        
        addLog(`Mundo de la granja cargado. ¡Buena suerte!`, 'system');
    };

    const processQueue = (ignoreBreakpoint = false, singleStep = false) => {
        if (!gameEngine || !isRunning.value) return;
        if (isPaused.value && !ignoreBreakpoint && !singleStep) return;

        const nextAction = gameEngine.actionQueue[0];
        if (nextAction && !ignoreBreakpoint && breakpoints.value.includes(Number(nextAction.line))) {
            isPaused.value = true;
            executionLine.value = Number(nextAction.line);
            addLog(`> Pausado en breakpoint: Línea ${nextAction.line}`, 'system');
            return;
        }

        const action = gameEngine.step();
        
        if (action) {
            // Sincronizar SIEMPRE la línea de ejecución
            if (action.line !== undefined && action.line !== null) {
                const lineNum = Number(action.line);
                if (executionLine.value !== lineNum) {
                    executionLine.value = lineNum;
                }
            }

            if (action.type === 'error') {
                addLog(action.message, 'error');
                isRunning.value = false;
                executionLine.value = null;
                return;
            }

            if (renderer) {
                if (action.type === 'move') {
                    renderer.moveRobot(gameEngine.robot.x, gameEngine.robot.y, 0.2);
                } else if (action.type === 'log') {
                    addLog(action.message, 'log');
                } else if (action.type === 'plant') {
                    renderer.updateGrid(gameEngine.grid);
                    tutorialStore.totalPlanted++;
                    renderer.showFloatingText('🌱', gameEngine.robot.x, gameEngine.robot.y, 0x10b981);
                } else if (action.type === 'harvest') {
                    renderer.updateGrid(gameEngine.grid);
                    tutorialStore.totalHarvested++;
                    
                    if (action.gains) {
                        const woodGain = action.gains.wood || 0;
                        let moneyGain = action.gains.money || 0;
                        
                        // Aplicar multiplicadores tecnológicos
                        let totalMoneyMultiplier = 1.0;
                        Object.values(techStore.technologies).forEach(tech => {
                            if (tech.unlocked && tech.modifiers?.moneyMultiplier) {
                                totalMoneyMultiplier *= tech.modifiers.moneyMultiplier;
                            }
                        });
                        moneyGain = Math.floor(moneyGain * totalMoneyMultiplier);
                        
                        resources.value.wood += woodGain;
                        resources.value.money += moneyGain;

                        if (woodGain > 0) renderer.showFloatingText(`+${woodGain} 🪵`, gameEngine.robot.x, gameEngine.robot.y, 0xb45309);
                        if (moneyGain > 0) renderer.showFloatingText(`+${moneyGain} 💰`, gameEngine.robot.x, gameEngine.robot.y, 0xfacc15);
                        
                        // Si estamos en modo infinito (o siempre ahora), los recursos cuentan como puntos
                        const score = moneyGain + woodGain * 10;
                        if (score > 0) {
                            RankingService.submitScore(score, 'infinite').catch(console.error);
                        }
                    }
                } else {
                    renderer.updateGrid(gameEngine.grid);
                }
            }

            // Verificar misiones en cada paso
            const completed = tutorialStore.completeMission(resources.value, techStore, gameEngine);
            if (completed) {
                addLog(`¡MISIÓN COMPLETADA! ${completed.title}`, 'success');
                if (toastRef.value) toastRef.value.show('Misión Superada', `+${completed.reward}💰 por ${completed.title}`, 'success');
                saveUserProgress();
            }

            // Verificar logros
            const previousAchievements = tutorialStore.achievements.filter(a => a.unlocked).length;
            tutorialStore.checkAchievements(resources.value, techStore);
            const currentAchievements = tutorialStore.achievements.filter(a => a.unlocked).length;
            
            if (currentAchievements > previousAchievements) {
                if (toastRef.value) toastRef.value.show('🏆 Logro Desbloqueado', 'Has ganado un nuevo trofeo', 'warning');
                saveUserProgress();
            }
            
            if (singleStep) {
                 isPaused.value = true;
                 const next = gameEngine.actionQueue[0];
                 if (next) executionLine.value = Number(next.line);
                 return;
            }

            // Velocidad basada en mejoras tecnológicas (Stackable)
            const speedBase = 300;
            let totalSpeedMultiplier = 1.0;
            Object.values(techStore.technologies).forEach(tech => {
                if (tech.unlocked && tech.modifiers?.speedMultiplier) {
                    totalSpeedMultiplier *= tech.modifiers.speedMultiplier;
                }
            });
            const delay = speedBase / totalSpeedMultiplier;

            setTimeout(() => processQueue(false), delay);
        } else {
            isRunning.value = false;
            // No reseteamos executionLine.value aquí para que se quede en la última línea ejecutada
            addLog("Ciclo de ejecución finalizado.", 'system');
        }
    };

    const runCode = async (code) => {
        if (isRunning.value) return;
        
        isRunning.value = true;
        logs.value = [];
        errorMarker.value = null;
        executionLine.value = 1; // Forzar inicio en línea 1 para feedback visual inmediato
        addLog("Preparando robot...", 'system');
        
        gameEngine.resetSimulation();
        
        try {
            const result = await runPython(code, gameEngine.getApi());
            
            if (!result.success) {
                // El mensaje ya viene traducido y con la línea desde pyodide.js
                addLog(result.message, 'error');
                if (result.line) {
                    errorMarker.value = {
                        line: result.line,
                        message: result.message
                    };
                }
                isRunning.value = false;
                return;
            }
            
            addLog("¡Código aceptado! Iniciando simulación...", 'success');
            processQueue();
            
        } catch (e) {
            addLog(`Error inesperado: ${e.message}`, 'error');
            isRunning.value = false;
        }
    };

    const reset = () => {
        if (gameEngine) {
            loadLevel();
            addLog("Simulación reiniciada.", 'system');
            isRunning.value = false;
            isPaused.value = false;
            executionLine.value = null;
        }
    };

    const pauseGame = () => {
        isPaused.value = true;
        addLog("> Pausado.", 'system');
    };

    const resumeGame = () => {
        if (!isPaused.value) return;
        isPaused.value = false;
        addLog("> Reanudando...", 'system');
        processQueue(true);
    };

    const stepGame = () => {
        if (!isPaused.value) return;
        processQueue(true, true);
    };

    const saveUserProgress = async () => {
        const token = localStorage.getItem('token');
        if (!token) return;

        try {
            await GameService.saveProgress({
                current_mission: tutorialStore.currentMissionIndex,
                unlocked_technologies: Object.keys(techStore.technologies).filter(id => techStore.technologies[id].unlocked),
                resources: resources.value,
                stats: {
                    harvested: tutorialStore.totalHarvested,
                    planted: tutorialStore.totalPlanted
                }
            });
        } catch (error) {
            console.error("Error al guardar progreso:", error);
            // No mostrar error 401/403 si el token expiró silenciosamente
        }
    };

    const loadUserProgress = async () => {
        try {
            const progress = await GameService.getProgress();
            if (progress) {
                // Cargar misión
                if (progress.current_mission !== undefined) {
                    tutorialStore.currentMissionIndex = progress.current_mission;
                }
                
                // Cargar tecnologías
                if (progress.unlocked_technologies) {
                    progress.unlocked_technologies.forEach(techId => {
                        if (techStore.technologies[techId]) {
                            techStore.technologies[techId].unlocked = true;
                        }
                    });
                }
                
                // Cargar recursos
                if (progress.resources) {
                    resources.value = { ...resources.value, ...progress.resources };
                }

                // Cargar estadísticas
                if (progress.stats) {
                    tutorialStore.totalHarvested = progress.stats.harvested || 0;
                    tutorialStore.totalPlanted = progress.stats.planted || 0;
                }
                
                // Reiniciar nivel actual con los nuevos datos
                loadLevel();
                addLog("Progreso cargado correctamente.", "success");
            }
        } catch (e) {
            console.error("Error al cargar progreso:", e);
        }
    };

    const handlePurchase = async (tech) => {
        if (techStore.unlock(tech.id, resources.value.money)) {
            resources.value.money -= tech.cost;
            tutorialStore.totalMoneySpent += tech.cost; // Actualizar estadística para logros
            addLog(`Tecnología desbloqueada: ${tech.name}`, 'success');
            
            // Notificación visual de compra
            if (toastRef.value) toastRef.value.show('Mejora Adquirida', `${tech.name} desbloqueada.`, 'success');
            
            if (gameEngine && tech.id === 'battery_1') {
                gameEngine.robot.maxEnergy += 50;
                gameEngine.robot.energy = gameEngine.robot.maxEnergy;
            }
            
            // Verificar logros después de una compra
            tutorialStore.checkAchievements(resources.value, techStore);
            
            await saveUserProgress();
        } else {
            addLog("Fondos insuficientes o requisitos no cumplidos.", "error");
        }
    };

    const handleAnalyze = async () => {
        if (!fsStore.activeFile || !fsStore.files[fsStore.activeFile]) {
            addLog("Abre un archivo para que pueda analizarlo.", "error");
            return;
        }
    
        const code = fsStore.files[fsStore.activeFile].content;
        addLog("Analizando tu código en busca de mejoras...", "system");
        
        try {
            const suggestions = await analyzeCode(code);
            
            if (suggestions.length === 0) {
                addLog("¡Todo se ve genial! No he encontrado problemas.", "success");
            } else {
                addLog(`Análisis terminado: He encontrado ${suggestions.length} cosas que podrías mejorar.`, "warning");
                suggestions.forEach(s => {
                    const msg = s.line > 0 ? `Línea ${s.line}: ${s.message}` : s.message;
                    // Simplificar mensajes de optimización
                    const simplifiedMsg = msg.replace('Optimization:', '💡 Sugerencia:').replace('Warning:', '⚠️ Cuidado:').replace('Error:', '❌ Error:');
                    addLog(simplifiedMsg, s.type === 'error' ? 'error' : (s.type === 'warning' ? 'warning' : 'info'));
                });
            }
        } catch (e) {
            addLog(`Vaya, no he podido analizar el código: ${e.message}`, "error");
        }
    };

    // Initialization
    onMounted(async () => {
        gameEngine = new GameEngine(12, 12, getTechConfig());
        renderer = new GameRenderer();
        
        if (canvasContainer.value) {
            await renderer.init(canvasContainer.value, 12, 12);
            renderer.setTheme(isDark.value);
        }

        try {
            addLog("Inicializando entorno Python...", 'system');
            await initPyodide();
            addLog("Python listo.", 'success');
        } catch (e) {
            console.error("Failed to load Pyodide", e);
            addLog("Error cargando Python.", 'error');
        }

        loadLevel();

        // Passive Income Loop (1s)
        setInterval(() => {
            let passiveMoney = 0;
            let passiveWater = 0;
            Object.values(techStore.technologies).forEach(tech => {
                if (tech.unlocked) {
                    if (tech.modifiers?.passiveIncome) passiveMoney += tech.modifiers.passiveIncome;
                    if (tech.modifiers?.passiveWater) passiveWater += tech.modifiers.passiveWater;
                }
            });
            
            if (passiveMoney > 0) resources.value.money += passiveMoney;
            if (passiveWater > 0) resources.value.water += passiveWater;
        }, 1000);

        eventInterval = setInterval(() => {
            currentTime.value = Date.now();
            const result = eventStore.tryTriggerEvent();
            if (result) {
                if (result.type === 'new') {
                    addLog(`EVENTO: ¡${result.event.name} ha comenzado!`, 'warning');
                    if (gameEngine) gameEngine.setModifiers(result.event.modifiers);
                } else if (result.type === 'expired') {
                    addLog(`El evento ha terminado. Condiciones normales restauradas.`, 'system');
                    if (gameEngine) gameEngine.setModifiers({});
                }
            }
        }, 100);
    });

    onBeforeUnmount(() => {
        if (renderer) renderer.destroy();
        if (eventInterval) clearInterval(eventInterval);
    });

    watch(() => tutorialStore.currentMissionIndex, () => {
        // Guardar progreso cuando cambia la misión
        saveUserProgress();
    });

    watch(isDark, (newVal) => {
        if (renderer) renderer.setTheme(newVal);
    });

    return {
        isRunning,
        isPaused,
        breakpoints,
        logs,
        errorMarker,
        executionLine,
        resources,
        showLevelComplete,
        currentTime,
        runCode,
        reset,
        pauseGame,
        resumeGame,
        stepGame,
        handlePurchase,
        handleAnalyze,
        addLog,
        saveUserProgress,
        loadUserProgress
    };
}
