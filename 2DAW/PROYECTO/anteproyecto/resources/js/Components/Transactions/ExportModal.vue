<script setup>
import { ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    format: {
        type: String,
        default: 'pdf'
    },
    minDate: {
        type: String,
        default: ''
    }
});

const emit = defineEmits(['close', 'confirm']);

const startDate = ref('');
const endDate = ref('');

// Reset dates when modal opens
watch(() => props.show, (newVal) => {
    if (newVal) {
        startDate.value = props.minDate || new Date().toISOString().split('T')[0];
        endDate.value = new Date().toISOString().split('T')[0];
    }
});

const onConfirm = () => {
    emit('confirm', {
        format: props.format,
        start_date: startDate.value,
        end_date: endDate.value
    });
};

const onClose = () => {
    emit('close');
};
</script>

<template>
    <Modal :show="show" @close="onClose">
        <div class="p-6">
            <h2 class="text-lg font-medium text-slate-900 dark:text-white mb-4">
                Exportar Historial ({{ format === 'pdf' ? 'PDF' : 'Excel' }})
            </h2>
            
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                Selecciona el rango de fechas para el reporte.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <InputLabel for="start_date" value="Fecha Inicio" />
                    <TextInput
                        id="start_date"
                        type="date"
                        class="mt-1 block w-full"
                        v-model="startDate"
                        required
                    />
                </div>
                <div>
                    <InputLabel for="end_date" value="Fecha Fin" />
                    <TextInput
                        id="end_date"
                        type="date"
                        class="mt-1 block w-full"
                        v-model="endDate"
                        required
                    />
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <SecondaryButton @click="onClose">
                    Cancelar
                </SecondaryButton>
                <PrimaryButton @click="onConfirm" :class="format === 'pdf' ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' : 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500'">
                    Descargar {{ format === 'pdf' ? 'PDF' : 'Excel' }}
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
