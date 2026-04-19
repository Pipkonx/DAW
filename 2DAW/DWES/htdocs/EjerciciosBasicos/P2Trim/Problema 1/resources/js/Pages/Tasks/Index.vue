<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

// Recibimos los datos desde el controlador de Laravel gracias a Inertia
const props = defineProps({
    tasks: Array,
    operators: Array
});

// Función simple para dar color según el estado
const getStatusColor = (status) => {
    if (status === 'pending') return 'text-warning';
    if (status === 'done') return 'text-success';
    return 'text-danger';
};

console.log("Cargado el componente de Vue con Vite!");
</script>

<template>
    <Head title="Tareas (Vue + Vite)" />

    <div class="py-12 bg-gray-100 min-vh-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Listado de Tareas (Componente Vue)</h2>
                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">Compilado con Vite</span>
                </div>

                <p class="mb-4 text-gray-600">
                    Este listado está renderizado usando un componente de Vue 3 (.vue) y compilado mediante Vite.
                    Los datos fluyen a través de Inertia.js sin necesidad de APIs separadas.
                </p>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operador</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Recorremos las tareas que nos mandó Laravel -->
                            <tr v-for="task in tasks" :key="task.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ task.id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ task.client ? task.client.name : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ task.description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ task.operator ? task.operator.name : 'Sin asignar' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold" :class="getStatusColor(task.status)">
                                    {{ task.status.toUpperCase() }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    <Link href="/dashboard" class="text-blue-600 hover:text-blue-800 underline">
                        &larr; Volver al Dashboard (Blade)
                    </Link>
                </div>

            </div>
        </div>
    </div>
</template>

<style scoped>
/* Estilos directos en el componente si hiciera falta */
table {
    width: 100%;
}
</style>
