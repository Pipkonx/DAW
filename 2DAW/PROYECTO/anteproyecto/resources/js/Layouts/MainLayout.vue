<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

// Props para controlar la visibilidad de elementos si es necesario
defineProps({
    canLogin: {
        type: Boolean,
        default: true
    },
    canRegister: {
        type: Boolean,
        default: true
    }
});

const isDark = ref(false);

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
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    }
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 font-sans selection:bg-blue-500 selection:text-white flex flex-col transition-colors duration-300">
        <!-- Navbar -->
        <nav class="w-full py-6 px-6 lg:px-12 flex justify-between items-center max-w-7xl mx-auto border-b border-transparent dark:border-slate-800">
            <Link href="/" class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white hover:opacity-80 transition flex items-center gap-2">
                Fintech<span class="text-blue-600 dark:text-blue-500">Pro</span>
            </Link>
            
            <div class="flex items-center gap-4">
                <div v-if="canLogin" class="flex gap-4 items-center">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="font-medium text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition"
                    >
                        Dashboard
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="font-medium text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition px-4 py-2"
                        >
                            Iniciar Sesión
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="font-medium text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 transition px-5 py-2 rounded-full shadow-lg shadow-blue-500/30"
                        >
                            Registrarse
                        </Link>
                    </template>
                </div>

                <!-- Theme Toggle Button -->
                <button 
                    @click="toggleTheme" 
                    class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                    title="Cambiar tema"
                >
                    <svg v-if="isDark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                </button>
            </div>
        </nav>

        <!-- Main Content Slot -->
        <main class="flex-grow">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="py-12 border-t border-slate-100 dark:border-slate-800 bg-gray-50 dark:bg-slate-900 mt-auto transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-6 lg:px-12 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="text-slate-500 dark:text-slate-400 text-sm">
                    &copy; 2026 FintechPro. Todos los derechos reservados.
                </div>
                <div class="flex gap-6 text-sm font-medium text-slate-600 dark:text-slate-400">
                    <Link :href="route('legal.terms')" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Términos y Condiciones</Link>
                    <Link :href="route('legal.privacy')" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Política de Privacidad</Link>
                    <Link :href="route('legal.notice')" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Aviso Legal</Link>
                </div>
            </div>
        </footer>
    </div>
</template>
