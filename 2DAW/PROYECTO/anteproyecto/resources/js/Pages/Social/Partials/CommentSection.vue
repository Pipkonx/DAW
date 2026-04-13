<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import ReactionPicker from './ReactionPicker.vue';

/**
 * CommentSection - Hilo de discusión en una publicación.
 * 
 * Gestiona la visualización de comentarios, anidación de respuestas y el envío de nuevas participaciones.
 */
const props = defineProps({
    post: Object,
    auth: Object,
});

const emit = defineEmits(['submit-comment', 'react-comment']);

const commentContent = ref('');
const replyingTo = ref(null); // Contiene el objeto del comentario al que se responde
const activeReactionPickerId = ref(null);

const getSmallAvatarRingClasses = (tier) => {
    switch (tier) {
        case 'premium': return 'ring-2 ring-purple-500 ring-offset-2 dark:ring-offset-slate-900 border-transparent';
        case 'pro': return 'ring-2 ring-indigo-500 ring-offset-2 dark:ring-offset-slate-900 border-transparent';
        case 'basic': return 'ring-2 ring-blue-500 ring-offset-2 dark:ring-offset-slate-900 border-transparent';
        default: return 'border border-slate-200 dark:border-slate-700';
    }
};

const handleReplyClick = (comment) => {
    replyingTo.value = comment;
};

const cancelReply = () => {
    replyingTo.value = null;
};

const handleSubmit = () => {
    if (!commentContent.value.trim()) return;
    emit('submit-comment', { 
        content: commentContent.value, 
        parent_id: replyingTo.value ? replyingTo.value.id : null 
    });
    commentContent.value = '';
    replyingTo.value = null;
};
</script>

