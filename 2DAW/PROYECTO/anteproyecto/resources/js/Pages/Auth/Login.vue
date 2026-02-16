<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import GoogleLoginButton from '@/Components/GoogleLoginButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Iniciar Sesión" />

        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">¡Hola de nuevo! 👋</h2>
            <p class="text-slate-500 dark:text-slate-400 mt-2">Ingresa a tu cuenta para gestionar tus finanzas.</p>
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30 p-3 rounded-lg border border-green-200 dark:border-green-800">
            {{ status }}
        </div>

        <!-- Google Login -->
        <div class="mb-6">
            <GoogleLoginButton />
        </div>

        <div class="relative flex py-2 items-center mb-6">
            <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
            <span class="flex-shrink-0 mx-4 text-slate-400 dark:text-slate-500 text-sm">O usa tu email</span>
            <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="email" value="Correo Electrónico" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="ejemplo@correo.com"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <InputLabel for="password" value="Contraseña" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium transition-colors"
                    >
                        ¿Olvidaste tu contraseña?
                    </Link>
                </div>
                
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block">
                <label class="flex items-center cursor-pointer group">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-slate-600 dark:text-slate-400 group-hover:text-slate-800 dark:group-hover:text-slate-200 transition-colors">Recordar mi sesión</span>
                </label>
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center"
                    :class="{ 'opacity-75': form.processing }"
                    :disabled="form.processing"
                >
                    Iniciar Sesión
                </PrimaryButton>
            </div>

            <div class="text-center mt-6">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    ¿Aún no tienes cuenta? 
                    <Link :href="route('register')" class="text-blue-600 dark:text-blue-400 font-semibold hover:text-blue-800 dark:hover:text-blue-300 hover:underline transition-all">
                        Regístrate gratis
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
