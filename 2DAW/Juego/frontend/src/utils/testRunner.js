import { GameEngine } from '../game/GameEngine';
import { runPython } from './pyodide';

export async function runTest(code, testCase, techConfig = {}) {
    // 1. Create isolated engine
    const engine = new GameEngine(12, 12, techConfig);
    
    // 2. Setup test scenario
    if (testCase.setup) {
        testCase.setup(engine);
    }
    
    // 3. Run user code
    // This populates actionQueue
    try {
        const result = await runPython(code, engine.getApi());
        
        if (result && !result.success) {
            return { 
                passed: false, 
                message: `Runtime Error: ${result.message}` 
            };
        }
    } catch (e) {
        return { passed: false, message: `Execution Error: ${e.message}` };
    }
    
    // 4. Execute actions (Fast Forward)
    let steps = 0;
    const maxSteps = 1000; // Safety limit
    
    while (engine.actionQueue.length > 0 && steps < maxSteps) {
        const action = engine.step();
        if (action && action.type === 'error') {
            return { passed: false, message: `Runtime Error: ${action.message}` };
        }
        steps++;
    }
    
    if (steps >= maxSteps) {
        return { passed: false, message: "Timeout: Too many steps." };
    }
    
    // 5. Assert
    try {
        const passed = testCase.assert(engine);
        return { 
            passed, 
            message: passed ? "Test Passed" : (testCase.failureMessage || "Assertion failed") 
        };
    } catch (e) {
        return { passed: false, message: `Assertion Error: ${e.message}` };
    }
}
