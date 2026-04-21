<script setup>
import { computed, ref, onMounted } from 'vue';
import { useTransactionForm } from '@/Composables/useTransactionForm';

// Componentes Base y Partials
import Modal from '@/Components/BaseUI/Modal.vue';
import InputLabel from '@/Components/BaseUI/InputLabel.vue';
import TextInput from '@/Components/BaseUI/TextInput.vue';
import InputError from '@/Components/BaseUI/InputError.vue';
import PrimaryButton from '@/Components/BaseUI/PrimaryButton.vue';
import SecondaryButton from '@/Components/BaseUI/SecondaryButton.vue';
import DangerButton from '@/Components/BaseUI/DangerButton.vue';
import AssetSearchInput from './Partials/AssetSearchInput.vue';
import CategorySelector from './Partials/CategorySelector.vue';
import InvestmentFields from './Partials/InvestmentFields.vue';
import ModalConfirm from '@/Components/BaseUI/ModalConfirm.vue';

const props = defineProps({
    show: Boolean,
    transaction: Object,
    portfolios: Array,
    categories: Array,
    defaultPortfolioId: [String, Number],
    allowedTypes: Array
});

const emit = defineEmits(['close']);

// Delegar lógica al composable
const {
    form,
    isFetchingPrice,
    priceSource,
    lastEditedField,
    isFormLoading,
    initForm,
    fetchPrice,
    submit,
    confirmDelete
} = useTransactionForm(props, emit);

const showDeleteConfirm = ref(false);

const handleAssetSelect = (asset) => {
    form.asset_name = asset.ticker;
    form.asset_full_name = asset.name;
    form.asset_type = asset.type;
    form.market_asset_id = asset.id;
    form.isin = asset.isin || '';
    form.currency_code = asset.currency_code || 'EUR';
    fetchPrice();
};

const handleDeleteClick = () => {
    showDeleteConfirm.value = true;
};

const handleConfirmDelete = () => {
    confirmDelete();
    showDeleteConfirm.value = false;
};

onMounted(() => { if (props.show) initForm(); });

const isInvestment = computed(() => ['buy', 'sell', 'dividend', 'reward'].includes(form.type));
const types = [
    { value: 'income', label: 'Ingreso' }, { value: 'expense', label: 'Gasto' },
    { value: 'buy', label: 'Compra Activo' }, { value: 'sell', label: 'Venta Activo' },
    { value: 'dividend', label: 'Dividendo' }, { value: 'reward', label: 'Recompensa' },
    { value: 'gift', label: 'Regalo' },
];
const availableTypes = computed(() => props.allowedTypes?.length ? types.filter(t => props.allowedTypes.includes(t.value)) : types);

</script>

<template>
    <Modal :show="show && !showDeleteConfirm" @close="emit('close')">
        <div class="transition-all duration-300 relative p-8 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100"
            :class="{ 'opacity-0 scale-95 pointer-events-none': form.processing }">
            
            <h2 class="text-xl font-black border-b-2 dark:border-slate-700 pb-3 mb-6 uppercase tracking-wider text-blue-600 dark:text-blue-400">
                {{ transaction?.id ? '✏️ Editar Operación' : '➕ Nueva Operación' }}
            </h2>

            <div v-if="isFormLoading" class="min-h-[400px] flex items-center justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>

            <form v-else @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- 1. Cartera y Tipo -->
                <div v-if="!transaction && portfolios.length > 0">
                    <InputLabel value="Cartera Destino" class="dark:text-slate-300" />
                    <select v-model="form.portfolio_id" class="mt-1 block w-full rounded-xl border-slate-300 dark:bg-slate-700 dark:border-slate-600">
                        <option v-for="p in portfolios" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                </div>

                <div>
                    <InputLabel value="Tipo de Operación" class="dark:text-slate-300" />
                    <select v-model="form.type" :disabled="!!transaction?.id" class="mt-1 block w-full rounded-xl border-slate-300 dark:bg-slate-700 dark:border-slate-600">
                        <option v-for="t in availableTypes" :key="t.value" :value="t.value">{{ t.label }}</option>
                    </select>
                </div>

                <!-- 2. Inversión vs Gasto -->
                <div v-if="isInvestment" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 dark:bg-slate-900/40 p-6 rounded-3xl border border-slate-100 dark:border-slate-700/50">
                    <AssetSearchInput v-model="form.asset_name" v-model:isin="form.isin" :error="form.errors.asset_name" @select="handleAssetSelect" />
                    <InvestmentFields :form="form" :is-fetching-price="isFetchingPrice" :price-source="priceSource" v-model:last-edited-field="lastEditedField" />
                </div>

                <div v-else class="md:col-span-2">
                    <CategorySelector v-model="form.category_id" v-model:category-name="form.category_name" :categories="categories" :transaction-type="form.type" :error="form.errors.category_id" />
                </div>

                <!-- 3. Importe y Fecha -->
                <div>
                    <InputLabel value="Importe Total" />
                    <TextInput type="number" step="any" v-model="form.amount" class="mt-1 block w-full text-lg font-black" @focus="lastEditedField = 'amount'" />
                    <InputError :message="form.errors.amount" class="mt-2" />
                </div>

                <div>
                    <InputLabel value="Fecha" />
                    <TextInput type="date" v-model="form.date" class="mt-1 block w-full" />
                    <InputError :message="form.errors.date" class="mt-2" />
                </div>

                <div class="md:col-span-2">
                    <InputLabel value="Nota / Descripción" />
                    <TextInput v-model="form.description" class="mt-1 block w-full" placeholder="Detalle opcional..." />
                </div>

                <!-- 4. Acciones -->
                <div class="md:col-span-2 pt-6 flex justify-between items-center border-t dark:border-slate-700 mt-4">
                    <DangerButton v-if="transaction?.id" type="button" @click="handleDeleteClick" :disabled="form.processing">Eliminar</DangerButton>
                    <div v-else></div>

                    <div class="flex gap-4">
                        <SecondaryButton @click="emit('close')" :disabled="form.processing">Cancelar</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">
                            {{ form.processing ? 'Procesando...' : (transaction?.id ? 'Guardar Cambios' : 'Confirmar') }}
                        </PrimaryButton>
                    </div>
                </div>
            </form>
        </div>

        <ModalConfirm 
            :show="showDeleteConfirm"
            title="¿Eliminar operación?"
            message="Esta acción es irreversible."
            confirm-text="Eliminar"
            type="danger"
            @confirm="handleConfirmDelete"
            @cancel="showDeleteConfirm = false"
        />
    </Modal>
</template>
