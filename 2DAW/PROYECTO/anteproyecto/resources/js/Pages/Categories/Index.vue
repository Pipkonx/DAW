<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
});

const activeTab = ref('expense'); // 'income' or 'expense'
const showingModal = ref(false);
const editingCategory = ref(null);
const parentOptions = computed(() => {
    // Only show top-level categories of the current type as potential parents
    // And exclude the category itself if editing
    return props.categories.filter(c => 
        c.type === activeTab.value && 
        (!editingCategory.value || c.id !== editingCategory.value.id)
    );
});

const form = useForm({
    name: '',
    type: 'expense',
    parent_id: null,
    icon: '',
    color: '#3b82f6', // default blue
    is_active: true,
});

const openModal = (category = null, parentId = null) => {
    editingCategory.value = category;
    form.type = activeTab.value;
    
    if (category) {
        form.name = category.name;
        form.parent_id = category.parent_id;
        form.icon = category.icon;
        form.color = category.color || '#3b82f6';
        form.is_active = !!category.is_active;
    } else {
        form.name = '';
        form.parent_id = parentId;
        form.icon = '';
        form.color = '#3b82f6';
        form.is_active = true;
    }
    
    showingModal.value = true;
};

const closeModal = () => {
    showingModal.value = false;
    form.reset();
    editingCategory.value = null;
};

