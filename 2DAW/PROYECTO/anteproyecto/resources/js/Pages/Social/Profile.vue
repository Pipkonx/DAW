<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ModalConfirm from '@/Components/ModalConfirm.vue';
import { useToast } from '@/Composables/useToast';

const { showToast } = useToast();

const props = defineProps({
    profileUser: Object,
    posts: Array,
    bookmarks: Array,
    isOwnProfile: Boolean,
    isFollowing: Boolean,
    isBlocked: Boolean,
});

const activeReactionPickerId = ref(null);
const activeCommentThread = ref(null);
const replyToCommentId = ref(null);
const editingPost = ref(null);
const editForm = useForm({
    content: ''
});

const confirmModal = ref({
    show: false,
    title: '',
    message: '',
    type: 'danger',
    onConfirm: () => {}
});

const reactions = [
    { emoji: '👍', label: 'Me gusta' },
    { emoji: '❤️', label: 'Encanta' },
    { emoji: '😂', label: 'Risa' },
    { emoji: '🔥', label: 'Fuego' },
    { emoji: '🚀', label: 'Cohete' },
    { emoji: '🤑', label: 'Beneficio' },
    { emoji: '📉', label: 'Bajista' },
    { emoji: '🆘', label: 'SOS' },
];

const toggleLike = (id, type = '👍', target = 'post') => {
    router.post(route('social.like'), {
        likeable_id: id,
        likeable_type: target,
        type: type,
    }, {
        preserveScroll: true,
        onSuccess: () => activeReactionPickerId.value = null
    });
};

const toggleRepost = (post) => {
    router.post(route('social.repost', post.id), {}, { preserveScroll: true });
};

const toggleBookmark = (post) => {
    router.post(route('social.bookmark', post.id), {}, { preserveScroll: true });
};

const togglePin = (post) => {
    router.post(route('social.pin', post.id), {}, { preserveScroll: true });
};

const startEdit = (post) => {
    editingPost.value = post;
    editForm.content = post.content;
};

const showShareMenu = ref(false);

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

const submitEdit = () => {
    editForm.put(route('social.update', editingPost.value.id), {
        preserveScroll: true,
        onSuccess: () => editingPost.value = null
    });
};

const deletePost = (post) => {
    confirmModal.value = {
        show: true,
        title: '¿Eliminar Publicación?',
        message: 'Esta acción es irreversible y borrará permanentemente tu análisis/post del muro comunitario.',
        type: 'danger',
        onConfirm: () => {
            router.delete(route('social.delete', post.id), { 
                preserveScroll: true,
                onSuccess: () => confirmModal.value.show = false
            });
        }
    };
};

const submitComment = (post, content, parentId = null) => {
    router.post(route('social.comment', post.id), {
        content: content,
        parent_id: parentId
    }, {
        preserveScroll: true,
        onSuccess: () => {
            replyToCommentId.value = null;
        }
    });
};

const toggleFollow = () => {
    router.post(route('profile.social.follow', props.profileUser.id), {}, { preserveScroll: true });
};

const toggleBlock = () => {
    confirmModal.value = {
        show: true,
        title: props.isBlocked ? '¿Desbloquear Usuario?' : '¿Bloquear Usuario?',
        message: props.isBlocked 
            ? `Volverás a ver el contenido de ${props.profileUser.name} en tu feed.` 
            : `Dejarás de ver los posts y comentarios de ${props.profileUser.name} en toda la plataforma. El usuario no será notificado.`,
        type: props.isBlocked ? 'info' : 'danger',
        onConfirm: () => {
            router.post(route('profile.social.block', props.profileUser.id), {}, {
                preserveScroll: true,
                onSuccess: () => {
                    confirmModal.value.show = false;
                }
            });
        }
    };
};

const activeTab = ref('all'); // 'all', 'posts', 'reposts', 'bookmarks'

import { computed } from 'vue';

