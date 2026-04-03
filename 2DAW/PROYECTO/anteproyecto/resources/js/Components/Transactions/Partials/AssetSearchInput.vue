<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import _ from 'lodash';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

/**
 * AssetSearchInput - Buscador inteligente de activos.
 * 
 * Permite buscar activos en el mercado global mediante Ticker, Nombre o ISIN.
 * Integra un sistema de sugerencias reactivo con debounce.
 */
const props = defineProps({
    modelValue: String,
    assetType: String,
    isin: String,
    error: String,
    disabled: Boolean,
});

const emit = defineEmits(['update:modelValue', 'update:isin', 'select']);

const searchResults = ref([]);
const isSearching = ref(false);
const showSuggestions = ref(false);

/**
 * Realiza la búsqueda federada en el servidor (Laravel).
 * Utiliza debounce para evitar saturar la API.
 */
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
        console.error('Error en búsqueda de activos:', error);
        searchResults.value = [];
    } finally {
        isSearching.value = false;
    }
}, 300);

// Vigilantes para disparar búsqueda al escribir
watch(() => props.modelValue, (val) => {
    if (showSuggestions.value) performSearch(val);
});

watch(() => props.isin, (val) => {
    if (val && val.length > 5 && showSuggestions.value) performSearch(val);
});

const selectAsset = (asset) => {
    emit('select', asset);
    showSuggestions.value = false;
};

const handleBlur = () => {
    // Retraso para permitir el clic en la sugerencia antes de cerrar el menú
    setTimeout(() => {
        showSuggestions.value = false;
    }, 200);
};

const getTypeColor = (type) => {
    switch (type) {
        case 'stock': return 'bg-blue-100 text-blue-800 dark:bg-blue-900';
        case 'etf': return 'bg-purple-100 text-purple-800 dark:bg-purple-900';
        case 'crypto': return 'bg-orange-100 text-orange-800 dark:bg-orange-900';
        case 'fund': return 'bg-green-100 text-green-800 dark:bg-green-900';
        default: return 'bg-gray-100 text-gray-800 dark:bg-gray-700';
    }
};
</script>

<template>
    <div class="relative z-20">
        <InputLabel for="asset_name" value="Buscar Activo (Ticker, Nombre, ISIN)" class="dark:text-slate-300" />
        <div class="relative">
            <TextInput
                id="asset_name"
                type="text"
                class="mt-1 block w-full uppercase dark:bg-slate-700 dark:text-white"
                :value="modelValue"
                @input="e => { emit('update:modelValue', e.target.value.toUpperCase()); showSuggestions = true; }"
                @focus="showSuggestions = true"
                @blur="handleBlur"
                placeholder="Ej: AAPL, Bitcoin, ES0..."
                autocomplete="off"
                :disabled="disabled"
            />
            <!-- Spinner de Carga -->
            <div v-if="isSearching" class="absolute right-3 top-3">
                <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
        
        <!-- Menú de Sugerencias -->
        <div v-if="showSuggestions && searchResults.length > 0" class="absolute left-0 right-0 mt-1 bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-100 dark:border-slate-700 max-h-60 overflow-auto z-50 animate-in fade-in slide-in-from-top-2">
            <ul>
                <li v-for="result in searchResults" :key="result.id || result.ticker"
                    @click="selectAsset(result)"
                    class="px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer border-b border-slate-100 dark:border-slate-700 last:border-0">
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-slate-800 dark:text-white">
                            {{ result.ticker }} 
                            <span v-if="result.isin" class="ml-2 text-[10px] font-normal text-slate-500 bg-slate-100 dark:bg-slate-600 px-1.5 py-0.5 rounded">{{ result.isin }}</span>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-widest" :class="getTypeColor(result.type)">{{ result.type }}</span>
                    </div>
                    <div class="text-slate-600 dark:text-slate-400 text-xs mt-0.5">{{ result.name }} <span class="text-[10px] opacity-60">({{ result.currency }})</span></div>
                </li>
            </ul>
        </div>
        <InputError :message="error" class="mt-2" />
    </div>
</template>
