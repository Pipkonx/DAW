<script setup>
import { ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    _method: 'PATCH',
    name: user.name,
    username: user.username,
    email: user.email,
    bio: user.bio,
    avatar: null,
    banner: null,
    delete_photo: false,
});

const avatarPreviewUrl = ref(user.avatar || null);
const bannerPreviewUrl = ref(user.banner_path ? `/storage/${user.banner_path}` : null);

const handleAvatarChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.avatar = file;
        avatarPreviewUrl.value = URL.createObjectURL(file);
    }
};

const handleBannerChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.banner = file;
        bannerPreviewUrl.value = URL.createObjectURL(file);
    }
};

const removeAvatar = () => {
    form.avatar = null;
    form.delete_photo = true;
    avatarPreviewUrl.value = null;
};

const submitForm = () => {
    form.post(route('profile.update'), {
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                Información del Perfil
            </h2>

            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400 font-medium">
                Gestiona tu identidad visual y los datos básicos de tu cuenta.
            </p>
        </header>

        <form
            @submit.prevent="submitForm"
            class="mt-6 space-y-6"
        >
            <div class="space-y-4 p-6 bg-slate-50 dark:bg-slate-900/50 rounded-3xl border-2 border-dashed border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-xs font-black uppercase tracking-widest text-slate-500">Identidad Visual</span>
                </div>

                <!-- Banner Input -->
                <div>
                    <InputLabel for="banner" value="Imagen de Portada (Banner Panorámico)" />
                    <div class="mt-2 relative w-full aspect-[3/1] rounded-2xl bg-slate-200 dark:bg-slate-800 overflow-hidden border-2 border-slate-100 dark:border-slate-700 group">
                        <img v-if="bannerPreviewUrl" :src="bannerPreviewUrl" class="w-full h-full object-cover" />
                        <div v-else class="w-full h-full flex items-center justify-center text-slate-400 font-bold italic text-sm">
                            Haz clic para subir un banner panorámico
                        </div>
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer" @click="$refs.bannerInput.click()">
                            <span class="text-white text-xs font-black uppercase tracking-widest">Cambiar Banner</span>
                        </div>
                    </div>
                    <input type="file" ref="bannerInput" class="hidden" @change="handleBannerChange" accept="image/*" />
                    <InputError class="mt-2" :message="form.errors.banner" />
                </div>

                <div class="flex flex-col md:flex-row gap-8 items-center">
                    <!-- Avatar Input -->
                    <div class="flex-shrink-0">
                        <InputLabel for="avatar" value="Foto de Perfil" />
                        <div class="mt-2 relative w-24 h-24 rounded-3xl bg-slate-200 dark:bg-slate-800 overflow-hidden border-4 border-white dark:border-slate-700 shadow-xl group">
                            <img v-if="avatarPreviewUrl" :src="avatarPreviewUrl" class="w-full h-full object-cover" />
                            <div v-else class="w-full h-full flex items-center justify-center text-slate-400 font-bold text-xl uppercase italic">
                                {{ form.name.charAt(0) }}
                            </div>
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer" @click="$refs.avatarInput.click()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                        </div>
                        <input type="file" ref="avatarInput" class="hidden" @change="handleAvatarChange" accept="image/*" />
                        <div v-if="avatarPreviewUrl" class="mt-2 text-center">
                            <button 
                                type="button" 
                                @click="removeAvatar" 
                                class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-rose-500 hover:text-rose-600 transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Eliminar
                            </button>
                        </div>
                        <InputError class="mt-2" :message="form.errors.avatar" />
                    </div>

                    <!-- Datos básicos resumidos -->
                    <div class="flex-grow space-y-4 w-full">
                        <div>
                            <InputLabel for="name" value="Nombre Público" />
                            <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required />
                            <p class="text-[9px] mt-1 text-slate-400 font-bold uppercase tracking-widest italic">Tu nombre visible en la plataforma. Puede contener espacios.</p>
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="username" value="Identificador / @Usuario" />
                            <div class="flex mt-1">
                                <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-500 text-sm font-bold">@</span>
                                <TextInput id="username" type="text" class="block w-full !rounded-l-none" v-model="form.username" required />
                            </div>
                            <p class="text-[9px] mt-1 text-indigo-500 font-bold uppercase tracking-widest italic">Tu ID único para URLs y menciones. No debe contener espacios.</p>
                            <InputError class="mt-2" :message="form.errors.username" />
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <InputLabel for="bio" value="Biografía / Sobre ti" />
                <textarea
                    id="bio"
                    class="mt-1 block w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl shadow-sm min-h-[100px] text-sm text-slate-900 dark:text-white"
                    v-model="form.bio"
                    placeholder="Cuéntale a la comunidad sobre tu estrategia o intereses..."
                ></textarea>
                <div class="flex justify-between mt-1">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Máximo 1000 caracteres</p>
                    <p class="text-[10px] font-bold" :class="form.bio?.length > 900 ? 'text-rose-500' : 'text-slate-400'">{{ form.bio?.length || 0 }}/1000</p>
                </div>
                <InputError class="mt-2" :message="form.errors.bio" />
            </div>

            <div class="pt-6 border-t border-slate-100 dark:border-slate-800">
                <InputLabel for="email" value="Correo Electrónico Privado" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-slate-800 dark:text-slate-200">
                    Tu dirección de correo no ha sido verificada.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-slate-600 dark:text-slate-400 underline hover:text-slate-900 dark:hover:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800"
                    >
                        Haz clic aquí para reenviar el correo de verificación.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600 dark:text-green-400"
                >
                    Se ha enviado un nuevo enlace de verificación a tu correo.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Guardar</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-slate-600 dark:text-slate-400"
                    >
                        Guardado.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
