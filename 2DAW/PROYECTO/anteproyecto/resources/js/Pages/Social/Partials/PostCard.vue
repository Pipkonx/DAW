<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import ReactionPicker from './ReactionPicker.vue';
import CommentSection from './CommentSection.vue';

/**
 * PostCard - Tarjeta individual de publicación.
 * 
 * Gestiona el ciclo de vida de una publicación (edición, reacción, comentario, borrado).
 */
const props = defineProps({
    post: Object,
    auth: Object,
    isProfileOwner: Boolean,
});

const emit = defineEmits(['like', 'repost', 'bookmark', 'pin', 'edit', 'delete', 'comment']);

const activeReactionPickerId = ref(null);
const showComments = ref(false);
const isEditing = ref(false);

const editForm = useForm({
    content: props.post.content
});

const toggleEdit = () => {
    isEditing.value = !isEditing.value;
    if (isEditing.value) editForm.content = props.post.content;
};

const submitEdit = () => {
    emit('edit', { id: props.post.id, content: editForm.content });
    isEditing.value = false;
};

const getSmallAvatarRingClasses = (tier) => {
    switch (tier) {
        case 'premium': return 'ring-2 ring-purple-500 ring-offset-2 dark:ring-offset-slate-900 border-transparent transition-all';
        case 'pro': return 'ring-2 ring-indigo-500 ring-offset-2 dark:ring-offset-slate-900 border-transparent transition-all';
        case 'basic': return 'ring-2 ring-blue-500 ring-offset-2 dark:ring-offset-slate-900 border-transparent transition-all';
        default: return 'border border-slate-200 dark:border-slate-700';
    }
};

const handleLike = (emoji, isComment = false, commentId = null) => {
    emit('like', { 
        id: isComment ? commentId : props.post.id, 
        type: emoji,
        likeable_type: isComment ? 'comment' : 'post'
    });
    activeReactionPickerId.value = null;
};
</script>

