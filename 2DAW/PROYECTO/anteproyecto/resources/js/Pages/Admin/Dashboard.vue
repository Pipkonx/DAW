<script setup>
/**
 * Admin Dashboard - Panel de Control Principal de Administración.
 * 
 * Este componente orquesta toda la telemetría, gestión de usuarios, 
 * copias de seguridad y mantenimiento del sistema. Se ha modularizado 
 * siguiendo principios de arquitectura limpia (Senior) para facilitar 
 * su escalabilidad y entendimiento por otros desarrolladores.
 */
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

// Componentes Modulares (Partials)
import AdminStats from './Partials/AdminStats.vue';
import MaintenanceActions from './Partials/MaintenanceActions.vue';
import SystemLogsViewer from './Partials/SystemLogsViewer.vue';
import UserManagementTable from './Partials/UserManagementTable.vue';
import GlobalActivityTable from './Partials/GlobalActivityTable.vue';
import BackupManager from './Partials/BackupManager.vue';
import ReportsTable from './Partials/ReportsTable.vue';
import ApiMonitorSection from './Partials/ApiMonitorSection.vue';
import SubscriptionModal from './Partials/SubscriptionModal.vue';

// Definición de Props provenientes del Backend (Inertia)
const props = defineProps({
    stats: Object,
    users: Array,
    backups: Array,
    global_activity: Array,
    reports: Array,
    api_health: Object, // Salud de APIs (booleano)
    api_consumption: Object, // Datos de consumo detallados
});

// Estados Reactivos del Dashboard
const lastLogs = ref('');
const loadingLogs = ref(false);
const activeUserForSub = ref(null);

/**
 * Obtiene las últimas líneas del log de Laravel.
 */
const fetchLogs = async () => {
    loadingLogs.value = true;
    try {
        const response = await axios.get(route('admin.system.logs'));
        lastLogs.value = response.data.logs;
    } catch (error) {
        console.error('Error al obtener logs:', error);
        lastLogs.value = "Error al recuperar los logs del sistema.";
    } finally {
        loadingLogs.value = false;
    }
};

/**
 * Tareas de mantenimiento rápido.
 */
const clearCache = () => router.post(route('admin.system.clear-cache'), {}, { preserveScroll: true });
const optimizeDb = () => router.post(route('admin.system.optimize'), {}, { preserveScroll: true });

/**
 * Gestión del Modal de Suscripción.
 */
const openSubModal = (user) => {
    activeUserForSub.value = user;
};

const closeSubModal = () => {
    activeUserForSub.value = null;
};
</script>

<template>
    <Head title="Panel de Administración" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="font-black text-2xl text-slate-800 dark:text-white leading-tight">
                        Centro de Mando Administrativo
                    </h2>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">Gestión Centralizada del Ecosistema</p>
                </div>
                
                <!-- Estado del Servidor (Discreto en el Header) -->
                <div class="flex items-center gap-6 px-6 py-3 bg-slate-100 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                        <span class="text-[10px] font-black uppercase tracking-tighter text-slate-500 dark:text-slate-400">DB: OK</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                        <span class="text-[10px] font-black uppercase tracking-tighter text-slate-500 dark:text-slate-400">FS: OK</span>
                    </div>
                    <div class="w-px h-4 bg-slate-200 dark:bg-slate-700"></div>
                    <span class="text-[10px] font-mono font-bold text-indigo-500">v2.4.0-stable</span>
                </div>
            </div>
        </template>

        <div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-screen transition-colors duration-500">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                
                <!-- Fila Superior: KPIs Críticos y Panel de Control de Mantenimiento -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 items-stretch">
                    <AdminStats :stats="stats" />
                    <MaintenanceActions 
                        :loading-logs="loadingLogs"
                        @clear-cache="clearCache"
                        @optimize-db="optimizeDb"
                        @fetch-logs="fetchLogs"
                    />
                </div>

                <!-- Visor de Logs del Sistema (Condicional) -->
                <SystemLogsViewer 
                    v-if="lastLogs" 
                    :logs="lastLogs" 
                    @close="lastLogs = ''" 
                />

                <!-- Monitorización Técnica (Ancho Completo para mayor legibilidad) -->
                <section>
                    <ApiMonitorSection :api_consumption="api_consumption" />
                </section>

                <!-- Moderación de Comunidad (Ancho Completo) -->
                <section>
                    <ReportsTable :reports="reports" />
                </section>

                <!-- Actividad Global del Sistema (Ancho Completo) -->
                <section>
                    <GlobalActivityTable :global_activity="global_activity" />
                </section>

                <!-- Copias de Seguridad (Ancho Completo) -->
                <section>
                    <BackupManager :backups="backups" />
                </section>

                <!-- Gestión de Usuarios (Ancho Completo) -->
                <section>
                    <UserManagementTable 
                        :users="users" 
                        @open-sub-modal="openSubModal"
                    />
                </section>
            </div>
        </div>

        <!-- Modal de Suscripción (Modularizado) -->
        <SubscriptionModal 
            v-if="activeUserForSub" 
            :user="activeUserForSub"
            @close="closeSubModal"
            @success="closeSubModal"
        />
    </AuthenticatedLayout>
</template>

<style scoped>
/* Optimizaciones de scrollbars suaves para campos de texto largos */
.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.scrollbar-thin::-webkit-scrollbar-track {
    background: transparent;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
    background: #475569;
    border-radius: 10px;
}
</style>
