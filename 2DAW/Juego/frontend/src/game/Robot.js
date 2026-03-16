export class Robot {
    constructor(id, x, y, type = 'basic') {
        this.id = id;
        this.x = x;
        this.y = y;
        this.type = type;
        this.inventory = {};
        this.modules = []; // Unlockable modules
        
        // Base stats
        this.maxEnergy = 100;
        this.energy = 100;
        
        // Type specific stats
        this.stats = this.getStats(type);
    }
    
    getStats(type) {
        const stats = {
            basic: { 
                speed: 1, 
                capacity: 10, 
                energyRate: 1.0,
                harvestRate: 1.0
            },
            scout: { 
                speed: 2, 
                capacity: 5, 
                energyRate: 0.8, // 20% less energy for move
                harvestRate: 0.5
            },
            harvester: { 
                speed: 0.8, 
                capacity: 25, 
                energyRate: 1.2,
                harvestRate: 2.0 // Double harvest yield
            }
        };
        return stats[type] || stats.basic;
    }
}
