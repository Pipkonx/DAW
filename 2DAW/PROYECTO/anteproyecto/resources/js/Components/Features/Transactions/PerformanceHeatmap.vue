<script setup>
import { formatCurrency } from '@/Utils/formatting';

/**
 * Componente de Mapa de Calor (Heatmap) para visualizar rendimientos mensuales por año.
 */
defineProps({
    heatmap: Array
});

/**
 * Lógica de color para las celdas del heatmap según el valor de rendimiento.
 */
const getHeatmapColor = (value) => {
    if (value === 0) return 'bg-slate-100 dark:bg-slate-800 text-slate-400';
    if (value > 0) {
        if (value > 500) return 'bg-emerald-500 text-white';
        if (value > 100) return 'bg-emerald-400 text-emerald-900';
        return 'bg-emerald-200 text-emerald-800';
    } else {
        if (value < -500) return 'bg-rose-500 text-white';
        if (value < -100) return 'bg-rose-400 text-rose-900';
        return 'bg-rose-200 text-rose-800';
    }
};

const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
</script>

<template>
    <div class="h-full overflow-x-auto">
        <div class="min-w-[800px] h-full flex flex-col">
            <!-- Cabecera de Meses -->
            <div class="grid gap-2 mb-2 font-medium text-xs text-slate-400 uppercase text-center tracking-wider" style="grid-template-columns: repeat(13, minmax(0, 1fr));">
                <div class="text-left font-bold pl-2">Año</div>
                <div v-for="month in months" :key="month">
                    {{ month }}
                </div>
            </div>

            <!-- Cuerpo del Heatmap -->
            <div class="flex-1 flex flex-col gap-2 overflow-y-auto pr-2 pb-2">
                <div v-for="row in heatmap" :key="row.year" class="grid gap-2 group" style="grid-template-columns: repeat(13, minmax(0, 1fr));">
                    <div class="font-bold text-slate-700 dark:text-slate-300 flex items-center pl-2">{{ row.year }}</div>
                    
                    <!-- Celdas Mensuales -->
                    <div v-for="(val, idx) in row.months" :key="idx" 
                        class="rounded-lg flex flex-col justify-center items-center h-14 transition-transform group-hover:scale-105"
                        :class="getHeatmapColor(val)"
                        :title="`Rendimiento: ${formatCurrency(val)}`"
                    >
                        <span v-if="val !== 0" class="text-[10px] font-bold opacity-90">{{ formatCurrency(val, 0) }}</span>
                        <span v-else class="text-xl font-bold opacity-40">-</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
