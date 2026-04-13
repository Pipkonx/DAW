<script setup>
/**
 * UserManagementTable - Componente de tabla para la gestión de usuarios.
 * 
 * Muestra la lista de usuarios con sus planes y roles, permitiendo 
 * acciones rápidas como borrar, cambiar rol o abrir el modal de suscripción.
 */
import { useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    users: Array,
});

const emit = defineEmits(['openSubModal']);

const form = useForm({});

// Estados de filtrado
const searchQuery = ref('');
const filterRole = ref('all');
const filterPlan = ref('all');

// Estados de confirmación personalizada (NUEVO)
const userInFocus = ref(null);
const confirmingDeletion = ref(false);
const confirmingRoleChange = ref(false);

/**
 * Lógica de filtrado reactivo.
 */
const filteredUsers = computed(() => {
    return props.users.filter(user => {
        const matchesSearch = !searchQuery.value || 
            user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
            user.email.toLowerCase().includes(searchQuery.value.toLowerCase());
        
        const matchesRole = filterRole.value === 'all' || 
            (filterRole.value === 'admin' ? user.is_admin : !user.is_admin);
            
        const matchesPlan = filterPlan.value === 'all' || user.tier === filterPlan.value;
        
        return matchesSearch && matchesRole && matchesPlan;
    });
});

/**
 * Inicia el proceso de cambio de rol (Abre modal).
 */
const initiateRoleChange = (user) => {
    userInFocus.value = user;
    confirmingRoleChange.value = true;
};

const executeRoleChange = () => {
    form.post(route('admin.users.toggle-admin', userInFocus.value.id), { 
        preserveScroll: true,
        onFinish: () => { confirmingRoleChange.value = false; userInFocus.value = null; }
    });
};

/**
 * Inicia el proceso de eliminación (Abre modal).
 */
const initiateDeletion = (user) => {
    userInFocus.value = user;
    confirmingDeletion.value = true;
};

const executeDeletion = () => {
    form.delete(route('admin.users.delete', userInFocus.value.id), { 
        preserveScroll: true,
        onFinish: () => { confirmingDeletion.value = false; userInFocus.value = null; }
    });
};

/**
 * Emite el evento para abrir el modal de suscripción gestionado por el padre.
 */
const openSubModal = (user) => {
    emit('openSubModal', user);
};
</script>

