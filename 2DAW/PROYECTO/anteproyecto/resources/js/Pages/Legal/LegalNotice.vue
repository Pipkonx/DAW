<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import InfoTooltip from '@/Components/InfoTooltip.vue';
</script>

<template>
    <MainLayout>
        <Head title="Aviso Legal" />

        <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-xl border border-slate-100 dark:border-slate-700 p-8 lg:p-12">
                    <div class="flex items-center justify-between mb-8 border-b border-slate-100 dark:border-slate-700 pb-6">
                        <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Aviso Legal</h1>
                        <InfoTooltip text="La información formal sobre quiénes somos." />
                    </div>

                    <div class="prose prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-400 space-y-8">
                        <section class="bg-slate-50 dark:bg-slate-700/50 p-6 rounded-lg border border-slate-100 dark:border-slate-600">
                            <div class="flex items-center gap-2 mb-3">
                                <h2 class="text-xl font-bold text-slate-800 dark:text-slate-200 m-0">1. Datos Identificativos</h2>
                                <InfoTooltip text="Quién está detrás de FintechPro." />
                            </div>
                            <p class="text-sm leading-relaxed mb-4">
                                En cumplimiento con el deber de información recogido en la normativa vigente, aquí tienes nuestros datos:
                            </p>
                            <ul class="list-none pl-0 space-y-2 text-sm">
                                <li class="flex items-start"><span class="font-semibold text-slate-800 dark:text-slate-200 w-24">Titular:</span> <span>FintechPro Inc.</span></li>
                                <li class="flex items-start"><span class="font-semibold text-slate-800 dark:text-slate-200 w-24">Domicilio:</span> <span>Calle Tecnológica 123, Madrid, España</span></li>
                                <li class="flex items-start"><span class="font-semibold text-slate-800 dark:text-slate-200 w-24">Email:</span> <span>contacto@fintechpro.com</span></li>
                                <li class="flex items-start"><span class="font-semibold text-slate-800 dark:text-slate-200 w-24">CIF:</span> <span>B-12345678</span></li>
                            </ul>
                        </section>

                        <section>
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-3 flex items-center gap-2">
                                2. Usuarios
                            </h2>
                            <p class="text-sm">
                                Al entrar aquí, te conviertes en USUARIO y aceptas nuestras Condiciones Generales. Sencillo.
                            </p>
                        </section>

                        <section>
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-3">3. Uso del Portal</h2>
                            <p class="text-sm">
                                FintechPro te da acceso a mucha información y herramientas. Úsalas con responsabilidad. No seas "ese" usuario que intenta romper cosas.
                            </p>
                        </section>

                        <section>
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-3">4. Exclusión de Garantías y Responsabilidad</h2>
                            <p class="text-sm">
                                Hacemos todo lo posible para que el sitio funcione perfecto y sin virus, pero la tecnología a veces falla. No nos hacemos responsables si algo sale mal a pesar de nuestras medidas de seguridad.
                            </p>
                        </section>

                        <section>
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-3">5. Legislación Aplicable y Jurisdicción</h2>
                            <p class="text-sm">
                                Nos regimos por la ley española. Si hay algún problema legal (esperemos que no), lo resolveremos en los juzgados de Madrid.
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
