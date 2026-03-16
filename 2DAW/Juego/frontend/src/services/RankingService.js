import ApiService from './ApiService';

/**
 * Servicio de Ranking y Puntuaciones.
 * Gestiona la obtención de rankings globales y personales.
 */
export const RankingService = {
    /**
     * Obtiene el ranking global.
     * @param {string} period - 'weekly', 'monthly', 'all_time'
     */
    async getRankings(period = 'weekly') {
        // Ejemplo de llamada: GET /api/rankings?period=weekly
        const response = await ApiService.get('/rankings', { params: { period } });
        return response.data;
    },

    /**
     * Envía una nueva puntuación al servidor.
     * @param {number} score - Puntuación obtenida
     * @param {string} levelId - Identificador del nivel
     */
    async submitScore(score, levelId) {
        const response = await ApiService.post('/scores', { score, level_id: levelId });
        return response.data;
    },

    /**
     * Obtiene la posición actual del usuario.
     */
    async getUserRank() {
        const response = await ApiService.get('/user/rank');
        return response.data;
    }
};
