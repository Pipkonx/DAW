<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ModalConfirm from '@/Components/ModalConfirm.vue';
import ProfileHeader from './Partials/ProfileHeader.vue';
import ProfileTabs from './Partials/ProfileTabs.vue';
import PostCard from './Partials/PostCard.vue';

/**
 * Profile - Página de Perfil Social (Muro).
 * 
 * Actúa como orquestador principal, gestionando el estado global del perfil
 * y las comunicaciones con el servidor a través de Inertia.
 */
const props = defineProps({
    profileUser: Object,
    posts: Object,
    bookmarks: Array,
    isOwnProfile: Boolean,
    isFollowing: Boolean,
    isBlocked: Boolean,
    joined_at: String,
});

const activeTab = ref('all'); // Pestaña activa ('all', 'posts', 'reposts', 'bookmarks')

// Estado del Modal de Confirmación
const confirmModal = ref({
    show: false,
    title: '',
    message: '',
    type: 'danger',
    onConfirm: () => {}
});

/**
 * Filtra las publicaciones según la pestaña seleccionada.
 */
const displayPosts = computed(() => {
    const postsArray = props.posts?.data || props.posts || [];
    if (activeTab.value === 'all') return postsArray;
    if (activeTab.value === 'posts') return postsArray.filter(p => !p.wall_is_repost);
    if (activeTab.value === 'reposts') return postsArray.filter(p => p.wall_is_repost);
    if (activeTab.value === 'bookmarks') return props.bookmarks || [];
    return postsArray;
});

/* -------------------------------------------------------------------------- */
/* ACCIONES DE PERFIL (Follow / Block)                                        */
/* -------------------------------------------------------------------------- */

const handleFollow = () => {
    router.post(route('profile.social.follow', props.profileUser.id), {}, { preserveScroll: true });
};

const handleBlock = () => {
    confirmModal.value = {
        show: true,
        title: props.isBlocked ? '¿Desbloquear Usuario?' : '¿Bloquear Usuario?',
        message: props.isBlocked 
            ? `Volverás a ver el contenido de ${props.profileUser.name} en tu feed.` 
            : `Dejarás de ver los posts de ${props.profileUser.name}. El usuario no será notificado.`,
        type: props.isBlocked ? 'info' : 'danger',
        onConfirm: () => {
            router.post(route('profile.social.block', props.profileUser.id), {}, {
                preserveScroll: true,
                onSuccess: () => confirmModal.value.show = false
            });
        }
    };
};

/* -------------------------------------------------------------------------- */
/* ACCIONES DE PUBLICACIÓN (Likes, Reposts, Bookmarks, Pins)                 */
/* -------------------------------------------------------------------------- */

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

const handlePin = (id) => {
    router.post(route('social.pin', id), {}, { preserveScroll: true });
};

const handleEdit = ({ id, content }) => {
    router.put(route('social.update', id), { content }, { preserveScroll: true });
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

const handleComment = (payload) => {
    // Acepta tanto un payload antiguo { post, content } como el nuevo que incluye parent_id
    const postId = payload.post ? payload.post.id : payload.id;
    router.post(route('social.comment', postId), { 
        content: payload.content,
        parent_id: payload.parent_id 
    }, { preserveScroll: true });
};
</script>

<template>
    <Head :title="`Perfil de ${profileUser.name}`" />

    <AuthenticatedLayout>
        <div class="py-8 bg-slate-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                
                <!-- Cabecera Modular -->
                <ProfileHeader 
                    :profileUser="profileUser"
                    :joinedAt="joined_at"
                    :isOwnProfile="isOwnProfile"
                    :isFollowing="isFollowing"
                    :isBlocked="isBlocked"
                    @toggle-follow="handleFollow"
                    @toggle-block="handleBlock"
                />

                <!-- Navegación por Pestañas -->
                <ProfileTabs 
                    v-model="activeTab"
                    :isOwnProfile="isOwnProfile"
                />

                <!-- Feed de Publicaciones -->
                <div class="space-y-6">
                    <!-- Estado Vacío -->
                    <div v-if="displayPosts.length === 0" class="bg-slate-100 dark:bg-slate-800/50 rounded-3xl p-12 text-center border-2 border-dashed border-slate-200 dark:border-slate-700">
                        <p class="text-slate-500 dark:text-slate-400 text-lg font-bold">
                            {{ activeTab === 'bookmarks' ? 'Aún no tienes publicaciones guardadas.' : 'Aún no hay publicaciones aquí.' }}
                        </p>
                    </div>

                    <!-- Lista de Publicaciones Modular -->
                    <PostCard 
                        v-for="post in displayPosts" 
                        :key="post.id"
                        :post="post"
                        :auth="$page.props.auth"
                        :isProfileOwner="isOwnProfile"
                        @like="handleLike"
                        @repost="handleRepost"
                        @bookmark="handleBookmark"
                        @pin="handlePin"
                        @edit="handleEdit"
                        @delete="handleDelete"
                        @comment="handleComment"
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

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
