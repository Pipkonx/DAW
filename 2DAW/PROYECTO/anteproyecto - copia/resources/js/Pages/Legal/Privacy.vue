<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import InfoTooltip from '@/Components/InfoTooltip.vue';
</script>

<template>
    <MainLayout>
        <Head title="Política de Privacidad" />

        <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-xl border border-slate-100 dark:border-slate-700 p-8 lg:p-12">
                    <div class="flex items-center justify-between mb-8 border-b border-slate-100 dark:border-slate-700 pb-6">
                        <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Política de Privacidad</h1>
                        <InfoTooltip text="Aquí explicamos qué hacemos con tus datos. Spoiler: cuidarlos mucho." />
                    </div>

                    <div class="prose prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-400 space-y-8">
                        <section class="bg-blue-50/50 dark:bg-blue-900/20 p-6 rounded-lg border border-blue-100 dark:border-blue-800/50">
                            <div class="flex items-center gap-2 mb-3">
                                <h2 class="text-xl font-bold text-slate-800 dark:text-slate-200 m-0">1. Recopilación de Información</h2>
                                <InfoTooltip text="Solo pedimos lo necesario para que la app funcione." />
                            </div>
                            <p class="text-sm leading-relaxed">
                                Recopilamos información personal que tú nos das voluntariamente al registrarte (nombre, email) y los datos financieros que introduces para gestionar tu patrimonio.
                                También recopilamos datos técnicos anónimos sobre cómo usas la app para mejorarla.
                            </p>
                        </section>

                        <section>
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-3 flex items-center gap-2">
                                2. Uso de la Información
                            </h2>
                            <p class="text-sm mb-4">
                                Usamos tus datos exclusivamente para:
                            </p>
                            <ul class="list-disc pl-5 space-y-2 text-sm marker:text-blue-500">
                                <li>Que puedas ver y gestionar tus finanzas.</li>
                                <li>Enviarte notificaciones importantes sobre tu cuenta.</li>
                                <li>Mejorar la seguridad y funcionalidad de la plataforma.</li>
                            </ul>
                        </section>

                        <section>
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-3">3. Seguridad de los Datos</h2>
                            <p class="text-sm">
                                Tu seguridad es nuestra prioridad. Usamos encriptación y medidas de seguridad estándar de la industria. Aunque nada en internet es 100% invulnerable, hacemos todo lo posible para protegerte.
                            </p>
                        </section>

                        <section>
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-3">4. Proveedores de Servicios</h2>
                            <p class="text-sm">
                                A veces usamos herramientas de terceros (como Google para el login o servidores de base de datos) que nos ayudan a darte el servicio. Solo compartimos lo estrictamente necesario.
                            </p>
                        </section>

                        <section>
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-3">5. Enlaces a Otros Sitios</h2>
                            <p class="text-sm">
                                Si enlazas a otros sitios desde nuestra app, revisa sus políticas de privacidad. No controlamos lo que hacen otros.
                            </p>
                        </section>

                        <div class="mt-10 pt-6 border-t border-slate-100 dark:border-slate-700 text-xs text-slate-400 text-center">
                            Última actualización: 16 de Febrero de 2026
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
