<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    show: Boolean,
});

const emit = defineEmits(['close']);

const categories = ref([]);
const loading = ref(false);
const activeTab = ref('expense'); // expense, income
const showForm = ref(false);
const editingCategory = ref(null);

const form = useForm({
    name: '',
    type: 'expense',
    parent_id: null,
    icon: '',
    color: '#000000',
});

const fetchCategories = async () => {
    loading.value = true;
    try {
        const res = await axios.get(route('categories.index'));
        categories.value = res.data;
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

watch(() => props.show, (val) => {
    if (val) fetchCategories();
});

const filteredCategories = computed(() => {
    return categories.value.filter(c => c.type === activeTab.value);
});

const openCreate = (parentId = null) => {
    editingCategory.value = null;
    form.reset();
    form.type = activeTab.value;
    form.parent_id = parentId;
    form.color = '#3b82f6'; // Default color
    showForm.value = true;
};

const edit = (cat) => {
    editingCategory.value = cat;
    form.name = cat.name;
    form.type = cat.type;
    form.parent_id = cat.parent_id;
    form.icon = cat.icon || '';
    form.color = cat.color || '#000000';
    showForm.value = true;
};

const submit = () => {
    if (editingCategory.value) {
        form.put(route('categories.update', editingCategory.value.id), {
            onSuccess: () => {
                showForm.value = false;
                fetchCategories();
            }
        });
    } else {
        form.post(route('categories.store'), {
            onSuccess: () => {
                showForm.value = false;
                fetchCategories();
            }
        });
    }
};

const remove = (id) => {
    if (confirm('¿Eliminar esta categoría?')) {
        axios.delete(route('categories.destroy', id))
            .then(() => fetchCategories())
            .catch(err => {
                alert('No se pudo eliminar (quizás es del sistema o tiene transacciones).');
            });
    }
};
</script>

<template>
    <Modal :show="show" @close="$emit('close')">
        <div class="p-6 h-[80vh] flex flex-col bg-white dark:bg-slate-800 rounded-lg">
            <div class="flex justify-between items-center mb-6 border-b border-slate-100 dark:border-slate-700 pb-4">
                <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">Gestionar Categorías</h2>
                <button @click="$emit('close')" class="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div v-if="!showForm" class="flex-grow flex flex-col overflow-hidden">
                <!-- Tabs -->
                <div class="flex space-x-2 mb-6 bg-slate-100 dark:bg-slate-700 p-1 rounded-xl">
                    <button 
                        @click="activeTab = 'expense'"
                        :class="activeTab === 'expense' ? 'bg-white dark:bg-slate-600 text-rose-600 dark:text-rose-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                        class="flex-1 py-2 rounded-lg text-sm font-bold transition-all"
                    >
                        Gastos
                    </button>
                    <button 
                        @click="activeTab = 'income'"
                        :class="activeTab === 'income' ? 'bg-white dark:bg-slate-600 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                        class="flex-1 py-2 rounded-lg text-sm font-bold transition-all"
                    >
                        Ingresos
                    </button>
                </div>

                <div class="flex justify-end mb-4">
                    <PrimaryButton @click="openCreate(null)" class="text-xs py-2">
                        + Nueva Categoría Principal
                    </PrimaryButton>
                </div>

                <!-- List -->
                <div class="flex-grow overflow-y-auto space-y-3 pr-2 custom-scrollbar">
                    <div v-if="loading" class="text-center py-10 text-slate-400 dark:text-slate-500 animate-pulse">Cargando categorías...</div>
                    
                    <div v-else-if="filteredCategories.length === 0" class="text-center py-10 text-slate-400 dark:text-slate-500 italic">
                        No hay categorías creadas.
                    </div>

                    <div v-else v-for="cat in filteredCategories" :key="cat.id" class="bg-slate-50 dark:bg-slate-900 rounded-xl p-3 border border-slate-100 dark:border-slate-700">
                        <div class="flex justify-between items-center group">
                            <div class="flex items-center font-medium text-slate-800 dark:text-slate-200">
                                <span class="w-3 h-3 rounded-full mr-3 shadow-sm" :style="{ backgroundColor: cat.color || '#cbd5e1' }"></span>
                                {{ cat.name }}
                                <span v-if="!cat.user_id" class="ml-2 text-[10px] bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 px-1.5 py-0.5 rounded uppercase tracking-wider font-bold">Sistema</span>
                            </div>
                            <div class="flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="openCreate(cat.id)" class="text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/30 p-1.5 rounded text-xs font-medium" title="Añadir Subcategoría">+ Sub</button>
                                <button v-if="cat.user_id" @click="edit(cat)" class="text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 p-1.5 rounded text-xs font-medium">Editar</button>
                                <button v-if="cat.user_id" @click="remove(cat.id)" class="text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/30 p-1.5 rounded text-xs font-medium">Eliminar</button>
                            </div>
                        </div>
                        <!-- Children -->
                        <div v-if="cat.children && cat.children.length > 0" class="mt-2 ml-1.5 space-y-1 border-l-2 border-slate-200 dark:border-slate-700 pl-3">
                             <div v-for="child in cat.children" :key="child.id" class="flex justify-between items-center group/child py-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded px-2 -ml-2 transition-colors">
                                <span class="text-sm text-slate-600 dark:text-slate-400">{{ child.name }}</span>
                                <div class="flex space-x-1 opacity-0 group-hover/child:opacity-100 transition-opacity">
                                    <button v-if="child.user_id" @click="edit(child)" class="text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/30 p-1 rounded text-xs">Editar</button>
                                    <button v-if="child.user_id" @click="remove(child.id)" class="text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/30 p-1 rounded text-xs">Eliminar</button>
                                </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="flex-grow flex flex-col">
                <div class="mb-4">
                    <button @click="showForm = false" class="text-sm text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 flex items-center transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Volver a la lista
                    </button>
                </div>
                
                <h3 class="text-lg font-bold mb-6 text-slate-800 dark:text-slate-100">{{ editingCategory ? 'Editar Categoría' : 'Nueva Categoría' }}</h3>
                
                <div class="space-y-6">
                    <div>
                        <InputLabel value="Nombre de la Categoría" />
                        <TextInput v-model="form.name" type="text" class="w-full mt-1" autofocus placeholder="Ej: Supermercado, Salario..." />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>
                    
                    <div>
                        <InputLabel value="Color Identificativo" />
                        <div class="flex items-center space-x-3 mt-2">
                            <div class="relative w-10 h-10 rounded-full overflow-hidden shadow-sm border border-slate-200 dark:border-slate-700">
                                <input type="color" v-model="form.color" class="absolute -top-2 -left-2 w-16 h-16 cursor-pointer" />
                            </div>
                            <TextInput v-model="form.color" type="text" class="flex-grow font-mono text-sm uppercase" />
                        </div>
                    </div>

                    <div v-if="form.parent_id" class="p-4 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-100 dark:border-slate-700 text-sm text-slate-600 dark:text-slate-400">
                        <span class="font-bold">Nota:</span> Esta será una subcategoría.
                    </div>
                </div>

                <div class="mt-auto flex justify-end pt-6 border-t border-slate-100 dark:border-slate-700">
                    <SecondaryButton @click="showForm = false">Cancelar</SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="submit">
                        {{ editingCategory ? 'Guardar Cambios' : 'Crear Categoría' }}
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </Modal>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 20px;
}
</style>
