<script setup>
import { ref, computed, watch } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

/**
 * CategorySelector - Selector de categorías jerárquico.
 * 
 * Gestiona el árbol de categorías del usuario (Ingresos y Gastos), permitiendo
 * una selección rápida mediante búsqueda o navegación por menús.
 */
const props = defineProps({
    modelValue: [String, Number],
    categoryName: String,
    categories: Array,
    transactionType: String,
    error: String,
});

const emit = defineEmits(['update:modelValue', 'update:categoryName', 'select']);

const showDropdown = ref(false);

/**
 * Estructura plana de categorías filtrada por el tipo de transacción activo.
 */
const filteredCategoriesList = computed(() => {
    const list = [];
    const type = ['buy', 'sell', 'dividend', 'reward', 'gift'].includes(props.transactionType) 
        ? null 
        : (props.transactionType === 'income' ? 'income' : 'expense');
    
    if (!type) return [];

    props.categories.filter(c => c.type === type).forEach(cat => {
        list.push({ id: cat.id, name: cat.name, isParent: true });
        if (cat.children) {
            cat.children.forEach(child => {
                list.push({ id: child.id, name: child.name, parentPath: cat.name, isParent: false });
            });
        }
    });

    if (!props.categoryName) return list;
    const query = props.categoryName.toLowerCase();
    return list.filter(c => c.name.toLowerCase().includes(query) || (c.parentPath && c.parentPath.toLowerCase().includes(query)));
});

const selectCategory = (cat) => {
    emit('update:modelValue', cat.id);
    emit('update:categoryName', cat.name);
    showDropdown.value = false;
};

const handleBlur = () => {
    setTimeout(() => {
        showDropdown.value = false;
    }, 200);
};

// Sincronización automática de ID si el nombre coincide exactamente
watch(() => props.categoryName, (newVal) => {
    if (newVal) {
        const exactMatch = filteredCategoriesList.value.find(c => c.name.toLowerCase() === newVal.toLowerCase());
        if (exactMatch) emit('update:modelValue', exactMatch.id);
        else emit('update:modelValue', null);
    } else {
        emit('update:modelValue', null);
    }
});
</script>

<template>
    <div class="relative">
        <InputLabel for="category_name" value="Categoría" class="dark:text-slate-300" />
        <div class="relative">
            <TextInput
                id="category_name"
                type="text"
                class="mt-1 block w-full dark:bg-slate-700 dark:text-white"
                :value="categoryName"
                @input="e => { emit('update:categoryName', e.target.value); showDropdown = true; }"
                @focus="showDropdown = true"
                @blur="handleBlur"
                placeholder="Selecciona o escribe una categoría..."
                autocomplete="off"
            />
            <!-- Botón de despliegue lateral -->
            <button @click="showDropdown = !showDropdown" type="button" class="absolute right-3 top-3.5 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <!-- Menú de Categorías -->
        <div v-if="showDropdown && filteredCategoriesList.length > 0" class="absolute left-0 right-0 mt-1 bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-100 dark:border-slate-700 max-h-60 overflow-auto z-50 animate-in fade-in slide-in-from-top-2">
            <ul>
                <li v-for="cat in filteredCategoriesList" :key="cat.id"
                    @click="selectCategory(cat)"
                    class="px-4 py-2 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm transition-colors border-b last:border-0 border-slate-50 dark:border-slate-700/50">
                    <div class="flex items-center gap-2">
                        <span v-if="!cat.isParent" class="text-xs text-slate-400 font-bold uppercase tracking-widest">{{ cat.parentPath }} ></span>
                        <span :class="cat.isParent ? 'font-black text-slate-800 dark:text-white uppercase tracking-wider text-xs' : 'text-slate-600 dark:text-slate-300 font-bold'">{{ cat.name }}</span>
                    </div>
                </li>
            </ul>
        </div>
        <InputError :message="error" class="mt-2" />
    </div>
</template>
