<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { useToast } from '@/Composables/useToast';

/**
 * ProfileHeader - Cabecera del perfil social.
 * 
 * Gestiona la identidad visual del usuario y las acciones de seguimiento/bloqueo.
 */
const props = defineProps({
    profileUser: Object,
    isOwnProfile: Boolean,
    isFollowing: Boolean,
    isBlocked: Boolean,
    joinedAt: String,
});

const emit = defineEmits(['toggle-block', 'toggle-follow']);

const { showToast } = useToast();
const showShareMenu = ref(false);

/**
 * Lógica para compartir el perfil en redes o copiar enlace.
 */
const shareProfile = (platform) => {
    const url = window.location.href;
    const text = `Sigue mis análisis y tesis de inversión en mi muro de Pipkonx: `;
    
    let shareUrl = '';
    switch (platform) {
        case 'whatsapp':
            shareUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(text + ' ' + url)}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`;
            break;
        case 'linkedin':
            shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`;
            break;
        case 'copy':
            navigator.clipboard.writeText(url);
            showToast('¡Enlace del perfil copiado al portapapeles!', 'success');
            showShareMenu.value = false;
            return;
    }
    
    if (shareUrl) window.open(shareUrl, '_blank');
    showShareMenu.value = false;
};

/**
 * Estilos reactivos para el anillo del avatar según el nivel de suscripción.
 */
const getAvatarRingClasses = (tier) => {
    switch (tier) {
        case 'premium': return 'ring-4 ring-purple-500 ring-offset-4 dark:ring-offset-slate-800 border-transparent transition-all';
        case 'pro': return 'ring-4 ring-indigo-500 ring-offset-4 dark:ring-offset-slate-800 border-transparent transition-all';
        case 'basic': return 'ring-4 ring-blue-500 ring-offset-4 dark:ring-offset-slate-800 border-transparent transition-all';
        default: return 'border-4 border-white dark:border-slate-800';
    }
};
</script>

