import { Grid } from './Grid';
import { Robot } from './Robot';
import { TILE_TYPES, DIRECTIONS } from './Constants';

/**
 * Motor principal del juego.
 * Gestiona la simulación lógica y la cola de acciones para la visualización.
 * Sigue el patrón Modelo en MVC.
 */
export class GameEngine {
    /**
     * @param {number} width - Ancho del mapa
     * @param {number} height - Alto del mapa
     * @param {Object} techConfig - Configuración de tecnologías desbloqueadas
     */
    constructor(width = 12, height = 12, techConfig = {}) {
        this.techConfig = techConfig;
        this.unlockedFunctions = techConfig.unlockedFunctions || [];
        
        // Estado Real (Visualización actual)
        this.grid = new Grid(width, height);
        
        // Configuración del Robot basada en tecnologías
        const robotType = techConfig.robot_type || 'basic';
        this.robot = new Robot('player', 0, 0, robotType);
        
        // Aplicar bonificaciones
        this.modifiers = techConfig.modifiers || {};
        this.robot.maxEnergy += (this.modifiers.maxEnergy || 0);
        this.robot.energy = this.robot.maxEnergy;
        
        // Estado de Simulación (Para pre-calcular acciones de Python)
        this.simulationGrid = new Grid(width, height);
        this.simulationRobot = new Robot('player_sim', 0, 0, robotType);
        this.simulationRobot.maxEnergy = this.robot.maxEnergy;
        
        this.actionQueue = []; // Cola de acciones a ejecutar visualmente
        this.resources = { money: 0, wood: 0, water: 0 };
        this.eventModifiers = {}; // Modificadores temporales de eventos
        
        this.resetSimulation();
    }

    /**
     * Aplica modificadores de eventos (ej. sequía, lluvia, boom económico)
     * @param {Object} modifiers 
     */
    setModifiers(modifiers) {
        this.eventModifiers = modifiers || {};
    }

    /**
     * Reinicia el estado de la simulación para una nueva ejecución de código.
     * Sincroniza el grid de simulación con el grid real actual.
     */
    resetSimulation() {
        // Copia profunda del grid para no alterar el real durante la simulación previa
        this.simulationGrid.cells = this.grid.cells.map(row => [...row]);
        
        // Reiniciar posición simulada
        this.simulationRobot.x = this.robot.x;
        this.simulationRobot.y = this.robot.y;
        
        // Reiniciar energía visual
        this.robot.energy = this.robot.maxEnergy;
        
        this.actionQueue = [];
    }

