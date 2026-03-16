import { defineStore } from 'pinia';
import { RankingService } from '../services/RankingService';

export const useRankingStore = defineStore('ranking', {
    state: () => ({
        rankings: [],
        loading: false,
        error: null,
        userRank: null
    }),
    actions: {
        async fetchRankings(period = 'weekly') {
            this.loading = true;
            this.error = null;
            try {
                // Intentar cargar desde API real
                const data = await RankingService.getRankings(period);
                this.rankings = data.rankings;
                this.userRank = data.userRank;
            } catch (e) {
                console.warn("API no disponible, usando datos simulados (Mock).", e);
                
                // Fallback: Datos simulados para desarrollo sin backend
                await new Promise(resolve => setTimeout(resolve, 800));
                
                this.rankings = Array.from({ length: 10 }, (_, i) => ({
                    id: i + 1,
                    username: `Jugador_${Math.floor(Math.random() * 1000)}`,
                    score: Math.floor(10000 - i * 500 + Math.random() * 200),
                    efficiency: (95 - i * 2).toFixed(1) + '%'
                }));
                
                this.userRank = {
                    rank: 42,
                    score: 4500,
                    efficiency: '68.5%'
                };
            } finally {
                this.loading = false;
            }
        },
        
        async submitScore(scoreData) {
            try {
                await RankingService.submitScore(scoreData.score, scoreData.levelId);
                return true;
            } catch (e) {
                console.warn("Fallo al enviar puntuación (API no disponible).", e);
                return false; // O true si queremos simular éxito
            }
        }
    }
});
