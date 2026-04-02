<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();

// Mapa de rutas a migas de pan
const breadcrumbMap = {
    'dashboard': [
        { label: 'Dashboard', href: route('dashboard') }
    ],
    'transactions.index': [
        { label: 'Dashboard', href: route('dashboard') },
        { label: 'Patrimonio Neto', href: route('transactions.index') }
    ],
    'expenses.index': [
        { label: 'Dashboard', href: route('dashboard') },
        { label: 'Análisis de Gastos', href: route('expenses.index') }
    ],
    'financial-planning.index': [
        { label: 'Dashboard', href: route('dashboard') },
        { label: 'Planificación Financiera', href: route('financial-planning.index') }
    ],
    'markets.index': [
        { label: 'Dashboard', href: route('dashboard') },
        { label: 'Mercados', href: route('markets.index') }
    ],
    'profile.edit': [
        { label: 'Dashboard', href: route('dashboard') },
        { label: 'Perfil', href: route('profile.edit') }
    ],
    // Añadir más rutas según sea necesario
};

const breadcrumbs = computed(() => {
    const currentRoute = route().current();
    
    // Si la ruta actual está en el mapa, devolvemos sus migas
    if (breadcrumbMap[currentRoute]) {
        return breadcrumbMap[currentRoute];
    }

    // Fallback: Intentar construir basado en segmentos si no está en el mapa
    // Esto es básico y puede mejorarse
    const path = window.location.pathname.split('/').filter(p => p);
    const crumbs = [{ label: 'Dashboard', href: route('dashboard') }];
    
    // Si estamos en la raíz o dashboard, ya está cubierto
    if (route().current('dashboard')) return crumbs;

    return crumbs; // Por defecto devolvemos al menos Dashboard
});
</script>

<template>
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li v-for="(crumb, index) in breadcrumbs" :key="index" class="inline-flex items-center">
                <div class="flex items-center">
                    <svg v-if="index > 0" class="w-3 h-3 text-slate-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    
                    <Link 
                        v-if="index < breadcrumbs.length - 1"
                        :href="crumb.href" 
                        class="inline-flex items-center text-sm font-medium text-slate-700 hover:text-blue-600 dark:text-slate-400 dark:hover:text-white"
                    >
                        <svg v-if="index === 0" class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                        </svg>
                        {{ crumb.label }}
                    </Link>
                    
                    <span 
                        v-else 
                        class="ms-1 text-sm font-medium text-slate-500 md:ms-2 dark:text-slate-400"
                    >
                        <span v-if="index === 0" class="flex items-center">
                            <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                            </svg>
                            {{ crumb.label }}
                        </span>
                        <span v-else>{{ crumb.label }}</span>
                    </span>
                </div>
            </li>
        </ol>
    </nav>
</template>
