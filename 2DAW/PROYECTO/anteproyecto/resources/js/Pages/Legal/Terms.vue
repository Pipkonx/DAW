<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const sections = [
  { id: 'contratante', title: '§ 1 Parte contratante' },
  { id: 'ambito', title: '§ 2 Ámbito de aplicación' },
  { id: 'objeto', title: '§ 3 Objeto del contrato' },
  { id: 'celebracion', title: '§ 4 Celebración del contrato' },
  { id: 'obligaciones', title: '§ 5 Obligaciones del usuario' },
  { id: 'suscripciones', title: '§ 8 Suscripciones' },
  { id: 'desistimiento', title: '§ 14 Derecho de desistimiento' },
  { id: 'ia', title: '§ 19 Política de IA' },
];
</script>

<template>
    <Head title="Términos y Condiciones - FintechPro" />
    
    <MainLayout>
        <div class="bg-white dark:bg-slate-900 min-h-screen pt-24 pb-16">
            <div class="max-w-4xl mx-auto px-6">
                <nav class="flex mb-8 text-sm text-slate-500 dark:text-slate-400">
                    <Link :href="route('welcome')" class="hover:text-blue-600 transition">Inicio</Link>
                    <span class="mx-2">/</span>
                    <span class="text-slate-900 dark:text-white font-medium">Términos</span>
                </nav>

                <h1 class="text-4xl font-extrabold text-slate-900 dark:text-white mb-4">Términos y Condiciones</h1>
                <p class="text-slate-500 dark:text-slate-400 mb-12 italic text-sm">Estas condiciones regulan el uso de la aplicación FintechPro y del sitio web accesible a través de fintechpro.com.</p>

                <div class="grid lg:grid-cols-[250px_1fr] gap-12">
                    <!-- Sidebar Navigation -->
                    <aside class="hidden lg:block sticky top-32 h-fit text-sm">
                        <ul class="space-y-3 border-l border-slate-200 dark:border-slate-800">
                            <li v-for="section in sections" :key="section.id">
                                <a :href="'#' + section.id" class="block pl-4 py-1 text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:border-l hover:border-blue-600 -ml-px transition-all">
                                    {{ section.title }}
                                </a>
                            </li>
                        </ul>
                    </aside>

                    <!-- Content -->
                    <div class="prose prose-slate dark:prose-invert max-w-none text-slate-700 dark:text-slate-300">
                        <section id="contratante" class="mb-12">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">§ 1 Parte contratante</h2>
                            <p>
                                Sobre la base de las presentes Condiciones Generales de Contratación (AGB), se celebra un contrato entre el cliente y:
                                <strong>FintechPro Technologies S.L.</strong>, con domicilio social en Berlín, representada por el equipo directivo de FintechPro.
                            </p>
                        </section>

                        <section id="ambito" class="mb-12">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">§ 2 Ámbito de aplicación</h2>
                            <p>
                                Las presentes condiciones regulan la adquisición de servicios a través de la plataforma en línea <strong>fintechpro.com</strong>, incluidos todos los subdominios y aplicaciones móviles asociadas.
                            </p>
                        </section>

                        <section id="objeto" class="mb-12">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">§ 3 Objeto del contrato</h2>
                            <p>
                                FintechPro es una plataforma en línea en la que los clientes registrados pueden vincular carteras de valores y consultar información financiera detallada. La vinculación de carteras es opcional y los datos sirven con fines puramente ilustrativos para facilitar la consulta del patrimonio.
                            </p>
                        </section>

                        <section id="obligaciones" class="mb-12">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">§ 5 Obligaciones generales del usuario</h2>
                            <p>
                                El usuario se compromete a abstenerse de cualquier acción que pueda perjudicar la infraestructura de FintechPro. No está permitido el uso de software de terceros para la extracción automatizada de datos (scraping).
                            </p>
                        </section>

                        <section id="suscripciones" class="mb-12">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">§ 8 Suscripciones</h2>
                            <p>
                                FintechPro ofrece una versión gratuita con acceso limitado y una suscripción de pago (Membresía Pro/Premium) con acceso a todas las funciones avanzadas, incluyendo análisis detallados y alertas automatizadas.
                            </p>
                        </section>

                        <section id="desistimiento" class="mb-12">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">§ 14 Derecho de desistimiento</h2>
                            <p>
                                Los consumidores disponen de un derecho legal de desistimiento de 14 días naturales desde la celebración del contrato de membresía de pago.
                            </p>
                        </section>

                        <section id="ia" class="mb-12">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">§ 19 Política de uso razonable - Funciones de IA</h2>
                            <p>
                                El uso de las funciones basadas en inteligencia artificial de FintechPro (como el Analista IA) está sujeto a una política de uso razonable para garantizar un rendimiento óptimo para todos los usuarios.
                            </p>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped>
section {
  scroll-margin-top: 120px;
}
</style>
