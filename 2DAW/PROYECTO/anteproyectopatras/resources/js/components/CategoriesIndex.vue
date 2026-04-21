<template>
    <div class="py-4">
        <div class="container overflow-hidden">
            <!-- Header -->
            <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Gestionar Categorías</h2>
                    <p class="text-muted small mb-0">Organiza tus ingresos y gastos para un análisis más preciso.</p>
                </div>
                <button class="btn btn-primary d-flex align-items-center gap-2 px-4 shadow-sm" @click="openModal()">
                    <i class="bi bi-plus-lg"></i> Nueva Categoría
                </button>
            </div>

            <!-- Tabs -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 pb-0 pt-3 px-4">
                    <ul class="nav nav-tabs border-bottom-0">
                        <li class="nav-item">
                            <button class="nav-link px-4 fw-bold" :class="activeTab === 'expense' ? 'active text-primary' : 'text-muted'" @click="activeTab = 'expense'">
                                Gastos
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link px-4 fw-bold" :class="activeTab === 'income' ? 'active text-success' : 'text-muted'" @click="activeTab = 'income'">
                                Ingresos
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div v-if="filteredCategories.length === 0" class="text-center py-5 text-muted italic">
                        No hay categorías definidas para este tipo.
                    </div>
                    
                    <div class="row g-3">
                        <div v-for="category in filteredCategories" :key="category.id" class="col-12">
                            <div class="card border border-light shadow-none bg-light bg-opacity-50 p-3 rounded-3 transition-hover">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" 
                                             :style="{ width: '36px', height: '36px', backgroundColor: category.color || '#6c757d', fontSize: '0.8rem' }">
                                            {{ category.icon || category.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark" :class="{ 'opacity-50': !category.is_active }">{{ category.name }}</span>
                                            <span v-if="!category.is_active" class="badge bg-danger ms-2" style="font-size: 0.6rem;">INACTIVA</span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-link btn-sm text-muted p-0" title="Nueva subcategoría" @click="openModal(null, category.id)">
                                            <i class="bi bi-plus-circle"></i>
                                        </button>
                                        <button class="btn btn-link btn-sm text-primary p-0" title="Editar" @click="openModal(category)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-link btn-sm text-danger p-0" title="Eliminar" @click="deleteCategory(category)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Subcategories -->
                                <div v-if="category.children && category.children.length > 0" class="mt-3 ms-5 border-start border-2 ps-4 py-1">
                                    <div v-for="child in category.children" :key="child.id" class="d-flex justify-content-between align-items-center py-1 group">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle" :style="{ width: '8px', height: '8px', backgroundColor: child.color || category.color || '#6c757d' }"></div>
                                            <span class="small" :class="child.is_active ? 'text-dark' : 'text-muted opacity-50'">{{ child.name }}</span>
                                        </div>
                                        <div class="d-flex gap-2 opacity-0-hover">
                                            <button class="btn btn-link btn-sm text-primary p-0 p-lg-1" @click="openModal(child)">
                                                <i class="bi bi-pencil small"></i>
                                            </button>
                                            <button class="btn btn-link btn-sm text-danger p-0 p-lg-1" @click="deleteCategory(child)">
                                                <i class="bi bi-trash small"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Modal -->
        <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true" ref="categoryModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-bottom-0 pt-4 px-4">
                        <h5 class="modal-title fw-bold">{{ editingCategory ? 'Editar Categoría' : 'Nueva Categoría' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 pb-4">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted uppercase">Nombre</label>
                                <input v-model="form.name" type="text" class="form-control" placeholder="Ej: Supermercado, Alquiler..." required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted uppercase">Categoría Padre (Opcional)</label>
                                <select v-model="form.parent_id" class="form-select">
                                    <option :value="null">Ninguna (Categoría Principal)</option>
                                    <option v-for="parent in parentOptions" :key="parent.id" :value="parent.id">
                                        {{ parent.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label small fw-bold text-muted uppercase">Color</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <input v-model="form.color" type="color" class="form-control form-control-color border-0 p-0" style="width: 40px; height: 40px;">
                                        <span class="small font-monospace">{{ form.color }}</span>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label small fw-bold text-muted uppercase">Icono / Emoji</label>
                                    <input v-model="form.icon" type="text" class="form-control text-center" placeholder="Icono/Emoji" maxlength="2">
                                </div>
                            </div>

                            <div class="mb-4 d-flex align-items-center gap-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="isActiveSwitch" v-model="form.is_active">
                                    <label class="form-check-label small fw-bold" for="isActiveSwitch">Categoría Activa</label>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary fw-bold py-2" :disabled="loading">
                                    <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                                    {{ editingCategory ? 'Actualizar Categoría' : 'Guardar Categoría' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { Modal } from 'bootstrap';

export default {
    name: 'CategoriesIndex',
    props: {
        categories: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            activeTab: 'expense',
            editingCategory: null,
            loading: false,
            modalInstance: null,
            form: {
                name: '',
                type: 'expense',
                parent_id: null,
                icon: '',
                color: '#3b82f6',
                is_active: true
            }
        }
    },
    computed: {
        filteredCategories() {
            return this.categories.filter(c => c.type === this.activeTab);
        },
        parentOptions() {
            return this.categories.filter(c => 
                c.type === this.activeTab && 
                (!this.editingCategory || c.id !== this.editingCategory.id)
            );
        }
    },
    mounted() {
        this.modalInstance = new Modal(this.$refs.categoryModal);
    },
    methods: {
        openModal(category = null, parentId = null) {
            this.editingCategory = category;
            this.form.type = this.activeTab;
            
            if (category) {
                this.form = {
                    name: category.name,
                    type: category.type,
                    parent_id: category.parent_id,
                    icon: category.icon || '',
                    color: category.color || '#3b82f6',
                    is_active: !!category.is_active
                };
            } else {
                this.form = {
                    name: '',
                    type: this.activeTab,
                    parent_id: parentId,
                    icon: '',
                    color: '#3b82f6',
                    is_active: true
                };
            }
            
            this.modalInstance.show();
        },
        async submit() {
            this.loading = true;
            try {
                if (this.editingCategory) {
                    await axios.put(`/categories/${this.editingCategory.id}`, this.form);
                } else {
                    await axios.post('/categories', this.form);
                }
                window.location.reload();
            } catch (e) {
                console.error(e);
                alert('Error al guardar la categoría.');
            } finally {
                this.loading = false;
            }
        },
        async deleteCategory(category) {
            if (confirm('¿Estás seguro de que deseas eliminar esta categoría?')) {
                try {
                    await axios.delete(`/categories/${category.id}`);
                    window.location.reload();
                } catch (e) {
                    console.error(e);
                    alert('Error al eliminar la categoría.');
                }
            }
        }
    }
}
</script>

<script setup>
// Nota: En Vue 2 no usamos script setup con export default, 
// pero mantengo la estructura si fuera Vue 3. 
// Para Vue 2 esto iría en el script normal.
</script>

<style scoped>
.transition-hover:hover {
    background-color: #f8f9fa !important;
}
.opacity-0-hover {
    opacity: 0;
    transition: opacity 0.2s ease;
}
.card:hover .opacity-0-hover {
    opacity: 1;
}
.group:hover .opacity-0-hover {
    opacity: 1;
}
.font-monospace {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}
.uppercase { text-transform: uppercase; }
.italic { font-style: italic; }
</style>
