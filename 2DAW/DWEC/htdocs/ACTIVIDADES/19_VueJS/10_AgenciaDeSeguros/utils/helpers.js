/**
 * Funciones utilitarias y Datos Geográficos
 */
const Helpers = {
    /**
     * Formatea una fecha de forma estandarizada
     */
    formatDate(date) {
        if (!date) return '';
        return new Date(date).toLocaleDateString('es-ES');
    }
};
