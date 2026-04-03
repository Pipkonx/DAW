<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ModalConfirm from '@/Components/ModalConfirm.vue';
import { useToast } from '@/Composables/useToast';

const { showToast } = useToast();

const props = defineProps({
    posts: Object,
    topGainers: Array,
    topLosers: Array,
    trends: Array,
    topCreators: Array,
    famousPortfolios: Array,
    filters: Object,
});

const postForm = useForm({
    content: '',
    market_asset_id: null,
    image: null,
});

const imagePreview = ref(null);
const fileInput = ref(null);

const handleImageUpload = (e) => {
    const file = e.target.files[0];
    postForm.image = file;
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => imagePreview.value = e.target.result;
        reader.readAsDataURL(file);
    }
};

const submitPost = () => {
    postForm.post(route('social.post'), {
        onSuccess: () => {
            postForm.reset();
            imagePreview.value = null;
        },
    });
};

const activeReactionPickerId = ref(null);
const activeMoverType = ref('gainers'); // gainers | losers
const activeCommentThread = ref(null);
const activeShareMenuId = ref(null);
const reportingPost = ref(null);
const reportReason = ref('');
const replyToCommentId = ref(null);
const editingPost = ref(null);
const activeTrendTab = ref('negotiated'); // negotiated, volume, social
const activeCreatorsTab = ref('popular'); // popular, active
const editForm = useForm({
    content: ''
});

const formatCompactNumber = (number) => {
    if (number >= 1000000000) return (number / 1000000000).toFixed(1) + 'B';
    if (number >= 1000000) return (number / 1000000).toFixed(1) + 'M';
    if (number >= 1000) return (number / 1000).toFixed(1) + 'K';
    return number.toFixed(0);
};

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
        message: 'Esta acción borrará permanentemente tu análisis del muro comunitario. No se puede deshacer.',
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
            activeCommentThread.value = post.id;
        }
    });
};

const submitReport = () => {
    if (!reportingPost.value || !reportReason.value) return;
    router.post(route('social.report'), {
        reportable_id: reportingPost.value.id,
        reportable_type: 'post',
        reason: reportReason.value,
    }, {
        onSuccess: () => {
            reportingPost.value = null;
            reportReason.value = '';
        }
    });
};
const sharePost = (post, platform) => {
    const url = `${window.location.origin}/social/feed?post=${post.id}`;
    const text = `¡Mira este análisis de ${post.market_asset ? post.market_asset.ticker : 'mercado'} en Pipkonx! 🚀\n\n"${post.content.substring(0, 60)}..."\n\nVer más: `;
    
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
            showToast('¡Enlace del post copiado al portapapeles!', 'success');
            activeShareMenuId.value = null;
            return;
    }
    
    if (shareUrl) window.open(shareUrl, '_blank');
    activeShareMenuId.value = null;
};

const blockUser = (user) => {
    confirmModal.value = {
        show: true,
        title: 'Bloquear Usuario',
        message: `¿Estás seguro de que quieres bloquear a ${user.name}? Dejarás de ver sus posts y comentarios en toda la plataforma. Esta acción se puede revertir desde su perfil.`,
        type: 'danger',
        onConfirm: () => {
            router.post(route('profile.social.block', user.id), {}, {
                preserveScroll: true,
                onSuccess: () => {
                    confirmModal.value.show = false;
                    showToast(`Usuario ${user.name} bloqueado. Feed actualizado.`, 'success');
                }
            });
        }
    };
};

</script>

