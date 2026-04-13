<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const sections = [
  { id: 'identificacion', title: '1. Datos Identificativos' },
  { id: 'condicion', title: '2. Condición de Usuario' },
  { id: 'responsabilidad', title: '3. Responsabilidades' },
  { id: 'jurisdiccion', title: '4. Jurisdicción' },
];
</script>

<template>
    <Head title="Aviso Legal - FintechPro" />
    
    <MainLayout>
        <div class="bg-white dark:bg-slate-900 min-h-screen pt-24 pb-16">
            <div class="max-w-4xl mx-auto px-6">
                <nav class="flex mb-8 text-sm text-slate-500 dark:text-slate-400">
                    <Link :href="route('welcome')" class="hover:text-blue-600 transition">Inicio</Link>
                    <span class="mx-2">/</span>
                    <span class="text-slate-900 dark:text-white font-medium">Aviso Legal</span>
                </nav>

                <h1 class="text-4xl font-extrabold text-slate-900 dark:text-white mb-4">Aviso Legal</h1>
                <p class="text-slate-500 dark:text-slate-400 mb-12 italic text-sm">Información obligatoria según la Ley 34/2002 (LSSI).</p>

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
                        <section id="identificacion" class="mb-12">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">1. Datos Identificativos</h2>
                            <p>
                                En cumplimiento con el deber de información recogido en el artículo 10 de la Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, se reflejan los siguientes datos:
                            </p>
                            <ul class="list-none pl-0 space-y-2">
                                <li><strong>Titular:</strong> FintechPro Technologies S.L.</li>
                                <li><strong>Email:</strong> legal@fintechpro.com</li>
                                <li><strong>Web:</strong> fintechpro.com</li>
                            </ul>
                        </section>

                        <section id="condicion" class="mb-12">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">2. Condición de Usuario</h2>
                            <p>
                                El acceso y/o uso de este portal de <strong>FintechPro</strong> atribuye la condición de USUARIO, que acepta, desde dicho acceso y/o uso, las Condiciones de Uso aquí reflejadas.
                            </p>
                        </section>

                        <section id="responsabilidad" class="mb-12">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">3. Responsabilidades</h2>
                            <p>
                                FintechPro proporciona este servicio "tal cual". Si bien realizamos nuestros mejores esfuerzos para garantizar la exactitud de los datos y el tiempo de actividad de la plataforma, no garantizamos la ausencia de errores o interrupciones técnicas.
                            </p>
                        </section>

                        <section id="jurisdiccion" class="mb-12">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">4. Jurisdicción</h2>
                            <p>
                                Para la resolución de todas las controversias o cuestiones relacionadas con el presente sitio web será de aplicación la legislación española, a la que se someten expresamente las partes.
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
