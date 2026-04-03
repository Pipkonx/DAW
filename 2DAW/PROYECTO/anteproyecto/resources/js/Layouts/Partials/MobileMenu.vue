<script setup>
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

defineProps({
    showingNavigationDropdown: Boolean
});
</script>

<template>
    <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shadow-lg transition-all duration-300">
        <div class="space-y-1 pb-3 pt-2">
            <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">Dashboard</ResponsiveNavLink>
            <ResponsiveNavLink :href="route('expenses.index')" :active="route().current('expenses.index')">Gastos</ResponsiveNavLink>
            <ResponsiveNavLink :href="route('transactions.index')" :active="route().current('transactions.index')">Patrimonio</ResponsiveNavLink>
            <ResponsiveNavLink :href="route('financial-planning.index')" :active="route().current('financial-planning.index')">Planificación</ResponsiveNavLink>
            <ResponsiveNavLink :href="route('markets.index')" :active="route().current('markets.index')">Mercados</ResponsiveNavLink>
            <ResponsiveNavLink :href="route('ai-analyst.index')" :active="route().current('ai-analyst.index')">Analista IA</ResponsiveNavLink>
            <ResponsiveNavLink v-if="$page.props.auth.user.is_admin" :href="route('admin.dashboard')" :active="route().current('admin.dashboard*')" class="text-amber-600 dark:text-amber-400 font-bold">Administración</ResponsiveNavLink>
        </div>

        <div class="border-t border-slate-200 dark:border-slate-700 pb-1 pt-4">
            <div class="px-4">
                <div class="text-base font-medium text-slate-800 dark:text-slate-200">{{ $page.props.auth.user.name }}</div>
                <div class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $page.props.auth.user.email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <ResponsiveNavLink :href="route('profile.edit')">Perfil</ResponsiveNavLink>
                <a :href="route('anteproyecto.download')" class="block w-full ps-3 pe-4 py-2 text-base font-medium text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200" download>Anteproyecto</a>
                <ResponsiveNavLink :href="route('logout')" method="post" as="button">Cerrar Sesión</ResponsiveNavLink>
            </div>
        </div>
    </div>
</template>
