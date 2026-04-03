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
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-3xl border border-slate-100 dark:border-slate-700 p-8 lg:p-16">
                    <!-- Header -->
                    <div class="mb-12 border-b border-slate-100 dark:border-slate-700 pb-8 text-center">
                        <span class="text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase tracking-[0.3em] mb-4 block">Contrato de Servicio</span>
                        <h1 class="text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-none">Términos y Condiciones</h1>
                        <p class="mt-4 text-slate-500 dark:text-slate-400 text-sm italic font-medium">Acuerdo legal para la utilización de la plataforma fintechPro</p>
                    </div>

                    <!-- Content -->
                    <div class="prose prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-400 space-y-12">
                        <!-- Section 1 -->
                        <section class="relative">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-600 text-white text-xs font-black">01</span>
                                <h2 class="text-xl font-black text-slate-800 dark:text-slate-100 m-0 uppercase tracking-tight">Introducción</h2>
                                <InfoTooltip text="Este documento es un contrato vinculante entre tú y fintechPro." />
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-900/50 p-6 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <p class="text-sm leading-relaxed m-0 font-medium">
                                    Bienvenido a <span class="text-blue-600 dark:text-blue-400 font-bold">fintechPro</span>. Al acceder y utilizar nuestra plataforma de inteligencia financiera y comunidad, el usuario declara haber leído, entendido y aceptado íntegramente los presentes términos. fintechPro se reserva el derecho de modificar estas condiciones para adaptarse a novedades legislativas o mejoras técnicas.
                                </p>
                            </div>
                        </section>

                        <!-- Section 2 -->
                        <section>
                            <div class="flex items-center gap-3 mb-4">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-black">02</span>
                                <h2 class="text-xl font-black text-slate-800 dark:text-slate-100 m-0 uppercase tracking-tight">Utilización del Servicio</h2>
                            </div>
                            <p class="text-sm leading-relaxed mb-6">
                                fintechPro proporciona herramientas de análisis de activos, carteras y comunidad social financiera. El usuario se compromete a:
                            </p>
                            <ul class="grid grid-cols-1 md:grid-cols-2 gap-4 list-none p-0">
                                <li class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-100 dark:border-slate-700 flex gap-3 items-start">
                                    <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                    <span class="text-xs font-bold">Uso estrictamente lícito y ético de las herramientas.</span>
                                </li>
                                <li class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-100 dark:border-slate-700 flex gap-3 items-start">
                                    <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                    <span class="text-xs font-bold">Protección y confidencialidad de sus credenciales.</span>
                                </li>
                                <li class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-100 dark:border-slate-700 flex gap-3 items-start">
                                    <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                    <span class="text-xs font-bold">Veracidad en la información y reportes financieros.</span>
                                </li>
                                <li class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-100 dark:border-slate-700 flex gap-3 items-start">
                                    <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                    <span class="text-xs font-bold">Respeto a las normas de convivencia comunitaria.</span>
                                </li>
                            </ul>
                        </section>

                        <!-- Section 3 -->
                        <section>
                            <div class="flex items-center gap-3 mb-4">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-black">03</span>
                                <h2 class="text-xl font-black text-slate-800 dark:text-slate-100 m-0 uppercase tracking-tight">Propiedad Intelectual</h2>
                            </div>
                            <p class="text-sm leading-relaxed">
                                Todo el diseño, micro-interacciones, arquitectura de datos y algoritmos de IA de fintechPro son propiedad intelectual exclusiva de sus creadores. Se prohíbe cualquier intento de ingeniería inversa, copia o distribución no autorizada del código base o diseño visual.
                            </p>
                        </section>

                        <!-- Section 4 -->
                        <section class="bg-amber-50/50 dark:bg-amber-900/10 p-8 rounded-3xl border border-amber-100 dark:border-amber-900/30">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-amber-500 text-white text-xs font-black">04</span>
                                <h2 class="text-xl font-black text-amber-800 dark:text-amber-200 m-0 uppercase tracking-tight">Descargo de Responsabilidad</h2>
                            </div>
                            <p class="text-sm leading-relaxed font-bold italic text-amber-900/70 dark:text-amber-400">
                                IMPORTANTE: fintechPro no proporciona asesoramiento financiero regulado. Los análisis generados por la IA y la comunidad deben utilizarse únicamente con fines informativos y educativos. Todas las decisiones de inversión son responsabilidad exclusiva del usuario.
                            </p>
                        </section>

                        <!-- Footer legal page -->
                        <div class="mt-16 pt-8 border-t border-slate-100 dark:border-slate-800 text-center">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                Última revisión: 03 de Abril de 2026 &bull; fintechPro Legal Compliance
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
