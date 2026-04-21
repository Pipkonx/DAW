<script setup>
import { toRefs } from 'vue';
import { router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/BaseUI/PrimaryButton.vue';
import ModalConfirm from '@/Components/BaseUI/ModalConfirm.vue';
import AssetRow from './Partials/AssetRow.vue';
import { useAssetTable } from '@/Composables/useAssetTable';
import { usePrivacy } from '@/Composables/usePrivacy';
import { ref } from 'vue';

const props = defineProps({
    assets: { type: Array, required: true },
    selectedAssetId: { type: [String, Number, Array], default: null }
});

const emit = defineEmits(['filter-asset', 'add-transaction', 'delete-asset']);

const { assets } = toRefs(props);
const { isPrivacyMode } = usePrivacy();
const {
    bulkSelectedAssets,
    selectedPeriod,
    periods,
    filteredAssets,
    toggleBulkSelection,
    toggleAllBulk,
    getAssetPerformance
} = useAssetTable(assets);

// Gestión local de Modales de Confirmación
const confirmModal = ref({ show: false, title: '', message: '', action: null });

const deleteBulkSelected = () => {
    confirmModal.value = {
        show: true,
        title: '¿Eliminar Activos?',
        message: `¿Estás seguro de que quieres eliminar ${bulkSelectedAssets.value.length} activos? Esto borrará TODAS las operaciones asociadas.`,
        action: () => {
            router.delete(route('assets.bulk-destroy'), {
                data: { ids: bulkSelectedAssets.value },
                onSuccess: () => {
                    bulkSelectedAssets.value = [];
                    confirmModal.value.show = false;
                }
            });
        }
    };
};

const onDeleteAsset = (asset) => {
    confirmModal.value = {
        show: true,
        title: '¿Eliminar Activo?',
        message: `¿Estás seguro de que quieres eliminar ${asset.name}? Esto borrará TODAS las operaciones asociadas.`,
        action: () => {
            emit('delete-asset', asset);
            confirmModal.value.show = false;
        }
    };
};

const isSelected = (assetId) => {
    if (!props.selectedAssetId) return false;
    const id = String(assetId);
    if (Array.isArray(props.selectedAssetId)) return props.selectedAssetId.map(String).includes(id);
    return String(props.selectedAssetId).split(',').map(s => s.trim()).includes(id);
};

</script>

<template>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden dark:bg-slate-800 dark:border-slate-700">
        <!-- Cabecera de la Tabla -->
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4 dark:border-slate-700">
            <div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Posiciones Activas</h3>
                <p class="text-sm text-slate-500 mt-1 dark:text-slate-400">Desglose detallado de tus activos actuales</p>
            </div>
            <div class="flex items-center gap-2">
                <button 
                    v-if="bulkSelectedAssets.length > 0"
                    @click="deleteBulkSelected"
                    class="px-4 py-2 text-sm font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors border border-rose-200 dark:bg-rose-900/20 dark:text-rose-400 dark:border-rose-900/50"
                >
                    Eliminar ({{ bulkSelectedAssets.length }})
                </button>
                
                <div class="flex bg-slate-100 dark:bg-slate-700 rounded-lg p-1 mr-2">
                    <button 
                        v-for="period in periods" :key="period.value"
                        @click="selectedPeriod = period.value"
                        class="px-3 py-1 text-xs font-medium rounded-md transition-all"
                        :class="selectedPeriod === period.value ? 'bg-white text-blue-600 shadow-sm dark:bg-slate-600 dark:text-white' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400'"
                    >
                        {{ period.label }}
                    </button>
                </div>

                <PrimaryButton @click="emit('add-transaction')">
                    + Añadir Transacción
                </PrimaryButton>
            </div>
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 dark:bg-slate-700 dark:text-slate-300">
                    <tr>
                        <th class="px-6 py-4 w-4">
                            <input 
                                type="checkbox" 
                                :checked="filteredAssets.length > 0 && filteredAssets.every(a => bulkSelectedAssets.includes(a.id))"
                                @change="toggleAllBulk"
                                class="rounded border-slate-300 text-blue-600"
                            />
                        </th>
                        <th class="px-6 py-4">Activo</th>
                        <th class="px-6 py-4 text-right">Precio / Valor</th>
                        <th class="px-6 py-4 text-right">Retorno</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    <AssetRow 
                        v-for="asset in filteredAssets" 
                        :key="asset.id"
                        :asset="asset"
                        :is-selected="isSelected(asset.id)"
                        :is-bulk-selected="bulkSelectedAssets.includes(asset.id)"
                        :is-privacy-mode="isPrivacyMode"
                        :performance="getAssetPerformance(asset)"
                        @toggle-bulk="toggleBulkSelection"
                        @filter="emit('filter-asset', $event)"
                        @delete="onDeleteAsset"
                    />
                    <tr v-if="filteredAssets.length === 0">
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <div class="flex flex-col items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-300 dark:text-slate-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-lg font-medium text-slate-900 dark:text-white">No hay activos en esta cartera</p>
                                <button @click="emit('add-transaction')" class="mt-4 text-blue-600 font-medium hover:text-blue-700">
                                    + Añadir Transacción
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <ModalConfirm
            :show="confirmModal.show"
            :title="confirmModal.title"
            :message="confirmModal.message"
            confirm-text="Confirmar"
            type="danger"
            @confirm="confirmModal.action"
            @cancel="confirmModal.show = false"
        />
    </div>
</template>