<template>
    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl overflow-hidden border border-slate-100 dark:border-slate-700 mt-8 relative">
        <div class="p-8 border-b border-slate-50 dark:border-slate-700 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-1">Gestión de Usuarios</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm italic">Control de accesos, planes y roles.</p>
            </div>
            
            <!-- Barra de Herramientas de Filtrado -->
            <div class="flex flex-wrap items-center gap-4">
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input 
                        v-model="searchQuery"
                        type="text" 
                        placeholder="Buscar usuario o email..."
                        class="pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500 text-slate-700 dark:text-slate-200 transition-all w-full md:w-64"
                    >
                </div>

                <select 
                    v-model="filterRole"
                    class="bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-xs font-bold text-slate-500 uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 py-2.5 px-4"
                >
                    <option value="all">Roles</option>
                    <option value="admin">Administradores</option>
                    <option value="user">Usuarios</option>
                </select>

                <select 
                    v-model="filterPlan"
                    class="bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-xs font-bold text-slate-500 uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 py-2.5 px-4"
                >
                    <option value="all">Planes</option>
                    <option value="premium">Premium</option>
                    <option value="pro">Pro</option>
                    <option value="basic">Basic</option>
                    <option value="none">Sin Plan</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400">Usuario</th>
                        <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400">Email</th>
                        <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400">Plan</th>
                        <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400 text-center">Rol</th>
                        <th class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700 text-sm">
                    <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-8 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs" :class="{'ring-2 ring-purple-500': user.tier === 'premium', 'ring-2 ring-indigo-500': user.tier === 'pro', 'ring-2 ring-blue-400': user.tier === 'basic'}">
                                    {{ user.name.substring(0,2).toUpperCase() }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 dark:text-white">{{ user.name }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest" v-if="user.tier !== 'none'">{{ user.subscription_status }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-4 text-slate-500 dark:text-slate-400">{{ user.email }}</td>
                        <td class="px-8 py-4">
                            <span v-if="user.tier === 'premium'" class="px-3 py-1 bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 text-[10px] font-black uppercase rounded-full">Premium</span>
                            <span v-else-if="user.tier === 'pro'" class="px-3 py-1 bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 text-[10px] font-black uppercase rounded-full">Pro</span>
                            <span v-else-if="user.tier === 'basic'" class="px-3 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 text-[10px] font-black uppercase rounded-full">Basic</span>
                            <span v-else class="px-3 py-1 bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 text-[10px] font-black uppercase rounded-full">No</span>
                        </td>
                        <td class="px-8 py-4 text-center">
                            <span v-if="user.is_admin" class="px-3 py-1 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase rounded-full">Administrador</span>
                            <span v-else class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-500 text-[10px] font-black uppercase rounded-full border border-slate-200 dark:border-slate-700">Usuario</span>
                        </td>
                        <td class="px-8 py-4 text-right flex justify-end gap-2">
                            <button 
                                @click="openSubModal(user)"
                                class="p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors"
                                title="Modificar Plan de Suscripción"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                </svg>
                            </button>
                            <button 
                                @click="initiateRoleChange(user)"
                                class="p-2 text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors"
                                title="Cambiar Rol"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            </button>
                            <button 
                                @click="initiateDeletion(user)"
                                class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-colors"
                                title="Eliminar Usuario"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6 4.125 2.25 2.25m0 0 2.25 2.25m-2.25-2.25-2.25 2.25m2.25-2.25-2.25-2.25M3.75 7.5l.625-10.632A2.25 2.25 0 0 1 6.622 3h10.756a2.25 2.25 0 0 1 2.247 2.118L20.25 7.5" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- MODAL DE CONFIRMACIÓN DE ELIMINACIÓN -->
        <div v-if="confirmingDeletion" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-200">
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 max-w-md w-full shadow-2xl border border-rose-100 dark:border-rose-900/30 animate-in zoom-in-95 duration-200">
                <div class="w-16 h-16 bg-rose-100 dark:bg-rose-900/40 rounded-3xl flex items-center justify-center text-rose-600 mb-6 mx-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white text-center mb-2 uppercase">¡Zona de Riesgo!</h3>
                <p class="text-slate-600 dark:text-slate-400 text-center text-sm mb-8">
                    ¿Estás seguro de que deseas eliminar permanentemente a <span class="font-bold text-slate-900 dark:text-white">{{ userInFocus?.name }}</span>? 
                    Esta acción destruirá todos sus datos, carteras y transacciones sin posibilidad de retorno.
                </p>
                <div class="flex gap-4">
                    <button @click="confirmingDeletion = false" class="flex-1 py-3 text-sm font-black text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-2xl transition-all">Cancelar</button>
                    <button @click="executeDeletion" class="flex-1 py-3 text-sm font-black text-white bg-rose-600 hover:bg-rose-700 rounded-2xl shadow-lg shadow-rose-500/30 transition-all active:scale-95">
                        Sí, Borrar Todo
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL DE CAMBIO DE ROL -->
        <div v-if="confirmingRoleChange" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-200">
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 max-w-md w-full shadow-2xl border border-indigo-100 dark:border-indigo-900/30 animate-in zoom-in-95 duration-200">
                <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900/40 rounded-3xl flex items-center justify-center text-indigo-600 mb-6 mx-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white text-center mb-2 uppercase">Jerarquía de Acceso</h3>
                <p class="text-slate-600 dark:text-slate-400 text-center text-sm mb-8">
                    ¿Quieres cambiar el rol de <span class="font-bold text-slate-900 dark:text-white">{{ userInFocus?.name }}</span> a 
                    <span class="font-black text-indigo-500 uppercase">{{ userInFocus?.is_admin ? 'Usuario Estándar' : 'Administrador Maestro' }}</span>?
                </p>
                <div class="flex gap-4">
                    <button @click="confirmingRoleChange = false" class="flex-1 py-3 text-sm font-black text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-2xl transition-all">No, Volver</button>
                    <button @click="executeRoleChange" class="flex-1 py-3 text-sm font-black text-white bg-indigo-600 hover:bg-indigo-700 rounded-2xl shadow-lg shadow-indigo-500/30 transition-all active:scale-95">
                        Confirmar Cambio
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
