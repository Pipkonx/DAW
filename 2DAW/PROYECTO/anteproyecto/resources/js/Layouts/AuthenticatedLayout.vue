<script setup>
import { ref, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const isDark = ref(true); // Default to dark

const toggleTheme = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

onMounted(() => {
    // Check local storage or default to dark
    if (localStorage.theme === 'light') {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    } else {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    }
});
</script>

<template>
    <div>
        <!-- 
            AuthenticatedLayout: Plantilla principal para usuarios logueados (Dashboard, Perfil).
            bg-slate-50: Fondo base coherente con el estilo Fintech global.
        -->
        <div class="min-h-screen bg-slate-50 dark:bg-slate-900 transition-colors duration-300">
            <!-- 
                Barra de navegación:
            -->
            <nav
                class="border-b border-slate-200 dark:border-slate-700 bg-white/80 dark:bg-slate-800/80 backdrop-blur-md sticky top-0 z-50 transition-colors duration-300"
            >
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-slate-800 dark:text-slate-200"
                                    />
                                </Link>
                            </div>

                            <!-- Enlaces de navegación (Desktop) -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                    class="text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300"
                                >
                                    Dashboard
                                </NavLink>
                                <NavLink
                                    :href="route('expenses.index')"
                                    :active="route().current('expenses.index')"
                                    class="text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300"
                                >
                                    Análisis de Gastos
                                </NavLink>
                                <NavLink
                                    :href="route('transactions.index')"
                                    :active="route().current('transactions.index')"
                                    class="text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300"
                                >
                                    Patrimonio Neto
                                </NavLink>
                                <NavLink
                                    :href="route('financial-planning.index')"
                                    :active="route().current('financial-planning.index')"
                                    class="text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300"
                                >
                                    Planificación
                                </NavLink>
                                <NavLink
                                    :href="route('markets.index')"
                                    :active="route().current('markets.index')"
                                    class="text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300"
                                >
                                    Mercados
                                </NavLink>
                            </div>
                        </div>

                        <!-- Menú de usuario (Desktop) -->
                        <div class="hidden sm:ms-6 sm:flex sm:items-center gap-4">
                            <!-- Theme Toggle -->
                            <button 
                                @click="toggleTheme" 
                                class="p-2 rounded-full text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700 focus:outline-none transition-colors"
                                :title="isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
                            >
                                <!-- Sun Icon -->
                                <svg v-if="isDark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                </svg>
                                <!-- Moon Icon -->
                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                                </svg>
                            </button>

                            <!-- Dropdown de configuración -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button
                                            type="button"
                                            class="flex items-center rounded-full border-2 border-transparent focus:outline-none focus:border-slate-300 dark:focus:border-slate-600 transition duration-150 ease-in-out"
                                        >
                                            <img 
                                                v-if="$page.props.auth.user.avatar" 
                                                :src="$page.props.auth.user.avatar" 
                                                :alt="$page.props.auth.user.name"
                                                class="h-8 w-8 rounded-full object-cover"
                                            />
                                            <div 
                                                v-else 
                                                class="h-8 w-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-400"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A9.793 9.793 0 0017.666 16c-.715-.827-1.572-1.555-2.558-2.023A6.244 6.244 0 0110 12.862a6.244 6.244 0 01-5.108 1.115A9.783 9.783 0 004.501 20.118z" />
                                                </svg>
                                            </div>
                                        </button>
                                    </template>

                                    <template #content>
                                        <div class="block px-4 py-2 text-xs text-slate-400">
                                            {{ $page.props.auth.user.name }}
                                        </div>
                                        <DropdownLink :href="route('profile.edit')">
                                            Perfil
                                        </DropdownLink>
                                        <DropdownLink :href="route('anteproyecto.download')">
                                            Descargar Anteproyecto
                                        </DropdownLink>
                                        <div class="border-t border-slate-100 dark:border-slate-700"></div>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Cerrar Sesión
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Botón menú hamburguesa (Móvil) -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-slate-400 transition duration-150 ease-in-out hover:bg-slate-100 hover:text-slate-500 focus:bg-slate-100 focus:text-slate-500 focus:outline-none dark:hover:bg-slate-700 dark:hover:text-slate-300 dark:focus:bg-slate-700 dark:focus:text-slate-300"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Menú de navegación responsive (Móvil) -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shadow-lg transition-colors duration-300"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('expenses.index')"
                            :active="route().current('expenses.index')"
                        >
                            Análisis de Gastos
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('transactions.index')"
                            :active="route().current('transactions.index')"
                        >
                            Patrimonio Neto
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('financial-planning.index')"
                            :active="route().current('financial-planning.index')"
                        >
                            Planificación
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('markets.index')"
                            :active="route().current('markets.index')"
                        >
                            Mercados
                        </ResponsiveNavLink>
                    </div>

                    <!-- Opciones de usuario responsive -->
                    <div class="border-t border-slate-200 dark:border-slate-700 pb-1 pt-4">
                        <div class="px-4">
                            <div
                                class="text-base font-medium text-slate-800 dark:text-slate-200"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Perfil
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('anteproyecto.download')">
                                Descargar Anteproyecto
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Cerrar Sesión
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Cabecera de página (opcional) -->
            <header
                class="bg-white dark:bg-slate-800 shadow transition-colors duration-300"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Contenido principal -->
            <main>
                <slot />
            </main>

            <!-- Footer for Authenticated Users -->
            <footer class="py-8 border-t border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 mt-auto transition-colors duration-300">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="text-slate-400 text-sm">
                        &copy; 2026 FintechPro. Todos los derechos reservados.
                    </div>
                    <div class="flex gap-6 text-sm font-medium text-slate-500 dark:text-slate-400">
                        <Link :href="route('legal.terms')" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Términos</Link>
                        <Link :href="route('legal.privacy')" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Privacidad</Link>
                        <Link :href="route('legal.notice')" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Aviso Legal</Link>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>
