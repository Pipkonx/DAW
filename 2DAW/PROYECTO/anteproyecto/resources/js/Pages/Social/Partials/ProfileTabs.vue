<script setup>
/**
 * ProfileTabs - Selector de contenido del muro.
 * 
 * Permite al usuario alternar entre diferentes vistas de su actividad social.
 */
const props = defineProps({
    modelValue: String, // El tab activo
    isOwnProfile: Boolean,
});

const emit = defineEmits(['update:modelValue']);
</script>

<template>
    <div class="flex items-center gap-1 bg-white dark:bg-slate-800 p-1.5 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm mb-6 overflow-x-auto no-scrollbar">
        <button 
            @click="emit('update:modelValue', 'all')"
            class="px-5 py-2 text-xs font-black uppercase tracking-widest rounded-xl transition-all whitespace-nowrap"
            :class="modelValue === 'all' ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
        >
            Todo
        </button>
        <button 
            @click="emit('update:modelValue', 'posts')"
            class="px-5 py-2 text-xs font-black uppercase tracking-widest rounded-xl transition-all whitespace-nowrap"
            :class="modelValue === 'posts' ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
        >
            Publicaciones
        </button>
        <button 
            @click="emit('update:modelValue', 'reposts')"
            class="px-5 py-2 text-xs font-black uppercase tracking-widest rounded-xl transition-all whitespace-nowrap"
            :class="modelValue === 'reposts' ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
        >
            Compartidos
        </button>
        <button 
            v-if="isOwnProfile"
            @click="emit('update:modelValue', 'bookmarks')"
            class="px-5 py-2 text-xs font-black uppercase tracking-widest rounded-xl transition-all whitespace-nowrap flex items-center gap-2"
            :class="modelValue === 'bookmarks' ? 'bg-rose-500 text-white shadow-lg' : 'text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20'"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" :class="modelValue === 'bookmarks' ? 'fill-white' : 'fill-none'" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
            </svg>
            Guardados
        </button>
    </div>
</template>
