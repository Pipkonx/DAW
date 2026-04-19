<script setup>
import { ref, watch, onMounted } from 'vue';
import { formatCurrency } from '@/Utils/formatting';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    items: {
        type: Array, // Full list of items from backend
        required: true
    },
    totalAmount: {
        type: [Number, String],
        default: 0
    },
    colorClass: {
        type: String,
        default: 'bg-emerald-500' // Default to green
    },
    emptyMessage: {
        type: String,
        default: 'No hay datos registrados.'
    },
    isPrivacyMode: {
        type: Boolean,
        default: false
    }
});

const displayedItems = ref([]);
const currentPage = ref(1);
const pageSize = 20;
const observerTarget = ref(null);

const loadMore = () => {
    if (!props.items) return;
    const start = (currentPage.value - 1) * pageSize;
    const end = start + pageSize;
    if (start < props.items.length) {
        displayedItems.value.push(...props.items.slice(start, end));
        currentPage.value++;
    }
};

// Reset list when items change (e.g. filter applied)
watch(() => props.items, () => {
    displayedItems.value = [];
    currentPage.value = 1;
    loadMore();
}, { immediate: true });

onMounted(() => {
    const options = { root: null, rootMargin: '50px', threshold: 0.1 };
    const observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) {
            loadMore();
        }
    }, options);
    
    if (observerTarget.value) observer.observe(observerTarget.value);
});
</script>

<template>
    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 max-h-96 overflow-y-auto custom-scrollbar">
        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 sticky top-0 bg-white dark:bg-slate-800 z-10 py-2">{{ title }}</h3>
        
        <div v-if="displayedItems.length > 0" class="space-y-4">
            <div v-for="(item, index) in displayedItems" :key="index" class="relative">
                <div class="flex justify-between items-center mb-1">
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300 truncate w-2/3" :title="item.category_name">{{ item.category_name }}</span>
                    <span class="text-sm font-bold text-slate-900 dark:text-white">{{ isPrivacyMode ? '****' : formatCurrency(item.total) }}</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                    <div :class="[colorClass, 'h-2 rounded-full']" :style="{ width: Math.min((item.total / (totalAmount || 1) * 100), 100) + '%' }"></div>
                </div>
            </div>
            <!-- Intersection Observer Target -->
            <div ref="observerTarget" class="h-1 w-full"></div>
        </div>
        
        <div v-else class="text-slate-400 text-sm italic text-center py-4">
            {{ emptyMessage }}
        </div>
    </div>
</template>
