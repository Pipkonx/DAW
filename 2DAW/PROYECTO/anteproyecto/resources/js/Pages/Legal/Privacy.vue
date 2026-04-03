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
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-3xl border border-slate-100 dark:border-slate-700 p-8 lg:p-16">
                    <!-- Header -->
                    <div class="mb-12 border-b border-slate-100 dark:border-slate-700 pb-8 text-center border-l-4 border-l-blue-600 pl-6 rounded-r-xl">
                        <span class="text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase tracking-[0.3em] mb-4 block">Protección de Datos</span>
                        <h1 class="text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-none text-left">Política de Privacidad</h1>
                        <p class="mt-4 text-slate-500 dark:text-slate-400 text-sm italic font-medium text-left">Cómo fintechPro custodia y gestiona su información personal y financiera</p>
                    </div>

                    <!-- Content -->
                    <div class="prose prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-400 space-y-12">
                        <!-- Section 1 -->
                        <section>
                            <div class="flex items-center gap-3 mb-6">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-600 text-white text-xs font-black">01</span>
                                <h2 class="text-xl font-black text-slate-800 dark:text-slate-100 m-0 uppercase tracking-tight">Recopilación de Información</h2>
                                <InfoTooltip text="Transparencia total: solo pedimos lo que necesitamos para darte el servicio." />
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-slate-50 dark:bg-slate-900/50 p-6 rounded-2xl border border-slate-100 dark:border-slate-800">
                                    <h3 class="text-sm font-black text-blue-600 mb-3 uppercase tracking-widest">Datos Directos</h3>
                                    <p class="text-xs leading-relaxed m-0 font-medium">
                                        Nombre, dirección de correo electrónico, credenciales de acceso y todos los registros de activos, carteras y transacciones financieras que decidas gestionar en fintechPro.
                                    </p>
                                </div>
                                <div class="bg-slate-50 dark:bg-slate-900/50 p-6 rounded-2xl border border-slate-100 dark:border-slate-800">
                                    <h3 class="text-sm font-black text-blue-600 mb-3 uppercase tracking-widest">Datos Técnicos</h3>
                                    <p class="text-xs leading-relaxed m-0 font-medium">
                                        Dirección IP, tipo de navegador, sistema operativo y patrones de uso anónimos recopilados exclusivamente para la optimización técnica de la plataforma.
                                    </p>
                                </div>
                            </div>
                        </section>

                        <!-- Section 2 -->
                        <section>
                            <div class="flex items-center gap-3 mb-4">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-black">02</span>
                                <h2 class="text-xl font-black text-slate-800 dark:text-slate-100 m-0 uppercase tracking-tight">Finalidad del Tratamiento</h2>
                            </div>
                            <p class="text-sm leading-relaxed mb-4">
                                Sus datos financieros son la columna vertebral de la experiencia fintechPro. Los utilizamos exclusivamente para:
                            </p>
                            <ul class="list-none p-0 space-y-3">
                                <li class="bg-blue-50/30 dark:bg-blue-900/10 p-4 rounded-xl border-l-4 border-blue-500 flex gap-4 items-center">
                                    <svg class="h-4 w-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" /></svg>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide">Cálculo de rendimientos y reportes IA personalizados.</span>
                                </li>
                                <li class="bg-blue-50/30 dark:bg-blue-900/10 p-4 rounded-xl border-l-4 border-blue-500 flex gap-4 items-center">
                                    <svg class="h-4 w-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" /></svg>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide">Gestión de la comunidad social y visibilidad de perfiles.</span>
                                </li>
                                <li class="bg-blue-50/30 dark:bg-blue-900/10 p-4 rounded-xl border-l-4 border-blue-500 flex gap-4 items-center">
                                    <svg class="h-4 w-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" /></svg>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide">Protocolos de seguridad y prevención de fraudes.</span>
                                </li>
                            </ul>
                        </section>

                        <!-- Section 3 -->
                        <section class="bg-slate-900 text-white p-10 rounded-3xl shadow-xl dark:border dark:border-slate-700">
                            <div class="flex items-center gap-3 mb-6">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-500 text-white text-xs font-black">03</span>
                                <h2 class="text-xl font-black text-white m-0 uppercase tracking-tight">Estándares de Seguridad</h2>
                            </div>
                            <p class="text-sm leading-relaxed opacity-80 mb-6 m-0 font-medium italic">
                                Implementamos medidas de encriptación y segregación de datos para garantizar que su patrimonio sea solo para su conocimiento. fintechPro nunca venderá sus datos financieros a terceros. Su confianza es nuestro activo más valioso.
                            </p>
                        </section>

                        <!-- Section 4 -->
                        <section>
                            <div class="flex items-center gap-3 mb-4">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-black">04</span>
                                <h2 class="text-xl font-black text-slate-800 dark:text-slate-100 m-0 uppercase tracking-tight">Derechos ARCO</h2>
                            </div>
                            <p class="text-sm leading-relaxed">
                                El usuario tiene pleno derecho a Acceder, Rectificar, Cancelar u Oponerse al tratamiento de sus datos. Puede ejercer estos derechos en cualquier momento eliminando su cuenta o contactándonos a través del canal de soporte oficial.
                            </p>
                        </section>

                        <!-- Footer legal page -->
                        <div class="mt-16 pt-8 border-t border-slate-100 dark:border-slate-800 text-center">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                Última revisión: 03 de Abril de 2026 &bull; fintechPro Privacy Compliance Team
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
