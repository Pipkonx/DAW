<script setup>
import { computed, watch, ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import axios from 'axios';
import _ from 'lodash'; // Lodash for debounce

const props = defineProps({
    show: { type: Boolean, default: false },
    closeable: { type: Boolean, default: true },
    transaction: { type: Object, default: null },
    portfolios: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    defaultPortfolioId: { type: [String, Number], default: null },
    allowedTypes: { type: Array, default: () => [] } // Lista de tipos permitidos (vacío = todos)
});

const emit = defineEmits(['close']);

const form = useForm({
    type: 'expense',
    amount: '',
    date: new Date().toISOString().substr(0, 10),
    category_id: null,
    description: '',
    asset_name: '', // Acts as Ticker/Symbol
    asset_full_name: '', // New field for display name
    asset_type: 'stock',
    market_asset_id: null, // Link to global market asset
    isin: '', // New ISIN field
    quantity: '',
    price_per_unit: '',
    portfolio_id: '',
    sector: '',
    industry: '',
    region: '',
    country: '',
    currency_code: '',
    logo_url: '', // Store logo URL if available
});

// State for Search & Price
const searchResults = ref([]);
const isSearching = ref(false);
const showSuggestions = ref(false);
const isFetchingPrice = ref(false);
const priceError = ref(null);
const priceSource = ref(null);
const lastEditedField = ref(null);

// Debounced Search Function
const performSearch = _.debounce(async (query) => {
    if (!query || query.length < 2) {
        searchResults.value = [];
        return;
    }
    isSearching.value = true;
    try {
        const response = await axios.get(route('market.search'), { params: { query } });
        searchResults.value = response.data;
    } catch (error) {
        console.error('Search error:', error);
        searchResults.value = [];
    } finally {
        isSearching.value = false;
    }
}, 300);

// Watch for input changes to trigger search
watch(() => form.asset_name, (val) => {
    if (showSuggestions.value) performSearch(val);
});

// Also search by ISIN
watch(() => form.isin, (val) => {
    if (val && val.length > 5) performSearch(val);
});

const selectAsset = (asset) => {
    form.asset_name = asset.ticker;
    form.asset_full_name = asset.name;
    form.asset_type = asset.type;
    form.market_asset_id = asset.id;
    form.isin = asset.isin || '';
    form.sector = asset.sector || '';
    form.currency_code = asset.currency_code || 'USD';
    form.logo_url = asset.logo_url || '';
    
    showSuggestions.value = false;
    
    // Trigger price fetch
    fetchPrice();
};

// Fetch Historical Price
const fetchPrice = async () => {
    if (!isTrade.value || (!form.asset_name && !form.market_asset_id) || !form.date) return;
    
    isFetchingPrice.value = true;
    priceError.value = null;
    priceSource.value = null;

    try {
        const params = {
            date: form.date,
            type: form.asset_type
        };

        if (form.market_asset_id) {
            params.market_asset_id = form.market_asset_id;
        } else {
            params.ticker = form.asset_name;
        }

        const response = await axios.get(route('market.price'), { params });
        
        if (response.data.price) {
            form.price_per_unit = response.data.price;
            priceSource.value = `Fuente: ${response.data.source} (${response.data.currency})`;
            if (response.data.currency) {
                form.currency_code = response.data.currency;
            }
            // Recalculate based on new price if we have quantity or amount
            if (form.quantity && lastEditedField.value !== 'amount') {
                 form.amount = parseFloat((form.quantity * form.price_per_unit).toFixed(2));
            } else if (form.amount && lastEditedField.value !== 'quantity') {
                 form.quantity = parseFloat((form.amount / form.price_per_unit).toFixed(8));
            }
        }
    } catch (error) {
        console.error('Price fetch error:', error);
        priceError.value = "No se encontró precio para esta fecha. Ingrese manualmente.";
    } finally {
        isFetchingPrice.value = false;
    }
};

// Watch for Date changes to re-fetch price
watch(() => form.date, () => {
    if (form.asset_name) fetchPrice();
});

// Auto-calculation logic
const calculateFromAmount = () => {
    if (!form.price_per_unit || form.price_per_unit <= 0) return;
    if (!form.amount) {
        form.quantity = '';
        return;
    }
    // Amount / Price = Quantity
    // Use more precision for crypto/stocks
    form.quantity = parseFloat((form.amount / form.price_per_unit).toFixed(8));
};

const calculateFromQuantity = () => {
    if (!form.price_per_unit || form.price_per_unit <= 0) return;
    if (!form.quantity) {
        form.amount = '';
        return;
    }
    // Quantity * Price = Amount
    // Standard currency rounding
    form.amount = parseFloat((form.quantity * form.price_per_unit).toFixed(2));
};

const calculateFromPrice = () => {
    // If price changes manually, update the field that wasn't last edited by user
    if (!form.price_per_unit) return;
    
    if (lastEditedField.value === 'quantity' && form.quantity) {
        calculateFromQuantity();
    } else if (lastEditedField.value === 'amount' && form.amount) {
        calculateFromAmount();
    } else if (form.quantity) {
        // Default to updating amount if no clear preference
        calculateFromQuantity();
    }
};

// Watchers for auto-calculation
watch(() => form.amount, (val) => {
    if (lastEditedField.value === 'amount') {
        calculateFromAmount();
    }
});

watch(() => form.quantity, (val) => {
    if (lastEditedField.value === 'quantity') {
        calculateFromQuantity();
    }
});

watch(() => form.price_per_unit, (val) => {
    calculateFromPrice();
});

// Category Logic (Existing)
const mostUsedCategoryId = computed(() => {
    if (isInvestment.value) return null;
    const type = form.type === 'income' ? 'income' : 'expense';
    const candidates = props.categories.filter(c => c.type === type);
    let maxUsage = -1;
    let bestId = null;
    candidates.forEach(c => {
         if (c.is_active && (c.usage_count || 0) > maxUsage) {
             maxUsage = c.usage_count || 0;
             bestId = c.id;
         }
         if (c.children) {
             c.children.forEach(child => {
                 if (child.is_active && (child.usage_count || 0) > maxUsage) {
                     maxUsage = child.usage_count || 0;
                     bestId = child.id;
                 }
             });
         }
    });
    return bestId;
});

const preselectCategory = (type) => {
    if (mostUsedCategoryId.value) {
        form.category_id = mostUsedCategoryId.value;
    }
};

// Reset or populate form
watch(() => props.show, (newVal) => {
    if (newVal) {
        searchResults.value = [];
        showSuggestions.value = false;
        priceError.value = null;
        
        if (props.transaction && props.transaction.id) {
            // Edit Mode
            form.type = props.transaction.type;
            form.amount = props.transaction.amount;
            form.date = props.transaction.date.substring(0, 10);
            form.category_id = props.transaction.category_id;
            form.description = props.transaction.description;
            form.asset_name = props.transaction.asset_name || '';
            form.isin = props.transaction.asset?.isin || '';
            form.quantity = props.transaction.quantity || '';
            form.price_per_unit = props.transaction.price_per_unit || '';
            if (props.transaction.asset) {
                form.sector = props.transaction.asset.sector || '';
                form.industry = props.transaction.asset.industry || '';
                form.region = props.transaction.asset.region || '';
                form.country = props.transaction.asset.country || '';
                form.currency_code = props.transaction.asset.currency_code || '';
            }
        } else {
            // Create Mode
            form.reset();
            form.clearErrors();
            form.date = new Date().toISOString().substr(0, 10);
            if (props.transaction && props.transaction.type) form.type = props.transaction.type;
            if (!['buy', 'sell', 'dividend', 'reward', 'gift'].includes(form.type)) preselectCategory(form.type);
            if (props.defaultPortfolioId && props.defaultPortfolioId !== 'aggregated') form.portfolio_id = props.defaultPortfolioId;
            else if (props.portfolios.length > 0) form.portfolio_id = props.portfolios[0].id;
        }
    }
});

const submit = () => {
    const routeName = props.transaction?.id ? 'transactions.update' : 'transactions.store';
    const routeParams = props.transaction?.id ? props.transaction.id : undefined;
    
    const method = props.transaction?.id ? 'put' : 'post';
    
    form[method](route(routeName, routeParams), {
        onSuccess: () => {
            form.reset();
            emit('close');
        },
    });
};

const deleteTransaction = () => {
    if (!confirm('¿Estás seguro de que deseas eliminar esta transacción? Esta acción afectará a los saldos y no se puede deshacer.')) {
        return;
    }

    form.delete(route('transactions.destroy', props.transaction.id), {
        onSuccess: () => {
            emit('close');
        },
    });
};

const close = () => {
    emit('close');
    form.reset();
    form.clearErrors();
    showSuggestions.value = false;
};

// Computed properties
const isInvestment = computed(() => ['buy', 'sell', 'dividend', 'reward'].includes(form.type));
const isTrade = computed(() => ['buy', 'sell', 'reward'].includes(form.type));
const availableCategories = computed(() => {
    if (isInvestment.value) return [];
    const type = form.type === 'income' ? 'income' : 'expense';
    const currentId = form.category_id;
    return props.categories
        .filter(c => c.type === type)
        .map(c => {
            const newCat = { ...c };
            if (newCat.children) {
                newCat.children = newCat.children
                    .filter(child => child.is_active || child.id === currentId)
                    .sort((a, b) => (b.usage_count || 0) - (a.usage_count || 0));
            }
            return newCat;
        })
        .filter(c => {
            const hasActiveChildren = c.children && c.children.length > 0;
            const isCurrent = c.id === currentId;
            return c.is_active || isCurrent || hasActiveChildren;
        })
        .sort((a, b) => (b.usage_count || 0) - (a.usage_count || 0));
});

const transactionTypes = [
    { value: 'income', label: 'Ingreso' },
    { value: 'expense', label: 'Gasto' },
    { value: 'buy', label: 'Compra Activo' },
    { value: 'sell', label: 'Venta Activo' },
    { value: 'dividend', label: 'Dividendo' },
    { value: 'reward', label: 'Recompensa' },
    { value: 'gift', label: 'Regalo' },
];

const assetTypes = [
    { value: 'stock', label: 'Acción (Stock)' },
    { value: 'etf', label: 'ETF' },
    { value: 'crypto', label: 'Criptomoneda' },
    { value: 'fund', label: 'Fondo de Inversión' },
    { value: 'bond', label: 'Bono' },
    { value: 'real_estate', label: 'Inmueble' },
    { value: 'other', label: 'Otro' },
];

// Tipos filtrados según allowedTypes
const availableTransactionTypes = computed(() => {
    if (props.allowedTypes && props.allowedTypes.length > 0) {
        return transactionTypes.filter(t => props.allowedTypes.includes(t.value));
    }
    return transactionTypes;
});

// Ajustar el tipo por defecto si el actual no es válido
watch(() => props.allowedTypes, (newTypes) => {
    if (newTypes && newTypes.length > 0 && !newTypes.includes(form.type)) {
        form.type = newTypes[0];
    }
}, { immediate: true });

</script>

<template>
    <Modal :show="show" :closeable="closeable" @close="close">
        <div class="p-6 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            <h2 class="text-lg font-bold border-b dark:border-slate-700 pb-2 mb-4">
                {{ transaction?.id ? 'Editar Transacción' : 'Nueva Transacción' }}
            </h2>

            <form @submit.prevent="submit" class="space-y-5">
                
                <!-- Tipo de Transacción y Fecha -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <InputLabel for="type" value="Tipo de Operación" class="dark:text-slate-300" />
                        <select
                            id="type"
                            v-model="form.type"
                            :disabled="!!transaction?.id" 
                            class="mt-1 block w-full border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-700"
                        >
                            <option v-for="type in availableTransactionTypes" :key="type.value" :value="type.value">
                                {{ type.label }}
                            </option>
                        </select>
                        <InputError :message="form.errors.type" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="date" value="Fecha" class="dark:text-slate-300" />
                        <TextInput
                            id="date"
                            type="date"
                            class="mt-1 block w-full dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                            v-model="form.date"
                            required
                        />
                        <InputError :message="form.errors.date" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="amount" value="Monto (€)" class="dark:text-slate-300" />
                        <TextInput
                            id="amount"
                            type="number"
                            step="0.01"
                            class="mt-1 block w-full font-mono dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                            v-model="form.amount"
                            required
                            placeholder="0.00"
                            @focus="lastEditedField = 'amount'"
                            @input="lastEditedField = 'amount'"
                        />
                        <InputError :message="form.errors.amount" class="mt-2" />
                    </div>
                </div>

                <!-- SECCIÓN: INVERSIONES -->
                <div v-if="isInvestment" class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl border border-blue-100 dark:border-blue-800 space-y-4">
                    <h3 class="text-sm font-bold text-blue-800 dark:text-blue-300 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        Detalles de Inversión
                    </h3>
                    
                    <!-- Cartera (Solo crear) -->
                    <div v-if="!transaction && portfolios.length > 0">
                        <InputLabel for="portfolio_id" value="Cartera Destino" class="dark:text-slate-300" />
                        <select
                            id="portfolio_id"
                            v-model="form.portfolio_id"
                            class="mt-1 block w-full border-blue-200 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-700"
                        >
                            <option value="" disabled>Selecciona una cartera</option>
                            <option v-for="portfolio in portfolios" :key="portfolio.id" :value="portfolio.id">
                                {{ portfolio.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Búsqueda Avanzada de Activo -->
                    <div class="relative z-20">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="asset_name" value="Buscar Activo (Ticker/Nombre)" class="dark:text-slate-300" />
                                <div class="relative">
                                    <TextInput
                                        id="asset_name"
                                        type="text"
                                        class="mt-1 block w-full uppercase dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                        v-model="form.asset_name"
                                        @input="showSuggestions = true"
                                        @focus="showSuggestions = true"
                                        placeholder="Ej: BTC, AAPL"
                                        autocomplete="off"
                                    />
                                    <div v-if="isSearching" class="absolute right-3 top-3">
                                        <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <InputLabel for="isin" value="ISIN (Opcional)" class="dark:text-slate-300" />
                                <TextInput
                                    id="isin"
                                    type="text"
                                    class="mt-1 block w-full dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                    v-model="form.isin"
                                    placeholder="Ej: US0378331005"
                                />
                            </div>
                        </div>
                        
                        <!-- Resultados de Búsqueda -->
                        <div v-if="showSuggestions && searchResults.length > 0" class="absolute left-0 right-0 mt-1 bg-white dark:bg-slate-800 rounded-md shadow-lg border border-slate-200 dark:border-slate-600 max-h-60 overflow-auto z-50">
                            <ul>
                                <li 
                                    v-for="asset in searchResults" 
                                    :key="asset.ticker"
                                    @click="selectAsset(asset)"
                                    class="px-4 py-3 hover:bg-blue-50 dark:hover:bg-slate-700 cursor-pointer flex items-center justify-between border-b dark:border-slate-700 last:border-0"
                                >
                                    <div class="flex items-center">
                                        <img v-if="asset.logo_url" :src="asset.logo_url" class="w-8 h-8 mr-3 rounded-full bg-white p-0.5" alt="" />
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-800 dark:text-white">{{ asset.ticker }}</span>
                                            <span class="text-xs text-slate-500 dark:text-slate-400">{{ asset.name }}</span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <span class="text-xs font-semibold px-2 py-1 rounded bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 uppercase">{{ asset.type }}</span>
                                        <span v-if="asset.isin" class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">{{ asset.isin }}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <InputError :message="form.errors.asset_name" class="mt-2" />
                    </div>

                    <!-- Cantidad y Precio Automático -->
                    <div v-if="isTrade" class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="quantity" value="Cantidad" class="dark:text-slate-300" />
                            <TextInput
                                id="quantity"
                                type="number"
                                step="any"
                                class="mt-1 block w-full dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                v-model="form.quantity"
                                @focus="lastEditedField = 'quantity'"
                                @input="lastEditedField = 'quantity'"
                            />
                        </div>
                        <div>
                            <div class="flex justify-between">
                                <InputLabel for="price_per_unit" value="Precio Unitario" class="dark:text-slate-300" />
                                <span v-if="isFetchingPrice" class="text-xs text-blue-500 animate-pulse">Buscando precio...</span>
                            </div>
                            <TextInput
                                id="price_per_unit"
                                type="number"
                                step="any"
                                class="mt-1 block w-full dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                v-model="form.price_per_unit"
                            />
                            <p v-if="priceSource" class="text-[10px] text-green-600 dark:text-green-400 mt-1">
                                ✓ Precio estimado: {{ priceSource }}
                            </p>
                            <p v-if="priceError" class="text-[10px] text-orange-500 mt-1">
                                ⚠ {{ priceError }}
                            </p>
                        </div>
                    </div>

                    <!-- Tipo Activo y Detalles Adicionales -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="asset_type" value="Tipo de Activo" class="dark:text-slate-300" />
                            <select
                                id="asset_type"
                                v-model="form.asset_type"
                                class="mt-1 block w-full border-blue-200 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-700"
                            >
                                <option v-for="type in assetTypes" :key="type.value" :value="type.value">
                                    {{ type.label }}
                                </option>
                            </select>
                        </div>
                         <!-- Detalles del Activo (Collapsible) -->
                         <div class="md:col-span-2 mt-2">
                            <details class="group">
                                <summary class="flex justify-between items-center font-medium cursor-pointer list-none text-blue-700 dark:text-blue-400 text-sm">
                                    <span>Información Avanzada (Sector, Divisa...)</span>
                                    <span class="transition group-open:rotate-180">
                                        <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                                    </span>
                                </summary>
                                <div class="text-neutral-600 dark:text-gray-400 mt-3 group-open:animate-fadeIn grid grid-cols-2 gap-4">
                                    <div>
                                        <InputLabel for="sector" value="Sector" class="dark:text-gray-300" />
                                        <TextInput id="sector" type="text" v-model="form.sector" class="mt-1 block w-full text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Ej: Tecnología" />
                                    </div>
                                    <div>
                                        <InputLabel for="currency_code" value="Divisa Base" class="dark:text-gray-300" />
                                        <TextInput id="currency_code" type="text" v-model="form.currency_code" class="mt-1 block w-full text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Ej: USD" maxlength="3" />
                                    </div>
                                </div>
                            </details>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN: GASTOS / OTROS -->
                <div v-else>
                    <div class="space-y-4">
                        <!-- Categoría -->
                        <div>
                            <InputLabel for="category_id" value="Categoría" class="dark:text-gray-300" />
                            <select
                                id="category_id"
                                v-model="form.category_id"
                                class="mt-1 block w-full border-slate-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm text-slate-700 dark:text-gray-300 bg-white dark:bg-gray-700"
                            >
                                <option :value="null">Sin categoría</option>
                                <template v-for="cat in availableCategories" :key="cat.id">
                                    <option :value="cat.id" class="font-bold">
                                        {{ cat.name }} {{ cat.id === mostUsedCategoryId ? ' (Más Usado)' : '' }}
                                    </option>
                                    <option 
                                        v-for="sub in cat.children" 
                                        :key="sub.id" 
                                        :value="sub.id"
                                        class="pl-4"
                                    >
                                        &nbsp;&nbsp;&nbsp;{{ sub.name }} {{ sub.id === mostUsedCategoryId ? ' (Más Usado)' : '' }}
                                    </option>
                                </template>
                            </select>
                        </div>
                        
                        <div>
                             <InputLabel for="description" value="Descripción (Opcional)" class="dark:text-gray-300" />
                             <TextInput
                                id="description"
                                type="text"
                                class="mt-1 block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                v-model="form.description"
                                placeholder="Detalles adicionales..."
                            />
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex justify-end space-x-3 border-t dark:border-gray-700 pt-4 mt-6">
                    <DangerButton 
                        v-if="transaction?.id" 
                        type="button" 
                        @click="deleteTransaction" 
                        class="mr-auto"
                        :disabled="form.processing"
                    >
                        Eliminar
                    </DangerButton>

                    <SecondaryButton @click="close" class="dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                        Cancelar
                    </SecondaryButton>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        {{ transaction?.id ? 'Guardar Cambios' : 'Agregar Transacción' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
