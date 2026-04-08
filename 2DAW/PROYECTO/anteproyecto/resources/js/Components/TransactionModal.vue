<script setup>
import { computed, ref, onMounted, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { useTransactionCalculator } from '@/Composables/useTransactionCalculator';
import axios from 'axios';

// Componentes Base y Partials
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import AssetSearchInput from './Transactions/Partials/AssetSearchInput.vue';
import CategorySelector from './Transactions/Partials/CategorySelector.vue';
import InvestmentFields from './Transactions/Partials/InvestmentFields.vue';
import ModalConfirm from '@/Components/ModalConfirm.vue';

/**
 * TransactionModal - Orquestador de Operaciones Financieras.
 * 
 * Gestiona la creación y edición de Ingresos, Gastos e Inversiones.
 * Utiliza composables para la lógica matemática y partials para la UI modular.
 */
const props = defineProps({
    show: Boolean,
    transaction: Object,
    portfolios: Array,
    categories: Array,
    defaultPortfolioId: [String, Number],
    allowedTypes: Array
});

const emit = defineEmits(['close']);

const form = useForm({
    type: 'expense',
    amount: '',
    date: new Date().toISOString().substring(0, 10),
    time: '',
    category_id: null,
    category_name: '',
    description: '',
    asset_name: '',
    asset_full_name: '',
    asset_type: 'stock',
    market_asset_id: null,
    isin: '',
    quantity: '',
    price_per_unit: '',
    portfolio_id: '',
    fees: '',
    exchange_fees: '',
    tax: '',
    currency_code: 'EUR',
});

// Estado de UI y Búsqueda
const isFetchingPrice = ref(false);
const priceSource = ref(null);
const lastEditedField = ref('amount');
const isFormLoading = ref(false);
const showDeleteConfirm = ref(false);

// Inicializar Composable de Cálculos
const { initWatchers, fetchPrice: _unused_fetchPrice } = useTransactionCalculator(form, lastEditedField);
initWatchers();

/**
 * Obtiene el precio de mercado actualizado para el activo seleccionado.
 */
const fetchPrice = async () => {
    if ((!form.asset_name && !form.market_asset_id) || !form.date || isFormLoading.value) return;
    
    isFetchingPrice.value = true;
    priceSource.value = null;

    try {
        const params = { date: form.date, type: form.asset_type };
        if (form.market_asset_id) params.market_asset_id = form.market_asset_id;
        else params.ticker = form.asset_name;

        const { data } = await axios.get(route('market.price'), { params });
        
        if (data.price) {
            form.price_per_unit = data.price;
            priceSource.value = `${data.source} (${data.currency})`;
            if (data.currency) form.currency_code = data.currency;
        }
    } catch (e) {
        if (e.response?.status !== 404) console.error('Error de precio:', e);
    } finally {
        isFetchingPrice.value = false;
    }
};

/**
 * Gestiona la selección de un activo desde el buscador modular.
 */
const handleAssetSelect = (asset) => {
    form.asset_name = asset.ticker;
    form.asset_full_name = asset.name;
    form.asset_type = asset.type;
    form.market_asset_id = asset.id;
    form.isin = asset.isin || '';
    form.currency_code = asset.currency_code || 'EUR';
    fetchPrice();
};

/**
 * Inicializa el formulario con datos nuevos o existentes (Edición).
 */
const initForm = () => {
    isFormLoading.value = true;
    priceSource.value = null;

    if (props.transaction?.id) {
        // MODO EDICIÓN: Mapeo de campos
        Object.keys(form.data()).forEach(key => {
            if (key === 'date' && props.transaction.date) {
                form.date = String(props.transaction.date).substring(0, 10);
            } else if (key === 'time' && props.transaction.time) {
                form.time = String(props.transaction.time).substring(0, 5);
            } else if (key === 'category_name') {
                 form.category_name = typeof props.transaction.category === 'object' 
                    ? props.transaction.category?.name 
                    : (props.transaction.category || '');
            } else if (props.transaction[key] !== undefined) {
                form[key] = props.transaction[key];
            }
        });
        
        // Datos específicos de activos (anidados)
        if (props.transaction.asset) {
            form.asset_name = props.transaction.asset.ticker;
            form.asset_full_name = props.transaction.asset.name;
            form.asset_type = props.transaction.asset.type;
            form.market_asset_id = props.transaction.asset.market_asset_id;
        }
    } else {
        // MODO CREACIÓN: Valores por defecto
        form.reset();
        form.date = new Date().toISOString().substring(0, 10);
        if (props.defaultPortfolioId && props.defaultPortfolioId !== 'aggregated') {
            form.portfolio_id = props.defaultPortfolioId;
        } else if (props.portfolios.length > 0) {
            form.portfolio_id = props.portfolios[0].id;
        }
    }

    setTimeout(() => isFormLoading.value = false, 150);
};

// Ciclo de Vida y Watchers de Visibilidad
onMounted(() => { if (props.show) initForm(); });
watch(() => [props.show, props.transaction?.id], ([show]) => { if (show) initForm(); });
watch(() => [form.date, form.market_asset_id], () => { if (!isFormLoading.value) fetchPrice(); });

const submit = () => {
    // Seguridad: Si ya no hay transacción o se está procesando, cancelamos
    if (form.processing) return;
    
    const routeName = props.transaction?.id ? 'transactions.update' : 'transactions.store';
    const method = props.transaction?.id ? 'put' : 'post';
    const routeParams = props.transaction?.id ? props.transaction.id : [];
    
    form[method](route(routeName, routeParams), {
        preserveScroll: true,
        onSuccess: () => emit('close'),
    });
};

const deleteTransaction = () => {
    showDeleteConfirm.value = true;
};

const confirmDelete = () => {
    // Seguridad: Si props.transaction es null al momento del borrado, abortamos
    if (!props.transaction?.id) {
        showDeleteConfirm.value = false;
        return;
    }

    form.delete(route('transactions.destroy', props.transaction.id), {
        onSuccess: () => {
            showDeleteConfirm.value = false;
            emit('close');
        },
    });
};

// Propiedades Computadas de Lógica de Negocio
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
        <div 
            class="transition-all duration-300 relative p-8 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100"
            :class="{ 'opacity-0 scale-95 pointer-events-none': form.processing }"
        >
            <h2 class="text-xl font-black border-b-2 dark:border-slate-700 pb-3 mb-6 uppercase tracking-wider text-blue-600 dark:text-blue-400">
                {{ transaction?.id ? '✏️ Editar Operación' : '➕ Nueva Operación' }}
            </h2>

            <!-- Indicador de Carga Interno -->
            <div v-if="isFormLoading" class="min-h-[400px] flex items-center justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>

            <form v-else @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- 1. Cartera y Tipo (Fila 1) -->
                <div v-if="!transaction && portfolios.length > 0">
                    <InputLabel value="Cartera Destino" class="dark:text-slate-300" />
                    <select v-model="form.portfolio_id" class="mt-1 block w-full rounded-xl border-slate-300 dark:bg-slate-700 dark:border-slate-600">
                        <option v-for="p in portfolios" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                </div>

                <div>
                    <InputLabel value="Tipo de Operación" class="dark:text-slate-300" />
                    <select v-model="form.type" :disabled="!!transaction?.id" class="mt-1 block w-full rounded-xl border-slate-300 dark:bg-slate-700 dark:border-slate-600 disabled:bg-slate-50 dark:disabled:bg-slate-900">
                        <option v-for="t in availableTypes" :key="t.value" :value="t.value">{{ t.label }}</option>
                    </select>
                </div>

                <!-- 2. Sección Dinámica: Inversión vs Gasto/Ingreso -->
                <div v-if="isInvestment" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 dark:bg-slate-900/40 p-6 rounded-3xl border border-slate-100 dark:border-slate-700/50">
                    <AssetSearchInput v-model="form.asset_name" v-model:isin="form.isin" :error="form.errors.asset_name" @select="handleAssetSelect" />
                    <InvestmentFields :form="form" :is-fetching-price="isFetchingPrice" :price-source="priceSource" v-model:last-edited-field="lastEditedField" />
                </div>

                <div v-else class="md:col-span-2">
                    <CategorySelector v-model="form.category_id" v-model:category-name="form.category_name" :categories="categories" :transaction-type="form.type" :error="form.errors.category_id" />
                </div>

                <!-- 3. Importe, Fecha y Descripción -->
                <div>
                    <InputLabel value="Importe Total" class="dark:text-slate-300" />
                    <TextInput type="number" step="any" v-model="form.amount" class="mt-1 block w-full text-lg font-black dark:bg-slate-700 dark:text-white" @focus="lastEditedField = 'amount'" />
                    <InputError :message="form.errors.amount" class="mt-2" />
                </div>

                <div>
                    <InputLabel value="Fecha" class="dark:text-slate-300" />
                    <TextInput type="date" v-model="form.date" class="mt-1 block w-full dark:bg-slate-700 dark:text-white" />
                    <InputError :message="form.errors.date" class="mt-2" />
                </div>

                <div class="md:col-span-2">
                    <InputLabel value="Nota / Descripción" class="dark:text-slate-300" />
                    <TextInput type="text" v-model="form.description" class="mt-1 block w-full dark:bg-slate-700 dark:text-white" placeholder="Detalle opcional de la operación..." />
                </div>

                <!-- 4. Acciones del Formulario -->
                <div class="md:col-span-2 pt-6 flex justify-between items-center border-t dark:border-slate-700 mt-4">
                    <DangerButton v-if="transaction?.id" type="button" @click="deleteTransaction" :disabled="form.processing">Eliminar</DangerButton>
                    <div v-else></div>

                    <div class="flex gap-4">
                        <SecondaryButton @click="emit('close')" :disabled="form.processing">Cancelar</SecondaryButton>
                        <PrimaryButton 
                            :class="{ 'opacity-50 cursor-not-allowed': form.processing }" 
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Procesando...
                            </span>
                            <span v-else>{{ transaction?.id ? 'Guardar Cambios' : 'Confirmar Operación' }}</span>
                        </PrimaryButton>
                    </div>
                </div>
            </form>
        </div>

        <!-- Overlay de Éxito / Procesando (Visual feedback) -->
        <div 
            v-if="form.processing" 
            class="absolute inset-0 flex flex-col items-center justify-center bg-white/60 dark:bg-slate-800/60 backdrop-blur-sm z-[101] rounded-lg transition-all duration-500"
        >
            <div class="w-20 h-20 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-3xl flex items-center justify-center animate-bounce shadow-xl border border-blue-200">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <p class="mt-6 text-sm font-black uppercase tracking-widest text-slate-800 dark:text-white animate-pulse">Sincronizando patrimonio...</p>
        </div>
    </Modal>

    <!-- MODAL DE CONFIRMACIÓN DE BORRADO INDIVIDUAL -->
    <ModalConfirm 
        :show="showDeleteConfirm"
        title="¿Eliminar operación?"
        message="Esta acción eliminará la transacción de forma permanente y no se podrá recuperar. ¿Estás seguro?"
        confirm-text="Eliminar Definitivamente"
        type="danger"
        @confirm="confirmDelete"
        @cancel="showDeleteConfirm = false"
    />
</template>
