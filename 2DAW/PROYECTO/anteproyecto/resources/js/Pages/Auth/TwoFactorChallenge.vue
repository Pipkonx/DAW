<script setup>
import { ref, onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/BaseUI/InputError.vue';
import InputLabel from '@/Components/BaseUI/InputLabel.vue';
import PrimaryButton from '@/Components/BaseUI/PrimaryButton.vue';
import TextInput from '@/Components/BaseUI/TextInput.vue';
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

        <form @submit.prevent="submit" class="space-y-8">
            <div class="space-y-4">
                <InputLabel for="code" value="Código de Seguridad de 6 dígitos" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 text-center" />
                
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-blue-600 rounded-[1.5rem] blur opacity-25 group-hover:opacity-50 transition duration-1000 group-focus-within:opacity-75"></div>
                    <TextInput
                        id="code"
                        ref="codeInput"
                        v-model="form.code"
                        type="text"
                        class="relative block w-full text-center text-5xl font-black tracking-[0.6rem] py-8 px-4 bg-white dark:bg-slate-950 border-2 border-slate-100 dark:border-slate-800 rounded-[1.5rem] focus:border-indigo-500 focus:ring-0 shadow-2xl text-slate-900 dark:text-white transition-all"
                        required
                        autofocus
                        autocomplete="one-time-code"
                        maxlength="6"
                        placeholder="000000"
                    />
                </div>

                <InputError class="mt-4 text-center font-bold uppercase text-[10px] tracking-widest" :message="form.errors.code" />
            </div>

            <div class="pt-4">
                <PrimaryButton 
                    class="w-full justify-center py-6 rounded-[1.5rem] text-xs font-black uppercase tracking-[0.25em] shadow-2xl shadow-indigo-500/40 active:scale-[0.97] transition-all bg-indigo-600 hover:bg-indigo-700 border-none group" 
                    :class="{ 'opacity-25': form.processing }" 
                    :disabled="form.processing"
                >
                    <span v-if="!form.processing">Verificar Mi Identidad</span>
                    <span v-else class="flex items-center gap-2">
                        <ArrowPathIcon class="w-5 h-5 animate-spin" />
                        Verificando...
                    </span>
                    <RocketLaunchIcon v-if="!form.processing" class="w-5 h-5 ml-2 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform" />
                </PrimaryButton>
            </div>

            <div class="flex flex-col items-center gap-4">
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest text-center leading-relaxed">
                    Abre tu aplicación de autenticación para obtener el código dinámico.
                </p>
                <Link :href="route('login')" class="text-[10px] text-indigo-500 hover:text-indigo-600 font-black uppercase tracking-widest transition-colors">
                    Volver al inicio de sesión
                </Link>
            </div>
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