<template>
    <Head title="Muro Comunitario" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-slate-800 dark:text-white leading-tight">
                Muro Comunitario
            </h2>
        </template>

        <div class="py-8 bg-slate-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    
                    <!-- Columna Izquierda: Inteligencia de Mercado -->
                    <div class="hidden lg:block space-y-6">
                        
                        <!-- Mini Perfil del Usuario -->
                        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 text-center overflow-hidden">
                            <!-- Banner del Usuario en Widget -->
                            <div class="h-20 w-full bg-slate-200 dark:bg-slate-700 relative">
                                <img v-if="$page.props.auth.user.banner_path" :src="`/storage/${$page.props.auth.user.banner_path}`" class="w-full h-full object-cover" />
                                <div v-else class="w-full h-full bg-gradient-to-r from-blue-600 to-indigo-600"></div>
                            </div>
                            
                            <!-- Foto superpuesta -->
                            <div class="-mt-10 mb-4 relative z-10 px-4">
                                <img :src="$page.props.auth.user.avatar || `https://ui-avatars.com/api/?name=${$page.props.auth.user.name}`" class="w-20 h-20 rounded-2xl mx-auto border-4 border-white dark:border-slate-800 bg-white object-cover shadow-lg" />
                                <h3 class="mt-2 text-lg font-black text-slate-800 dark:text-white leading-tight">{{ $page.props.auth.user.name }}</h3>
                                <p class="text-xs text-slate-500 font-bold italic">@{{ $page.props.auth.user.username || `user_${$page.props.auth.user.id}` }}</p>
                            </div>
                            
                            <div class="px-6 pb-6 space-y-2">
                                <Link :href="route('social.profile', $page.props.auth.user.username)" class="block w-full py-2.5 bg-slate-50 dark:bg-slate-900/50 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-100 dark:border-slate-600 text-slate-600 dark:text-slate-300 text-[10px] font-black uppercase tracking-widest rounded-xl transition-colors">
                                    Ver mi Muro
                                </Link>
                                <Link :href="route('profile.edit')" class="block w-full py-2.5 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase tracking-widest rounded-xl transition-colors">
                                    Configuración
                                </Link>
                            </div>
                        </div>

                        <!-- Widget de Activos Dinámico -->
                        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-sm font-black uppercase tracking-widest text-slate-400">Top Movers Hoy</h3>
                                <div class="flex bg-slate-50 dark:bg-slate-900 rounded-lg p-1">
                                    <button 
                                        @click="activeMoverType = 'gainers'"
                                        class="p-1.5 rounded-md transition-all"
                                        :class="activeMoverType === 'gainers' ? 'bg-white dark:bg-slate-800 shadow-sm text-emerald-500' : 'text-slate-400'"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        </svg>
                                    </button>
                                    <button 
                                        @click="activeMoverType = 'losers'"
                                        class="p-1.5 rounded-md transition-all"
                                        :class="activeMoverType === 'losers' ? 'bg-white dark:bg-slate-800 shadow-sm text-rose-500' : 'text-slate-400'"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div v-if="topGainers || topLosers" class="space-y-4">
                                <div 
                                    v-for="mover in (activeMoverType === 'gainers' ? topGainers : topLosers)?.slice(0, 5)" 
                                    :key="mover.symbol" 
                                    class="flex items-center justify-between group cursor-pointer animate-in fade-in duration-300" 
                                    @click="$inertia.get(route('assets.show', mover.symbol))"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-700/50 flex items-center justify-center border border-slate-100 dark:border-slate-600 overflow-hidden">
                                            <img :src="mover.image" class="w-7 h-7 object-contain" />
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-slate-800 dark:text-white group-hover:text-blue-500 transition-colors">{{ mover.symbol }}</div>
                                            <div class="text-[10px] text-slate-500 uppercase font-black tracking-tighter">{{ mover.name?.substring(0,10) }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs font-black" :class="activeMoverType === 'gainers' ? 'text-emerald-500' : 'text-rose-500'">
                                            {{ activeMoverType === 'gainers' ? '+' : '' }}{{ mover.changesPercentage?.toFixed(2) }}%
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-bold">${{ mover.price?.toFixed(2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Central: Post Feed -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Create Post -->
                        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-xl border border-slate-100 dark:border-slate-700 border-b-4 border-b-blue-500">
                            <div class="flex items-start gap-4">
                                <img :src="$page.props.auth.user.avatar || `https://ui-avatars.com/api/?name=${$page.props.auth.user.name}`" class="w-12 h-12 rounded-2xl object-cover border-2 border-slate-100 dark:border-slate-700" />
                                <div class="flex-grow">
                                    <textarea 
                                        v-model="postForm.content"
                                        class="w-full bg-transparent border-none focus:ring-0 text-slate-800 dark:text-white placeholder-slate-400 resize-none min-h-[100px]"
                                        placeholder="¿Qué está pasando en el mercado hoy?"
                                    ></textarea>
                                    
                                    <!-- Image Preview -->
                                    <div v-if="imagePreview" class="relative mt-4 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700">
                                        <img :src="imagePreview" class="w-full max-h-[400px] object-cover" />
                                        <button @click="imagePreview = null; postForm.image = null" class="absolute top-2 right-2 p-1.5 bg-black/50 hover:bg-black/70 text-white rounded-full backdrop-blur-md transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between">
                                <div class="flex gap-2">
                                    <button @click="fileInput.click()" class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl transition-all" title="Añadir imagen">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                    <input type="file" ref="fileInput" class="hidden" accept="image/*" @change="handleImageUpload" />
                                </div>
                                <button 
                                    @click="submitPost"
                                    :disabled="postForm.processing || !postForm.content"
                                    class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-bold rounded-2xl shadow-lg shadow-blue-500/30 transition-all active:scale-95"
                                >
                                    Publicar
                                </button>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="flex gap-4 border-b border-slate-200 dark:border-slate-700 pb-2">
                            <Link 
                                :href="route('social.feed', { tab: 'recent' })" 
                                class="px-4 py-2 text-sm font-bold transition-all relative"
                                :class="filters.tab === 'recent' || !filters.tab ? 'text-blue-600 border-b-2 border-blue-600' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white'"
                            >
                                Reciente
                            </Link>
                            <Link 
                                :href="route('social.feed', { tab: 'following' })" 
                                class="px-4 py-2 text-sm font-bold transition-all relative"
                                :class="filters.tab === 'following' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white'"
                            >
                                Para ti
                            </Link>
                            <Link 
                                :href="route('social.feed', { tab: 'best' })" 
                                class="px-4 py-2 text-sm font-bold transition-all relative"
                                :class="filters.tab === 'best' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white'"
                            >
                                Lo mejor
                            </Link>
                        </div>

                        <!-- Posts List -->
                        <div class="space-y-6">
                            <div v-for="post in posts.data" :key="post.id" class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <Link :href="route('social.profile', post.user.username)" class="hover:opacity-80 transition-opacity">
                                            <img :src="post.user.avatar || `https://ui-avatars.com/api/?name=${post.user.name}`" class="w-10 h-10 rounded-full border border-slate-100 dark:border-slate-700" />
                                        </Link>
                                        <div>
                                            <div class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                                <Link :href="route('social.profile', post.user.username)" class="hover:underline">
                                                    {{ post.user.name }}
                                                </Link>
                                                <span v-if="post.user.is_admin" class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[8px] font-black uppercase px-1.5 py-0.5 rounded italic">Admin</span>
                                                <span v-if="post.is_pinned" class="text-indigo-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                    </svg>
                                                </span>
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

                                                <button v-if="post.user_id !== $page.props.auth.user.id" @click="reportingPost = post" class="w-full text-left px-4 py-2 text-xs font-bold text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                    Reportar
                                                </button>

                                                <!-- Opción de Bloqueo Rápido -->
                                                <button v-if="post.user_id !== $page.props.auth.user.id" @click="blockUser(post.user)" class="w-full text-left px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 border-t border-slate-50 dark:border-slate-700/50 mt-1 flex items-center gap-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                    Bloquear Usuario
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

                                        <!-- Reaction Picker -->
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

                                    <!-- Botón Compartir -->
                                    <div class="relative">
                                        <button 
                                            @click="activeShareMenuId = activeShareMenuId === post.id ? null : post.id"
                                            class="flex items-center gap-2 p-2 hover:bg-slate-50 dark:hover:bg-slate-900/50 rounded-xl transition-all text-slate-400 group"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:text-indigo-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                            </svg>
                                        </button>

                                        <!-- Share Menu -->
                                        <div v-if="activeShareMenuId === post.id" class="absolute bottom-full left-0 mb-2 bg-white dark:bg-slate-800 shadow-2xl border border-slate-100 dark:border-slate-700 rounded-2xl p-2 flex gap-4 animate-in fade-in slide-in-from-top-2 duration-200 z-50 min-w-[200px] justify-around">
                                            <button @click="sharePost(post, 'whatsapp')" class="text-emerald-500 hover:scale-125 transition-transform" title="WhatsApp">
                                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.483 8.413-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.308 1.656zm6.757-4.242c1.612.955 3.178 1.468 4.949 1.469 5.408 0 9.809-4.401 9.812-9.812.001-2.624-1.022-5.09-2.88-6.948-1.859-1.858-4.325-2.88-6.944-2.882-5.41 0-9.811 4.401-9.815 9.813 0 1.936.569 3.46 1.54 5.01l-1.002 3.661 3.74-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                            </button>
                                            <button @click="sharePost(post, 'twitter')" class="text-slate-800 dark:text-white hover:scale-125 transition-transform" title="Twitter (X)">
                                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                            </button>
                                            <button @click="sharePost(post, 'linkedin')" class="text-blue-600 hover:scale-125 transition-transform" title="LinkedIn">
                                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.761 0 5-2.239 5-5v-14c0-2.761-2.239-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                                            </button>
                                            <div class="w-px bg-slate-100 dark:bg-slate-700 mx-1"></div>
                                            <button @click="sharePost(post, 'copy')" class="text-slate-400 hover:text-blue-500 hover:scale-125 transition-transform" title="Copiar Enlace">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 012-2v-8a2 2 0 01-2-2h-8a2 2 0 01-2 2v8a2 2 0 012 2z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    
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

                                <!-- Comments Section -->
                                <div v-if="activeCommentThread === post.id" class="mt-4 pt-4 border-t border-slate-50 dark:border-slate-700 animate-in fade-in slide-in-from-top-2">
                                    <div class="space-y-6 mb-6">
                                        <div v-for="comment in post.comments" :key="comment.id" class="space-y-4">
                                            <div class="flex gap-3 group/comment">
                                                <img :src="`https://ui-avatars.com/api/?name=${comment.user.name}`" class="w-8 h-8 rounded-full shadow-sm" />
                                                <div class="flex-grow">
                                                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-4 border border-slate-100 dark:border-slate-700 group-hover/comment:border-slate-200 transition-colors">
                                                        <div class="flex justify-between items-center mb-1">
                                                            <span class="text-xs font-black text-slate-800 dark:text-white">{{ comment.user.name }}</span>
                                                            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">{{ comment.created_at_human || 'Ahora' }}</span>
                                                        </div>
                                                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-1">{{ comment.content }}</p>
                                                        
                                                        <!-- Acciones de Comentario -->
                                                        <div class="flex items-center gap-4">
                                                            <button 
                                                                @click="toggleLike(comment.id, '👍', 'comment')" 
                                                                class="text-[9px] font-black uppercase tracking-tighter transition-colors flex items-center gap-1" 
                                                                :class="comment.is_liked ? 'text-rose-500' : 'text-slate-400 hover:text-slate-600'"
                                                            >
                                                                {{ comment.is_liked ? 'Reaccionado' : 'Me gusta' }}
                                                            </button>
                                                            
                                                            <button @click="replyToCommentId = replyToCommentId === comment.id ? null : comment.id" class="text-[9px] font-black uppercase tracking-tighter text-slate-400 hover:text-blue-500 transition-colors">
                                                                Responder
                                                            </button>
                                                        </div>
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
                                        <img :src="$page.props.auth.user.avatar || `https://ui-avatars.com/api/?name=${$page.props.auth.user.name}`" class="w-8 h-8 rounded-full border border-slate-100" />
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

                        <!-- Empty State -->
                        <div v-if="posts.data.length === 0" class="py-20 text-center">
                            <div class="bg-white dark:bg-slate-800 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6 shadow-xl text-slate-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-800 dark:text-white">No hay publicaciones</h3>
                            <p class="text-slate-500 mt-2">¡Sé el primero en compartir algo con la comunidad!</p>
                        </div>
                    </div>

                    <!-- Columna Derecha: Pulso del Mercado y Creadores -->
                    <div class="hidden lg:block space-y-6">
                        <!-- Pulso del Mercado (Tendencias + Negociación) -->
                        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700">
                            <h3 class="text-sm font-black uppercase tracking-widest text-slate-400 mb-6 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span>
                                Pulso del Mercado
                            </h3>
                            
                            <!-- Tabs Selector -->
                            <div class="flex p-1 bg-slate-50 dark:bg-slate-900/50 rounded-xl mb-6">
                                <button @click="activeTrendTab = 'negotiated'" :class="activeTrendTab === 'negotiated' ? 'bg-white dark:bg-slate-800 text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-1.5 text-[10px] font-black uppercase tracking-tighter rounded-lg transition-all">Negociados</button>
                                <button @click="activeTrendTab = 'volume'" :class="activeTrendTab === 'volume' ? 'bg-white dark:bg-slate-800 text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-1.5 text-[10px] font-black uppercase tracking-tighter rounded-lg transition-all">Volumen $</button>
                                <button @click="activeTrendTab = 'social'" :class="activeTrendTab === 'social' ? 'bg-white dark:bg-slate-800 text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-1.5 text-[10px] font-black uppercase tracking-tighter rounded-lg transition-all">Social</button>
                            </div>

                            <!-- Listado Dinámico -->
                            <div class="space-y-5">
                                <!-- Opción: Más Negociados (Unidades) -->
                                <template v-if="activeTrendTab === 'negotiated'">
                                    <div v-for="asset in $page.props.mostActive" :key="'neg-' + asset.ticker" class="flex items-center gap-3 group cursor-pointer" @click="$inertia.get(route('assets.show', asset.ticker))">
                                        <div class="w-9 h-9 rounded-xl bg-slate-50 dark:bg-slate-700 flex items-center justify-center border border-slate-100 dark:border-slate-600 overflow-hidden shrink-0">
                                            <img :src="asset.logo" class="w-6 h-6 object-contain" />
                                        </div>
                                        <div class="flex-grow min-w-0">
                                            <div class="text-[13px] font-bold text-slate-800 dark:text-white truncate group-hover:text-blue-600 transition-colors">{{ asset.ticker }}</div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-[10px] text-slate-400 font-bold uppercase">{{ formatCompactNumber(asset.volume) }} acciones</span>
                                                <span class="text-[10px] font-black" :class="asset.change >= 0 ? 'text-emerald-500' : 'text-rose-500'">{{ asset.change >= 0 ? '+' : '' }}{{ asset.change.toFixed(2) }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Opción: Mayor Volumen de Negocio ($) -->
                                <template v-if="activeTrendTab === 'volume'">
                                    <div v-for="asset in [...$page.props.mostActive].sort((a,b) => b.business_volume - a.business_volume)" :key="'vol-' + asset.ticker" class="flex items-center gap-3 group cursor-pointer" @click="$inertia.get(route('assets.show', asset.ticker))">
                                        <div class="w-9 h-9 rounded-xl bg-slate-50 dark:bg-slate-700 flex items-center justify-center border border-slate-100 dark:border-slate-600 overflow-hidden shrink-0">
                                            <img :src="asset.logo" class="w-6 h-6 object-contain" />
                                        </div>
                                        <div class="flex-grow min-w-0">
                                            <div class="text-[13px] font-bold text-slate-800 dark:text-white truncate group-hover:text-blue-600 transition-colors">{{ asset.ticker }}</div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-[10px] text-blue-500 font-black tracking-tighter">${{ formatCompactNumber(asset.business_volume) }}</span>
                                                <span class="text-[9px] text-slate-400 font-bold uppercase">{{ formatCompactNumber(asset.volume) }} vol.</span>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Opción: Tendencias Sociales (Menciones) -->
                                <template v-if="activeTrendTab === 'social'">
                                    <div v-for="trend in trends" :key="'soc-' + trend.ticker" class="flex items-center gap-4 group cursor-pointer" @click="$inertia.get(route('assets.show', trend.ticker))">
                                        <div class="w-9 h-9 rounded-xl bg-slate-50 dark:bg-slate-700 flex items-center justify-center border border-slate-100 dark:border-slate-600 overflow-hidden shrink-0">
                                            <img :src="trend.logo" class="w-6 h-6 object-contain" />
                                        </div>
                                        <div class="flex-grow min-w-0">
                                            <div class="text-[13px] font-bold text-slate-800 dark:text-white truncate group-hover:text-blue-600 transition-colors">{{ trend.name }}</div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-[10px] text-slate-400 font-bold uppercase">{{ trend.count }} menciones</span>
                                                <span class="text-[10px] text-indigo-500 font-black">+{{ trend.change }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <p v-if="trends.length === 0" class="text-[10px] text-slate-400 italic text-center py-4">Sin actividad social reciente</p>
                                </template>
                            </div>
                        </div>

                        <!-- Mejores Creadores y Aportadores -->
                        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700">
                            <h3 class="text-sm font-black uppercase tracking-widest text-slate-400 mb-6 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Comunidad
                            </h3>

                            <!-- Tabs Selector -->
                            <div class="flex p-1 bg-slate-50 dark:bg-slate-900/50 rounded-xl mb-6">
                                <button @click="activeCreatorsTab = 'popular'" :class="activeCreatorsTab === 'popular' ? 'bg-white dark:bg-slate-800 text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-1.5 text-[10px] font-black uppercase tracking-tighter rounded-lg transition-all">Más Populares</button>
                                <button @click="activeCreatorsTab = 'active'" :class="activeCreatorsTab === 'active' ? 'bg-white dark:bg-slate-800 text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-1.5 text-[10px] font-black uppercase tracking-tighter rounded-lg transition-all">Más Activos</button>
                            </div>

                            <div class="space-y-6">
                                <!-- Opción: Más Populares (Reacciones) -->
                                <template v-if="activeCreatorsTab === 'popular'">
                                    <Link v-for="(creator, idx) in topCreators" :key="'pop-' + creator.id" :href="route('social.profile', creator.username || creator.id)" class="flex items-start gap-4 group cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 p-2 -mx-2 rounded-2xl transition-all">
                                        <div class="relative">
                                            <img :src="creator.avatar || `https://ui-avatars.com/api/?name=${creator.name}`" class="w-10 h-10 rounded-xl object-cover border border-slate-100 dark:border-slate-700" />
                                            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-indigo-600 text-white text-[8px] font-black rounded-lg flex items-center justify-center border-2 border-white dark:border-slate-800 shadow-sm">
                                                #{{ idx + 1 }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-slate-800 dark:text-white group-hover:text-indigo-600 transition-colors">{{ creator.name }}</div>
                                            <div class="text-[10px] text-slate-500 font-bold tracking-tight">
                                                {{ creator.reactions_count || 0 }} reacciones
                                            </div>
                                        </div>
                                    </Link>
                                </template>

                                <!-- Opción: Más Activos (Aportaciones) -->
                                <template v-if="activeCreatorsTab === 'active'">
                                    <Link v-for="(creator, idx) in $page.props.activeCreators" :key="'act-' + creator.id" :href="route('social.profile', creator.username || creator.id)" class="flex items-start gap-4 group cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 p-2 -mx-2 rounded-2xl transition-all">
                                        <div class="relative">
                                            <img :src="creator.avatar || `https://ui-avatars.com/api/?name=${creator.name}`" class="w-10 h-10 rounded-xl object-cover border border-slate-100 dark:border-slate-700" />
                                            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 text-white text-[8px] font-black rounded-lg flex items-center justify-center border-2 border-white dark:border-slate-800 shadow-sm">
                                                #{{ idx + 1 }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-slate-800 dark:text-white group-hover:text-emerald-600 transition-colors">{{ creator.name }}</div>
                                            <div class="text-[10px] text-slate-500 font-bold tracking-tight flex items-center gap-1.5">
                                                <span class="w-1 h-1 bg-emerald-500 rounded-full"></span>
                                                {{ creator.posts_count || 0 }} aportes esta semana
                                            </div>
                                        </div>
                                    </Link>
                                    <p v-if="!$page.props.activeCreators?.length" class="text-[10px] text-slate-400 italic text-center py-4">Sin actividad reciente</p>
                                </template>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Report Modal -->
        <div v-if="reportingPost" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-md p-8 shadow-2xl border border-slate-100 dark:border-slate-700 animate-in zoom-in-95 duration-200">
                <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2">Reportar contenido</h3>
                <p class="text-sm text-slate-500 font-bold mb-6 italic">Ayúdanos a mantener la comunidad libre de spam y toxicidad.</p>
                
                <div class="space-y-3 mb-8">
                    <div v-for="reason in ['Contenido ofensivo', 'Spam / Estafa', 'Información falsa', 'Inapropiado']" :key="reason"
                         @click="reportReason = reason"
                         class="p-4 rounded-2xl border-2 cursor-pointer transition-all flex items-center justify-between"
                         :class="reportReason === reason ? 'border-rose-500 bg-rose-50 dark:bg-rose-900/20' : 'border-slate-100 dark:border-slate-700 hover:border-slate-300'">
                        <span class="text-sm font-bold" :class="reportReason === reason ? 'text-rose-600 dark:text-rose-400' : 'text-slate-600 dark:text-slate-400'">{{ reason }}</span>
                        <div v-if="reportReason === reason" class="w-2 h-2 rounded-full bg-rose-500"></div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button @click="reportingPost = null" class="flex-grow px-6 py-3 text-sm font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">Cancelar</button>
                    <button 
                        @click="submitReport"
                        :disabled="!reportReason"
                        class="flex-grow px-6 py-3 bg-rose-500 hover:bg-rose-600 disabled:opacity-50 text-white text-sm font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-rose-500/30 transition-all active:scale-95">
                        Enviar Reporte
                    </button>
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
