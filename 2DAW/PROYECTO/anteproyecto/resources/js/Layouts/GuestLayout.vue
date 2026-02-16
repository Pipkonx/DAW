<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <!-- 
        GuestLayout: Plantilla para usuarios NO autenticados (Login, Registro, Recuperar contraseña).
        
        Diseño Fintech:
        - bg-slate-50: Fondo gris muy claro y frío.
        - flex-col items-center: Centrado vertical y horizontal del contenido.
    -->
    <div class="flex min-h-screen flex-col items-center justify-center bg-slate-50 dark:bg-slate-900 relative overflow-hidden p-6 transition-colors duration-300">
        
        <!-- Background Gradient Blur Effect (matches Welcome.vue) -->
        <div class="absolute top-0 left-0 right-0 h-96 bg-gradient-to-b from-blue-100/40 dark:from-blue-900/20 to-transparent blur-3xl -z-10 pointer-events-none"></div>

        <!-- Logo y enlace al inicio -->
        <div class="mb-8">
            <Link href="/">
                <ApplicationLogo class="fill-current text-slate-500 dark:text-slate-400" />
            </Link>
        </div>

        <!-- 
            Tarjeta de contenido (Formulario):
        -->
        <div
            class="w-full overflow-hidden bg-white dark:bg-slate-800 px-8 py-8 shadow-xl shadow-slate-200/50 dark:shadow-none sm:max-w-md sm:rounded-2xl border border-slate-100 dark:border-slate-700 relative z-10 transition-colors duration-300"
        >
            <slot />
        </div>
        
        <!-- Footer simple -->
        <div class="mt-8 flex flex-col items-center gap-2">
            <div class="flex gap-4 text-xs font-medium text-slate-500 dark:text-slate-400">
                <Link :href="route('legal.terms')" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Términos</Link>
                <Link :href="route('legal.privacy')" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Privacidad</Link>
                <Link :href="route('legal.notice')" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Aviso Legal</Link>
            </div>
            <div class="text-xs text-slate-400 dark:text-slate-500">
                &copy; 2026 FintechPro. Todos los derechos reservados.
            </div>
        </div>
    </div>
</template>