<template>
    <div class="mt-4 pt-4 border-t border-slate-50 dark:border-slate-700 animate-in fade-in slide-in-from-top-2">
        <div class="space-y-6 mb-6">
            <!-- Lista de Comentarios Existentes -->
            <div v-for="comment in post.comments" :key="comment.id" class="space-y-4">
                <!-- Comentario Raíz -->
                <div class="flex gap-3 group/comment">
                    <Link :href="route('social.profile', comment.user.username || `user_${comment.user.id}`)" class="shrink-0 hover:opacity-80 transition-opacity">
                        <img :src="comment.user.avatar || `https://ui-avatars.com/api/?name=${comment.user.name}`" 
                             :class="['w-8 h-8 rounded-full shadow-sm object-cover', getSmallAvatarRingClasses(comment.user.tier)]" />
                    </Link>
                    <div class="flex-grow">
                        <div class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-4 border border-slate-100 dark:border-slate-700">
                            <div class="flex justify-between items-center mb-1">
                                <Link :href="route('social.profile', comment.user.username || `user_${comment.user.id}`)" class="text-xs font-black text-slate-800 dark:text-white hover:underline">
                                    {{ comment.user.name }}
                                </Link>
                                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">{{ comment.created_at_human || 'Ahora' }}</span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">{{ comment.content }}</p>
                        </div>
                        
                        <!-- Acciones del comentario raíz -->
                        <div class="flex gap-4 mt-2 px-2 relative items-center">
                            <button @click="activeReactionPickerId = activeReactionPickerId === comment.id ? null : comment.id" class="text-[10px] font-bold transition-colors" :class="comment.is_liked ? 'text-blue-600' : 'text-slate-500 hover:text-blue-500'">
                                {{ comment.is_liked ? (comment.user_reaction || '❤') : '👍' }} Reaccionar
                            </button>
                            
                            <!-- Picker Flotante -->
                            <ReactionPicker 
                                v-if="activeReactionPickerId === comment.id"
                                @mouseleave="activeReactionPickerId = null"
                                @select="(emoji) => { emit('react-comment', { id: comment.id, type: emoji }); activeReactionPickerId = null; }" 
                            />
                            <button @click="handleReplyClick(comment)" class="text-[10px] font-bold text-slate-500 hover:text-indigo-500">
                                💬 Responder
                            </button>
                            <span v-if="comment.reactions_summary && Object.keys(comment.reactions_summary).length" class="text-[10px] font-bold text-slate-400">
                                {{ Object.entries(comment.reactions_summary).map(([k, v]) => `${k} ${v}`).join(', ') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Lista de Respuestas Anidadas (Replies) -->
                <div v-if="comment.replies && comment.replies.length > 0" class="pl-11 space-y-4">
                    <div v-for="reply in comment.replies" :key="reply.id" class="flex gap-2 group/reply">
                        <Link :href="route('social.profile', reply.user.username || `user_${reply.user.id}`)" class="shrink-0 hover:opacity-80 transition-opacity">
                            <img :src="reply.user.avatar || `https://ui-avatars.com/api/?name=${reply.user.name}`" 
                                 :class="['w-6 h-6 rounded-full shadow-sm object-cover', getSmallAvatarRingClasses(reply.user.tier)]" />
                        </Link>
                        <div class="flex-grow">
                            <div class="bg-slate-50 dark:bg-slate-900/30 rounded-2xl p-3 border border-slate-100 dark:border-slate-700">
                                <div class="flex justify-between items-center mb-1">
                                    <Link :href="route('social.profile', reply.user.username || `user_${reply.user.id}`)" class="text-[11px] font-black text-slate-800 dark:text-white hover:underline">
                                        {{ reply.user.name }}
                                    </Link>
                                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">{{ reply.created_at_human || 'Ahora' }}</span>
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">{{ reply.content }}</p>
                            </div>

                            <!-- Acciones de respuesta -->
                            <div class="flex gap-4 mt-1 px-2 relative items-center">
                                <button @click="activeReactionPickerId = activeReactionPickerId === reply.id ? null : reply.id" class="text-[9px] font-bold transition-colors" :class="reply.is_liked ? 'text-blue-600' : 'text-slate-500 hover:text-blue-500'">
                                    {{ reply.is_liked ? (reply.user_reaction || '❤') : '👍' }} Reaccionar
                                </button>

                                <ReactionPicker 
                                    v-if="activeReactionPickerId === reply.id"
                                    @mouseleave="activeReactionPickerId = null"
                                    @select="(emoji) => { emit('react-comment', { id: reply.id, type: emoji }); activeReactionPickerId = null; }" 
                                />
                                <span v-if="reply.reactions_summary && Object.keys(reply.reactions_summary).length" class="text-[9px] font-bold text-slate-400">
                                    {{ Object.entries(reply.reactions_summary).map(([k, v]) => `${k} ${v}`).join(', ') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicador de que estamos respondiendo -->
        <div v-if="replyingTo" class="mb-2 px-2 flex justify-between items-center text-xs text-indigo-500 font-bold bg-indigo-50 dark:bg-indigo-900/30 p-2 rounded-xl">
            <span>Respondiendo a @{{ replyingTo.user.name }}</span>
            <button @click="cancelReply" class="text-slate-400 hover:text-red-500">✕ Cancelar</button>
        </div>

        <!-- Área de Nuevo Comentario -->
        <div class="flex gap-3">
            <img :src="auth.user.avatar || `https://ui-avatars.com/api/?name=${auth.user.name}`" 
                 :class="['w-8 h-8 rounded-full object-cover', getSmallAvatarRingClasses(auth.user.tier)]" />
            <div class="flex-grow relative">
                <input 
                    type="text" 
                    v-model="commentContent"
                    :placeholder="replyingTo ? 'Escribe tu respuesta...' : 'Escribe un comentario...'" 
                    class="w-full bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 border-none focus:ring-2 focus:ring-blue-500 rounded-2xl text-xs py-2 pr-10"
                    @keyup.enter="handleSubmit"
                />
                <button @click="handleSubmit" class="absolute right-2 top-1.5 p-1 text-blue-500 hover:text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>
