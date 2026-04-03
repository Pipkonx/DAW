<script setup>
import { formatCurrency, formatPercent, formatDate } from '@/Utils/formatting';

defineProps({
    userPosition: Object,
    latestTransactions: Array,
    isPrivacyMode: Boolean
});

defineEmits(['openModal']);
</script>

<template>
    <div class="space-y-8">
        <!-- Resumen de Cartera -->
        <div v-if="userPosition" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div v-for="(item, key) in [
                { label: 'Valor Actual', value: formatCurrency(userPosition.current_value), sub: userPosition.quantity + ' uds', color: 'text-slate-800 dark:text-white' },
                { label: 'Invertido', value: formatCurrency(userPosition.total_invested), sub: 'Coste Base', color: 'text-slate-600 dark:text-slate-300' },
                { label: 'Plusvalía', value: formatCurrency(userPosition.profit_loss), sub: formatPercent(userPosition.profit_loss_percentage), color: userPosition.profit_loss >= 0 ? 'text-emerald-500' : 'text-rose-500' },
                { label: 'Retorno Neto', value: formatCurrency(userPosition.total_return || userPosition.profit_loss), sub: 'Inc. Gastos', color: 'text-indigo-600 dark:text-indigo-400' },
                { label: 'Precio Medio', value: formatCurrency(userPosition.avg_buy_price), sub: 'WAC', color: 'text-slate-700 dark:text-slate-200' },
                { label: 'Impuestos', value: formatCurrency(userPosition.total_tax || 0), sub: 'Retenciones', color: 'text-rose-400' },
                { label: 'Gastos', value: formatCurrency(userPosition.total_fees || 0), sub: 'Comisiones', color: 'text-slate-400' }
            ]" :key="key" class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm group hover:border-indigo-200 transition-all">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 group-hover:text-indigo-500 transition-colors">{{ item.label }}</p>
                <p class="text-xl font-black truncate" :class="item.color">{{ isPrivacyMode ? '****' : item.value }}</p>
                <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-tighter">{{ isPrivacyMode ? '****' : item.sub }}</p>
            </div>
        </div>

        <!-- Sin Posiciones -->
        <div v-else class="bg-white dark:bg-slate-800/50 p-16 rounded-3xl text-center border-2 border-dashed border-slate-200 dark:border-slate-700">
            <div class="w-20 h-20 bg-white dark:bg-slate-800 rounded-3xl mx-auto flex items-center justify-center text-slate-300 mb-6 shadow-sm border border-slate-100 dark:border-slate-700">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <h4 class="text-2xl font-black text-slate-800 dark:text-white">Sin posición activa</h4>
            <p class="text-slate-500 text-sm mt-2 max-w-sm mx-auto font-medium">No tienes operaciones registradas para este activo. ¡Empieza hoy!</p>
            <button @click="$emit('openModal')" class="mt-8 bg-indigo-600 text-white font-black py-4 px-10 rounded-2xl shadow-xl hover:scale-[1.02] transition-all">Registrar Operación</button>
        </div>

        <!-- Listado de Transacciones -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-50 dark:border-slate-700/50 flex justify-between items-center">
                <h3 class="font-black text-slate-800 dark:text-white text-xs uppercase tracking-widest">Historial de Operaciones</h3>
                <div class="px-3 py-1 bg-slate-50 dark:bg-slate-900 rounded-lg text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ latestTransactions.length }} Movimientos</div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50 dark:bg-slate-900/50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <tr>
                            <th class="px-6 py-4">Fecha</th>
                            <th class="px-6 py-4">Tipo</th>
                            <th class="px-6 py-4 text-right">Cantidad</th>
                            <th class="px-6 py-4 text-right">Precio</th>
                            <th class="px-6 py-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                        <tr v-for="tx in latestTransactions" :key="tx.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-900/10 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-700 dark:text-slate-200">{{ formatDate(tx.date) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest" :class="tx.type === 'buy' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20' : 'bg-rose-50 text-rose-600 dark:bg-rose-900/20'">
                                    {{ tx.type === 'buy' ? 'Compra' : 'Venta' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-black text-slate-600 dark:text-slate-300">{{ tx.quantity }}</td>
                            <td class="px-6 py-4 text-right font-black text-slate-600 dark:text-slate-300">{{ formatCurrency(tx.price_per_unit) }}</td>
                            <td class="px-6 py-4 text-right font-black text-slate-800 dark:text-white">{{ formatCurrency(tx.amount) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
