<script setup>
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { useForm } from '@inertiajs/vue3';
import { watch, ref, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    show: Boolean,
    portfolio: Object, // null if creating
});

const emit = defineEmits(['close']);

const step = ref(1); // 1: Name, 2: Choice, 3: Upload, 4: Preview
const importedTransactions = ref([]);
const isDragging = ref(false);
const previewLoading = ref(false);
const previewError = ref(null);

const form = useForm({
    name: '',
    transactions: [],
});

watch(() => props.portfolio, (newVal) => {
    if (newVal) {
        form.name = newVal.name;
        step.value = 1;
    } else {
        form.name = ''; // Reset for create mode
        step.value = 1;
        importedTransactions.value = [];
    }
}, { immediate: true });

watch(() => props.show, (newVal) => {
    if (!newVal) {
        // Reset state on close
        setTimeout(() => {
            step.value = 1;
            importedTransactions.value = [];
            previewError.value = null;
        }, 300);
    }
});

const submitName = () => {
    if (props.portfolio) {
        // Update existing
        form.put(route('portfolios.update', props.portfolio.id), {
            onSuccess: () => close(),
        });
    } else {
        // Create new flow
        if (form.name.trim() === '') {
            form.errors.name = 'El nombre es obligatorio';
            return;
        }
        step.value = 2; // Go to Choice
    }
};

const skipImport = () => {
    // Create without transactions
    form.post(route('portfolios.store'), {
        onSuccess: () => close(),
    });
};

const previewImage = ref(null);

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    processFile(file);
};

const onDrop = (e) => {
    isDragging.value = false;
    const file = e.dataTransfer.files[0];
    processFile(file);
};

