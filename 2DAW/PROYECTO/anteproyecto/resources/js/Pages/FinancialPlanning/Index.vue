<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { usePrivacy } from '@/Composables/usePrivacy';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DoughnutChart from '@/Components/Charts/DoughnutChart.vue';

const props = defineProps({
    bankAccounts: Array,
    projections: Array,
    aggregated: Object,
    settings: Object,
});

const { isPrivacyMode } = usePrivacy();

const showModal = ref(false);
const editingAccount = ref(null);

// Chart Options Modernized
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    layout: {
        padding: 20
    },
    cutout: '75%',
    plugins: {
        legend: {
            position: 'bottom',
            labels: { 
                boxWidth: 12, 
                usePointStyle: true, 
                pointStyle: 'circle',
                color: '#64748b',
                font: { size: 12, weight: '500' },
                padding: 20
            }
        },
        tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.9)',
            padding: 12,
            cornerRadius: 10,
            displayColors: true,
            usePointStyle: true,
            callbacks: {
                label: function(context) {
                    let label = context.label || '';
                    let value = context.raw || 0;
                    let total = context.chart._metasets[context.datasetIndex].total;
                    let percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                    
                    if (label) {
                        label += ': ';
                    }
                    return ` ${label}${new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value)} (${percentage}%)`;
                }
            }
        }
    }
};

const chartData = computed(() => {
    const rawData = [
        { label: 'Líquido (Bancos)', value: props.aggregated.liquid_balance, color: '#0ea5e9' },
        { label: 'Invertido (Activos)', value: props.aggregated.invested_balance, color: '#6366f1' }
    ].sort((a, b) => b.value - a.value);

    return {
        labels: rawData.map(d => d.label),
        datasets: [{
            data: rawData.map(d => d.value),
            backgroundColor: rawData.map(d => d.color),
            borderWidth: 0,
            hoverOffset: 15,
            borderRadius: 10,
            spacing: 5
        }]
    };
});




const form = useForm({
    name: '',
    type: 'checking',
    balance: '',
    apy: '',
    currency: 'EUR',
});

const formSettings = useForm({
    investment_return_rate: props.settings.investment_return_rate,
    tax_rate: props.settings.tax_rate,
    enable_tax_projection: props.settings.enable_tax_projection,
});

const updateSettings = () => {
    formSettings.post(route('financial-planning.update-settings'), {
        preserveScroll: true,
    });
};

const openModal = (account = null) => {
    editingAccount.value = account;
    if (account) {
        form.name = account.name;
        form.type = account.type;
        form.balance = account.balance;
        form.apy = account.apy;
        form.currency = account.currency;
    } else {
        form.reset();
        form.clearErrors();
    }
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    editingAccount.value = null;
};

