<script setup>
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { formatCurrency } from '@/Utils/formatting';
import { usePrivacy } from '@/Composables/usePrivacy';

const { isPrivacyMode } = usePrivacy();

const props = defineProps({
    show: Boolean,
    portfolios: Array,
});

const emit = defineEmits(['close']);

const activeTab = ref('general'); // 'general', 'accounts'
const selectedCurrency = ref('EUR'); // Mock for now

const deletePortfolio = (portfolio) => {
    if (confirm(`¿Estás seguro de que deseas eliminar la cartera "${portfolio.name}"? Esta acción no se puede deshacer y eliminará todas sus transacciones asociadas.`)) {
        router.delete(route('portfolios.destroy', portfolio.id), {
            preserveScroll: true,
            onSuccess: () => {
                // If we were on this portfolio, we might need to redirect, but standard Inertia handling should work
            }
        });
    }
};

const close = () => {
    emit('close');
};
</script>

<template>
    <Modal :show="show" @close="close">
        <div class="flex h-[500px] bg-white dark:bg-slate-800">
            <!-- Sidebar -->
            <div class="w-1/4 bg-slate-50 dark:bg-slate-900 border-r border-slate-100 dark:border-slate-700 p-4 space-y-2">
                <h3 class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-4">Ajustes</h3>
                <button 
                    @click="activeTab = 'general'"
                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                    :class="activeTab === 'general' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
                >
                    General
                </button>
                <button 
                    @click="activeTab = 'accounts'"
                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                    :class="activeTab === 'accounts' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
                >
                    Cuentas y Carteras
                </button>
            </div>

            <!-- Content -->
            <div class="w-3/4 p-6 overflow-y-auto">
                <div v-if="activeTab === 'general'">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-6">Configuración General</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Moneda Principal</label>
                            <select v-model="selectedCurrency" class="w-full border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="EUR">Euro (€)</option>
                                <option value="USD">Dólar Estadounidense ($)</option>
                                <option value="GBP">Libra Esterlina (£)</option>
                            </select>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Esta moneda se usará para mostrar todos los valores agregados.</p>
                        </div>
                    </div>
                </div>

                <div v-if="activeTab === 'accounts'">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-6">Mis Carteras</h2>
                    
                    <div class="space-y-3">
                        <div v-for="portfolio in portfolios" :key="portfolio.id" class="flex justify-between items-center p-3 bg-white dark:bg-slate-700/50 border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm hover:border-blue-300 dark:hover:border-blue-500 transition-colors">
                            <div>
                                <h4 class="font-medium text-slate-800 dark:text-slate-200">{{ portfolio.name }}</h4>
                                <div class="flex items-center space-x-2 text-xs text-slate-500 dark:text-slate-400">
                                    <span>{{ portfolio.assets_count || 0 }} activos</span>
                                    <span>•</span>
                                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ isPrivacyMode ? '****' : formatCurrency(portfolio.total_value || 0) }}</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <DangerButton @click="deletePortfolio(portfolio)" class="px-3 py-1 text-xs">
                                    Eliminar
                                </DangerButton>
                            </div>
                        </div>
                        <div v-if="portfolios.length === 0" class="text-center py-8 text-slate-500 dark:text-slate-400 italic">
                            No tienes carteras creadas.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Modal>
</template>
