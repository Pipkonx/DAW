<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    assets: {
        type: Array,
        required: true
    }
});

const isExpanded = ref(true);

const toggleExpand = () => {
    isExpanded.value = !isExpanded.value;
};

const resolveAsset = (asset) => {
    // Redirigir a la vista de transacciones filtrada por este activo para editarlo
    // O idealmente abrir un modal de edición rápida.
    // Por ahora, redirigimos a la vista de transacciones con el filtro de activo
    router.get(route('transactions.index'), { asset_id: asset.id });
};
</script>

<template>
    <div v-if="assets.length > 0" class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 border-l-4 border-amber-500">
        <div class="p-4">
            <div class="flex justify-between items-center cursor-pointer" @click="toggleExpand">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 class="text-lg font-medium text-slate-900 dark:text-slate-100">
                        Activos Pendientes de Vinculación ({{ assets.length }})
                    </h3>
                </div>
                <button class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform transition-transform" :class="{ 'rotate-180': isExpanded }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>
            
            <div v-if="isExpanded" class="mt-4">
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    Los siguientes activos fueron detectados pero no se pudieron vincular automáticamente con datos de mercado. 
                    Por favor, verifique el nombre o asigne un ISIN manualmente para obtener precios actualizados.
                </p>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-700/50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nombre Detectado</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nombre Original (OCR)</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                            <tr v-for="asset in assets" :key="asset.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-slate-100">
                                    {{ asset.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                    {{ asset.original_name || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                                        {{ asset.link_status === 'pending' ? 'Pendiente' : 'Fallido' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="resolveAsset(asset)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        Gestionar
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
