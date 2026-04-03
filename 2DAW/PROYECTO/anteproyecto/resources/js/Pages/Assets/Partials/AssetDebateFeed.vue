<script setup>
import { useForm, router } from '@inertiajs/vue3';
import { formatDate } from '@/Utils/formatting';

const props = defineProps({
    marketAsset: Object,
    posts: Object
});

const postForm = useForm({
    market_asset_id: props.marketAsset.id,
    content: ''
});

const submitPost = () => {
    postForm.post(route('social.post'), {
        onSuccess: () => postForm.reset('content')
    });
};

const toggleLike = (id, type) => {
    router.post(route('social.like'), { likeable_id: id, likeable_type: type }, { preserveScroll: true });
};

const report = (id, type) => {
    const reason = prompt('Motivo del reporte:');
    if (reason) router.post(route('social.report'), { reportable_id: id, reportable_type: type, reason }, { preserveScroll: true });
};
</script>

<template>
    <div class="max-w-3xl mx-auto space-y-8">
        <!-- Caja de Publicación -->
        <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
            <h3 class="font-black text-slate-800 dark:text-white mb-6 uppercase tracking-widest text-xs">Debate sobre {{ marketAsset.name }}</h3>
            <div class="flex gap-5">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center font-black text-indigo-600 dark:text-indigo-400 shrink-0">
                    {{ $page.props.auth.user.name.charAt(0) }}
                </div>
                <div class="flex-1">
                    <textarea 
                        v-model="postForm.content"
                        placeholder="Comparte tu opinión técnica o fundamental..."
                        class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500 min-h-[120px] p-5 dark:text-white font-medium resize-none placeholder:text-slate-400"
                    ></textarea>
                    <div class="flex justify-between items-center mt-6">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ posts.total }} Publicaciones</p>
                        <button 
                            @click="submitPost"
                            :disabled="postForm.processing || !postForm.content"
                            class="bg-indigo-600 text-white font-black px-10 py-3 rounded-2xl text-sm shadow-xl active:scale-95 transition-all"
                        >
                            Publicar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Muro de Comentarios -->
        <div class="space-y-6">
            <div v-for="post in posts.data" :key="post.id" class="bg-white dark:bg-slate-800 p-8 rounded-3xl border border-slate-50 dark:border-slate-700/50 group transition-all">
                <div class="flex gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-900 flex items-center justify-center font-black text-slate-400 shrink-0 border border-slate-100">
                        {{ post.user.name.charAt(0) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-3">
                                <span class="font-black text-slate-800 dark:text-white">{{ post.user.name }}</span>
                                <span class="text-[10px] font-bold text-slate-400">• {{ formatDate(post.created_at) }}</span>
                            </div>
                        </div>
                        <p class="text-sm text-slate-600 dark:text-slate-300 font-medium leading-relaxed mb-6">{{ post.content }}</p>
                        
                        <div class="flex items-center justify-between pt-4 border-t border-slate-50 dark:border-slate-700/50">
                            <div class="flex gap-4">
                                <button @click="toggleLike(post.id, 'post')" class="flex items-center gap-2 px-3 py-2 rounded-xl group transition-all hover:bg-emerald-50 dark:hover:bg-emerald-900/10">
                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                                    <span class="text-xs font-black text-slate-400 group-hover:text-emerald-500">{{ post.likes_count || 0 }}</span>
                                </button>
                                <button class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-900 transition-all font-black text-xs text-slate-400">{{ post.comments_count || 0 }} Respuestas</button>
                            </div>
                            <button @click="report(post.id, 'post')" class="p-2 rounded-xl text-slate-300 hover:text-rose-500 transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="posts.data.length === 0" class="text-center py-16 border-4 border-dashed border-slate-100 rounded-[3rem]">
                <p class="text-slate-400 font-bold text-xs uppercase tracking-widest font-mono">Sé el primero en opinar sobre {{ marketAsset.ticker }}</p>
            </div>
        </div>
    </div>
</template>
