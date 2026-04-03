/**
 * Utilidades de Formateo Global
 * 
 * Este archivo centraliza la lógica de presentación de datos para asegurar
 * consistencia en toda la aplicación y facilitar el mantenimiento (DRY).
 */

/**
 * Formatea un valor numérico como moneda Euro (€).
 * 
 * @param {number|string} value - El valor a formatear.
 * @returns {string} El valor formateado (ej: 1.250,50 €).
 */
export const formatCurrency = (value) => {
    const num = Number(value);
    if (isNaN(num)) return '0,00 €';
    
    return new Intl.NumberFormat('es-ES', { 
        style: 'currency', 
        currency: 'EUR',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(num);
};

/**
 * Formatea un valor numérico como porcentaje con símbolo %.
 * 
 * @param {number|string} value - El valor (ej: 12.5).
 * @param {number} decimals - Cantidad de decimales (por defecto 2).
 * @returns {string} El valor formateado (ej: 12,50 %).
 */
export const formatPercent = (value, decimals = 2) => {
    const num = Number(value);
    if (isNaN(num)) return '0,00%';
    
    const formatted = new Intl.NumberFormat('es-ES', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(num);
    
    return `${formatted}%`;
};

/**
 * Determina la clase CSS de color según si un valor es positivo o negativo.
 * Útil para indicadores de plusvalía/minusvalía.
 * 
 * @param {number|string} value 
 * @returns {string} Clase de Tailwind para el color.
 */
export const getTrendColor = (value) => {
    const num = Number(value);
    if (num > 0) return 'text-emerald-600 dark:text-emerald-400';
    if (num < 0) return 'text-rose-600 dark:text-rose-400';
    return 'text-slate-500 dark:text-slate-400';
};

/**
 * Determina la clase CSS de fondo suave según la tendencia.
 * 
 * @param {number|string} value 
 * @returns {string} Clase de Tailwind para el fondo.
 */
export const getTrendBg = (value) => {
    const num = Number(value);
    if (num >= 0) return 'bg-emerald-50 dark:bg-emerald-900/20';
    return 'bg-rose-50 dark:bg-rose-900/20';
};

/**
 * Formatea una fecha ISO o string a formato legible d.m.y o personalizado.
 * 
 * @param {string} dateStr 
 * @returns {string} Fecha formateada (ej: 02.04.2024).
 */
export const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return dateStr;
    
    return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).replace(/\//g, '.');
};