const submit = () => {
    if (editingAccount.value) {
        form.put(route('bank-accounts.update', editingAccount.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('bank-accounts.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteAccount = (account) => {
    if (confirm('¿Estás seguro de que quieres eliminar esta cuenta?')) {
        form.delete(route('bank-accounts.destroy', account.id));
    }
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value);
};

const formatPercent = (value) => {
    return new Intl.NumberFormat('es-ES', { style: 'percent', minimumFractionDigits: 2 }).format(value / 100);
};
</script>

<template>
    <Head title="Planificación Financiera" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                Planificación Financiera
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Resumen y Proyecciones -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Balance Actual con Gráfico (Simplificado) -->
                    <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col justify-center border border-slate-200 dark:border-slate-700 h-48">
                        <div>
                            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest text-center">Patrimonio Total</h3>
                            <p class="mt-4 text-4xl font-black text-blue-900 dark:text-white text-center">{{ isPrivacyMode ? '****' : formatCurrency(aggregated.current_balance) }}</p>
                        </div>
                    </div>

                    <!-- Proyección 1 Año -->
                    <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500 border-y border-r border-slate-200 dark:border-slate-700 dark:border-l-blue-500">
                        <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Proyección 1 Año</h3>
                        <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ isPrivacyMode ? '****' : formatCurrency(aggregated.projected_1y) }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Ganancia: <span v-if="isPrivacyMode">****</span><span v-else>+{{ formatCurrency(aggregated.projected_1y - aggregated.current_balance) }}</span>
                        </p>
                    </div>

                    <!-- Proyección 5 Años -->
                    <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500 border-y border-r border-slate-200 dark:border-slate-700 dark:border-l-indigo-500">
                        <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Proyección 5 Años</h3>
                        <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ isPrivacyMode ? '****' : formatCurrency(aggregated.projected_5y) }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Ganancia: <span v-if="isPrivacyMode">****</span><span v-else>+{{ formatCurrency(aggregated.projected_5y - aggregated.current_balance) }}</span>
                        </p>
                    </div>

                    <!-- Proyección 10 Años -->
                    <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500 border-y border-r border-slate-200 dark:border-slate-700 dark:border-l-purple-500">
                        <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Proyección 10 Años</h3>
                        <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ isPrivacyMode ? '****' : formatCurrency(aggregated.projected_10y) }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Ganancia: <span v-if="isPrivacyMode">****</span><span v-else>+{{ formatCurrency(aggregated.projected_10y - aggregated.current_balance) }}</span>
                        </p>
                    </div>
                </div>

                <!-- Configuración de Proyecciones -->
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-4">Configuración de Proyecciones</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <InputLabel for="investment_return_rate" value="Tasa de Retorno Esperado Inversiones (%)" class="dark:text-slate-300" />
                            <TextInput 
                                id="investment_return_rate" 
                                v-model="formSettings.investment_return_rate" 
                                type="number" 
                                step="0.01" 
                                min="0"
                                max="100"
                                class="mt-1 block w-full dark:bg-slate-900 dark:border-slate-700 dark:text-slate-300" 
                                @change="updateSettings"
                            />
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Rentabilidad media anual proyectada para tu cartera de inversión.</p>
                        </div>

                        <div>
                            <InputLabel for="enable_tax_projection" value="¿Calcular Impuestos sobre Rendimientos?" class="dark:text-slate-300" />
                            <div class="mt-2 flex items-center">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        v-model="formSettings.enable_tax_projection" 
                                        class="sr-only peer"
                                        @change="updateSettings"
                                    >
                                    <div class="relative w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-blue-600"></div>
                                    <span class="ms-3 text-sm font-medium text-slate-700 dark:text-slate-300">
                                        {{ formSettings.enable_tax_projection ? 'Activado' : 'Desactivado' }}
                                    </span>
                                </label>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Resta automáticamente los impuestos de las proyecciones de beneficio.</p>
                        </div>

                        <div v-if="formSettings.enable_tax_projection">
                            <InputLabel for="tax_rate" value="Tipo Impositivo / Impuestos (%)" class="dark:text-slate-300" />
                            <TextInput 
                                id="tax_rate" 
                                v-model="formSettings.tax_rate" 
                                type="number" 
                                step="0.01" 
                                min="0"
                                max="100"
                                class="mt-1 block w-full dark:bg-slate-900 dark:border-slate-700 dark:text-slate-300" 
                                @change="updateSettings"
                            />
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Porcentaje de impuestos que se aplica a tus ganancias (ej: 19% en España).</p>
                        </div>
                    </div>
                </div>

                <!-- Lista de Cuentas -->
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg border border-slate-200 dark:border-slate-700">
                    <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-slate-900 dark:text-white">Mis Cuentas y Depósitos</h3>
                        <PrimaryButton @click="openModal()">
                            Añadir Cuenta
                        </PrimaryButton>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                            <thead class="bg-slate-50 dark:bg-slate-700/50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nombre</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tipo</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Balance</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">APY %</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Interés Mensual Est.</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Acciones</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                                <tr v-for="account in projections" :key="account.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">
                                        {{ account.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                            :class="account.type === 'savings' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'">
                                            {{ account.type === 'savings' ? 'Ahorro' : 'Corriente' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-slate-900 dark:text-white font-mono">
                                        {{ isPrivacyMode ? '****' : formatCurrency(account.current_balance) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-slate-500 dark:text-slate-400">
                                        {{ account.apy }}%
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600 dark:text-green-400 font-mono">
                                        <span v-if="isPrivacyMode">****</span><span v-else>+{{ formatCurrency(account.monthly_earnings) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click="openModal(bankAccounts.find(a => a.id === account.id))" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-4">Editar</button>
                                        <button @click="deleteAccount(account)" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">Eliminar</button>
                                    </td>
                                </tr>
                                <tr v-if="projections.length === 0">
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-slate-500 dark:text-slate-400">
                                        No has añadido ninguna cuenta bancaria aún.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Crear/Editar -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6 bg-white dark:bg-slate-800">
                <h2 class="text-lg font-medium text-slate-900 dark:text-white mb-4">
                    {{ editingAccount ? 'Editar Cuenta' : 'Nueva Cuenta Bancaria' }}
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <InputLabel for="name" value="Nombre del Banco / Cuenta" class="dark:text-slate-300" />
                        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full dark:bg-slate-900 dark:border-slate-700 dark:text-slate-300" placeholder="Ej: BBVA Ahorro" />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="type" value="Tipo de Cuenta" class="dark:text-slate-300" />
                            <select id="type" v-model="form.type" class="mt-1 block w-full border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm">
                                <option value="checking">Corriente (Día a día)</option>
                                <option value="savings">Ahorro / Remunerada</option>
                            </select>
                            <InputError :message="form.errors.type" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="currency" value="Moneda" class="dark:text-slate-300" />
                            <select id="currency" v-model="form.currency" class="mt-1 block w-full border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm">
                                <option value="EUR">Euro (€)</option>
                                <option value="USD">Dólar ($)</option>
                                <option value="GBP">Libra (£)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="balance" value="Saldo Actual" class="dark:text-slate-300" />
                            <TextInput id="balance" v-model="form.balance" type="number" step="0.01" class="mt-1 block w-full dark:bg-slate-900 dark:border-slate-700 dark:text-slate-300" placeholder="0.00" />
                            <InputError :message="form.errors.balance" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="apy" value="APY / Rentabilidad Anual (%)" class="dark:text-slate-300" />
                            <TextInput id="apy" v-model="form.apy" type="number" step="0.01" class="mt-1 block w-full dark:bg-slate-900 dark:border-slate-700 dark:text-slate-300" placeholder="Ej: 2.5" />
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Si es cuenta remunerada, indica el %.</p>
                            <InputError :message="form.errors.apy" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="closeModal" class="dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 dark:border-slate-600"> Cancelar </SecondaryButton>
                    <PrimaryButton @click="submit" :disabled="form.processing">
                        {{ editingAccount ? 'Actualizar' : 'Guardar' }}
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
