<script setup>
import { ref, onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { usePrivacy } from '@/Composables/usePrivacy';
import { useToast } from '@/Composables/useToast';
import Navbar from './Partials/Navbar.vue';
import MobileMenu from './Partials/MobileMenu.vue';
import Toast from '@/Components/Toast.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import Footer from '@/Components/Footer.vue';
import AdSlot from '@/Components/AdSense/AdSlot.vue';
import AdBlockDetector from '@/Components/AdSense/AdBlockDetector.vue';

const { isPrivacyMode, togglePrivacyMode } = usePrivacy();
const { activeToasts, showToast, removeToast } = useToast();
const page = usePage();

// Gestión de notificaciones Flash de Inertia
watch(() => page.props.flash, (flash) => {
    if (flash?.success) showToast(flash.success, 'success');
    if (flash?.error) showToast(flash.error, 'error');
}, { deep: true, immediate: true });

const showingNavigationDropdown = ref(false);
const isDark = ref(true);

/**
 * Alterna entre modo claro y oscuro, persistiendo la preferencia en localStorage.
 */
const toggleTheme = () => {
    isDark.value = !isDark.value;
    document.documentElement.classList.toggle('dark', isDark.value);
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
};

onMounted(() => {
    isDark.value = localStorage.theme !== 'light';
    document.documentElement.classList.toggle('dark', isDark.value);
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-900 transition-colors duration-300">
        <!-- Sistema Global de Notificaciones (Toasts) -->
        <div class="fixed top-5 right-5 z-[200] flex flex-col gap-3 pointer-events-none w-80">
            <Toast 
                v-for="toast in activeToasts" 
                :key="toast.id" 
                v-bind="toast"
                @close="removeToast(toast.id)"
            />
        </div>

        <!-- Barra de Navegación Principal (Escritorio) -->
        <Navbar 
            :is-dark="isDark"
            :is-privacy-mode="isPrivacyMode"
            :showing-navigation-dropdown="showingNavigationDropdown"
            @toggle-theme="toggleTheme"
            @toggle-privacy="togglePrivacyMode"
            @toggle-mobile-menu="showingNavigationDropdown = !showingNavigationDropdown"
        />

        <!-- Menú Desplegable (Móvil) -->
        <MobileMenu :showing-navigation-dropdown="showingNavigationDropdown" />

        <!-- Cabecera de Página y Navegación de Migas (Breadcrumbs) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <Breadcrumbs />
        </div>

        <header v-if="$slots.header" class="bg-white dark:bg-slate-800 shadow transition-colors duration-300">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Contenido Principal de la Aplicación -->
        <main>
            <!-- Espacio publicitario superior -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                <AdSlot slot-id="7890123456" />
            </div>

            <!-- Inyección de contenido de página específica -->
            <slot />

            <!-- Espacio publicitario inferior -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
                <AdSlot slot-id="8901234567" />
            </div>
        </main>

        <!-- Pie de página global -->
        <Footer />

        <!-- Detector de Bloqueadores de Publicidad (Solo usuarios no-premium) -->
        <AdBlockDetector v-if="!$page.props.auth.user?.is_premium" />
    </div>
</template>
