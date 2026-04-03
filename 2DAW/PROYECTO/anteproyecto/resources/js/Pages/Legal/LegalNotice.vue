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
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-3xl border border-slate-100 dark:border-slate-700 p-8 lg:p-16">
                    <!-- Header -->
                    <div class="mb-12 border-b border-slate-100 dark:border-slate-700 pb-8 text-center flex flex-col items-center">
                        <div class="w-16 h-1 bg-blue-600 mb-6 rounded-full"></div>
                        <span class="text-slate-400 dark:text-slate-500 text-[10px] font-black uppercase tracking-[0.4em] mb-4 block">Identificación Corporativa</span>
                        <h1 class="text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-none mb-2">Aviso Legal</h1>
                    </div>

                    <!-- Content -->
                    <div class="prose prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-400 space-y-12">
                        <!-- Section 1: Identificación -->
                        <section class="bg-slate-50 dark:bg-slate-900/50 p-8 rounded-3xl border border-slate-100 dark:border-slate-800 relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-4 opacity-5">
                                <svg class="w-24 h-24 text-slate-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                            </div>
                            <div class="flex items-center gap-3 mb-6">
                                <h2 class="text-xl font-black text-slate-800 dark:text-slate-100 m-0 uppercase tracking-tight">01. Datos Identificativos</h2>
                                <InfoTooltip text="Información fiscal y de contacto oficial de fintechPro." />
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-12">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1">Titular</span>
                                    <span class="text-sm font-bold text-slate-800 dark:text-slate-200">fintechPro Intelligence Platform</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1">Email Oficial</span>
                                    <span class="text-sm font-bold text-slate-800 dark:text-slate-200">legal@pipkonx.com</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1">Domicilio Fiscal</span>
                                    <span class="text-sm font-bold text-slate-800 dark:text-slate-200">Tech District, Madrid, España</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1">Registro</span>
                                    <span class="text-sm font-bold text-slate-800 dark:text-slate-200">fintechPro Dev Core Team</span>
                                </div>
                            </div>
                        </section>

                        <!-- Section 2 -->
                        <section>
                            <div class="flex items-center gap-3 mb-4">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-black">02</span>
                                <h2 class="text-xl font-black text-slate-800 dark:text-slate-100 m-0 uppercase tracking-tight">Condición de Usuario</h2>
                            </div>
                            <p class="text-sm leading-relaxed m-0 font-medium">
                                El acceso y/o uso de este portal de fintechPro atribuye la condición de USUARIO, que acepta, desde dicho acceso y/o uso, las Condiciones de Uso aquí reflejadas. Las citadas Condiciones serán de aplicación independientemente de las Condiciones Generales de Contratación que en su caso resulten de obligado cumplimiento.
                            </p>
                        </section>

                        <!-- Section 3 -->
                        <section>
                            <div class="flex items-center gap-3 mb-4">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-black">03</span>
                                <h2 class="text-xl font-black text-slate-800 dark:text-slate-100 m-0 uppercase tracking-tight">Responsabilidades</h2>
                            </div>
                            <p class="text-sm leading-relaxed m-0 font-medium">
                                fintechPro proporciona este servicio "tal cual". Si bien realizamos nuestros mejores esfuerzos para garantizar la exactitud de los datos y el tiempo de actividad de la plataforma, no garantizamos la ausencia de errores o interrupciones técnicas. El usuario asume toda responsabilidad derivada de las decisiones tomadas tras el análisis de la información aquí disponible.
                            </p>
                        </section>

                        <!-- Section 4 -->
                        <section>
                            <div class="flex items-center gap-3 mb-4">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-black">04</span>
                                <h2 class="text-xl font-black text-slate-800 dark:text-slate-100 m-0 uppercase tracking-tight">Jurisdicción</h2>
                            </div>
                            <p class="text-sm leading-relaxed m-0 font-medium italic">
                                Para la resolución de todas las controversias o cuestiones relacionadas con el presente sitio web o de las actividades en él desarrolladas, será de aplicación la legislación española, a la que se someten expresamente las partes, siendo competentes para la resolución de todos los conflictos derivados o relacionados con su uso los Juzgados y Tribunales de Madrid.
                            </p>
                        </section>

                        <!-- Footer legal page -->
                        <div class="mt-16 pt-8 border-t border-slate-100 dark:border-slate-800 text-center">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                Emitido: 03 de Abril de 2026 &bull; fintechPro Corporate Affairs
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
