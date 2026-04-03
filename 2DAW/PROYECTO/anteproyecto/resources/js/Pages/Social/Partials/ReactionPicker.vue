<script setup>
/**
 * ReactionPicker - Selector de emojis para interacciones.
 * 
 * Un componente ligero que emite el emoji seleccionado por el usuario.
 */
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

const emit = defineEmits(['select']);
</script>

<template>
    <div class="absolute bottom-full left-0 mb-2 bg-white dark:bg-slate-800 shadow-2xl border border-slate-100 dark:border-slate-700 rounded-2xl p-2 flex gap-3 animate-in fade-in slide-in-from-top-2 duration-200 z-50">
        <button 
            v-for="reaction in reactions" 
            :key="reaction.emoji" 
            @click="emit('select', reaction.emoji)"
            class="text-2xl hover:scale-150 transition-transform duration-200 p-1"
            :title="reaction.label"
        >
            {{ reaction.emoji }}
        </button>
    </div>
</template>
