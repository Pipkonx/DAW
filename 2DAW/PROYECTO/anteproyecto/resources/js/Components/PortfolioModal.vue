<script setup>
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
    show: Boolean,
    portfolio: Object, // null if creating
});

const emit = defineEmits(['close']);

const form = useForm({
    name: '',
});

watch(() => props.portfolio, (newVal) => {
    if (newVal) {
        form.name = newVal.name;
    } else {
        form.name = ''; // Reset for create mode
    }
}, { immediate: true });

const submit = () => {
    if (props.portfolio) {
        form.put(route('portfolios.update', props.portfolio.id), {
            onSuccess: () => close(),
        });
    } else {
        form.post(route('portfolios.store'), {
            onSuccess: () => {
                form.reset();
                close();
            },
        });
    }
};

const close = () => {
    emit('close');
    form.clearErrors();
};
</script>

<template>
    <Modal :show="show" @close="close">
        <div class="p-6 bg-white dark:bg-slate-800">
            <h2 class="text-lg font-medium text-slate-900 dark:text-slate-100">
                {{ portfolio ? 'Editar Cartera' : 'Nueva Cartera' }}
            </h2>

            <div class="mt-6">
                <InputLabel for="name" value="Nombre de la Cartera" class="dark:text-slate-300" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full dark:bg-slate-900 dark:border-slate-700 dark:text-slate-300"
                    placeholder="Ej. Trade Republic, MyInvestor..."
                    autofocus
                    @keyup.enter="submit"
                />
                <p v-if="form.errors.name" class="text-sm text-red-600 dark:text-red-400 mt-2">{{ form.errors.name }}</p>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <SecondaryButton @click="close" class="dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 dark:border-slate-600"> Cancelar </SecondaryButton>
                <PrimaryButton
                    class=""
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="submit"
                >
                    {{ portfolio ? 'Guardar Cambios' : 'Crear Cartera' }}
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