const displayPosts = computed(() => {
    if (activeTab.value === 'all') return props.posts;
    if (activeTab.value === 'posts') return props.posts.filter(p => !p.wall_is_repost);
    if (activeTab.value === 'reposts') return props.posts.filter(p => p.wall_is_repost);
    if (activeTab.value === 'bookmarks') return props.bookmarks || [];
    return props.posts;
});

const getAvatarRingClasses = (tier) => {
    switch (tier) {
        case 'premium': return 'ring-4 ring-purple-500 ring-offset-4 dark:ring-offset-slate-800 border-transparent transition-all';
        case 'pro': return 'ring-4 ring-indigo-500 ring-offset-4 dark:ring-offset-slate-800 border-transparent transition-all';
        case 'basic': return 'ring-4 ring-blue-500 ring-offset-4 dark:ring-offset-slate-800 border-transparent transition-all';
        default: return 'border-4 border-white dark:border-slate-800';
    }
};

const getSmallAvatarRingClasses = (tier) => {
    switch (tier) {
        case 'premium': return 'ring-2 ring-purple-500 ring-offset-2 dark:ring-offset-slate-900 border-transparent';
        case 'pro': return 'ring-2 ring-indigo-500 ring-offset-2 dark:ring-offset-slate-900 border-transparent';
        case 'basic': return 'ring-2 ring-blue-500 ring-offset-2 dark:ring-offset-slate-900 border-transparent';
        default: return 'border border-slate-200 dark:border-slate-700';
    }
};

</script>

