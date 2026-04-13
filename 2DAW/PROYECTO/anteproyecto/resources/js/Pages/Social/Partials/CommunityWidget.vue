<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

/**
 * CommunityWidget - Clasificación de los usuarios más influyentes.
 * 
 * Incentiva la participación mostrando a los creadores que más valor aportan 
 * a la red mediante análisis y comentarios.
 */
const props = defineProps({
    topCreators: Array,
    activeCreators: Array,
});

const activeCreatorsTab = ref('popular'); // 'popular' | 'active'

/**
 * Retorna las clases CSS del anillo del avatar según el Tier del usuario.
 */
const getAvatarRingClasses = (tier) => {
    switch (tier) {
        case 'premium': return 'ring-2 ring-purple-500 ring-offset-2 dark:ring-offset-slate-900 border-transparent';
        case 'pro': return 'ring-2 ring-indigo-500 ring-offset-2 dark:ring-offset-slate-900 border-transparent';
        case 'basic': return 'ring-2 ring-blue-500 ring-offset-2 dark:ring-offset-slate-900 border-transparent';
        default: return 'border border-slate-200 dark:border-slate-700';
    }
};
</script>

<template>
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700">
        <!-- Título con Icono de Grupo -->
        <h3 class="text-sm font-black uppercase tracking-widest text-slate-400 mb-6 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Comunidad
        </h3>

        <!-- Selector de Pestañas de Audiencia -->
        <div class="flex p-1 bg-slate-50 dark:bg-slate-900/50 rounded-xl mb-6">
            <button @click="activeCreatorsTab = 'popular'" :class="activeCreatorsTab === 'popular' ? 'bg-white dark:bg-slate-800 text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-1.5 text-[10px] font-black uppercase tracking-tighter rounded-lg transition-all">Más Populares</button>
            <button @click="activeCreatorsTab = 'active'" :class="activeCreatorsTab === 'active' ? 'bg-white dark:bg-slate-800 text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-1.5 text-[10px] font-black uppercase tracking-tighter rounded-lg transition-all">Más Activos</button>
        </div>

        <!-- Listado de Usuarios Destacados -->
        <div class="space-y-6">
            <!-- Opción A: Pioneros (Más Reacciones) -->
            <template v-if="activeCreatorsTab === 'popular'">
                <Link v-for="(creator, idx) in topCreators" :key="'pop-' + creator.id" :href="route('social.profile', creator.username || `user_${creator.id}`)" class="flex items-start gap-4 group cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 p-2 -mx-2 rounded-2xl transition-all">
                    <div class="relative">
                        <img :src="creator.avatar || `https://ui-avatars.com/api/?name=${creator.name}`" :class="['w-10 h-10 rounded-xl object-cover', getAvatarRingClasses(creator.tier)]" />
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-indigo-600 text-white text-[8px] font-black rounded-lg flex items-center justify-center border-2 border-white dark:border-slate-800 shadow-sm">
                            #{{ idx + 1 }}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-slate-800 dark:text-white group-hover:text-indigo-600 transition-colors truncate">{{ creator.name }}</div>
                        <div class="text-[10px] text-slate-500 font-bold tracking-tight">
                            {{ creator.reactions_count || 0 }} reacciones
                        </div>
                    </div>
                </Link>
                <p v-if="!topCreators?.length" class="text-[10px] text-slate-400 italic text-center py-4">Sin datos de popularidad</p>
            </template>

            <!-- Opción B: Colaboradores (Más Aportes esta semana) -->
            <template v-if="activeCreatorsTab === 'active'">
                <Link v-for="(creator, idx) in activeCreators" :key="'act-' + creator.id" :href="route('social.profile', creator.username || `user_${creator.id}`)" class="flex items-start gap-4 group cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 p-2 -mx-2 rounded-2xl transition-all">
                    <div class="relative">
                        <img :src="creator.avatar || `https://ui-avatars.com/api/?name=${creator.name}`" :class="['w-10 h-10 rounded-xl object-cover', getAvatarRingClasses(creator.tier)]" />
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 text-white text-[8px] font-black rounded-lg flex items-center justify-center border-2 border-white dark:border-slate-800 shadow-sm">
                            #{{ idx + 1 }}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-slate-800 dark:text-white group-hover:text-emerald-600 transition-colors truncate">{{ creator.name }}</div>
                        <div class="text-[10px] text-slate-500 font-bold tracking-tight flex items-center gap-1.5">
                            <span class="w-1 h-1 bg-emerald-500 rounded-full"></span>
                            {{ creator.posts_count || 0 }} aportes esta semana
                        </div>
                    </div>
                </Link>
                <p v-if="!activeCreators?.length" class="text-[10px] text-slate-400 italic text-center py-4">Muro muy tranquilo esta semana</p>
            </template>
        </div>
    </div>
</template>
