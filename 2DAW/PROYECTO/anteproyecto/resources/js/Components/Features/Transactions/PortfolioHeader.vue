<script setup>
import { computed } from 'vue';

const props = defineProps({
    portfolios: {
        type: Array,
        required: true
    },
    selectedPortfolioId: {
        type: [String, Number],
        required: true
    }
});

const emit = defineEmits(['switch', 'create', 'settings']);

const switchPortfolio = (id) => {
    emit('switch', id);
};
</script>

<template>
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-white leading-tight">Patrimonio Neto</h2>
        
        <div class="flex items-center space-x-2 overflow-x-auto max-w-full pb-2 md:pb-0 no-scrollbar">
                <!-- Aggregated Button -->
                <button 
                @click="switchPortfolio('aggregated')"
                class="px-4 py-2 rounded-full text-sm font-medium transition-colors whitespace-nowrap"
                :class="selectedPortfolioId === 'aggregated' ? 'bg-slate-800 text-white shadow-md dark:bg-blue-600' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 dark:hover:bg-slate-700'"
                >
                Todo
                </button>

                <!-- Portfolio List -->
                <button 
                v-for="p in portfolios" 
                :key="p.id"
                @click="switchPortfolio(p.id)"
                class="px-4 py-2 rounded-full text-sm font-medium transition-colors whitespace-nowrap flex items-center space-x-2"
                :class="selectedPortfolioId == p.id ? 'bg-slate-800 text-white shadow-md dark:bg-blue-600' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 dark:hover:bg-slate-700'"
                >
                <span>{{ p.name }}</span>
                </button>

                <!-- Add Portfolio Button -->
                <button 
                @click="$emit('create')"
                class="p-2 rounded-full bg-blue-600 text-white hover:bg-blue-700 shadow-md transition-colors dark:bg-blue-600 dark:hover:bg-blue-500"
                title="Nueva Cartera"
                >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                </button>

                <!-- Settings Button -->
                <button 
                @click="$emit('settings')"
                class="p-2 rounded-full bg-white text-slate-400 hover:text-slate-600 hover:bg-slate-50 border border-slate-200 transition-colors dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700 dark:hover:text-slate-200 dark:hover:bg-slate-700"
                title="Ajustes"
                >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                </button>
        </div>
    </div>
</template>