<template>
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700 relative group/card hover:border-slate-200 dark:hover:border-slate-600 transition-colors">
        <!-- Indicador de Post Anclado -->
        <div v-if="post.is_pinned" class="absolute -top-3 left-6 px-3 py-1 bg-indigo-500 text-white text-[10px] font-black uppercase tracking-widest rounded-full shadow-lg flex items-center gap-1 z-10 transition-transform group-hover/card:scale-110">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
            </svg>
            Post Anclado
        </div>

        <!-- Indicador de Repost -->
        <div v-if="post.wall_is_repost" class="mb-4 text-xs font-bold text-slate-400 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
            Compartido por este perfil
        </div>
        
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <!-- Avatar del Autor -->
                <Link :href="route('social.profile', post.user.username)" class="hover:opacity-80 transition-opacity">
                    <img :src="post.user.avatar || `https://ui-avatars.com/api/?name=${post.user.name}`" 
                         :class="['w-12 h-12 rounded-full object-cover', getSmallAvatarRingClasses(post.user.tier)]" />
                </Link>
                <div>
                    <div class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <Link :href="route('social.profile', post.user.username)" class="hover:underline">{{ post.user.name }}</Link>
                    </div>
                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">{{ post.created_at_human || 'Ahora' }}</div>
                </div>
            </div>

            <!-- Menú de Acciones (Derecha) -->
            <div class="flex items-center gap-2">
                <div v-if="post.market_asset" class="px-3 py-1 bg-slate-50 dark:bg-slate-900/50 rounded-lg border border-slate-100 dark:border-slate-700">
                    <Link :href="route('assets.show', post.market_asset.ticker)" class="text-xs font-black text-slate-600 dark:text-slate-400 hover:text-blue-500 transition-colors">
                        ${{ post.market_asset.ticker }}
                    </Link>
                </div>
                
                <div class="relative group z-20">
                    <button class="p-2 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-full transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                        </svg>
                    </button>
                    <!-- Menú Desplegable -->
                    <div class="absolute right-0 top-full mt-1 w-48 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                        <button @click="emit('bookmark', post.id)" class="w-full text-left px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :class="post.is_bookmarked ? 'text-amber-500 fill-amber-500' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                            {{ post.is_bookmarked ? 'Quitar Marcador' : 'Guardar Marcador' }}
                        </button>
                        
                        <button v-if="post.user_id === auth.user.id" @click="emit('pin', post.id)" class="w-full text-left px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :class="post.is_pinned ? 'text-indigo-500 fill-indigo-500' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            {{ post.is_pinned ? 'Desanclar' : 'Anclar al Perfil' }}
                        </button>

                        <button v-if="post.can_edit" @click="toggleEdit" class="w-full text-left px-4 py-2 text-xs font-bold text-blue-600 hover:bg-blue-50 flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Editar Análisis ({{ post.created_at_human }})
                        </button>
                        
                        <button v-if="post.user_id === auth.user.id" @click="emit('delete', post)" class="w-full text-left px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido y Edición -->
        <div v-if="isEditing" class="space-y-4 mb-4">
            <textarea v-model="editForm.content" rows="3" class="w-full bg-slate-50 dark:bg-slate-900 border-2 border-indigo-100 rounded-2xl p-4 text-sm focus:border-indigo-500 text-slate-900 dark:text-slate-100"></textarea>
            <div class="flex justify-end gap-3">
                <button @click="isEditing = false" class="px-4 py-2 text-xs font-bold text-slate-400">Cancelar</button>
                <button @click="submitEdit" class="px-6 py-2 bg-indigo-600 text-white text-xs font-black rounded-xl shadow-lg">Guardar</button>
            </div>
        </div>
        <p v-else class="text-slate-700 dark:text-slate-200 leading-relaxed mb-4 whitespace-pre-wrap">{{ post.content }}</p>
                
        <img v-if="post.image_path" :src="`/storage/${post.image_path}`" class="w-full rounded-2xl mb-4 border border-slate-100 dark:border-slate-700 shadow-lg" />
        
        <!-- Resumen de Reacciones -->
        <div v-if="post.reactions_summary && Object.keys(post.reactions_summary).length" class="flex flex-wrap gap-2 mb-4 px-2">
            <div v-for="(count, emoji) in post.reactions_summary" :key="emoji" 
                 class="flex items-center gap-1.5 px-2.5 py-1 bg-slate-50 dark:bg-slate-900/50 rounded-full border border-slate-100 dark:border-slate-700 hover:scale-110 transition-transform">
                <span class="text-sm">{{ emoji }}</span>
                <span class="text-[10px] font-black text-slate-500">{{ count }}</span>
            </div>
        </div>

        <!-- Acciones del Post -->
        <div class="flex items-center gap-6 pt-4 border-t border-slate-50 dark:border-slate-700">
            <div class="relative">
                <button @mouseenter="activeReactionPickerId = post.id" 
                        @click="handleLike('👍')"
                        class="flex items-center gap-2 group p-2 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-xl transition-all">
                    <span v-if="post.user_reaction" class="text-lg animate-in zoom-in-50 duration-300">{{ post.user_reaction }}</span>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400 transition-transform group-active:scale-125" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span class="text-xs font-bold text-slate-500">{{ post.is_liked ? 'Reaccionado' : 'Reaccionar' }}</span>
                </button>

                <!-- Picker de Reacciones -->
                <ReactionPicker v-if="activeReactionPickerId === post.id" @mouseleave="activeReactionPickerId = null" @select="handleLike" />
            </div>

            <button @click="showComments = !showComments" class="flex items-center gap-2 group p-2 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-xl transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400 group-hover:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <span class="text-xs font-bold text-slate-500">{{ post.comments_count || 0 }}</span>
            </button>

            <button @click="emit('repost', post.id)" 
                    class="flex items-center gap-2 px-3 py-2 rounded-xl transition-all duration-300"
                    :class="post.is_reposted ? 'text-emerald-500 bg-emerald-50 dark:bg-emerald-900/30' : 'text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-900'">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
                <span class="text-xs font-black">{{ post.reposts_count || 0 }}</span>
            </button>
        </div>

        <!-- Componente de Comentarios -->
        <CommentSection v-if="showComments" :post="post" :auth="auth" @submit-comment="(data) => emit('comment', { post, content: data.content, parent_id: data.parent_id })" @react-comment="(data) => handleLike(data.type, true, data.id)" />
    </div>
</template>
