<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
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
});

const showModal = ref(false);
const editingAccount = ref(null);

// Chart Options
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '70%',
    plugins: {
        legend: {
            position: 'right',
            labels: { boxWidth: 12, usePointStyle: true, color: '#94a3b8' }
        },
        tooltip: {
            callbacks: {
                label: function(context) {
                    let label = context.label || '';
                    let value = context.raw || 0;
                    let total = context.chart._metasets[context.datasetIndex].total;
                    let percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                    
                    if (label) {
                        label += ': ';
                    }
                    return label + new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value) + ' (' + percentage + '%)';
                }
            }
        }
    }
};

const chartData = computed(() => ({
    labels: ['Líquido (Bancos)', 'Invertido (Activos)'],
    datasets: [{
        data: [props.aggregated.liquid_balance, props.aggregated.invested_balance],
        backgroundColor: ['#3b82f6', '#8b5cf6'],
        borderWidth: 0,
        hoverOffset: 4
    }]
}));

const form = useForm({
    name: '',
    type: 'checking',
    balance: '',
    apy: '',
    currency: 'EUR',
});

const formSettings = useForm({
    investment_return_rate: props.aggregated.investment_return_rate,
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
                    <!-- Balance Actual con Gráfico -->
                    <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col justify-between border border-slate-200 dark:border-slate-700">
                        <div>
                            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Patrimonio Total</h3>
                            <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ formatCurrency(aggregated.current_balance) }}</p>
                        </div>
                        <div class="h-32 relative mt-4">
                             <DoughnutChart :data="chartData" :options="chartOptions" />
                        </div>
                    </div>

                    <!-- Proyección 1 Año -->
                    <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500 border-y border-r border-slate-200 dark:border-slate-700 dark:border-l-blue-500">
                        <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Proyección 1 Año</h3>
                        <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ formatCurrency(aggregated.projected_1y) }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Ganancia: +{{ formatCurrency(aggregated.projected_1y - aggregated.current_balance) }}
                        </p>
                    </div>

                    <!-- Proyección 5 Años -->
                    <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500 border-y border-r border-slate-200 dark:border-slate-700 dark:border-l-indigo-500">
                        <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Proyección 5 Años</h3>
                        <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ formatCurrency(aggregated.projected_5y) }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Ganancia: +{{ formatCurrency(aggregated.projected_5y - aggregated.current_balance) }}
                        </p>
                    </div>

                    <!-- Proyección 10 Años -->
                    <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500 border-y border-r border-slate-200 dark:border-slate-700 dark:border-l-purple-500">
                        <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Proyección 10 Años</h3>
                        <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ formatCurrency(aggregated.projected_10y) }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Ganancia: +{{ formatCurrency(aggregated.projected_10y - aggregated.current_balance) }}
                        </p>
                    </div>
                </div>

                <!-- Configuración de Proyecciones -->
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-4">Configuración de Proyecciones</h3>
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
                                        {{ formatCurrency(account.current_balance) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-slate-500 dark:text-slate-400">
                                        {{ account.apy }}%
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600 dark:text-green-400 font-mono">
                                        +{{ formatCurrency(account.monthly_earnings) }}
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