const submit = () => {
    if (editingCategory.value) {
        form.put(route('categories.update', editingCategory.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('categories.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteCategory = (category) => {
    if (confirm('¿Estás seguro de que deseas eliminar esta categoría?')) {
        router.delete(route('categories.destroy', category.id));
    }
};

const toggleActive = (category) => {
    router.patch(route('categories.toggle', category.id), {}, {
        preserveScroll: true,
    });
};

const filteredCategories = computed(() => {
    return props.categories.filter(c => c.type === activeTab.value);
});
</script>

<template>
    <AppLayout title="Gestionar Categorías">
        <template #header>
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                Gestionar Categorías
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Tabs -->
                <div class="mb-6 border-b border-slate-200 dark:border-slate-700">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                        <li class="mr-2">
                            <button @click="activeTab = 'expense'" 
                                :class="{'border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400': activeTab === 'expense', 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:border-slate-600': activeTab !== 'expense'}"
                                class="inline-block p-4 border-b-2 rounded-t-lg transition-colors duration-200">
                                Gastos
                            </button>
                        </li>
                        <li class="mr-2">
                            <button @click="activeTab = 'income'"
                                :class="{'border-green-600 text-green-600 dark:text-green-400 dark:border-green-400': activeTab === 'income', 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:border-slate-600': activeTab !== 'income'}"
                                class="inline-block p-4 border-b-2 rounded-t-lg transition-colors duration-200">
                                Ingresos
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border border-slate-200 dark:border-slate-700">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-slate-900 dark:text-white">
                            {{ activeTab === 'expense' ? 'Categorías de Gastos' : 'Categorías de Ingresos' }}
                        </h3>
                        <PrimaryButton @click="openModal()">
                            Nueva Categoría
                        </PrimaryButton>
                    </div>

                    <!-- Categories List -->
                    <div class="space-y-4">
                        <div v-for="category in filteredCategories" :key="category.id" 
                            class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-4 shadow-sm transition-colors duration-200 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm" :style="{ backgroundColor: category.color }">
                                        <span v-if="category.icon">{{ category.icon }}</span>
                                        <span v-else>{{ category.name.substring(0, 1).toUpperCase() }}</span>
                                    </div>
                                    <span class="font-semibold text-slate-800 dark:text-slate-200" :class="{ 'opacity-50': !category.is_active }">
                                        {{ category.name }}
                                    </span>
                                    <span v-if="!category.is_active" class="text-xs text-red-600 bg-red-100 dark:bg-red-900/30 dark:text-red-400 px-2 py-0.5 rounded">
                                        Inactivo
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button @click="toggleActive(category)" class="text-xs font-medium px-2 py-1 rounded transition-colors text-slate-500 hover:bg-slate-100 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-slate-200">
                                        {{ category.is_active ? 'Desactivar' : 'Activar' }}
                                    </button>
                                    <button @click="openModal(category)" class="text-xs font-medium px-2 py-1 rounded transition-colors text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/30">
                                        Editar
                                    </button>
                                    <button @click="deleteCategory(category)" class="text-xs font-medium px-2 py-1 rounded transition-colors text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/30">
                                        Eliminar
                                    </button>
                                    <button @click="openModal(null, category.id)" class="text-xs font-medium px-2 py-1 rounded transition-colors text-green-600 hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-900/30 ml-2">
                                        + Subcategoría
                                    </button>
                                </div>
                            </div>

                            <!-- Subcategories -->
                            <div v-if="category.children && category.children.length > 0" class="mt-3 ml-8 space-y-2 border-l-2 border-slate-200 dark:border-slate-700 pl-4">
                                <div v-for="child in category.children" :key="child.id" class="flex justify-between items-center py-1 group">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-2 h-2 rounded-full" :style="{ backgroundColor: child.color || category.color }"></div>
                                        <span class="text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-slate-200 transition-colors" :class="{ 'opacity-50': !child.is_active }">
                                            {{ child.name }}
                                        </span>
                                        <span v-if="!child.is_active" class="text-xs text-red-600 bg-red-100 dark:bg-red-900/30 dark:text-red-400 px-2 py-0.5 rounded">
                                            Inactivo
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button @click="toggleActive(child)" class="text-xs font-medium px-2 py-1 rounded transition-colors text-slate-500 hover:bg-slate-100 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-slate-200">
                                            {{ child.is_active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                        <button @click="openModal(child)" class="text-xs font-medium px-2 py-1 rounded transition-colors text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/30">
                                            Editar
                                        </button>
                                        <button @click="deleteCategory(child)" class="text-xs font-medium px-2 py-1 rounded transition-colors text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/30">
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <Modal :show="showingModal" @close="closeModal">
            <div class="p-6 bg-white dark:bg-slate-800">
                <h2 class="text-lg font-medium text-slate-900 dark:text-slate-100 mb-4">
                    {{ editingCategory ? 'Editar Categoría' : 'Nueva Categoría' }}
                </h2>

                <div class="space-y-4">
                    <div>
                        <InputLabel for="name" value="Nombre" class="dark:text-slate-300" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full dark:bg-slate-900 dark:border-slate-700 dark:text-slate-300 dark:focus:border-blue-600 dark:focus:ring-blue-600"
                            placeholder="Nombre de la categoría"
                        />
                        <div v-if="form.errors.name" class="text-red-600 dark:text-red-400 text-xs mt-1">{{ form.errors.name }}</div>
                    </div>

                    <div>
                        <InputLabel for="parent" value="Categoría Padre (Opcional)" class="dark:text-slate-300" />
                        <select
                            id="parent"
                            v-model="form.parent_id"
                            class="mt-1 block w-full border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                        >
                            <option :value="null">Ninguna (Categoría Principal)</option>
                            <option v-for="parent in parentOptions" :key="parent.id" :value="parent.id">
                                {{ parent.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <InputLabel for="color" value="Color" class="dark:text-slate-300" />
                        <div class="flex items-center mt-1 space-x-2">
                            <input
                                id="color"
                                v-model="form.color"
                                type="color"
                                class="h-10 w-10 border border-slate-300 dark:border-slate-700 rounded cursor-pointer bg-white dark:bg-slate-900 p-1"
                            />
                            <span class="text-sm text-slate-500 dark:text-slate-400 font-mono">{{ form.color }}</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <Checkbox id="is_active" v-model:checked="form.is_active" />
                        <InputLabel for="is_active" value="Activa" class="ml-2 dark:text-slate-300" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="closeModal" class="dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 dark:border-slate-600"> Cancelar </SecondaryButton>
                    <PrimaryButton @click="submit" :disabled="form.processing">
                        {{ editingCategory ? 'Actualizar' : 'Guardar' }}
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>
