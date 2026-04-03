<script setup>
import { computed } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { usePrivacy } from '@/Composables/usePrivacy';

/**
 * InvestmentFields - Bloque de campos técnicos de inversión.
 * 
 * Centraliza la captura de datos de mercado: cantidad de títulos, precio unitario,
 * costes operativos (fees, impuestos) y gestión de divisas.
 */
const props = defineProps({
    form: Object,
    isPrivacyMode: Boolean,
    isFetchingPrice: Boolean,
    priceSource: String,
    lastEditedField: String,
});

const emit = defineEmits(['update:lastEditedField', 'fetch-price']);

const { isPrivacyMode: privacy } = usePrivacy();

/**
 * Registra cuál ha sido el último campo de entrada manual.
 * Es crucial para el autocalculado reactivo.
 */
const setLastEdited = (field) => {
    emit('update:lastEditedField', field);
};
</script>

<template>
    <div class="space-y-5">
        <!-- 1. Cantidad / Unidades -->
        <div>
            <InputLabel for="quantity" value="Cantidad (Unidades / Títulos)" class="dark:text-slate-300" />
            <TextInput
                id="quantity"
                type="number"
                step="any"
                class="mt-1 block w-full dark:bg-slate-700 dark:text-white"
                :class="{ 'blur-sm focus:blur-none transition-all': privacy }"
                v-model="form.quantity"
                @focus="setLastEdited('quantity')"
                @input="setLastEdited('quantity')"
                placeholder="0.00000000"
            />
        </div>

        <!-- 2. Precio Unitario e Información de Fuente -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <div class="flex justify-between">
                    <InputLabel for="price_per_unit" value="Precio Unitario" class="dark:text-slate-300" />
                    <span v-if="isFetchingPrice" class="text-[10px] text-blue-500 animate-pulse font-black uppercase">Obteniendo...</span>
                </div>
                <TextInput
                    id="price_per_unit"
                    type="number"
                    step="any"
                    class="mt-1 block w-full dark:bg-slate-700 dark:text-white"
                    :class="{ 'blur-sm focus:blur-none transition-all': privacy }"
                    v-model="form.price_per_unit"
                    @input="setLastEdited('price')"
                    placeholder="0.00"
                />
                <p v-if="priceSource" class="text-[9px] text-emerald-600 dark:text-emerald-400 mt-1 font-bold uppercase tracking-tighter">
                    ✓ {{ priceSource }}
                </p>
            </div>
            
            <!-- 3. Selección de Divisa -->
            <div>
                <InputLabel for="currency_code" value="Divisa de Operación" class="dark:text-slate-300" />
                <select
                    id="currency_code"
                    v-model="form.currency_code"
                    class="mt-1 block w-full border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-700"
                >
                    <option value="EUR">EUR (€)</option>
                    <option value="USD">USD ($)</option>
                    <option value="GBP">GBP (£)</option>
                    <option value="BTC">BTC</option>
                    <option value="ETH">ETH</option>
                </select>
            </div>
        </div>

        <!-- 4. Sección Avanzada (Comisiones e Impuestos) -->
        <div class="mt-4 border-t dark:border-slate-700 pt-4">
            <details class="group">
                <summary class="flex justify-between items-center font-black cursor-pointer list-none text-blue-600 dark:text-blue-400 hover:text-blue-800 uppercase text-[10px] tracking-widest">
                    <span>Opciones de Negociación Avanzadas</span>
                    <span class="transition group-open:rotate-180">
                        <svg fill="none" height="16" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="16"><path d="M6 9l6 6 6-6"></path></svg>
                    </span>
                </summary>
                
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 group-open:animate-in fade-in slide-in-from-top-1">
                    <!-- Comisión de Operación -->
                    <div>
                        <InputLabel for="fees" value="Comisión Broker / Red" class="dark:text-slate-300" />
                        <TextInput
                            id="fees"
                            type="number"
                            step="any"
                            class="mt-1 block w-full dark:bg-slate-700 dark:text-white"
                            v-model="form.fees"
                            placeholder="0.00"
                        />
                    </div>

                    <!-- Comisión de Cambio de Divisa -->
                    <div>
                        <InputLabel for="exchange_fees" value="Tasa de Cambio (FX)" class="dark:text-slate-300" />
                        <TextInput
                            id="exchange_fees"
                            type="number"
                            step="any"
                            class="mt-1 block w-full dark:bg-slate-700 dark:text-white"
                            v-model="form.exchange_fees"
                            placeholder="0.00"
                        />
                    </div>

                    <!-- Impuestos (Tobin, etc) -->
                    <div class="md:col-span-2">
                        <InputLabel for="tax" value="Impuestos / Retenciones" class="dark:text-slate-300" />
                        <TextInput
                            id="tax"
                            type="number"
                            step="any"
                            class="mt-1 block w-full dark:bg-slate-700 dark:text-white"
                            v-model="form.tax"
                            placeholder="0.00"
                        />
                    </div>
                </div>
            </details>
        </div>
    </div>
</template>