<template>
    <div class="bg-white dark:bg-slate-800 rounded-3xl overflow-hidden shadow-xl border border-slate-100 dark:border-slate-700">
        <!-- Banner de Portada -->
        <div class="h-64 w-full bg-slate-200 dark:bg-slate-800 relative">
            <img v-if="profileUser.banner_path" :src="`/storage/${profileUser.banner_path}`" class="w-full h-full object-cover" />
            <div v-else class="w-full h-full bg-gradient-to-r from-indigo-500 to-purple-600"></div>
        </div>
        
        <div class="px-8 pb-8 relative">
            <!-- Avatar y Acciones Rápidas -->
            <div class="flex justify-between items-end -mt-16 mb-4">
                <img :src="profileUser.avatar || `https://ui-avatars.com/api/?name=${profileUser.name}`" 
                     :class="['w-32 h-32 rounded-3xl object-cover shadow-2xl bg-white relative z-10', getAvatarRingClasses(profileUser.tier)]" />
                
                <div class="flex gap-3 relative">
                    <!-- Menú Compartir -->
                    <div class="relative">
                        <button @click="showShareMenu = !showShareMenu" 
                                class="p-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 rounded-xl transition-all shadow-sm"
                                title="Compartir perfil">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                        </button>

                        <div v-if="showShareMenu" class="absolute bottom-full right-0 mb-4 bg-white dark:bg-slate-800 shadow-2xl border border-slate-100 dark:border-slate-700 rounded-2xl p-2 flex gap-4 animate-in fade-in slide-in-from-top-2 duration-200 z-50 min-w-[200px] justify-around">
                            <button @click="shareProfile('whatsapp')" class="text-emerald-500 hover:scale-125 transition-transform" title="WhatsApp">
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.483 8.413-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.308 1.656zm6.757-4.242c1.612.955 3.178 1.468 4.949 1.469 5.408 0 9.809-4.401 9.812-9.812.001-2.624-1.022-5.09-2.88-6.948-1.859-1.858-4.325-2.88-6.944-2.882-5.41 0-9.811 4.401-9.815 9.813 0 1.936.569 3.46 1.54 5.01l-1.002 3.661 3.74-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479-1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                            </button>
                            <button @click="shareProfile('twitter')" class="text-slate-800 dark:text-white hover:scale-125 transition-transform" title="Twitter (X)">
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            </button>
                            <button @click="shareProfile('linkedin')" class="text-blue-600 hover:scale-125 transition-transform" title="LinkedIn">
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.761 0 5-2.239 5-5v-14c0-2.761-2.239-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                            </button>
                            <div class="w-px bg-slate-100 dark:bg-slate-700 mx-1"></div>
                            <button @click="shareProfile('copy')" class="text-slate-400 hover:text-blue-500 hover:scale-125 transition-transform" title="Copiar Enlace">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 012-2v-8a2 2 0 01-2-2h-8a2 2 0 01-2 2v8a2 2 0 012 2z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <Link v-if="isOwnProfile" :href="route('profile.edit')" class="px-6 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-800 dark:text-white font-bold rounded-xl shadow-sm transition-all border border-slate-200 dark:border-slate-600">
                        Configurar Perfil
                    </Link>

                    <Link v-if="isOwnProfile && profileUser.tier !== 'premium' && profileUser.tier !== 'pro'" :href="route('subscription.index')" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/30 transition-all active:scale-95 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                        </svg>
                        Pagar Membresía
                    </Link>
                    <button v-if="!isOwnProfile" @click="emit('toggle-block')" 
                            class="p-2.5 rounded-xl transition-all shadow-sm flex items-center justify-center border border-transparent" 
                            :class="isBlocked ? 'bg-rose-100 text-rose-600' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 hover:text-rose-500 hover:border-rose-200'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :class="isBlocked ? 'fill-current' : 'fill-none'" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </button>

                    <button v-if="!isOwnProfile && !isBlocked" @click="emit('toggle-follow')" 
                            class="px-8 py-2.5 font-bold rounded-xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2 min-w-[140px]" 
                            :class="isFollowing ? 'bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-white hover:bg-rose-100 hover:text-rose-600' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-blue-500/30'">
                        {{ isFollowing ? 'Dejar de seguir' : 'Seguir' }}
                    </button>
                </div>
            </div>

            <!-- Datos Biográficos -->
            <div>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white flex items-center gap-3 flex-wrap">
                    {{ profileUser.name }}
                    <span v-if="profileUser.is_admin" class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-[10px] uppercase px-2 py-1 rounded-lg font-black tracking-widest">Admin</span>
                    
                    <span v-if="profileUser.tier === 'premium'" class="bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800 text-[10px] uppercase px-2 py-1 rounded-lg font-black tracking-widest flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        Premium
                    </span>
                    <span v-else-if="profileUser.tier === 'pro'" class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 text-[10px] uppercase px-2 py-1 rounded-lg font-black tracking-widest flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                        </svg>
                        Pro
                    </span>
                    <span v-else-if="profileUser.tier === 'basic'" class="bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800 text-[10px] uppercase px-2 py-1 rounded-lg font-black tracking-widest flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Basic
                    </span>
                </h1>
                <p class="text-slate-500 font-bold mb-4 flex items-center gap-2">
                    @{{ profileUser.username || `user_${profileUser.id}` }}
                    <span class="text-[10px] text-slate-400 font-medium normal-case flex items-center gap-1 before:content-['•'] before:mr-1">
                        Miembro desde {{ joinedAt }}
                    </span>
                </p>
                
                <p class="text-slate-700 dark:text-slate-300 mb-6 max-w-2xl text-sm leading-relaxed whitespace-pre-wrap">
                    {{ profileUser.bio || 'Este usuario aún no ha escrito una biografía.' }}
                </p>

                <div class="flex items-center gap-6 text-sm">
                    <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                        <span class="font-black text-slate-900 dark:text-white text-lg">{{ profileUser.following_count || 0 }}</span> Siguiendo
                    </div>
                    <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                        <span class="font-black text-slate-900 dark:text-white text-lg">{{ profileUser.followers_count || 0 }}</span> Seguidores
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
