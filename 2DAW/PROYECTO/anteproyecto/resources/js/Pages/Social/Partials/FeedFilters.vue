<script setup>
import { Link } from '@inertiajs/vue3';

/**
 * FeedFilters - Pestañas de descubrimiento de contenido.
 * 
 * Permite al usuario alternar entre diferentes algoritmos de visualización 
 * (orden cronológico, seguidos o popularidad).
 */
const props = defineProps({
    activeTab: String, // Pestaña actual según la URL
});
</script>

<template>
    <div class="flex gap-4 border-b border-slate-200 dark:border-slate-700 pb-2 mb-6">
        <!-- Pestaña: Reciente -->
        <Link 
            :href="route('social.feed', { tab: 'recent' })" 
            class="px-4 py-2 text-sm font-bold transition-all relative"
            :class="activeTab === 'recent' || !activeTab ? 'text-blue-600' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white group'"
        >
            Reciente
            <div v-if="activeTab === 'recent' || !activeTab" class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 rounded-full"></div>
            <div v-else class="absolute bottom-0 left-1/2 right-1/2 group-hover:left-2 group-hover:right-2 h-0.5 bg-slate-200 dark:bg-slate-700 transition-all rounded-full"></div>
        </Link>

        <!-- Pestaña: Para Ti (Siguiendo) -->
        <Link 
            :href="route('social.feed', { tab: 'following' })" 
            class="px-4 py-2 text-sm font-bold transition-all relative"
            :class="activeTab === 'following' ? 'text-blue-600' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white group'"
        >
            Para ti
            <div v-if="activeTab === 'following'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 rounded-full"></div>
            <div v-else class="absolute bottom-0 left-1/2 right-1/2 group-hover:left-2 group-hover:right-2 h-0.5 bg-slate-200 dark:bg-slate-700 transition-all rounded-full"></div>
        </Link>
        
        <!-- Pestaña: Lo Mejor (Popularidad) -->
        <Link 
            :href="route('social.feed', { tab: 'best' })" 
            class="px-4 py-2 text-sm font-bold transition-all relative"
            :class="activeTab === 'best' ? 'text-blue-600' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white group'"
        >
            Lo mejor
            <div v-if="activeTab === 'best'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 rounded-full"></div>
            <div v-else class="absolute bottom-0 left-1/2 right-1/2 group-hover:left-2 group-hover:right-2 h-0.5 bg-slate-200 dark:bg-slate-700 transition-all rounded-full"></div>
        </Link>
    </div>
</template>
