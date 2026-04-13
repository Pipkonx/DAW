<script setup>
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import NavLink from '@/Components/NavLink.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import AssetSearch from '@/Components/AssetSearch.vue';

defineProps({
    isDark: Boolean,
    isPrivacyMode: Boolean,
    showingNavigationDropdown: Boolean
});

defineEmits(['toggleTheme', 'togglePrivacy', 'toggleMobileMenu']);
</script>

<template>
    <nav class="border-b border-slate-200 dark:border-slate-700 bg-white/80 dark:bg-slate-800/80 backdrop-blur-md sticky top-0 z-50 transition-colors duration-300">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between">
                <!-- Sección Izquierda: Logo y Enlaces -->
                <div class="flex">
                    <div class="flex shrink-0 items-center">
                        <Link :href="route('dashboard')">
                            <ApplicationLogo class="block h-9 w-auto fill-current text-slate-800 dark:text-slate-200" />
                        </Link>
                    </div>

                    <div class="hidden space-x-4 sm:-my-px sm:ms-10 sm:flex items-center">
                        <!-- Menú Patrimonio -->
                        <Dropdown align="left" width="48">
                            <template #trigger>
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 focus:outline-none transition">
                                    Patrimonio
                                    <svg class="ms-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                                </button>
                            </template>
                            <template #content>
                                <DropdownLink :href="route('dashboard')">Dashboard</DropdownLink>
                                <DropdownLink :href="route('transactions.index')">Mi Patrimonio</DropdownLink>
                                <DropdownLink :href="route('expenses.index')">Gastos</DropdownLink>
                                <DropdownLink :href="route('financial-planning.index')">Planificación</DropdownLink>
                            </template>
                        </Dropdown>

                        <!-- Menú Mercados -->
                        <Dropdown align="left" width="48">
                            <template #trigger>
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 focus:outline-none transition">
                                    Mercados
                                    <svg class="ms-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                                </button>
                            </template>
                            <template #content>
                                <DropdownLink :href="route('markets.index')">Ver Mercados</DropdownLink>
                                <DropdownLink :href="route('ai-analyst.index')">Asistente IA</DropdownLink>
                            </template>
                        </Dropdown>

                        <NavLink :href="route('social.feed')" :active="route().current('social.feed')">Feed</NavLink>
                        
                        <NavLink v-if="$page.props.auth.user.is_admin" :href="route('admin.dashboard')" :active="route().current('admin.dashboard*')" class="text-amber-600 dark:text-amber-400 font-bold">Admin</NavLink>
                    </div>
                </div>

                <!-- Buscador Central -->
                <div class="hidden sm:flex flex-1 items-center justify-center px-8">
                    <AssetSearch />
                </div>

                <!-- Sección Derecha: Toggles y Perfil -->
                <div class="hidden sm:ms-6 sm:flex sm:items-center gap-4">
                    <!-- Privacidad -->
                    <button @click="$emit('togglePrivacy')" class="p-2 rounded-full text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700 transition">
                        <svg v-if="isPrivacyMode" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>

                    <!-- Tema -->
                    <button @click="$emit('toggleTheme')" class="p-2 rounded-full text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700 transition">
                        <svg v-if="isDark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                        </svg>
                    </button>

                    <!-- Configuración -->
                    <div class="relative ms-3">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="flex items-center rounded-full border-2 border-transparent transition">
                                    <img v-if="$page.props.auth.user.avatar" :src="$page.props.auth.user.avatar" class="h-8 w-8 rounded-full object-cover" />
                                    <div v-else class="h-8 w-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A9.793 9.793 0 0017.666 16c-.715-.827-1.572-1.555-2.558-2.023A6.244 6.244 0 0110 12.862" /></svg>
                                    </div>
                                </button>
                            </template>
                            <template #content>
                                <div class="px-4 py-2 text-xs text-slate-400">{{ $page.props.auth.user.name }}</div>
                                <DropdownLink :href="route('profile.edit')">Perfil</DropdownLink>
                                <DropdownLink :href="route('profile.security')">Seguridad</DropdownLink>
                                <DropdownLink :href="route('support.index')">Centro de Ayuda</DropdownLink>
                                <a :href="route('anteproyecto.download')" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100" download>Anteproyecto</a>
                                <div class="border-t border-slate-100 dark:border-slate-700"></div>
                                <DropdownLink :href="route('logout')" method="post" as="button">Cerrar Sesión</DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>

                <!-- Botón Móvil -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="$emit('toggleMobileMenu')" class="p-2 rounded-md text-slate-400 hover:bg-slate-100 transition">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>
</template>
