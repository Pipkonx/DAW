<template>
  <div>
    <div 
        class="flex items-center py-1.5 px-2 hover:bg-white/5 cursor-pointer rounded transition-colors group"
        :style="{ paddingLeft: `${depth * 12 + 8}px` }"
        @click="handleClick"
    >
        <!-- Icon -->
        <span class="mr-2 text-slate-500 group-hover:text-slate-300 transition-colors">
            <template v-if="item.type === 'folder'">
                <svg v-if="isOpen" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-accent/80" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-accent/80" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </template>
            <template v-else>
                <!-- File Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-secondary" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.586l4 4a1 1 0 01.586 1.414V19a2 2 0 01-2 2z" />
                </svg>
            </template>
        </span>

        <!-- Name -->
        <span class="truncate text-slate-400 group-hover:text-slate-200 transition-colors" :class="{'text-primary font-medium': item.isOpen && item.type === 'file'}">{{ item.name }}</span>
    </div>

    <!-- Children -->
    <div v-if="item.type === 'folder' && isOpen">
        <FileTreeItem 
            v-for="(child, key) in item.children" 
            :key="key" 
            :item="child" 
            :depth="depth + 1"
            @open="$emit('open', $event)"
            @delete="$emit('delete', $event)"
        />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import FileTreeItem from './FileTreeItem.vue'; // Recursive import

const props = defineProps({
    item: Object,
    depth: Number
});

const emit = defineEmits(['open', 'delete']);

const isOpen = ref(true);

const handleClick = () => {
    if (props.item.type === 'folder') {
        isOpen.value = !isOpen.value;
    } else {
        emit('open', props.item.path);
    }
};
</script>