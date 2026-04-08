<script setup>
import { ref, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// IMPORTACIÓN DE COMPONENTES MODULARES (REFACCIÓN)
import SidebarProfile from '../Social/Partials/SidebarProfile.vue';
import MarketMovers from '../Social/Partials/MarketMovers.vue';
import MarketPulse from '../Social/Partials/MarketPulse.vue';
import CommunityWidget from '../Social/Partials/CommunityWidget.vue';
import CreatePostForm from '../Social/Partials/CreatePostForm.vue';
import FeedFilters from '../Social/Partials/FeedFilters.vue';
import PostCard from '../Social/Partials/PostCard.vue';
import PostModal from '../Social/Partials/PostModal.vue';
import ModalConfirm from '@/Components/ModalConfirm.vue';

/**
 * Feed/Index - Orquestador del Muro Comunitario.
 * 
 * Centraliza la visualización de la actividad social y del mercado.
 * Delega la lógica de widgets y posts a componentes especializados de alta cohesión.
 */
const props = defineProps({
    posts: Object,
    featuredPost: Object,
    topGainers: Array,
    topLosers: Array,
    trends: Array,
    topCreators: Array,
    activeCreators: Array,
    filters: Object,
    mostActive: Array,
});

// GESTIÓN DE POST DESTACADO (DEEP-LINKING)
const featuredModalOpen = ref(false);
const postToShowInModal = ref(null);

// Estado del Modal de Confirmación
const confirmModal = ref({
    show: false,
    title: '',
    message: '',
    type: 'danger',
    onConfirm: () => {}
});

onMounted(() => {
    if (props.featuredPost) {
        postToShowInModal.value = props.featuredPost;
        featuredModalOpen.value = true;
    }
});

/**
 * Cierra el modal de vista detallada y limpia la URL.
 */
const closeFeaturedModal = () => {
    featuredModalOpen.value = false;
    postToShowInModal.value = null;
    
    const url = new URL(window.location.href);
    url.searchParams.delete('post');
    window.history.replaceState({}, '', url);
};

const handleLike = (payload) => {
    const typeId = payload.id;
    const emoji = payload.type;
    const modelType = payload.likeable_type || 'post';

    router.post(route('social.like'), { 
        likeable_id: typeId, 
        likeable_type: modelType, 
        type: emoji 
    }, { preserveScroll: true });
};

const handleRepost = (id) => {
    router.post(route('social.repost', id), {}, { preserveScroll: true });
};

const handleBookmark = (id) => {
    router.post(route('social.bookmark', id), {}, { preserveScroll: true });
};

const handleComment = (payload) => {
    const postId = payload.post ? payload.post.id : payload.id;
    router.post(route('social.comment', postId), { 
        content: payload.content,
        parent_id: payload.parent_id 
    }, { preserveScroll: true });
};

const handleEdit = ({ id, content }) => {
    router.put(route('social.update', id), { content }, { preserveScroll: true });
};

const handlePin = (id) => {
    router.post(route('social.pin', id), {}, { preserveScroll: true });
};

const handleDelete = (post) => {
    confirmModal.value = {
        show: true,
        title: '¿Eliminar Publicación?',
        message: 'Esta acción borrará permanentemente tu análisis del muro.',
        type: 'danger',
        onConfirm: () => {
            router.delete(route('social.delete', post.id), { 
                preserveScroll: true,
                onSuccess: () => confirmModal.value.show = false
            });
        }
    };
};
</script>

<template>
    <Head title="Muro Comunitario" />

    <AuthenticatedLayout>
        <!-- Cabecera de la Página -->
        <template #header>
            <h2 class="font-black text-xl text-slate-800 dark:text-white leading-tight uppercase tracking-wider">
                Muro Comunitario
            </h2>
        </template>

        <div class="py-10 bg-slate-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
                    
                    <!-- COLUMNA IZQUIERDA: Identidad e Inteligencia Rápida -->
                    <aside class="hidden lg:block space-y-8 sticky top-24">
                        <SidebarProfile :user="$page.props.auth.user" />
                        <MarketMovers :top-gainers="topGainers" :top-losers="topLosers" />
                    </aside>

                    <!-- COLUMNA CENTRAL: El Corazón del Feed -->
                    <main class="lg:col-span-2 space-y-8">
                        <!-- Redacción de Nuevo Análisis -->
                        <CreatePostForm :user="$page.props.auth.user" />

                        <!-- Filtros del Algoritmo del Muro -->
                        <FeedFilters :active-tab="filters.tab" />

                        <!-- Listado Reactivo de Publicaciones -->
                        <div class="space-y-6">
                            <PostCard 
                                v-for="post in posts.data" 
                                :key="post.id" 
                                :post="post" 
                                :auth="$page.props.auth"
                                @like="handleLike"
                                @repost="handleRepost"
                                @bookmark="handleBookmark"
                                @comment="handleComment"
                                @edit="handleEdit"
                                @delete="handleDelete"
                                @pin="handlePin"
                            />

                            <!-- Estado Vacío del Muro -->
                            <div v-if="posts.data.length === 0" class="py-24 text-center">
                                <div class="bg-white dark:bg-slate-800 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6 shadow-2xl text-slate-200 dark:text-slate-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wider">Muro en silencio</h3>
                                <p class="text-slate-500 mt-2 font-bold italic text-sm">¡Sé el primero en compartir algo con la comunidad Pipkonx!</p>
                            </div>
                        </div>

                        <!-- Paginación Simple (Inertia suele manejar esto con scroll infinito o links abajo) -->
                        <div v-if="posts.links && posts.links.length > 3" class="pt-8 flex justify-center">
                            <!-- Aquí irían los links de paginación si se prefiere sobre infinite scroll -->
                        </div>
                    </main>

                    <!-- COLUMNA DERECHA: Pulso del Mercado y Ranking -->
                    <aside class="hidden lg:block space-y-8 sticky top-24">
                        <MarketPulse :most-active="mostActive" :trends="trends" />
                        <CommunityWidget :top-creators="topCreators" :active-creators="activeCreators" />
                    </aside>

                </div>
            </div>
        </div>

        <!-- MODAL DE DEEP-LINKING (VISTA DETALLADA DE POST) -->
        <div v-if="featuredModalOpen && postToShowInModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md overflow-y-auto">
            <div class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-2xl my-8 relative shadow-2xl animate-in zoom-in-95 duration-200">
                <!-- Botón de Cierre Flotante -->
                <button @click="closeFeaturedModal" class="absolute -top-12 right-0 md:-right-12 p-3 bg-white/10 hover:bg-white/20 text-white rounded-full transition-all border border-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                <!-- Reutilización del PostCard en modo modal si es necesario, o visualización directa -->
                <div class="overflow-hidden rounded-3xl">
                    <PostCard 
                        :post="postToShowInModal" 
                        :auth="$page.props.auth" 
                        @like="handleLike"
                        @repost="handleRepost"
                        @bookmark="handleBookmark"
                        @comment="handleComment"
                        @edit="handleEdit"
                        @delete="handleDelete"
                        @pin="handlePin"
                    />
                </div>
            </div>
        </div>

        <!-- Modal de Confirmación Global -->
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
