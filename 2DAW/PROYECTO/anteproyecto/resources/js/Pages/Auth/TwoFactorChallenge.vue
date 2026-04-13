<script setup>
import { ref, onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ShieldCheckIcon, RocketLaunchIcon } from '@heroicons/vue/24/outline';

const codeInput = ref(null);

const form = useForm({
    code: '',
});

const submit = () => {
    form.post(route('login.2fa.store'));
};

onMounted(() => {
    if (codeInput.value) {
        codeInput.value.focus();
    }
});
</script>

<template>
    <GuestLayout>
        <Head title="Verificación de Identidad" />

        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-[2rem] bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 mb-6 group">
                <ShieldCheckIcon class="w-10 h-10 group-hover:scale-110 transition-transform" />
            </div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tight italic mb-2">
                Doble Factor
            </h1>
            <p class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                Introduce el código de 6 dígitos de tu aplicación de autenticación
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="code" value="Código de Seguridad" class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2" />
                
                <div class="relative">
                    <TextInput
                        id="code"
                        ref="codeInput"
                        v-model="form.code"
                        type="text"
                        class="block w-full text-center text-4xl font-black tracking-[0.5rem] py-6 px-4 bg-slate-50 dark:bg-slate-900/50 border-none rounded-[1.5rem] focus:ring-2 focus:ring-indigo-500 shadow-inner text-slate-900 dark:text-white"
                        required
                        autofocus
                        autocomplete="one-time-code"
                        maxlength="6"
                        placeholder="000 000"
                    />
                </div>

                <InputError class="mt-2 text-center" :message="form.errors.code" />
            </div>

            <div class="pt-2">
                <PrimaryButton 
                    class="w-full justify-center py-5 rounded-[1.5rem] text-xs font-black uppercase tracking-[0.2em] shadow-2xl shadow-indigo-500/20 active:scale-[0.98] transition-all bg-indigo-600 hover:bg-indigo-700" 
                    :class="{ 'opacity-25': form.processing }" 
                    :disabled="form.processing"
                >
                    Verificar Identidad
                    <RocketLaunchIcon class="w-5 h-5 ml-2 animate-pulse" />
                </PrimaryButton>
            </div>

            <p class="text-center text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                ¿No tienes acceso a tu teléfono? contacta con soporte
            </p>
        </form>
    </GuestLayout>
</template>

<style scoped>
/* Eliminar flechas en inputs numéricos si se usara type="number" */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>
