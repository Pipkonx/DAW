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
    asset: Object,
});

const emit = defineEmits(['close']);

const form = useForm({
    name: '',
    ticker: '',
    color: '#3b82f6',
    sector: '',
    industry: '',
    region: '',
    country: '',
    currency_code: 'EUR',
});

watch(() => props.asset, (newVal) => {
    if (newVal) {
        form.name = newVal.name;
        form.ticker = newVal.ticker;
        form.color = newVal.color || '#3b82f6';
        form.sector = newVal.sector || '';
        form.industry = newVal.industry || '';
        form.region = newVal.region || '';
        form.country = newVal.country || '';
        form.currency_code = newVal.currency_code || 'EUR';
    }
}, { immediate: true });

const submit = () => {
    if (props.asset) {
        form.put(route('assets.update', props.asset.id), {
            onSuccess: () => close(),
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
                Editar Activo: {{ asset ? asset.name : '' }}
            </h2>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Basic Info -->
                <div class="col-span-1 md:col-span-2">
                    <InputLabel for="name" value="Nombre del Activo" class="dark:text-slate-300" />
                    <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full dark:bg-slate-900 dark:border-slate-700 dark:text-slate-300" />
                    <p v-if="form.errors.name" class="text-sm text-red-600 dark:text-red-400 mt-1">{{ form.errors.name }}</p>
                </div>

                <div>
                    <InputLabel for="ticker" value="Ticker / Símbolo" class="dark:text-slate-300" />
                    <TextInput id="ticker" v-model="form.ticker" type="text" class="mt-1 block w-full dark:bg-slate-900 dark:border-slate-700 dark:text-slate-300" />
                    <p v-if="form.errors.ticker" class="text-sm text-red-600 dark:text-red-400 mt-1">{{ form.errors.ticker }}</p>
                </div>

                <div>
                    <InputLabel for="color" value="Color (Hex)" class="dark:text-slate-300" />
                    <div class="flex items-center space-x-2 mt-1">
                        <input type="color" v-model="form.color" class="h-10 w-10 rounded border border-slate-300 dark:border-slate-700 cursor-pointer bg-white dark:bg-slate-900 p-1" />
                        <TextInput id="color" v-model="form.color" type="text" class="block w-full dark:bg-slate-900 dark:border-slate-700 dark:text-slate-300" />
                    </div>
                    <p v-if="form.errors.color" class="text-sm text-red-600 dark:text-red-400 mt-1">{{ form.errors.color }}</p>
                </div>

                <!-- Metadata -->
                <div>
                    <InputLabel for="sector" value="Sector" class="dark:text-slate-300" />
                    <TextInput id="sector" v-model="form.sector" type="text" class="mt-1 block w-full dark:bg-slate-900 dark:border-slate-700 dark:text-slate-300" placeholder="Ej. Tecnología" />
                </div>

                <div>
                    <InputLabel for="industry" value="Industria" class="dark:text-slate-300" />
                    <TextInput id="industry" v-model="form.industry" type="text" class="mt-1 block w-full dark:bg-slate-900 dark:border-slate-700 dark:text-slate-300" placeholder="Ej. Software" />
                </div>

                <div>
                    <InputLabel for="region" value="Región" class="dark:text-slate-300" />
                    <TextInput id="region" v-model="form.region" type="text" class="mt-1 block w-full dark:bg-slate-900 dark:border-slate-700 dark:text-slate-300" placeholder="Ej. Norteamérica" />
                </div>

                <div>
                    <InputLabel for="country" value="País" class="dark:text-slate-300" />
                    <TextInput id="country" v-model="form.country" type="text" class="mt-1 block w-full dark:bg-slate-900 dark:border-slate-700 dark:text-slate-300" placeholder="Ej. EE.UU." />
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <SecondaryButton @click="close" class="dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 dark:border-slate-600"> Cancelar </SecondaryButton>
                <PrimaryButton
                    class=""
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="submit"
                >
                    Guardar Cambios
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
