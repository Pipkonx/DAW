import { watch } from 'vue';

/**
 * useTransactionCalculator - Composable para cálculos de transacciones.
 * 
 * Gestiona la relación matemática entre:
 * Importe Total = (Cantidad * Precio) +/- (Comisiones + Impuestos)
 */
export function useTransactionCalculator(form, lastEditedField) {

    /**
     * Calcula la CANTIDAD basándose en el IMPORTE ingresado.
     * Cantidad = Importe / Precio
     */
    const calculateFromAmount = () => {
        if (!form.price_per_unit || form.price_per_unit <= 0) return;
        if (!form.amount) {
            form.quantity = '';
            return;
        }
        form.quantity = parseFloat((form.amount / form.price_per_unit).toFixed(8));
    };

    /**
     * Calcula el IMPORTE basándose en la CANTIDAD ingresada.
     * Importe = (Cantidad * Precio) + Comisiones (si es compra/gasto)
     * Importe = (Cantidad * Precio) - Comisiones (si es venta/dividendo)
     */
    const calculateFromQuantity = () => {
        if (!form.price_per_unit || form.price_per_unit <= 0) return;
        if (!form.quantity) {
            form.amount = '';
            return;
        }

        let baseValue = form.quantity * form.price_per_unit;
        
        const fees = parseFloat(form.fees || 0);
        const exchangeFees = parseFloat(form.exchange_fees || 0);
        const tax = parseFloat(form.tax || 0);
        const totalCosts = fees + exchangeFees + tax;

        if (['buy', 'expense'].includes(form.type)) {
            // El coste total aumenta el importe a pagar
            form.amount = parseFloat((baseValue + totalCosts).toFixed(2));
        } else {
            // El coste total reduce el importe neto recibido (venta, dividendos)
            form.amount = parseFloat((baseValue - totalCosts).toFixed(2));
        }
    };

    /**
     * Recalcula según cuál haya sido el último campo editado por el usuario.
     */
    const calculateFromPrice = () => {
        if (!form.price_per_unit) return;
        
        if (lastEditedField.value === 'quantity' && form.quantity) {
            calculateFromQuantity();
        } else if (lastEditedField.value === 'amount' && form.amount) {
            calculateFromAmount();
        } else if (form.quantity) {
            calculateFromQuantity();
        }
    };

    /**
     * Inicializar Vigilantes (Watchers) para reactividad automática.
     */
    const initWatchers = () => {
        // Al editar importe directamente
        watch(() => form.amount, () => {
            if (lastEditedField.value === 'amount') calculateFromAmount();
        });

        // Al editar cantidad directamente
        watch(() => form.quantity, () => {
            if (lastEditedField.value === 'quantity') calculateFromQuantity();
        });

        // Al cambiar precio (manual o por API)
        watch(() => form.price_per_unit, () => {
            calculateFromPrice();
        });

        // Al cambiar costes secundarios (comisiones/tasas)
        watch(() => [form.fees, form.exchange_fees, form.tax], () => {
            if (form.quantity && form.price_per_unit) calculateFromQuantity();
        });
    };

    return {
        calculateFromAmount,
        calculateFromQuantity,
        calculateFromPrice,
        initWatchers
    };
}
