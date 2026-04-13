<script setup>
import { Link } from '@inertiajs/vue3';

/**
 * SidebarProfile - Widget de identidad del usuario en la barra lateral.
 * 
 * Muestra una vista resumida del perfil del usuario autenticado con accesos directos.
 */
const props = defineProps({
    user: Object,
});

/**
 * Define los estilos del anillo del avatar según el nivel de suscripción.
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
    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 text-center overflow-hidden">
        <!-- Banner del Usuario -->
        <div class="h-20 w-full bg-slate-200 dark:bg-slate-700 relative">
            <img v-if="user.banner_path" :src="`/storage/${user.banner_path}`" class="w-full h-full object-cover" />
            <div v-else class="w-full h-full bg-gradient-to-r from-blue-600 to-indigo-600"></div>
        </div>
        
        <!-- Foto y Nombre superpuestos -->
        <div class="-mt-10 mb-4 relative z-10 px-4">
            <img :src="user.avatar || `https://ui-avatars.com/api/?name=${user.name}`" 
                 :class="['w-20 h-20 rounded-2xl mx-auto bg-white object-cover shadow-lg', getAvatarRingClasses(user.tier)]" />
            <h3 class="mt-2 text-lg font-black text-slate-800 dark:text-white leading-tight">{{ user.name }}</h3>
            <p class="text-xs text-slate-500 font-bold italic">@{{ user.username || `user_${user.id}` }}</p>
        </div>
        
        <!-- Botones de Acción Rápida -->
        <div class="px-6 pb-6 space-y-2">
            <Link :href="route('social.profile', user.username || `user_${user.id}`)" class="block w-full py-2.5 bg-slate-50 dark:bg-slate-900/50 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-100 dark:border-slate-600 text-slate-600 dark:text-slate-300 text-[10px] font-black uppercase tracking-widest rounded-xl transition-colors">
                Ver mi Muro
            </Link>
            <Link :href="route('profile.edit')" class="block w-full py-2.5 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase tracking-widest rounded-xl transition-colors">
                Configuración
            </Link>
        </div>
    </div>
</template>
