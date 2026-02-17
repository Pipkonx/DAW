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
    time: '', // New field
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
    fees: '', // New field
    exchange_fees: '', // New field
    tax: '', // New field
    currency_code: 'EUR',
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
        const response = await axios.get(route('market.search'), { 
            params: { 
                query,
                // Remove type restriction to allow searching across all asset types
                // type: form.asset_type 
            } 
        });
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

// Watch for asset type changes to re-trigger search if input exists
watch(() => form.asset_type, () => {
    if (form.asset_name && form.asset_name.length >= 2) {
        performSearch(form.asset_name);
    }
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
    form.currency_code = asset.currency_code || 'EUR';
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
    let total = form.quantity * form.price_per_unit;
    
    const fees = parseFloat(form.fees || 0);
    const exchange_fees = parseFloat(form.exchange_fees || 0);
    const tax = parseFloat(form.tax || 0);

    if (form.type === 'buy' || form.type === 'expense') {
        total += fees + exchange_fees + tax;
    } else {
        // For sell, dividend, reward, income - fees/tax reduce the net amount received
        total -= (fees + exchange_fees + tax);
    }

    // Standard currency rounding
    form.amount = parseFloat(total.toFixed(2));
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

watch(() => [form.fees, form.exchange_fees, form.tax], () => {
    // Only update if we have base data
    if (form.quantity && form.price_per_unit) {
        calculateFromQuantity();
    }
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
            form.time = props.transaction.time ? props.transaction.time.substring(0, 5) : '';
            form.category_id = props.transaction.category_id;
            form.description = props.transaction.description;
            
            // Populate Asset Data from Relation
            if (props.transaction.asset) {
                form.asset_name = props.transaction.asset.ticker || props.transaction.asset.name;
                form.asset_full_name = props.transaction.asset.name;
                form.asset_type = props.transaction.asset.type;
                form.market_asset_id = props.transaction.asset.market_asset_id;
                form.isin = props.transaction.asset.isin || '';
            } else {
                form.asset_name = '';
                form.asset_full_name = '';
                form.asset_type = 'stock'; // Default
                form.market_asset_id = null;
                form.isin = '';
            }

            form.quantity = props.transaction.quantity || '';
            form.price_per_unit = props.transaction.price_per_unit || '';
            form.fees = props.transaction.fees || '';
            form.exchange_fees = props.transaction.exchange_fees || '';
            form.tax = props.transaction.tax || '';
            form.currency_code = props.transaction.currency || 'EUR';
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

const getTypeColor = (type) => {
    switch (type) {
        case 'stock': return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
        case 'etf': return 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300';
        case 'crypto': return 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300';
        case 'fund': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'bond': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        case 'real_estate': return 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300';
        default: return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
    }
};

const getTypeLabel = (type) => {
    const found = assetTypes.find(t => t.value === type);
    return found ? found.label : type;
};

</script>

<template>
    <Modal :show="show" :closeable="closeable" @close="close">
        <div class="p-6 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            <h2 class="text-lg font-bold border-b dark:border-slate-700 pb-2 mb-4">
                {{ transaction?.id ? 'Editar Transacción' : 'Nueva Transacción' }}
            </h2>

            <form @submit.prevent="submit" class="space-y-5">
                
                <!-- 1. Cartera (Solo crear y si hay carteras) -->
                <div v-if="!transaction && portfolios.length > 0">
                    <InputLabel for="portfolio_id" value="Cartera de Inversiones" class="dark:text-slate-300" />
                    <select
                        id="portfolio_id"
                        v-model="form.portfolio_id"
                        class="mt-1 block w-full border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-700"
                    >
                        <option value="" disabled>Selecciona una cartera</option>
                        <option v-for="portfolio in portfolios" :key="portfolio.id" :value="portfolio.id">
                            {{ portfolio.name }}
                        </option>
                    </select>
                </div>

                <!-- 2. Tipo de Transacción -->
                <div>
                    <InputLabel for="type" value="Tipo de Transacción" class="dark:text-slate-300" />
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

                <!-- SECCIÓN: INVERSIONES -->
                <div v-if="isInvestment" class="space-y-5">
                    
                    <!-- 3. Tipo de Activo y Agregar Activo -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Tipo de Activo -->
                        <div class="md:col-span-1">
                            <InputLabel for="asset_type" value="Tipo de Activo" class="dark:text-slate-300" />
                            <select
                                id="asset_type"
                                v-model="form.asset_type"
                                class="mt-1 block w-full border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-700 disabled:bg-slate-100 dark:disabled:bg-slate-800 disabled:text-slate-500"
                                :disabled="!!transaction?.id"
                            >
                                <option value="stock">Acción</option>
                                <option value="fund">Fondo</option>
                                <option value="etf">ETF</option>
                                <option value="crypto">Criptomoneda</option>
                                <option value="bond">Bono</option>
                                <option value="real_estate">Inmueble</option>
                                <option value="other">Otro</option>
                            </select>
                        </div>

                        <!-- Agregar Activo -->
                        <div class="md:col-span-2 relative z-20">
                            <InputLabel for="asset_name" value="Buscar Activo (Ticker, Nombre, ISIN)" class="dark:text-slate-300" />
                            <div class="relative">
                                <TextInput
                                    id="asset_name"
                                    type="text"
                                    class="mt-1 block w-full uppercase dark:bg-slate-700 dark:border-slate-600 dark:text-white disabled:bg-slate-100 dark:disabled:bg-slate-800 disabled:text-slate-500"
                                    v-model="form.asset_name"
                                    @input="showSuggestions = true"
                                    @focus="showSuggestions = true"
                                    placeholder="Ej: AAPL, Bitcoin, ES01..."
                                    autocomplete="off"
                                    :disabled="!!transaction?.id"
                                />
                                <div v-if="isSearching" class="absolute right-3 top-3">
                                    <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Resultados de Búsqueda -->
                            <div v-if="showSuggestions && searchResults.length > 0" class="absolute left-0 right-0 mt-1 bg-white dark:bg-slate-800 rounded-md shadow-lg border border-slate-200 dark:border-slate-600 max-h-60 overflow-auto z-50">
                                <ul>
                                    <li 
                                        v-for="result in searchResults" 
                                        :key="result.id || result.ticker"
                                        @click="selectAsset(result)"
                                        class="px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer border-b border-slate-100 dark:border-slate-700 last:border-0"
                                    >
                                        <div class="flex items-center justify-between">
                                            <div class="font-bold text-slate-800 dark:text-white">
                                                {{ result.ticker }} 
                                                <span v-if="result.isin" class="ml-2 text-xs font-normal text-slate-500 bg-slate-100 dark:bg-slate-600 dark:text-slate-300 px-1.5 py-0.5 rounded">{{ result.isin }}</span>
                                            </div>
                                            <span class="text-xs font-bold px-2 py-0.5 rounded-full" :class="getTypeColor(result.type)">{{ getTypeLabel(result.type) }}</span>
                                        </div>
                                        <div class="text-slate-600 dark:text-slate-400 text-sm mt-0.5">{{ result.name }} <span class="text-xs text-slate-400">({{ result.currency }})</span></div>
                                    </li>
                                </ul>
                            </div>
                            <InputError :message="form.errors.asset_name" class="mt-2" />
                        </div>
                    </div>

                    <!-- 4. Componentes (Cantidad) -->
                    <div>
                        <InputLabel for="quantity" value="Componentes (Cantidad)" class="dark:text-slate-300" />
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

                    <!-- 5. Fecha -->
                    <div>
                        <InputLabel for="date" value="Fecha de Operación" class="dark:text-slate-300" />
                        <TextInput
                            id="date"
                            type="date"
                            class="mt-1 block w-full dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                            v-model="form.date"
                            required
                        />
                        <InputError :message="form.errors.date" class="mt-2" />
                    </div>

                    <!-- 6. Precio y Divisa -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="flex justify-between">
                                <InputLabel for="price_per_unit" value="Precio de Compra" class="dark:text-slate-300" />
                                <span v-if="isFetchingPrice" class="text-xs text-blue-500 animate-pulse">Buscando...</span>
                            </div>
                            <TextInput
                                id="price_per_unit"
                                type="number"
                                step="any"
                                class="mt-1 block w-full dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                v-model="form.price_per_unit"
                            />
                            <p v-if="priceSource" class="text-[10px] text-green-600 dark:text-green-400 mt-1">
                                ✓ {{ priceSource }}
                            </p>
                        </div>
                        <div>
                            <InputLabel for="currency_code" value="Divisa" class="dark:text-slate-300" />
                            <select
                                id="currency_code"
                                v-model="form.currency_code"
                                class="mt-1 block w-full border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-700"
                            >
                                <option value="EUR">EUR</option>
                                <option value="USD">USD</option>
                                <option value="GBP">GBP</option>
                            </select>
                        </div>
                    </div>

                    <!-- Opciones Avanzadas (Collapsible) -->
                    <div class="mt-4 border-t dark:border-slate-700 pt-4">
                        <details class="group">
                            <summary class="flex justify-between items-center font-medium cursor-pointer list-none text-blue-600 dark:text-blue-400 hover:text-blue-800">
                                <span>Introducción de transacción avanzada (opcional)</span>
                                <span class="transition group-open:rotate-180">
                                    <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                                </span>
                            </summary>
                            
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 group-open:animate-fadeIn">
                                <!-- Hora -->
                                <div>
                                    <InputLabel for="time" value="Hora de Transacción" class="dark:text-slate-300" />
                                    <TextInput
                                        id="time"
                                        type="time"
                                        class="mt-1 block w-full dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                        v-model="form.time"
                                    />
                                </div>

                                <!-- Costo de Negociación -->
                                <div>
                                    <InputLabel for="fees" value="Costo de Negociación" class="dark:text-slate-300" />
                                    <TextInput
                                        id="fees"
                                        type="number"
                                        step="0.01"
                                        class="mt-1 block w-full dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                        v-model="form.fees"
                                        placeholder="0.00"
                                    />
                                </div>

                                <!-- Comisión de Cambio -->
                                <div>
                                    <InputLabel for="exchange_fees" value="Comisión de Cambio" class="dark:text-slate-300" />
                                    <TextInput
                                        id="exchange_fees"
                                        type="number"
                                        step="0.01"
                                        class="mt-1 block w-full dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                        v-model="form.exchange_fees"
                                        placeholder="0.00"
                                    />
                                </div>

                                <!-- Impuestos -->
                                <div>
                                    <InputLabel for="tax" value="Impuestos" class="dark:text-slate-300" />
                                    <TextInput
                                        id="tax"
                                        type="number"
                                        step="0.01"
                                        class="mt-1 block w-full dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                        v-model="form.tax"
                                        placeholder="0.00"
                                    />
                                </div>

                                <!-- Descripción -->
                                <div class="md:col-span-2">
                                    <InputLabel for="description" value="Descripción" class="dark:text-slate-300" />
                                    <TextInput
                                        id="description"
                                        type="text"
                                        class="mt-1 block w-full dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                        v-model="form.description"
                                        placeholder="Notas adicionales..."
                                    />
                                </div>
                            </div>
                        </details>
                    </div>

                    <!-- Importe Total -->
                    <div class="mt-4 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-lg flex justify-between items-center">
                        <div>
                            <InputLabel value="Importe Total" class="dark:text-slate-300 font-bold text-lg" />
                            <p class="text-xs text-slate-500 mt-1 dark:text-slate-400">Calculado automáticamente</p>
                        </div>
                        <div class="text-2xl font-bold text-slate-800 dark:text-white">
                            {{ form.amount ? Number(form.amount).toLocaleString('es-ES', { style: 'currency', currency: form.currency_code }) : '0,00 €' }}
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN: GASTOS / OTROS (Simplificado) -->
                <div v-else class="space-y-4">
                    <div>
                        <InputLabel for="date" value="Fecha" class="dark:text-slate-300" />
                        <TextInput
                            id="date"
                            type="date"
                            class="mt-1 block w-full dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                            v-model="form.date"
                            required
                        />
                    </div>
                    <div>
                        <InputLabel for="amount" value="Monto (€)" class="dark:text-slate-300" />
                        <TextInput
                            id="amount"
                            type="number"
                            step="0.01"
                            class="mt-1 block w-full dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                            v-model="form.amount"
                            required
                        />
                    </div>
                    <div>
                        <InputLabel for="category_id" value="Categoría" class="dark:text-gray-300" />
                        <select
                            id="category_id"
                            v-model="form.category_id"
                            class="mt-1 block w-full border-slate-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm text-slate-700 dark:text-gray-300 bg-white dark:bg-gray-700"
                        >
                            <option :value="null">Sin categoría</option>
                            <template v-for="cat in availableCategories" :key="cat.id">
                                <option :value="cat.id">{{ cat.name }}</option>
                                <option v-for="sub in cat.children" :key="sub.id" :value="sub.id" class="pl-4">
                                    &nbsp;&nbsp;&nbsp;{{ sub.name }}
                                </option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <InputLabel for="description" value="Descripción" class="dark:text-gray-300" />
                        <TextInput
                            id="description"
                            type="text"
                            class="mt-1 block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            v-model="form.description"
                        />
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