const processFile = async (file) => {
    if (!file) return;
    
    // Reset states
    previewLoading.value = true;
    previewError.value = null;
    previewImage.value = null;

    // Check if image for preview
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImage.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    const formData = new FormData();
    formData.append('file', file);

    try {
        const response = await axios.post(route('portfolios.preview-import'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        importedTransactions.value = response.data.transactions;
        if (importedTransactions.value.length === 0) {
            previewError.value = "No se encontraron transacciones válidas o el formato no es compatible.";
        } else {
            step.value = 4; // Go to Preview
        }
    } catch (error) {
        console.error(error);
        if (error.response?.data?.errors?.file) {
            previewError.value = error.response.data.errors.file[0];
        } else {
            previewError.value = error.response?.data?.message || "Error al procesar el archivo.";
        }
    } finally {
        previewLoading.value = false;
    }
};

const removeTransaction = (index) => {
    importedTransactions.value.splice(index, 1);
};

const updateAmount = (tx) => {
    if (tx.quantity && tx.price_per_unit) {
        tx.amount = (parseFloat(tx.quantity) * parseFloat(tx.price_per_unit)).toFixed(2);
    }
};

const confirmImport = () => {
    form.transactions = importedTransactions.value;
    form.post(route('portfolios.store'), {
        onSuccess: () => close(),
    });
};

const close = () => {
    emit('close');
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <Modal :show="show" @close="close">
        <div class="p-6 bg-white dark:bg-slate-800">
            <h2 class="text-lg font-medium text-slate-900 dark:text-slate-100 mb-6">
                {{ portfolio ? 'Editar Cartera' : 'Nueva Cartera' }}
            </h2>

            <!-- Step 1: Name -->
            <div v-if="step === 1">
                <InputLabel for="name" value="Nombre de la Cartera" class="dark:text-slate-300" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full dark:bg-slate-900 dark:border-slate-700 dark:text-slate-300"
                    placeholder="Ej. Trade Republic, MyInvestor..."
                    autofocus
                    @keyup.enter="submitName"
                />
                <p v-if="form.errors.name" class="text-sm text-red-600 dark:text-red-400 mt-2">{{ form.errors.name }}</p>

                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="close"> Cancelar </SecondaryButton>
                    <PrimaryButton @click="submitName">
                        {{ portfolio ? 'Guardar Cambios' : 'Siguiente' }}
                    </PrimaryButton>
                </div>
            </div>

            <!-- Step 2: Choice -->
            <div v-else-if="step === 2" class="space-y-4">
                <p class="text-slate-600 dark:text-slate-300">
                    ¿Deseas importar transacciones automáticamente para esta cartera?
                </p>
                <div class="flex flex-col gap-3">
                    <button @click="step = 3" class="flex items-center justify-center p-4 border-2 border-dashed border-indigo-200 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 dark:border-slate-600 dark:hover:border-indigo-400 dark:hover:bg-slate-700 transition-all group">
                        <div class="text-center">
                            <span class="block font-semibold text-indigo-600 dark:text-indigo-400 group-hover:text-indigo-700 dark:group-hover:text-indigo-300">Sí, importar archivo</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">CSV, PDF o Foto</span>
                        </div>
                    </button>
                    <button @click="skipImport" class="flex items-center justify-center p-4 border border-slate-200 rounded-lg hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-700 transition-all">
                        <span class="font-medium text-slate-600 dark:text-slate-300">Ingreso Manual / Crear vacía</span>
                    </button>
                </div>
                <div class="mt-4 flex justify-start">
                    <button @click="step = 1" class="text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 underline">
                        Atrás
                    </button>
                </div>
            </div>

            <!-- Step 3: Upload -->
            <div v-else-if="step === 3">
                <div 
                    class="border-2 border-dashed rounded-xl p-8 text-center transition-colors cursor-pointer"
                    :class="[
                        isDragging ? 'border-indigo-500 bg-indigo-50 dark:bg-slate-700' : 'border-slate-300 dark:border-slate-600',
                        previewLoading ? 'opacity-50 pointer-events-none' : ''
                    ]"
                    @dragover.prevent="isDragging = true"
                    @dragleave.prevent="isDragging = false"
                    @drop.prevent="onDrop"
                    @click="$refs.fileInput.click()"
                >
                    <input 
                        type="file" 
                        ref="fileInput" 
                        class="hidden" 
                        accept=".csv,.pdf,.txt,.jpg,.jpeg,.png" 
                        @change="handleFileUpload"
                    />
                    
                    <div v-if="previewLoading" class="flex flex-col items-center">
                        <svg class="animate-spin h-8 w-8 text-indigo-500 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-slate-600 dark:text-slate-300">Procesando archivo...</span>
                    </div>
                    <div v-else>
                        <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                            Arrastra tu archivo aquí o <span class="text-indigo-600 font-medium hover:underline">selecciónalo</span>
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            Soporta CSV, PDF y Fotos
                        </p>
                    </div>
                </div>

                <p v-if="previewError" class="mt-2 text-sm text-red-600 text-center">
                    {{ previewError }}
                </p>

                <div class="mt-4 flex justify-between">
                    <button @click="step = 2" class="text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 underline">
                        Atrás
                    </button>
                </div>
            </div>

            <!-- Step 4: Preview -->
            <div v-else-if="step === 4">
                <div v-if="previewImage" class="mb-4">
                     <p class="text-sm text-slate-600 dark:text-slate-300 mb-2 font-medium">Vista previa de imagen:</p>
                     <img :src="previewImage" class="max-h-40 rounded-lg border border-slate-200 dark:border-slate-700 mx-auto" />
                </div>
                
                <div class="mb-4">
                    <p class="text-sm text-slate-600 dark:text-slate-300">
                        Se han detectado <span class="font-bold text-slate-900 dark:text-white">{{ importedTransactions.length }}</span> transacciones.
                    </p>
                </div>

                <div class="max-h-60 overflow-y-auto border border-slate-200 dark:border-slate-700 rounded-lg">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-700">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Fecha</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Tipo</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Activo / ISIN</th>
                                <th class="px-2 py-2 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Cant.</th>
                                <th class="px-2 py-2 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Precio</th>
                                <th class="px-2 py-2 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Total</th>
                                <th class="px-2 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                            <tr v-for="(tx, idx) in importedTransactions" :key="idx" class="hover:bg-slate-50 dark:hover:bg-slate-700/50 group">
                                <td class="p-1">
                                    <input type="date" v-model="tx.date" class="w-full text-xs border-0 bg-transparent focus:ring-1 focus:ring-indigo-500 rounded px-1 py-1 text-slate-900 dark:text-slate-300" />
                                </td>
                                <td class="p-1">
                                    <select v-model="tx.type" class="w-full text-xs border-0 bg-transparent focus:ring-1 focus:ring-indigo-500 rounded px-1 py-1 text-slate-900 dark:text-slate-300">
                                        <option value="buy">Compra</option>
                                        <option value="sell">Venta</option>
                                        <option value="transfer_in">Depósito</option>
                                        <option value="transfer_out">Retiro</option>
                                        <option value="dividend">Dividendo</option>
                                    </select>
                                </td>
                                <td class="p-1">
                                    <input type="text" v-model="tx.ticker" class="w-full text-xs border-0 bg-transparent focus:ring-1 focus:ring-indigo-500 rounded px-1 py-1 text-slate-900 dark:text-slate-300 font-medium uppercase" />
                                </td>
                                <td class="p-1">
                                    <input type="number" step="any" v-model="tx.quantity" @input="updateAmount(tx)" class="w-full text-xs text-right border-0 bg-transparent focus:ring-1 focus:ring-indigo-500 rounded px-1 py-1 text-slate-900 dark:text-slate-300" />
                                </td>
                                <td class="p-1">
                                    <input type="number" step="any" v-model="tx.price_per_unit" @input="updateAmount(tx)" class="w-full text-xs text-right border-0 bg-transparent focus:ring-1 focus:ring-indigo-500 rounded px-1 py-1 text-slate-900 dark:text-slate-300" />
                                </td>
                                <td class="p-1">
                                    <input type="number" step="any" v-model="tx.amount" class="w-full text-xs text-right border-0 bg-transparent focus:ring-1 focus:ring-indigo-500 rounded px-1 py-1 text-slate-900 dark:text-slate-300 font-bold" />
                                </td>
                                <td class="p-1 text-center">
                                    <button @click="removeTransaction(idx)" class="text-slate-400 hover:text-red-500 transition-colors p-1 opacity-0 group-hover:opacity-100" title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="step = 3"> Atrás </SecondaryButton>
                    <PrimaryButton 
                        @click="confirmImport"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Confirmar e Importar
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </Modal>
</template>
