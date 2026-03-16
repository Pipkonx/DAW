import ApiService from './ApiService';

/**
 * Servicio de Juego.
 * Gestiona el progreso del usuario, desbloqueos y estado global del juego.
 */
export const GameService = {
    /**
     * Obtiene el progreso del jugador.
     * Incluye niveles desbloqueados, tecnologías y recursos.
     */
    async getProgress() {
        const response = await ApiService.get('/progress');
        return response.data;
    },

    /**
     * Guarda el progreso actual.
     * @param {Object} data - { levels, techTree, resources }
     */
    async saveProgress(data) {
        const response = await ApiService.post('/progress/save', data);
        return response.data;
    },

    /**
     * Carga un nivel específico desde el servidor.
     * @param {string} levelId - ID del nivel
     */
    async getLevel(levelId) {
        const response = await ApiService.get(`/levels/${levelId}`);
        return response.data;
    },

    /**
     * Registra un evento de juego (ej. inicio de nivel, error, completion).
     * @param {string} type - 'start', 'complete', 'fail'
     * @param {Object} payload - Datos adicionales
     */
    // eslint-disable-next-line no-unused-vars
    async logEvent(type, payload) {
        // Opcional: Para analíticas o logs de servidor
        // await ApiService.post('/events', { type, payload });
    }
};