<template>
    <Head :title="`Perfil de ${profileUser.name}`" />

    <AuthenticatedLayout>
        <div class="py-8 bg-slate-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                
                <!-- Cabecera de Perfil -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl overflow-hidden shadow-xl border border-slate-100 dark:border-slate-700">
                    <div class="h-64 w-full bg-slate-200 dark:bg-slate-800 relative">
                        <img v-if="profileUser.banner_path" :src="`/storage/${profileUser.banner_path}`" class="w-full h-full object-cover" />
                        <div v-else class="w-full h-full bg-gradient-to-r from-indigo-500 to-purple-600"></div>
                    </div>
                    
                    <div class="px-8 pb-8 relative">
                        <div class="flex justify-between items-end -mt-16 mb-4">
                            <img :src="profileUser.avatar || `https://ui-avatars.com/api/?name=${profileUser.name}`" :class="['w-32 h-32 rounded-3xl object-cover shadow-2xl bg-white relative z-10', getAvatarRingClasses(profileUser.tier)]" />
                            
                            <div class="flex gap-3 relative">
                                <!-- Menú Compartir Perfil -->
                                <div class="relative">
                                    <button 
                                        @click="showShareMenu = !showShareMenu"
                                        class="p-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 rounded-xl transition-all shadow-sm"
                                        title="Compartir perfil"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                        </svg>
                                    </button>

                                    <!-- Share Menu Dropdown -->
                                    <div v-if="showShareMenu" class="absolute bottom-full right-0 mb-4 bg-white dark:bg-slate-800 shadow-2xl border border-slate-100 dark:border-slate-700 rounded-2xl p-2 flex gap-4 animate-in fade-in slide-in-from-top-2 duration-200 z-50 min-w-[200px] justify-around">
                                        <button @click="shareProfile('whatsapp')" class="text-emerald-500 hover:scale-125 transition-transform" title="WhatsApp">
                                            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.483 8.413-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.308 1.656zm6.757-4.242c1.612.955 3.178 1.468 4.949 1.469 5.408 0 9.809-4.401 9.812-9.812.001-2.624-1.022-5.09-2.88-6.948-1.859-1.858-4.325-2.88-6.944-2.882-5.41 0-9.811 4.401-9.815 9.813 0 1.936.569 3.46 1.54 5.01l-1.002 3.661 3.74-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
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

                                <Link v-if="isOwnProfile" :href="route('profile.edit')" class="px-6 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-800 dark:text-white font-bold rounded-xl shadow-sm transition-all focus:ring-2 focus:ring-slate-300 dark:focus:ring-slate-500">
                                    Configurar Perfil
                                </Link>
                                <button v-if="!isOwnProfile" @click="toggleBlock" class="p-2.5 rounded-xl transition-all shadow-sm flex items-center justify-center" :class="isBlocked ? 'bg-rose-100 text-rose-600' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 hover:text-rose-500'" :title="isBlocked ? 'Desbloquear' : 'Bloquear'">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :class="isBlocked ? 'fill-current' : 'fill-none'" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                </button>

                                <button v-if="!isOwnProfile && !isBlocked" @click="toggleFollow" class="px-8 py-2.5 font-bold rounded-xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2 min-w-[140px]" :class="isFollowing ? 'bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-white hover:bg-rose-100 hover:text-rose-600 dark:hover:bg-rose-900/30' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-blue-500/30'">
                                    {{ isFollowing ? 'Dejar de seguir' : 'Seguir' }}
                                </button>
                            </div>
                        </div>

                        <div>
                            <h1 class="text-3xl font-black text-slate-900 dark:text-white flex items-center gap-3 flex-wrap">
                                {{ profileUser.name }}
                                <span v-if="profileUser.is_admin" class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-[10px] uppercase px-2 py-1 rounded-lg font-black tracking-widest shadow-sm">Admin</span>
                                
                                <span v-if="profileUser.tier === 'premium'" class="bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800 text-[10px] uppercase px-2 py-1 rounded-lg font-black tracking-widest flex items-center gap-1 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    Premium
                                </span>
                                <span v-else-if="profileUser.tier === 'pro'" class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 text-[10px] uppercase px-2 py-1 rounded-lg font-black tracking-widest flex items-center gap-1 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Pro
                                </span>
                                <span v-else-if="profileUser.tier === 'basic'" class="bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800 text-[10px] uppercase px-2 py-1 rounded-lg font-black tracking-widest flex items-center gap-1 shadow-sm">
                                    Básico
                                </span>
                            </h1>
                            <p class="text-slate-500 font-bold mb-4 flex items-center gap-2">
                                @{{ profileUser.username || `user_${profileUser.id}` }}
                                <span class="text-[10px] text-slate-400 font-medium normal-case flex items-center gap-1 before:content-['•'] before:mr-1">
                                    Miembro desde {{ profileUser.joined_at }}
                                </span>
                            </p>
                            
                            <p v-if="isOwnProfile && $page.props.auth.user.tier !== 'none'" class="inline-flex items-center gap-2 mb-4 mt-1 px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-bold rounded-xl border border-indigo-100 dark:border-indigo-800/50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Membresía: {{ $page.props.auth.user.subscription_status }}
                            </p>
                            
                            <p class="text-slate-700 dark:text-slate-300 mb-6 max-w-2xl text-sm leading-relaxed whitespace-pre-wrap">
                                {{ profileUser.bio || 'Este usuario aún no ha escrito una biografía.' }}
                            </p>

                            <div class="flex items-center gap-6 text-sm">
                                <div class="flex items-center gap-2 cursor-pointer hover:underline text-slate-600 dark:text-slate-400">
                                    <span class="font-black text-slate-900 dark:text-white text-lg">{{ profileUser.following_count || 0 }}</span> Siguiendo
                                </div>
                                <div class="flex items-center gap-2 cursor-pointer hover:underline text-slate-600 dark:text-slate-400">
                                    <span class="font-black text-slate-900 dark:text-white text-lg">{{ profileUser.followers_count || 0 }}</span> Seguidores
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selector de Pestañas -->
                <div class="flex items-center gap-1 bg-white dark:bg-slate-800 p-1.5 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm mb-6 overflow-x-auto no-scrollbar">
                    <button 
                        @click="activeTab = 'all'"
                        class="px-5 py-2 text-xs font-black uppercase tracking-widest rounded-xl transition-all whitespace-nowrap"
                        :class="activeTab === 'all' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200 dark:shadow-none' : 'text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                    >
                        Todo
                    </button>
                    <button 
                        @click="activeTab = 'posts'"
                        class="px-5 py-2 text-xs font-black uppercase tracking-widest rounded-xl transition-all whitespace-nowrap"
                        :class="activeTab === 'posts' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200 dark:shadow-none' : 'text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                    >
                        Publicaciones
                    </button>
                    <button 
                        @click="activeTab = 'reposts'"
                        class="px-5 py-2 text-xs font-black uppercase tracking-widest rounded-xl transition-all whitespace-nowrap"
                        :class="activeTab === 'reposts' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200 dark:shadow-none' : 'text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                    >
                        Compartidos
                    </button>
                    <button 
                        v-if="isOwnProfile"
                        @click="activeTab = 'bookmarks'"
                        class="px-5 py-2 text-xs font-black uppercase tracking-widest rounded-xl transition-all whitespace-nowrap flex items-center gap-2"
                        :class="activeTab === 'bookmarks' ? 'bg-rose-500 text-white shadow-lg shadow-rose-200 dark:shadow-none hover:bg-rose-600' : 'text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" :class="activeTab === 'bookmarks' ? 'fill-white' : 'fill-none'" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                        </svg>
                        Guardados
                    </button>
                </div>

                <!-- Feed del Usuario -->
                <div class="space-y-6">
                    <div v-if="displayPosts.length === 0" class="bg-slate-100 dark:bg-slate-800/50 rounded-3xl p-12 text-center border-2 border-dashed border-slate-200 dark:border-slate-700">
                        <p class="text-slate-500 dark:text-slate-400 text-lg font-bold">
                            {{ activeTab === 'bookmarks' ? 'Aún no tienes publicaciones guardadas.' : 'Aún no hay publicaciones aquí.' }}
                        </p>
                    </div>

                    <div v-for="post in displayPosts" :key="post.id" class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700 relative">
                        <div v-if="post.is_pinned" class="absolute -top-3 left-6 px-3 py-1 bg-indigo-500 text-white text-[10px] font-black uppercase tracking-widest rounded-full shadow-lg flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                              <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            Post Anclado
                        </div>
                        <div v-if="post.wall_is_repost" class="mb-4 text-xs font-bold text-slate-400 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            {{ profileUser.name }} reposteó
                        </div>
                        
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <Link :href="route('social.profile', post.user.username)" class="hover:opacity-80 transition-opacity">
                                    <img :src="post.user.avatar || `https://ui-avatars.com/api/?name=${post.user.name}`" :class="['w-12 h-12 rounded-full object-cover', getSmallAvatarRingClasses(post.user.tier)]" />
                                </Link>
                                <div>
                                    <div class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                        <Link :href="route('social.profile', post.user.username)" class="hover:underline">
                                            {{ post.user.name }}
                                        </Link>
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">{{ post.created_at_human || 'Hace un momento' }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div v-if="post.market_asset" class="px-3 py-1 bg-slate-50 dark:bg-slate-900/50 rounded-lg border border-slate-100 dark:border-slate-700">
                                    <Link :href="route('assets.show', post.market_asset.ticker)" class="text-xs font-black text-slate-600 dark:text-slate-400 hover:text-blue-500 transition-colors">
                                        ${{ post.market_asset.ticker }}
                                    </Link>
                                </div>
                                        
                                <!-- Menú de Acciones -->
                                <div class="relative group z-20">
                                    <button class="p-2 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-full transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                        </svg>
                                    </button>
                                    <div class="absolute right-0 top-full mt-1 w-48 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                                        <button @click="toggleBookmark(post)" class="w-full text-left px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :class="post.is_bookmarked ? 'text-amber-500 fill-amber-500' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                            </svg>
                                            {{ post.is_bookmarked ? 'Quitar Marcador' : 'Guardar Marcador' }}
                                        </button>
                                        
                                        <button v-if="post.user_id === $page.props.auth.user.id" @click="togglePin(post)" class="w-full text-left px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :class="post.is_pinned ? 'text-indigo-500 fill-indigo-500' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            {{ post.is_pinned ? 'Desanclar' : 'Anclar al Perfil' }}
                                        </button>

                                        <button v-if="post.can_edit" @click="startEdit(post)" class="w-full text-left px-4 py-2 text-xs font-bold text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 flex items-center gap-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Editar
                                        </button>
                                        
                                        <button v-if="post.can_delete" @click="deletePost(post)" class="w-full text-left px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 flex items-center gap-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edición vs Vista -->
                        <div v-if="editingPost && editingPost.id === post.id" class="space-y-4 mb-4">
                            <textarea 
                                v-model="editForm.content"
                                class="w-full bg-slate-50 dark:bg-slate-900 border-2 border-indigo-100 dark:border-indigo-900/30 rounded-2xl p-4 text-sm focus:ring-0 focus:border-indigo-500"
                                rows="3"
                            ></textarea>
                            <div class="flex justify-end gap-3">
                                <button @click="editingPost = null" class="px-4 py-2 text-xs font-bold text-slate-400 uppercase tracking-widest">Cancelar</button>
                                <button @click="submitEdit" class="px-6 py-2 bg-indigo-600 text-white text-xs font-black rounded-xl shadow-lg shadow-indigo-200 dark:shadow-none uppercase tracking-widest">Guardar</button>
                            </div>
                        </div>
                        <p v-else class="text-slate-700 dark:text-slate-200 leading-relaxed mb-4 whitespace-pre-wrap">{{ post.content }}</p>
                                
                        <!-- Post Image -->
                        <img v-if="post.image_path" :src="`/storage/${post.image_path}`" class="w-full rounded-2xl mb-4 border border-slate-100 dark:border-slate-700 shadow-lg" />
                        
                        <!-- Reaction Summary -->
                        <div v-if="post.reactions_summary && Object.keys(post.reactions_summary).length" class="flex flex-wrap gap-2 mb-4 px-2">
                            <div v-for="(count, emoji) in post.reactions_summary" :key="emoji" 
                                 class="flex items-center gap-1.5 px-2.5 py-1 bg-slate-50 dark:bg-slate-900/50 rounded-full border border-slate-100 dark:border-slate-700 animate-in zoom-in-75 duration-300 hover:scale-110 transition-transform cursor-default">
                                <span class="text-sm">{{ emoji }}</span>
                                <span class="text-[10px] font-black text-slate-500">{{ count }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-6 pt-4 border-t border-slate-50 dark:border-slate-700">
                            <div class="relative">
                                <button 
                                    @mouseenter="activeReactionPickerId = `post-${post.id}`" 
                                    @click="toggleLike(post.id, '👍', 'post')"
                                    class="flex items-center gap-2 group p-2 hover:bg-slate-50 dark:hover:bg-slate-900/50 rounded-xl transition-all"
                                >
                                    <span v-if="post.user_reaction" class="text-lg animate-in zoom-in-50 duration-300">{{ post.user_reaction }}</span>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400 transition-transform group-active:scale-125" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    <span class="text-xs font-bold text-slate-500">{{ post.is_liked ? 'Reaccionado' : 'Reaccionar' }}</span>
                                </button>

                                <div 
                                    v-if="activeReactionPickerId === `post-${post.id}`" 
                                    @mouseleave="activeReactionPickerId = null"
                                    class="absolute bottom-full left-0 mb-2 bg-white dark:bg-slate-800 shadow-2xl border border-slate-100 dark:border-slate-700 rounded-2xl p-2 flex gap-3 animate-in fade-in slide-in-from-top-2 duration-200 z-50"
                                >
                                    <button 
                                        v-for="reaction in reactions" 
                                        :key="reaction.emoji" 
                                        @click="toggleLike(post.id, reaction.emoji, 'post')"
                                        class="text-2xl hover:scale-150 transition-transform duration-200 p-1"
                                        :title="reaction.label"
                                    >
                                        {{ reaction.emoji }}
                                    </button>
                                </div>
                            </div>

                            <button @click="activeCommentThread = activeCommentThread === post.id ? null : post.id" class="flex items-center gap-2 group p-2 hover:bg-slate-50 dark:hover:bg-slate-900/50 rounded-xl transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400 group-hover:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <span class="text-xs font-bold text-slate-500">{{ post.comments_count || 0 }}</span>
                            </button>

                            <button 
                                @click="toggleRepost(post)"
                                class="flex items-center gap-2 px-3 py-2 rounded-xl transition-all duration-300"
                                :class="post.is_reposted ? 'text-emerald-500 bg-emerald-50 dark:bg-emerald-900/30' : 'text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-900/50'"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                                <span class="text-xs font-black">{{ post.reposts_count || 0 }}</span>
                            </button>
                        </div>

                        <!-- Comentarios -->
                        <div v-if="activeCommentThread === post.id" class="mt-4 pt-4 border-t border-slate-50 dark:border-slate-700 animate-in fade-in slide-in-from-top-2">
                            <div class="space-y-6 mb-6">
                                <div v-for="comment in post.comments" :key="comment.id" class="space-y-4">
                                    <div class="flex gap-3 group/comment">
                                        <img :src="comment.user.avatar || `https://ui-avatars.com/api/?name=${comment.user.name}`" :class="['w-8 h-8 rounded-full shadow-sm object-cover', getSmallAvatarRingClasses(comment.user.tier)]" />
                                        <div class="flex-grow">
                                            <div class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-4 border border-slate-100 dark:border-slate-700 group-hover/comment:border-slate-200 transition-colors">
                                                <div class="flex justify-between items-center mb-1">
                                                    <span class="text-xs font-black text-slate-800 dark:text-white">{{ comment.user.name }}</span>
                                                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">{{ comment.created_at_human || 'Ahora' }}</span>
                                                </div>
                                                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-3">{{ comment.content }}</p>
                                            </div>
                                            
                                            <!-- Repuesta Inline Form -->
                                            <div v-if="replyToCommentId === comment.id" class="mt-2 animate-in slide-in-from-left-2 duration-200">
                                                <input 
                                                    type="text" 
                                                    placeholder="Escribe tu respuesta..." 
                                                    class="w-full bg-white dark:bg-slate-800 border-2 border-blue-500/20 focus:border-blue-500 focus:ring-0 rounded-xl text-[10px] py-1.5 px-3"
                                                    @keyup.enter="(e) => { submitComment(post, e.target.value, comment.id); e.target.value = ''; }"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <img :src="$page.props.auth.user.avatar || `https://ui-avatars.com/api/?name=${$page.props.auth.user.name}`" :class="['w-8 h-8 rounded-full object-cover', getSmallAvatarRingClasses($page.props.auth.user.tier)]" />
                                <div class="flex-grow relative">
                                    <input 
                                        type="text" 
                                        placeholder="Escribe un comentario..." 
                                        class="w-full bg-slate-100 dark:bg-slate-900 border-none focus:ring-2 focus:ring-blue-500 rounded-2xl text-xs py-2 pr-10"
                                        @keyup.enter="(e) => { submitComment(post, e.target.value); e.target.value = ''; }"
                                    />
                                    <button class="absolute right-2 top-1.5 p-1 text-blue-500 hover:text-blue-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal de Confirmación Premium -->
        <ModalConfirm 
            :show="confirmModal.show"
            :title="confirmModal.title"
            :message="confirmModal.message"
            :type="confirmModal.type"
            @confirm="confirmModal.onConfirm"
            @cancel="confirmModal.show = false"
        />
    </AuthenticatedLayout>
</template>
