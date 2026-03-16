import { defineStore } from 'pinia';

export const useRandomEventStore = defineStore('randomEvents', {
    state: () => ({
        activeEvent: null,
        eventHistory: [],
        lastEventTime: 0,
        eventCooldown: 30000, // 30 seconds minimum between events
        chance: 0.3 // 30% chance to trigger when check happens
    }),
    actions: {
        tryTriggerEvent() {
            const now = Date.now();
            if (this.activeEvent) {
                // Check if expired
                if (now - this.activeEvent.startTime > this.activeEvent.duration) {
                    this.clearEvent();
                    return { type: 'expired' };
                }
                return null;
            }

            if (now - this.lastEventTime < this.eventCooldown) return null;

            if (Math.random() < this.chance) {
                const events = [
                    {
                        id: 'solar_flare',
                        name: 'Llamarada Solar',
                        description: '¡Radiación alta! El consumo de energía se duplica.',
                        duration: 10000, // 10s
                        modifiers: { energyCostMultiplier: 2.0 },
                        color: 'text-red-500',
                        bg: 'bg-red-100'
                    },
                    {
                        id: 'market_boom',
                        name: 'Auge del Mercado',
                        description: '¡Alta demanda! Los precios de venta se duplican.',
                        duration: 15000, // 15s
                        modifiers: { sellPriceMultiplier: 2.0 },
                        color: 'text-green-500',
                        bg: 'bg-green-100'
                    },
                    {
                        id: 'tech_glitch',
                        name: 'Fallo del Sistema',
                        description: 'Sistemas de movimiento comprometidos. Movimiento 50% más lento.',
                        duration: 8000,
                        modifiers: { moveSpeedMultiplier: 0.5 }, // Visual only maybe? Or delay in step?
                        color: 'text-yellow-500',
                        bg: 'bg-yellow-100'
                    },
                    {
                        id: 'bountiful_rain',
                        name: 'Lluvia Abundante',
                        description: 'Condiciones perfectas. La cosecha rinde +1 recurso extra.',
                        duration: 12000,
                        modifiers: { yieldBonus: 1 },
                        color: 'text-blue-500',
                        bg: 'bg-blue-100'
                    }
                ];
                
                const event = events[Math.floor(Math.random() * events.length)];
                this.triggerEvent(event);
                return { type: 'new', event };
            }
            
            return null;
        },

        triggerEvent(event) {
            this.activeEvent = {
                ...event,
                startTime: Date.now()
            };
            this.lastEventTime = Date.now();
            this.eventHistory.push(this.activeEvent);
        },

        clearEvent() {
            this.activeEvent = null;
        }
    }
});