    /**
     * Ejecuta la siguiente acción de la cola visualmente.
     * Modifica el estado REAL del juego.
     * @returns {Object|null} La acción ejecutada o null si la cola está vacía.
     */
    step() {
        const action = this.actionQueue.shift();
        if (!action) return null;

        // Gestión de Energía (excluyendo logs)
        if (action.type !== 'log') {
            let cost = 1 * (this.robot.stats.energyRate || 1);
            
            // Bonificación de eficiencia tecnológica
            if (this.modifiers.energyCostMultiplier) {
                cost *= this.modifiers.energyCostMultiplier;
            }

            // Bonificación específica por acción (ej. till)
            if (action.type === 'till' && this.modifiers.tillCostMultiplier) {
                cost *= this.modifiers.tillCostMultiplier;
            }

            // Modificadores de evento
            if (this.eventModifiers.energyCostMultiplier) {
                cost *= this.eventModifiers.energyCostMultiplier;
            }

            // Passive Recharge (Regeneración por paso)
            if (this.modifiers.passiveRecharge) {
                // Recupera una fracción de energía por acción (simulando tiempo)
                // passiveRecharge 1 = 0.2 energía por paso (ejemplo)
                this.robot.energy = Math.min(this.robot.maxEnergy, this.robot.energy + (this.modifiers.passiveRecharge * 0.2));
            }

            // Verificación de batería
            if (this.robot.energy < cost) {
                return { type: 'error', message: '¡Batería agotada! El robot necesita descansar y recargar energía.' };
            }
            this.robot.energy -= cost;
        }

        // Simulación de Crecimiento (Respawn de plantas)
        if (this.modifiers.growthSpeedMultiplier > 1.0) {
            // Probabilidad de que un suelo vacío regenere una planta
            // Base chance 1% * multiplier
            if (Math.random() < 0.01 * this.modifiers.growthSpeedMultiplier) {
                 // Encontrar un tile de suelo aleatorio
                 // Nota: Esto es solo visual/efecto secundario, no afecta la lógica determinista del script usuario
                 // a menos que el script re-lea el grid.
                 // Para simplificar, lo haremos determinista o lo omitiremos para no romper la lógica del usuario.
                 // Mejor opción: Aumentar el rendimiento de cosecha si growth speed es alto ("plantas más maduras")
            }
        }

        // Ejecutar acción
        if (action.type === 'move') {
            const { dx, dy } = this.getDelta(action.dir);
            const newX = this.robot.x + dx;
            const newY = this.robot.y + dy;
            
            if (this.grid.isValid(newX, newY)) {
                this.robot.x = newX;
                this.robot.y = newY;
            }
        } else if (action.type === 'plant') {
            if (this.grid.getCell(this.robot.x, this.robot.y) === TILE_TYPES.SOIL) {
                this.grid.setCell(this.robot.x, this.robot.y, TILE_TYPES.PLANT);
            }
        } else if (action.type === 'harvest') {
            if (this.grid.getCell(this.robot.x, this.robot.y) === TILE_TYPES.PLANT) {
                
                // Verificar capacidad de inventario
                const currentWood = this.resources.wood;
                const maxInventory = this.modifiers.inventorySize || 10;
                
                if (currentWood >= maxInventory && !this.modifiers.autoSell) {
                     return { type: 'error', message: '¡Inventario lleno! Vende tus recursos o mejora tu capacidad.' };
                }

                this.grid.setCell(this.robot.x, this.robot.y, TILE_TYPES.SOIL);
                
                // Calcular ganancias
                let woodGain = 1 * (this.robot.stats.harvestRate || 1);
                let moneyGain = 5;

                // Modificadores
                if (this.modifiers.yieldBonus) {
                    woodGain += this.modifiers.yieldBonus;
                }
                
                // Growth Speed afecta la calidad/cantidad (Simulación)
                if (this.modifiers.growthSpeedMultiplier) {
                     woodGain = Math.ceil(woodGain * this.modifiers.growthSpeedMultiplier);
                }

                if (this.modifiers.sellPriceMultiplier) {
                    moneyGain *= this.modifiers.sellPriceMultiplier;
                }

                // Auto Sell Logic
                if (this.modifiers.autoSell) {
                    // Vende la madera recolectada inmediatamente + bono
                    this.resources.money += moneyGain + (woodGain * 2); // Precio por madera
                    action.gains = { money: moneyGain + (woodGain * 2) };
                } else {
                    this.resources.wood += woodGain;
                    this.resources.money += moneyGain; // Dinero base por acción
                    action.gains = { wood: woodGain, money: moneyGain };
                }
            }
        } else if (action.type === 'drill') {
            // Acción de taladrar (Romper rocas)
            if (this.grid.getCell(this.robot.x, this.robot.y) === TILE_TYPES.ROCK) {
                this.grid.setCell(this.robot.x, this.robot.y, TILE_TYPES.SOIL);
                
                // Posible ganancia de mineral raro o simplemente piedra
                let moneyGain = 2;
                this.resources.money += moneyGain;
                action.gains = { money: moneyGain };
            }
        } else if (action.type === 'terraform') {
            // Convertir terreno (Agua/Roca -> Tierra)
            const cell = this.grid.getCell(this.robot.x, this.robot.y);
            if (cell === TILE_TYPES.ROCK || cell === TILE_TYPES.WATER) {
                this.grid.setCell(this.robot.x, this.robot.y, TILE_TYPES.SOIL);
            }
        } else if (action.type === 'win') {
            // Win condition met
            return action;
        } else if (action.type === 'teleport') {
            // Teletransportarse
            this.robot.x = action.x;
            this.robot.y = action.y;
        }
        
        return action;
    }

    /**
     * Convierte dirección numérica a delta X/Y
     */
    getDelta(dir) {
        if (dir === DIRECTIONS.UP) return { dx: 0, dy: -1 };
        if (dir === DIRECTIONS.RIGHT) return { dx: 1, dy: 0 };
        if (dir === DIRECTIONS.DOWN) return { dx: 0, dy: 1 };
        if (dir === DIRECTIONS.LEFT) return { dx: -1, dy: 0 };
        return { dx: 0, dy: 0 };
    }

