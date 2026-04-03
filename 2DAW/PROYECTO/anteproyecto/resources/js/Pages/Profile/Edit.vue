<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import ManageBlockedUsers from './Partials/ManageBlockedUsers.vue';
import ManageSubscription from './Partials/ManageSubscription.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: { type: Boolean },
    status:          { type: String },
    blockedUsers:    { type: Array, default: () => [] },
    subscription:    { type: Object, default: null },
});

const activeTab = ref('profile');

const tabs = [
    {
        id: 'profile',
        label: 'Información de Perfil',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>`,
    },
    {
        id: 'password',
        label: 'Contraseña',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>`,
    },
    {
        id: 'privacy',
        label: 'Privacidad',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636M12 12v.01"/>`,
    },
    {
        id: 'subscription',
        label: 'Membresía',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>`,
    },
    {
        id: 'danger',
        label: 'Zona de Peligro',
        danger: true,
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>`,
    },
];
</script>

<template>
    <Head title="Ajustes de Cuenta" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-black leading-tight text-slate-800 dark:text-white tracking-tight">
                Ajustes de Cuenta
            </h2>
        </template>

        <div class="py-10 bg-slate-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row gap-8 items-start">

                    <!-- ══════════════ SIDEBAR DE NAVEGACIÓN ══════════════ -->
                    <aside class="w-full lg:w-64 shrink-0 lg:sticky lg:top-24">
                        <nav class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 p-3 space-y-1">
                            <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 px-3 pt-2 pb-1">
                                Mi cuenta
                            </p>

                            <button
                                v-for="tab in tabs"
                                :key="tab.id"
                                @click="activeTab = tab.id"
                                :class="[
                                    'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 text-left',
                                    activeTab === tab.id
                                        ? tab.danger
                                            ? 'bg-rose-500 text-white shadow-lg shadow-rose-500/20'
                                            : 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20'
                                        : tab.danger
                                            ? 'text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20'
                                            : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'
                                ]"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-html="tab.icon"></svg>
                                {{ tab.label }}

                                <!-- Indicador activo -->
                                <span v-if="activeTab === tab.id" class="ml-auto w-1.5 h-1.5 rounded-full bg-white/60"></span>
                            </button>
                        </nav>
                    </aside>

                    <!-- ══════════════ CONTENIDO PRINCIPAL ══════════════ -->
                    <main class="flex-1 min-w-0">
                        <transition
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="opacity-0 translate-y-2"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition duration-100 ease-in"
                            leave-from-class="opacity-100 translate-y-0"
                            leave-to-class="opacity-0 translate-y-2"
                            mode="out-in"
                        >
                            <!-- ─── INFORMACIÓN DE PERFIL ─── -->
                            <div v-if="activeTab === 'profile'" key="profile"
                                class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 p-8">
                                <UpdateProfileInformationForm
                                    :must-verify-email="mustVerifyEmail"
                                    :status="status"
                                />
                            </div>

                            <!-- ─── CONTRASEÑA ─── -->
                            <div v-else-if="activeTab === 'password'" key="password"
                                class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 p-8">
                                <UpdatePasswordForm />
                            </div>

                            <!-- ─── PRIVACIDAD (BLOQUEADOS) ─── -->
                            <div v-else-if="activeTab === 'privacy'" key="privacy"
                                class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 p-8">
                                <ManageBlockedUsers :blocked-users="blockedUsers" />
                            </div>

                            <!-- ─── MEMBRESÍA ─── -->
                            <div v-else-if="activeTab === 'subscription'" key="subscription"
                                class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 p-8">
                                <ManageSubscription :subscription="subscription" />
                            </div>

                            <!-- ─── ZONA DE PELIGRO ─── -->
                            <div v-else-if="activeTab === 'danger'" key="danger"
                                class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-rose-200 dark:border-rose-900 p-8">
                                <DeleteUserForm />
                            </div>
                        </transition>
                    </main>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
