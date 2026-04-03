<script setup>
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    blockedUsers: Array,
});

const unblockingId = ref(null);

const unblockUser = (user) => {
    unblockingId.value = user.id;
    router.post(route('profile.social.block', user.id), {}, {
        preserveScroll: true,
        onFinish: () => unblockingId.value = null,
    });
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                Gestión de Privacidad
            </h2>
            <p class="mt-1 text-sm text-slate-500 font-bold italic">
                Aquí puedes ver a los usuarios que has bloqueado. No verás sus posts ni comentarios en tu feed.
            </p>
        </header>

        <div v-if="blockedUsers.length > 0" class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm">
            <div class="divide-y divide-slate-50 dark:divide-slate-800">
                <div v-for="user in blockedUsers" :key="user.id" class="p-4 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                    <div class="flex items-center gap-3">
                        <img :src="user.avatar || `https://ui-avatars.com/api/?name=${user.name}`" class="w-10 h-10 rounded-full border border-slate-200 dark:border-slate-700" />
                        <div>
                            <div class="text-sm font-black text-slate-800 dark:text-white">{{ user.name }}</div>
                            <div class="text-xs text-slate-400 font-bold uppercase tracking-widest">@{{ user.username }}</div>
                        </div>
                    </div>
                    
                    <button 
                        @click="unblockUser(user)" 
                        :disabled="unblockingId === user.id"
                        class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-rose-100 hover:text-rose-600 dark:hover:bg-rose-900/30 text-slate-600 dark:text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all active:scale-95 disabled:opacity-50"
                    >
                        {{ unblockingId === user.id ? 'Desbloqueando...' : 'Desbloquear' }}
                    </button>
                </div>
            </div>
        </div>
        
        <div v-else class="p-8 text-center bg-slate-50 dark:bg-slate-900/50 rounded-3xl border-2 border-dashed border-slate-200 dark:border-slate-800">
            <div class="mb-2 text-slate-300 dark:text-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-sm text-slate-400 font-bold">No tienes usuarios bloqueados.</p>
        </div>
    </section>
</template>
