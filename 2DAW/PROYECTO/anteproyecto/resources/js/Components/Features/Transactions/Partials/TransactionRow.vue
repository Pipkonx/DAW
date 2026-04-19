<script setup>
import { computed } from 'vue';
import { formatCurrency } from '@/Utils/formatting';

/**
 * TransactionRow - Unidad visual de operación contable.
 * 
 * Renderiza los detalles de una transacción: tipo, activos, importes y fechas.
 * Soporta selección múltiple y modo privacidad.
 */
const props = defineProps({
    tx: Object,
    isSelected: Boolean,
    isPrivacyMode: Boolean,
});

const emit = defineEmits(['toggle', 'edit']);

/**
 * Formatea la fecha en formato corto (DD.MM).
 */
const getShortDate = (dateStr) => {
    const date = new Date(dateStr);
    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    return `${day}.${month}`;
};

/**
 * Genera el texto descriptivo de la acción según el tipo de activo y cantidad.
 */
const getActionText = (tx) => {
    if (props.isPrivacyMode) {
         switch (tx.type) {
            case 'buy': return `Compra realizada`;
            case 'sell': return `Venta realizada`;
            case 'dividend': return `Dividendo ingresado`;
            case 'reward': return `Recompensa abonada`;
            case 'gift': return `Regalo recibido`;
            default: return `${tx.description || tx.type}`;
        }
    }

    const qty = parseFloat(tx.quantity);
    const price = formatCurrency(tx.price_per_unit);
    
    switch (tx.type) {
        case 'buy': return `Compró ${qty} a ${price}`;
        case 'sell': return `Vendió ${qty} a ${price}`;
        case 'dividend': return `Dividendo recibido`;
        case 'reward': return `Recompensa recibida`;
        case 'gift': return `Regalo recibido`;
        default: return null;
    }
};

/**
 * Determina el subtítulo o categoría de la operación.
 */
const getSubtitle = (tx) => {
    const actionText = getActionText(tx);
    if (actionText) return actionText;

    if (!tx.asset && tx.description) {
        const typeLabels = {
            'income': 'Ingreso Directo',
            'expense': 'Gasto / Pago',
            'transfer_in': 'Transferencia Entrante',
            'transfer_out': 'Transferencia Saliente',
            'buy': 'Compra',
            'sell': 'Venta'
        };
        return typeLabels[tx.type] || tx.type;
    }
    
    return tx.description || tx.type;
};

/**
 * Retorna las clases de color según el impacto de la operación en el balance.
 */
const getTypeIconClasses = (type) => {
    switch (type) {
        case 'buy': return 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400';
        case 'sell': return 'bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400';
        case 'dividend': 
        case 'income':
        case 'transfer_in':
        case 'gift':
        case 'reward':
            return 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400';
        case 'expense':
        case 'transfer_out':
            return 'bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400';
        default: return 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400';
    }
};

/**
 * Retorna la ruta SVG (path) del icono representativo.
 */
const getTypeIconSvgPath = (type) => {
     switch (type) {
        case 'buy': return 'M12 4v16m8-8H4';
        case 'sell': return 'M20 12H4';
        case 'dividend': return 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
        case 'income':
        case 'transfer_in':
        case 'gift':
        case 'reward':
            return 'M19 14l-7 7m0 0l-7-7m7 7V3';
        case 'expense':
        case 'transfer_out':
            return 'M5 10l7-7m0 0l7 7m-7-7v18';
        default: return 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
    }
};

/**
 * Clase de color para el importe (Negativo/Positivo).
 */
const getAmountClass = (type) => {
    switch (type) {
        case 'income':
        case 'transfer_in':
        case 'dividend':
        case 'gift':
        case 'reward':
        case 'sell':
            return 'text-emerald-600 dark:text-emerald-400';
        case 'expense':
        case 'transfer_out':
        case 'buy':
            return 'text-rose-600 dark:text-rose-400';
        default:
            return 'text-slate-800 dark:text-white';
    }
};
</script>

<template>
    <tr @click="emit('edit', tx)" class="hover:bg-slate-50 transition-colors dark:hover:bg-slate-700/50 cursor-pointer group border-b border-slate-50 dark:border-slate-700/50 last:border-0">
        <!-- Selector checkbox -->
        <td class="pl-6 py-4 w-10 align-middle" @click.stop>
            <input 
                type="checkbox" 
                :checked="isSelected"
                @change="emit('toggle', tx.id)"
                class="rounded-lg border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500/20 transition-all cursor-pointer"
            />
        </td>

        <!-- Identidad de la Transacción -->
        <td class="px-4 py-4">
            <div class="flex items-center gap-4">
                <!-- Fecha Corta -->
                <span class="text-[10px] font-black text-slate-400 w-10 uppercase tracking-tighter">{{ getShortDate(tx.date) }}</span>
                
                <!-- Avatar del Activo o Icono de Tipo -->
                <div v-if="tx.asset && tx.asset.logo" class="w-10 h-10 rounded-full overflow-hidden bg-white dark:bg-slate-700 flex items-center justify-center shrink-0 shadow-sm border border-slate-100 dark:border-slate-600">
                    <img :src="tx.asset.logo" class="w-full h-full object-cover" @error="tx.asset.logo = null" />
                </div>
                <div v-else :class="getTypeIconClasses(tx.type)" class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 border border-current opacity-70">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getTypeIconSvgPath(tx.type)" />
                    </svg>
                </div>
                
                <!-- Títulos -->
                <div class="flex flex-col min-w-0">
                    <span class="font-black text-slate-900 dark:text-white text-sm truncate uppercase tracking-tight">
                        {{ tx.asset ? (tx.asset.name || tx.asset.ticker) : (tx.description || tx.category || 'Operación Sin Nombre') }}
                    </span>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest mt-0.5 truncate">
                        {{ getSubtitle(tx) }}
                    </span>
                </div>
            </div>
        </td>

        <!-- Importe Monetario -->
        <td class="px-6 py-4 text-right align-middle">
            <span class="font-black block text-sm tracking-tight" :class="getAmountClass(tx.type)">
                {{ isPrivacyMode ? '••••••' : formatCurrency(tx.amount) }}
            </span>
        </td>
    </tr>
</template>
