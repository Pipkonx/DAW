import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { TILE_TYPES } from '../game/Constants';

export const useTutorialStore = defineStore('tutorial', () => {
    const currentMissionIndex = ref(0);
    const completedMissions = ref([]);
    const totalHarvested = ref(0);
    const totalPlanted = ref(0);
    const totalMoneySpent = ref(0);
    
    const missions = [
        {
            id: 'mission_1',
            title: 'Primeros Pasos',
            description: 'Aprende a moverte: Llega a la posición (3,0).',
            reward: 50,
            check: (engine) => engine.robot.x >= 3,
            completed: false
        },
        {
            id: 'mission_2',
            title: 'Agricultor Novato',
            description: 'Planta tu primera semilla.',
            reward: 100,
            check: (engine) => {
                for(let x=0; x<12; x++) {
                    for(let y=0; y<12; y++) {
                        if (engine.grid.getCell(x,y) === TILE_TYPES.PLANT) return true;
                    }
                }
                return false;
            },
            completed: false
        },
        {
            id: 'mission_3',
            title: 'Granja en Expansión',
            description: 'Ten 5 plantas creciendo al mismo tiempo.',
            reward: 200,
            check: (engine) => {
                let count = 0;
                for(let x=0; x<12; x++) {
                    for(let y=0; y<12; y++) {
                        if (engine.grid.getCell(x,y) === TILE_TYPES.PLANT) count++;
                    }
                }
                return count >= 5;
            },
            completed: false
        },
        {
            id: 'mission_4',
            title: 'Cosecha Dorada',
            description: 'Consigue tus primeras 10 maderas.',
            reward: 500,
            check: (engine, resources) => resources.wood >= 10,
            completed: false
        },
        {
            id: 'mission_5',
            title: 'Automatización Básica',
            description: 'Compra tu primera mejora en el árbol de tecnologías.',
            reward: 1000,
            check: (engine, resources, techStore) => {
                return Object.values(techStore.technologies).some(t => t.unlocked);
            },
            completed: false
        },
        {
            id: 'mission_6',
            title: 'Magnate Agrícola',
            description: 'Consigue 5000 monedas totales.',
            reward: 2000,
            check: (engine, resources) => resources.money >= 5000,
            completed: false
        },
        {
            id: 'mission_7',
            title: 'Bosque Artificial',
            description: 'Ten 20 plantas creciendo simultáneamente.',
            reward: 3000,
            check: (engine) => {
                let count = 0;
                for(let x=0; x<12; x++) {
                    for(let y=0; y<12; y++) {
                        if (engine.grid.getCell(x,y) === TILE_TYPES.PLANT) count++;
                    }
                }
                return count >= 20;
            },
            completed: false
        },
        {
            id: 'mission_8',
            title: 'Maestro de la Eficiencia',
            description: 'Desbloquea 10 tecnologías diferentes.',
            reward: 5000,
            check: (engine, resources, techStore) => {
                return Object.values(techStore.technologies).filter(t => t.unlocked).length >= 10;
            },
            completed: false
        }
    ];

    const achievements = ref([
        { id: 'ach_1', title: 'Pionero', description: 'Realiza tu primer movimiento con el robot.', icon: '👣', condition: (res, stats) => true, unlocked: false },
        { id: 'ach_2', title: 'Capitalista', description: 'Consigue 1.000 monedas.', icon: '💰', condition: (res, stats) => res.money >= 1000, unlocked: false },
        { id: 'ach_3', title: 'Hortelano', description: 'Planta 50 semillas.', icon: '🌱', condition: (res, stats) => stats.planted >= 50, unlocked: false },
        { id: 'ach_4', title: 'Leñador', description: 'Cosecha 100 de madera.', icon: '🪵', condition: (res, stats) => stats.harvested >= 100, unlocked: false },
        { id: 'ach_5', title: 'Inversor', description: 'Gasta 5.000 monedas en tecnologías.', icon: '📈', condition: (res, stats) => stats.spent >= 5000, unlocked: false },
        { id: 'ach_6', title: 'Magnate', description: 'Consigue 100.000 monedas.', icon: '💎', condition: (res, stats) => res.money >= 100000, unlocked: false },
        { id: 'ach_7', title: 'Automatización Total', description: 'Desbloquea todas las mejoras de velocidad.', icon: '⚡', condition: (res, stats, tech) => tech.technologies['speed_2']?.unlocked, unlocked: false }
    ]);

    const activeMission = computed(() => missions[currentMissionIndex.value] || { title: 'Modo Libre', description: '¡Sigue mejorando tu granja!' });

    function completeMission(resources, techStore, engine) {
        const mission = missions[currentMissionIndex.value];
        if (mission && !completedMissions.value.includes(mission.id)) {
            if (mission.check(engine, resources, techStore)) {
                completedMissions.value.push(mission.id);
                resources.money += mission.reward;
                currentMissionIndex.value++;
                return mission;
            }
        }
        return null;
    }

    function checkAchievements(resources, techStore) {
        const stats = {
            planted: totalPlanted.value,
            harvested: totalHarvested.value,
            spent: totalMoneySpent.value
        };
        
        achievements.value.forEach(ach => {
            if (!ach.unlocked && ach.condition(resources, stats, techStore)) {
                ach.unlocked = true;
            }
        });
    }

    return {
        missions,
        activeMission,
        currentMissionIndex,
        completedMissions,
        achievements,
        completeMission,
        checkAchievements,
        totalHarvested,
        totalPlanted,
        totalMoneySpent
    };
});