    /**
     * Genera la API que se inyecta en el entorno Python (Pyodide).
     * Estas funciones se ejecutan INSTANTÁNEAMENTE durante la compilación/simulación,
     * generando una cola de acciones (actionQueue) que luego se reproduce visualmente.
     */
    getApi() {
        const api = {
            move: (dir, line) => {
                const { dx, dy } = this.getDelta(dir);
                const newX = this.simulationRobot.x + dx;
                const newY = this.simulationRobot.y + dy;
                
                // Verificar colisiones en la simulación
                if (this.simulationGrid.isValid(newX, newY)) {
                    this.simulationRobot.x = newX;
                    this.simulationRobot.y = newY;
                    this.actionQueue.push({ type: 'move', dir, line });
                    return true;
                }
                this.actionQueue.push({ type: 'error', message: '¡Cuidado! El robot ha chocado contra el borde del mapa.', line });
                return false;
            },
            plant: (line) => {
                const x = this.simulationRobot.x;
                const y = this.simulationRobot.y;
                if (this.simulationGrid.getCell(x, y) === TILE_TYPES.SOIL) {
                    this.simulationGrid.setCell(x, y, TILE_TYPES.PLANT);
                    this.actionQueue.push({ type: 'plant', line });
                    return true;
                }
                this.actionQueue.push({ type: 'error', message: 'No puedes plantar aquí. Busca una zona de tierra fértil.', line });
                return false;
            },
            harvest: (line) => {
                const x = this.simulationRobot.x;
                const y = this.simulationRobot.y;
                if (this.simulationGrid.getCell(x, y) === TILE_TYPES.PLANT) {
                    this.simulationGrid.setCell(x, y, TILE_TYPES.SOIL);
                    this.actionQueue.push({ type: 'harvest', line });
                    return true;
                }
                this.actionQueue.push({ type: 'error', message: 'No hay nada que cosechar en esta posición.', line });
                return false;
            },
            get_pos: () => {
                return [this.simulationRobot.x, this.simulationRobot.y];
            },
            get_cell: (x, y) => {
                const tx = x !== undefined ? x : this.simulationRobot.x;
                const ty = y !== undefined ? y : this.simulationRobot.y;
                
                // View Distance Check
                if (this.modifiers.viewDistance && this.modifiers.viewDistance > 0) {
                    const dist = Math.abs(tx - this.simulationRobot.x) + Math.abs(ty - this.simulationRobot.y);
                    if (dist > this.modifiers.viewDistance) {
                        return -1; // Unknown/Out of range
                    }
                }
                
                return this.simulationGrid.getCell(tx, ty);
            },
            log: (msg, line) => {
                this.actionQueue.push({ type: 'log', message: msg, line });
            }
        };

        // Funciones desbloqueables
            if (this.unlockedFunctions.includes('measure_soil')) {
                api.measure_soil = (line) => {
                    const x = this.simulationRobot.x;
                    const y = this.simulationRobot.y;
                    const cell = this.simulationGrid.getCell(x, y);
                    return cell;
                };
            }

            if (this.unlockedFunctions.includes('inventory_count')) {
                api.inventory_count = (line) => {
                    return this.resources.wood;
                };
            }

            if (this.unlockedFunctions.includes('drill')) {
                api.drill = (line) => {
                    const x = this.simulationRobot.x;
                    const y = this.simulationRobot.y;
                    if (this.simulationGrid.getCell(x, y) === 3) { // ROCK
                        this.simulationGrid.setCell(x, y, TILE_TYPES.SOIL);
                        this.actionQueue.push({ type: 'drill', line }); 
                        return true;
                    }
                    return false;
                };
            }

            if (this.unlockedFunctions.includes('teleport_home')) {
                api.teleport_home = (line) => {
                    this.simulationRobot.x = 0;
                    this.simulationRobot.y = 0;
                    this.actionQueue.push({ type: 'teleport', x: 0, y: 0, line });
                    return true;
                };
            }

            if (this.unlockedFunctions.includes('teleport_anywhere')) {
                api.teleport = (x, y, line) => {
                    // Check bounds
                    if (this.simulationGrid.isValid(x, y)) {
                        this.simulationRobot.x = x;
                        this.simulationRobot.y = y;
                        this.actionQueue.push({ type: 'teleport', x, y, line });
                        return true;
                    }
                    return false;
                };
            }

            if (this.unlockedFunctions.includes('terraform')) {
                api.terraform = (line) => {
                    const x = this.simulationRobot.x;
                    const y = this.simulationRobot.y;
                    // Converts ROCK (3) or WATER (1) to SOIL (0)
                    const cell = this.simulationGrid.getCell(x, y);
                    if (cell === 3 || cell === 1) {
                        this.simulationGrid.setCell(x, y, TILE_TYPES.SOIL);
                        this.actionQueue.push({ type: 'terraform', line });
                        return true;
                    }
                    return false;
                };
            }
            
            if (this.unlockedFunctions.includes('get_market_price')) {
                api.get_market_price = (resource) => {
                    // Simulate market fluctuations
                    const basePrice = 5;
                    const fluctuation = Math.floor(Math.random() * 5) - 2;
                    if (resource === 'wood') return basePrice + fluctuation;
                    return 0;
                };
            }

            if (this.unlockedFunctions.includes('reveal_map')) {
                // This is a passive effect, but we can expose a function to check status
                api.is_map_revealed = () => true;
            }

            if (this.unlockedFunctions.includes('launch_rocket')) {
                api.launch_rocket = (line) => {
                    // Win condition trigger
                    this.actionQueue.push({ type: 'win', line });
                    return true;
                };
            }

            return api;
        }
}
