<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import InfoTooltip from '@/Components/InfoTooltip.vue';
</script>

<template>
    <MainLayout>
        <Head title="Términos y Condiciones" />

        <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-xl border border-slate-100 dark:border-slate-700 p-8 lg:p-12">
                    <div class="flex items-center justify-between mb-8 border-b border-slate-100 dark:border-slate-700 pb-6">
                        <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Términos y Condiciones</h1>
                        <InfoTooltip text="Estas son las reglas del juego. Definen tus derechos y responsabilidades al usar nuestra plataforma." />
                    </div>

                    <div class="prose prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-400 space-y-8">
                        <section class="bg-slate-50 dark:bg-slate-700/50 p-6 rounded-lg border border-slate-100 dark:border-slate-600">
                            <div class="flex items-center gap-2 mb-3">
                                <h2 class="text-xl font-bold text-slate-800 dark:text-slate-200 m-0">1. Introducción</h2>
                                <InfoTooltip text="Lo básico: al usar la app, aceptas estas reglas." />
                            </div>
                            <p class="text-sm leading-relaxed">
                                Bienvenido a <strong>FintechPro</strong>. Al acceder y utilizar nuestro sitio web y servicios, aceptas cumplir y estar sujeto a los siguientes términos y condiciones.
                                Si no estás de acuerdo con alguna parte, no podrás acceder al servicio. Queremos que tu experiencia sea segura y transparente.
                            </p>
                        </section>

                        <section>
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-3 flex items-center gap-2">
                                2. Uso del Servicio
                            </h2>
                            <p class="text-sm mb-4">
                                FintechPro es una herramienta para gestionar tus finanzas personales. Te comprometes a usarla de forma legal y ética.
                            </p>
                            <ul class="list-disc pl-5 space-y-2 text-sm marker:text-blue-500">
                                <li>No usarás el servicio para actividades fraudulentas o ilegales.</li>
                                <li>No intentarás dañar o acceder sin permiso a nuestros sistemas.</li>
                                <li>Eres responsable de cuidar tu contraseña y acceso.</li>
                            </ul>
                        </section>

                        <section>
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-3">3. Propiedad Intelectual</h2>
                            <p class="text-sm">
                                El diseño, código y contenido original de FintechPro son nuestros. Tus datos financieros, por supuesto, son tuyos.
                            </p>
                        </section>

                        <section>
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-3">4. Limitación de Responsabilidad</h2>
                            <p class="text-sm">
                                Trabajamos duro para que todo funcione perfecto, pero no podemos garantizar que el servicio esté libre de errores al 100% ni nos hacemos responsables de decisiones financieras que tomes basándote en la app.
                            </p>
                        </section>

                        <section>
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-3">5. Cambios en los Términos</h2>
                            <p class="text-sm">
                                Podemos actualizar estas reglas. Si hacemos cambios importantes, te avisaremos con al menos 30 días de antelación.
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
