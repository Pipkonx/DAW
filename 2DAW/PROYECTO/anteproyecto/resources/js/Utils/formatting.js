export const formatCurrency = (value, currency = 'EUR', locale = 'es-ES') => {
    if (value === undefined || value === null || isNaN(value)) {
        return new Intl.NumberFormat(locale, { style: 'currency', currency }).format(0);
    }
    return new Intl.NumberFormat(locale, { style: 'currency', currency }).format(value);
};

export const formatPercent = (value, locale = 'es-ES') => {
    if (value === undefined || value === null || isNaN(value)) return '0,00%';
    // Si el valor ya viene como porcentaje (ej: 15.5), dividir por 100 si es necesario, 
    // pero usualmente formatPercent espera 0.155 para 15.5%.
    // En el código original se dividía por 100: value / 100. Asumiremos que entra como entero/float directo (15.5)
    return new Intl.NumberFormat(locale, { style: 'percent', minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value / 100);
};

export const formatDate = (dateString, locale = 'es-ES', options = { year: 'numeric', month: 'short', day: 'numeric' }) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString(locale, options);
};

export const formatCompactNumber = (value, locale = 'es-ES') => {
    return new Intl.NumberFormat(locale, { notation: "compact", compactDisplay: "short" }).format(value);
};
