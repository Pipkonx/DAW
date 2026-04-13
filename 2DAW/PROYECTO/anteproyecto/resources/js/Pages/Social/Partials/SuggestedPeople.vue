<script setup>
import { Link } from '@inertiajs/vue3';

/**
 * SuggestedPeople - Widget para mostrar personas destacadas en el feed.
 * 
 * En esta versión, se muestra el porcentaje de rentabilidad diaria.
 */
const props = defineProps({
    people: {
        type: Array,
        required: true // Se espera { id, name, username, avatar, tier, gain? }
    },
    title: {
        type: String,
        default: 'Personas Destacadas'
    }
});

/**
 * Retorna las clases CSS del anillo del avatar según el Tier del usuario o su estado.
 */
const getAvatarRingClasses = (tier) => {
    switch (tier) {
        case 'legend': return 'ring-2 ring-amber-500 ring-offset-2 dark:ring-offset-slate-900 border-transparent transition-all';
        case 'premium': return 'ring-2 ring-purple-500 ring-offset-2 dark:ring-offset-slate-900 border-transparent';
        case 'pro': return 'ring-2 ring-indigo-500 ring-offset-2 dark:ring-offset-slate-900 border-transparent';
        default: return 'border border-slate-200 dark:border-slate-700';
    }
};
</script>

<template>
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700">
        <!-- Título con Icono de Personas -->
        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-6 flex items-center justify-between">
            <span class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                {{ title }}
            </span>
            <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-pulse"></span>
        </h3>

        <!-- Listado de Personas -->
        <div class="space-y-5">
            <Link 
                v-for="person in people.slice(0, 5)" 
                :key="person.id" 
                :href="person.tier === 'legend' ? route('famous-portfolios.show', person.id) : route('social.profile', person.username || `user_${person.id}`)" 
                class="flex items-center gap-3 group transition-all"
            >
                <!-- Avatar Compacto -->
                <div class="relative shrink-0">
                    <img 
                        :src="person.avatar || `https://ui-avatars.com/api/?name=${person.name}&background=6366f1&color=fff`" 
                        :class="['w-10 h-10 rounded-xl object-cover transition-transform group-hover:scale-105 duration-200 shadow-sm', getAvatarRingClasses(person.tier)]" 
                    />
                    <div v-if="person.tier === 'legend'" class="absolute -top-1.5 -left-1.5 text-xs">👑</div>
                </div>

                <!-- Información del Usuario -->
                <div class="min-w-0 flex-1">
                    <div class="text-xs font-black text-slate-800 dark:text-white truncate group-hover:text-indigo-600 transition-colors uppercase tracking-tight">
                        {{ person.name }}
                    </div>
                </div>

                <!-- Ganancia Porcentual (Derecha) -->
                <div v-if="person.gain !== undefined" class="shrink-0 text-right">
                    <div :class="[
                        'text-xs font-black font-mono px-2 py-0.5 rounded-lg border',
                        person.gain >= 0 
                            ? 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30' 
                            : 'bg-rose-50 text-rose-600 border-rose-100 dark:bg-rose-900/20 dark:text-rose-400 dark:border-rose-800/30'
                    ]">
                        {{ person.gain >= 0 ? '+' : '' }}{{ person.gain.toFixed(2) }}%
                    </div>
                </div>
            </Link>

            <div v-if="!people?.length" class="text-center py-4">
                <p class="text-[10px] text-slate-400 italic font-medium tracking-widest">Sincronizando mentes maestras...</p>
            </div>
        </div>

        <!-- Enlace Opcional (Pie del Widget) -->
        <div class="mt-8 pt-4 border-t border-slate-50 dark:border-slate-700/50">
            <p class="text-[8px] text-slate-400 font-bold text-center uppercase tracking-widest leading-relaxed">
                Rendimiento hoy de los inversores más influyentes del mundo
            </p>
        </div>
    </div>
</template>
