import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useTechStore = defineStore('techTree', () => {
    // Definición masiva de tecnologías
    const technologies = ref({
        // === RAMA PRINCIPAL: EFICIENCIA Y BASES ===
        'efficiency_1': {
            id: 'efficiency_1',
            name: 'Motores Eficientes',
            description: 'Reduce el consumo de energía en un 10%.',
            cost: 50,
            unlocked: false,
            dependencies: [],
            icon: '⚡',
            position: { x: 100, y: 1500 }, // Start Center-ish
            modifiers: { energyCostMultiplier: 0.9 }
        },
        'battery_1': {
            id: 'battery_1',
            name: 'Batería de Litio',
            description: 'Aumenta la capacidad máxima de energía en 50.',
            cost: 80,
            unlocked: false,
            dependencies: ['efficiency_1'],
            icon: '🔋',
            position: { x: 600, y: 1500 },
            modifiers: { maxEnergy: 50 }
        },
        'battery_2': {
            id: 'battery_2',
            name: 'Celdas Solares',
            description: 'Aumenta la capacidad máxima de energía en 150.',
            cost: 500,
            unlocked: false,
            dependencies: ['battery_1'],
            icon: '☀️',
            position: { x: 600, y: 1900 },
            modifiers: { maxEnergy: 150 }
        },
        'speed_1': {
            id: 'speed_1',
            name: 'Engranajes Turbo',
            description: 'Aumenta la velocidad de ejecución de código.',
            cost: 120,
            unlocked: false,
            dependencies: ['efficiency_1'],
            icon: '⏩',
            position: { x: 600, y: 1100 },
            modifiers: { speedMultiplier: 1.5 }
        },
        'speed_2': {
            id: 'speed_2',
            name: 'Procesador Cuántico',
            description: 'Duplica la velocidad de ejecución de código.',
            cost: 1500,
            unlocked: false,
            dependencies: ['speed_1'],
            icon: '⚛️',
            position: { x: 600, y: 700 },
            modifiers: { speedMultiplier: 2.0 }
        },
        'passive_1': {
            id: 'passive_1',
            name: 'Venta Automática I',
            description: 'Genera 1 moneda por segundo de forma pasiva.',
            cost: 200,
            unlocked: false,
            dependencies: [],
            icon: '🪙',
            position: { x: -400, y: 1500 },
            modifiers: { passiveIncome: 1 }
        },
        'passive_2': {
            id: 'passive_2',
            name: 'Mercado Local',
            description: 'Genera 5 monedas por segundo de forma pasiva.',
            cost: 800,
            unlocked: false,
            dependencies: ['passive_1'],
            icon: '🏪',
            position: { x: -400, y: 1100 },
            modifiers: { passiveIncome: 5 }
        },
        'water_1': {
            id: 'water_1',
            name: 'Recolector de Lluvia',
            description: 'Genera 1 de agua por segundo de forma pasiva.',
            cost: 300,
            unlocked: false,
            dependencies: [],
            icon: '💧',
            position: { x: -100, y: 1900 },
            modifiers: { passiveWater: 1 }
        },
        'water_2': {
            id: 'water_2',
            name: 'Pozo Automático',
            description: 'Genera 10 de agua por segundo de forma pasiva.',
            cost: 1500,
            unlocked: false,
            dependencies: ['water_1'],
            icon: '🚰',
            position: { x: -100, y: 2300 },
            modifiers: { passiveWater: 10 }
        },
        'sensor_1': {
            id: 'sensor_1',
            name: 'Sensores de Suelo',
            description: 'Permite al robot detectar si una celda está vacía.',
            cost: 250,
            unlocked: false,
            dependencies: [],
            icon: '📡',
            position: { x: -600, y: 1500 },
            modifiers: { unlockedFunctions: ['get_cell'] }
        },
        'drone_1': {
            id: 'drone_1',
            name: 'IA de Enjambre',
            description: 'Aumenta el dinero obtenido por cada madera cosechada un 50%.',
            cost: 2500,
            unlocked: false,
            dependencies: ['passive_2'],
            icon: '🤖',
            position: { x: -400, y: 700 },
            modifiers: { moneyMultiplier: 1.5 }
        },
        'storage_1': {
            id: 'storage_1',
            name: 'Almacén Industrial',
            description: 'Duplica el dinero obtenido por cada venta.',
            cost: 10000,
            unlocked: false,
            dependencies: ['drone_1'],
            icon: '🏭',
            position: { x: -400, y: 300 },
            modifiers: { moneyMultiplier: 2.0 }
        },

        // === RAMA DE ENERGÍA (SUPERIOR) ===
        'solar_panels': {
            id: 'solar_panels',
            name: 'Paneles Solares',
            description: 'Recarga 1 de energía cada 5 segundos durante el día.',
            cost: 400,
            unlocked: false,
            dependencies: ['battery_1'],
            icon: '☀️',
            position: { x: 1100, y: 1500 },
            modifiers: { passiveRecharge: 1 }
        },
        'battery_2': {
            id: 'battery_2',
            name: 'Batería de Grafeno',
            description: 'Aumenta la capacidad máxima de energía en 150.',
            cost: 800,
            unlocked: false,
            dependencies: ['solar_panels'],
            icon: '🔋+',
            position: { x: 1600, y: 1500 },
            modifiers: { maxEnergy: 150 }
        },
        'fusion_core': {
            id: 'fusion_core',
            name: 'Núcleo de Fusión',
            description: 'El consumo de energía se reduce a la mitad.',
            cost: 2500,
            unlocked: false,
            dependencies: ['battery_2'],
            icon: '⚛️',
            position: { x: 2200, y: 1500 },
            modifiers: { energyCostMultiplier: 0.5 }
        },

        // === RAMA DE RECOLECCIÓN Y CULTIVO (INFERIOR) ===
        'sensor_1': {
            id: 'sensor_1',
            name: 'Sensor de Suelo',
            description: 'Desbloquea la función measure_soil().',
            cost: 300,
            unlocked: false,
            dependencies: ['efficiency_1'],
            icon: '📡',
            position: { x: 600, y: 1900 },
            unlocksFunction: 'measure_soil'
        },
        'plow_upgrade': {
            id: 'plow_upgrade',
            name: 'Arado de Titanio',
            description: 'Reduce el costo de energía de arar (till) en un 30%.',
            cost: 500,
            unlocked: false,
            dependencies: ['sensor_1'],
            icon: '🚜',
            position: { x: 1100, y: 1900 },
            modifiers: { tillCostMultiplier: 0.7 }
        },
        'auto_harvest': {
            id: 'auto_harvest',
            name: 'Cosecha Automática',
            description: 'Desbloquea la función harvest_all().',
            cost: 800,
            unlocked: false,
            dependencies: ['plow_upgrade'],
            icon: '🌾',
            position: { x: 1600, y: 1900 },
            unlocksFunction: 'harvest_all'
        },
        'gmo_seeds': {
            id: 'gmo_seeds',
            name: 'Semillas OGM',
            description: 'Las plantas crecen un 20% más rápido.',
            cost: 1200,
            unlocked: false,
            dependencies: ['auto_harvest'],
            icon: '🧬',
            position: { x: 2100, y: 1900 },
            modifiers: { growthSpeedMultiplier: 1.2 }
        },

        // === RAMA DE DRONES Y ROBÓTICA (DERECHA - ARRIBA) ===
        'unlock_scout': {
            id: 'unlock_scout',
            name: 'Dron Explorador',
            description: 'Desbloquea dron rápido para mapear el terreno.',
            cost: 500,
            unlocked: false,
            dependencies: ['speed_1'],
            icon: '🚁',
            position: { x: 1100, y: 1100 },
            unlocksRobot: 'scout'
        },
        'optical_sensors': {
            id: 'optical_sensors',
            name: 'Sensores Ópticos',
            description: 'Aumenta el rango de visión del explorador.',
            cost: 700,
            unlocked: false,
            dependencies: ['unlock_scout'],
            icon: '👁️',
            position: { x: 1600, y: 1100 },
            modifiers: { viewDistance: 2 }
        },
        'teleport_pad': {
            id: 'teleport_pad',
            name: 'Teletransporte',
            description: 'Permite volver a la base instantáneamente (coste alto).',
            cost: 2000,
            unlocked: false,
            dependencies: ['optical_sensors'],
            icon: '🌌',
            position: { x: 2100, y: 1100 },
            unlocksFunction: 'teleport_home'
        },

        // === RAMA DE INTELIGENCIA ARTIFICIAL (CENTRO - SUPERIOR) ===
        'logistics_1': {
            id: 'logistics_1',
            name: 'Escáner de Inventario',
            description: 'Desbloquea la función inventory_count().',
            cost: 300,
            unlocked: false,
            dependencies: ['efficiency_1'],
            icon: '📦',
            position: { x: 600, y: 700 },
            unlocksFunction: 'inventory_count'
        },
        'ai_core': {
            id: 'ai_core',
            name: 'Núcleo de IA',
            description: 'Permite scripts asíncronos más complejos.',
            cost: 1000,
            unlocked: false,
            dependencies: ['logistics_1', 'battery_1'],
            icon: '🧠',
            position: { x: 1100, y: 700 },
            modifiers: { scriptTimeout: 2000 }
        },
        'swarm_control': {
            id: 'swarm_control',
            name: 'Control de Enjambre',
            description: 'Permite coordinar múltiples drones (Futuro).',
            cost: 3000,
            unlocked: false,
            dependencies: ['ai_core'],
            icon: '🤖',
            position: { x: 1700, y: 700 },
            modifiers: { maxDrones: 3 }
        },

        // === RAMA DE ECONOMÍA (IZQUIERDA) ===
        'market_api': {
            id: 'market_api',
            name: 'API de Mercado',
            description: 'Desbloquea get_market_price().',
            cost: 400,
            unlocked: false,
            dependencies: ['efficiency_1'],
            icon: '📈',
            position: { x: 600, y: 2300 },
            unlocksFunction: 'get_market_price'
        },
        'trading_bot': {
            id: 'trading_bot',
            name: 'Bot de Trading',
            description: 'Venta automática cuando el precio es alto.',
            cost: 1500,
            unlocked: false,
            dependencies: ['market_api'],
            icon: '💹',
            position: { x: 1100, y: 2300 },
            modifiers: { autoSell: true }
        },

        // === RAMA PESADA (DERECHA - ABAJO) ===
        'unlock_harvester': {
            id: 'unlock_harvester',
            name: 'Dron Cosechador',
            description: 'Desbloquea dron pesado con gran capacidad.',
            cost: 800,
            unlocked: false,
            dependencies: ['battery_1', 'plow_upgrade'],
            icon: '🚜',
            position: { x: 1100, y: 1500 },
            unlocksRobot: 'harvester'
        },
        'drill_arm': {
            id: 'drill_arm',
            name: 'Brazo Taladro',
            description: 'Permite romper rocas para obtener minerales.',
            cost: 1200,
            unlocked: false,
            dependencies: ['unlock_harvester'],
            icon: '🔩',
            position: { x: 1600, y: 1500 },
            unlocksFunction: 'drill'
        },
        'cargo_expansion': {
                    id: 'cargo_expansion',
                    name: 'Expansión de Carga',
                    description: 'Duplica la capacidad de inventario del Cosechador.',
                    cost: 1500,
                    unlocked: false,
                    dependencies: ['unlock_harvester'],
                    icon: '🚛',
                    position: { x: 1600, y: 1100 },
                    modifiers: { inventorySize: 20 }
                },

                // === RAMA DE MATERIALES AVANZADOS (IZQUIERDA - ABAJO) ===
                'nanobots': {
                    id: 'nanobots',
                    name: 'Nanobots Reparadores',
                    description: 'Reparación automática lenta del robot.',
                    cost: 2000,
                    unlocked: false,
                    dependencies: ['efficiency_1'],
                    icon: '🦠',
                    position: { x: 100, y: 1900 },
                    modifiers: { passiveRecharge: 2 } // Simulating repair as energy for now
                },
                'diamond_drill': {
                    id: 'diamond_drill',
                    name: 'Taladro de Diamante',
                    description: 'Permite extraer recursos raros de rocas duras.',
                    cost: 3000,
                    unlocked: false,
                    dependencies: ['nanobots', 'drill_arm'],
                    icon: '💎',
                    position: { x: 100, y: 2300 },
                    modifiers: { drillEfficiency: 2.0 }
                },
                'self_repair': {
                    id: 'self_repair',
                    name: 'Auto-Reparación',
                    description: 'Reduce el coste de mantenimiento.',
                    cost: 5000,
                    unlocked: false,
                    dependencies: ['nanobots'],
                    icon: '🔧',
                    position: { x: 100, y: 2700 },
                    modifiers: { energyCostMultiplier: 0.8 }
                },

                // === RAMA CUÁNTICA (CENTRO - MUY ARRIBA) ===
                'quantum_processor': {
                    id: 'quantum_processor',
                    name: 'Procesador Cuántico',
                    description: 'Velocidad de procesamiento instantánea.',
                    cost: 5000,
                    unlocked: false,
                    dependencies: ['ai_core'],
                    icon: '💻',
                    position: { x: 1600, y: 700 },
                    modifiers: { scriptTimeout: 5000 }
                },
                'entanglement_comms': {
                    id: 'entanglement_comms',
                    name: 'Com. de Entrelazamiento',
                    description: 'Control de drones sin latencia ni límite de distancia.',
                    cost: 8000,
                    unlocked: false,
                    dependencies: ['quantum_processor'],
                    icon: '🔗',
                    position: { x: 2100, y: 700 },
                    modifiers: { viewDistance: 100 } // Infinite view
                },
                'teleport_gate': {
                    id: 'teleport_gate',
                    name: 'Portal de Teletransporte',
                    description: 'Teletransporte gratuito a cualquier punto visitado.',
                    cost: 15000,
                    unlocked: false,
                    dependencies: ['entanglement_comms', 'teleport_pad'],
                    icon: '⛩️',
                    position: { x: 2600, y: 700 },
                    unlocksFunction: 'teleport_anywhere'
                },

                // === RAMA ESPACIAL (DERECHA - MUY ARRIBA) ===
                'orbital_scanner': {
                    id: 'orbital_scanner',
                    name: 'Escáner Orbital',
                    description: 'Revela todo el mapa permanentemente.',
                    cost: 10000,
                    unlocked: false,
                    dependencies: ['optical_sensors', 'quantum_processor'],
                    icon: '🛰️',
                    position: { x: 2100, y: 1100 },
                    unlocksFunction: 'reveal_map'
                },
                'satellite_link': {
                    id: 'satellite_link',
                    name: 'Enlace Satelital',
                    description: 'Aumenta las ganancias de mercado un 50%.',
                    cost: 12000,
                    unlocked: false,
                    dependencies: ['orbital_scanner', 'market_api'],
                    icon: '📡',
                    position: { x: 2600, y: 1100 },
                    modifiers: { sellPriceMultiplier: 1.5 }
                },
                'rocket_silo': {
                    id: 'rocket_silo',
                    name: 'Silo de Cohetes',
                    description: 'Permite exportar recursos a otros planetas (Win Condition).',
                    cost: 50000,
                    unlocked: false,
                    dependencies: ['satellite_link', 'fusion_core'],
                    icon: '🚀',
                    position: { x: 3200, y: 1100 },
                    unlocksFunction: 'launch_rocket'
                },

                // === RAMA DE TERRAFORMACIÓN (CENTRO - MUY ABAJO) ===
                'climate_control': {
                    id: 'climate_control',
                    name: 'Control Climático',
                    description: 'Optimiza el crecimiento de las plantas al máximo.',
                    cost: 5000,
                    unlocked: false,
                    dependencies: ['gmo_seeds'],
                    icon: '🌦️',
                    position: { x: 2600, y: 1900 },
                    modifiers: { growthSpeedMultiplier: 2.0 }
                },
                'terraformer_droid': {
                    id: 'terraformer_droid',
                    name: 'Dron Terraformador',
                    description: 'Puede convertir roca en suelo fértil.',
                    cost: 8000,
                    unlocked: false,
                    dependencies: ['climate_control', 'drill_arm'],
                    icon: '🌍',
                    position: { x: 3100, y: 1900 },
                    unlocksFunction: 'terraform'
                },
                'atmosphere_gen': {
                    id: 'atmosphere_gen',
                    name: 'Gen. de Atmósfera',
                    description: 'Recarga de energía pasiva masiva para todos los robots.',
                    cost: 12000,
                    unlocked: false,
                    dependencies: ['terraformer_droid'],
                    icon: '🌫️',
                    position: { x: 3600, y: 1900 },
                    modifiers: { passiveRecharge: 5 }
                }
    });

    const activeRobot = ref('basic'); // basic, scout, harvester

    const setActiveRobot = (robotType) => {
        activeRobot.value = robotType;
    };

    const canUnlock = (techId, currentMoney) => {
        const tech = technologies.value[techId];
        if (!tech || tech.unlocked) return false;
        if (currentMoney < tech.cost) return false;
        
        // Check dependencies
        if (tech.dependencies.length === 0) return true;
        return tech.dependencies.every(depId => technologies.value[depId] && technologies.value[depId].unlocked);
    };

    const unlockTech = (techId) => {
        if (technologies.value[techId]) {
            technologies.value[techId].unlocked = true;
        }
    };

    return {
        technologies,
        activeRobot,
        setActiveRobot,
        canUnlock,
        unlockTech
    };
});
