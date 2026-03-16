import { describe, it, expect, beforeEach } from 'vitest';
import { GameEngine } from './GameEngine';
import { DIRECTIONS, TILE_TYPES } from './Constants';

describe('GameEngine', () => {
    let engine;

    beforeEach(() => {
        engine = new GameEngine(10, 10);
    });

    it('initializes correctly', () => {
        expect(engine.grid.width).toBe(10);
        expect(engine.grid.height).toBe(10);
        expect(engine.robot.x).toBe(0);
        expect(engine.robot.y).toBe(0);
        expect(engine.robot.energy).toBe(100);
    });

    it('handles simulation API calls correctly', () => {
        const api = engine.getApi();
        
        // Test move
        const result = api.move(DIRECTIONS.RIGHT, 1);
        expect(result).toBe(true);
        expect(engine.simulationRobot.x).toBe(1);
        expect(engine.actionQueue.length).toBe(1);
        expect(engine.actionQueue[0]).toEqual({ type: 'move', dir: DIRECTIONS.RIGHT, line: 1 });
    });

    it('prevents invalid moves in simulation', () => {
        const api = engine.getApi();
        
        // Try to move left from 0,0 (invalid)
        const result = api.move(DIRECTIONS.LEFT, 1);
        expect(result).toBe(false);
        expect(engine.simulationRobot.x).toBe(0);
        expect(engine.actionQueue[0].type).toBe('error');
    });

    it('executes steps correctly', () => {
        // Queue a move action
        engine.actionQueue.push({ type: 'move', dir: DIRECTIONS.RIGHT, line: 1 });
        
        // Initial state
        const initialEnergy = engine.robot.energy;
        
        // Execute step
        const action = engine.step();
        
        expect(action.type).toBe('move');
        expect(engine.robot.x).toBe(1);
        expect(engine.robot.energy).toBeLessThan(initialEnergy);
    });

    it('handles planting and harvesting', () => {
        const api = engine.getApi();
        
        // Plant
        api.plant(1);
        expect(engine.simulationGrid.getCell(0, 0)).toBe(TILE_TYPES.PLANT);
        
        // Harvest
        api.harvest(2);
        expect(engine.simulationGrid.getCell(0, 0)).toBe(TILE_TYPES.SOIL);
        
        // Verify queue
        expect(engine.actionQueue.length).toBe(2);
        expect(engine.actionQueue[0].type).toBe('plant');
        expect(engine.actionQueue[1].type).toBe('harvest');
        
        // Execute steps
        engine.step(); // plant
        expect(engine.grid.getCell(0, 0)).toBe(TILE_TYPES.PLANT);
        
        engine.step(); // harvest
        expect(engine.grid.getCell(0, 0)).toBe(TILE_TYPES.SOIL);
        expect(engine.resources.wood).toBeGreaterThan(0);
    });
});
