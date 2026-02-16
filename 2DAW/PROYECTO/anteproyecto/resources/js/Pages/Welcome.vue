<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { ref, onMounted } from 'vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

const isDark = ref(false);

const toggleTheme = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

onMounted(() => {
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    }
});
</script>

<template>
    <Head title="Bienvenido al Futuro Financiero" />
    
    <MainLayout :can-login="canLogin" :can-register="canRegister">
        <!-- Hero Section -->
        <section class="max-w-7xl mx-auto px-6 lg:px-12 py-16 lg:py-24">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Text Content -->
                <div class="space-y-8">
                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-sm font-medium border border-blue-100 dark:border-blue-800">
                        <span class="mr-2">🚀</span> Nueva Generación de Finanzas
                    </div>
                    
                    <h1 class="text-5xl lg:text-7xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-[1.1]">
                        Tu dinero, <br />
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-teal-500 dark:from-blue-400 dark:to-teal-400">
                            inteligente.
                        </span>
                    </h1>
                    
                    <p class="text-lg text-slate-600 dark:text-slate-300 max-w-lg leading-relaxed">
                        Controla tus finanzas, visualiza tus inversiones y alcanza tus objetivos con nuestra plataforma todo en uno. Simple, segura y poderosa.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="inline-flex justify-center items-center px-8 py-4 text-base font-semibold text-white bg-blue-600 rounded-xl shadow-xl shadow-blue-500/20 hover:bg-blue-700 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 dark:bg-blue-600 dark:hover:bg-blue-500"
                        >
                            Empezar Ahora
                        </Link>
                        <a href="#features" class="inline-flex justify-center items-center px-8 py-4 text-base font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-300">
                            Ver Características
                        </a>
                    </div>
                    
                    <div class="pt-8 flex items-center gap-4 text-sm text-slate-500 dark:text-slate-400">
                        <div class="flex -space-x-2">
                            <div class="w-8 h-8 rounded-full bg-gray-200 border-2 border-white dark:border-slate-900"></div>
                            <div class="w-8 h-8 rounded-full bg-gray-300 border-2 border-white dark:border-slate-900"></div>
                            <div class="w-8 h-8 rounded-full bg-gray-400 border-2 border-white dark:border-slate-900"></div>
                        </div>
                        <p>Más de 10,000 usuarios confían en nosotros</p>
                    </div>
                </div>

                <!-- Visual/Graph Placeholder -->
                <div class="relative">
                    <div class="absolute -inset-4 bg-gradient-to-r from-blue-100 to-teal-100 dark:from-blue-900/30 dark:to-teal-900/30 rounded-[2rem] blur-2xl opacity-50 -z-10"></div>
                    <div class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.1)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] border border-slate-100 dark:border-slate-700 p-8 transform rotate-2 hover:rotate-0 transition-all duration-500">
                        <!-- Mockup UI Header -->
                        <div class="flex justify-between items-center mb-8">
                            <div>
                                <div class="text-sm text-slate-400 font-medium">Balance Total</div>
                                <div class="text-3xl font-bold text-slate-900 dark:text-white">€24,500.00</div>
                            </div>
                            <div class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-3 py-1 rounded-lg text-sm font-semibold">
                                +12.5%
                            </div>
                        </div>
                        
                        <!-- Mockup Graph Area -->
                        <div class="h-64 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-slate-100 dark:border-slate-700 flex items-center justify-center relative overflow-hidden group">
                            <div class="absolute inset-0 flex items-end justify-around px-4 pb-4 gap-2">
                                <div class="w-full bg-blue-200 dark:bg-blue-800 h-[40%] rounded-t-sm group-hover:h-[50%] transition-all duration-500"></div>
                                <div class="w-full bg-blue-300 dark:bg-blue-700 h-[60%] rounded-t-sm group-hover:h-[65%] transition-all duration-500"></div>
                                <div class="w-full bg-blue-400 dark:bg-blue-600 h-[30%] rounded-t-sm group-hover:h-[45%] transition-all duration-500"></div>
                                <div class="w-full bg-blue-500 dark:bg-blue-500 h-[80%] rounded-t-sm group-hover:h-[85%] transition-all duration-500"></div>
                                <div class="w-full bg-blue-600 dark:bg-blue-400 h-[55%] rounded-t-sm group-hover:h-[70%] transition-all duration-500"></div>
                            </div>
                        </div>

                        <!-- Mockup List -->
                        <div class="mt-8 space-y-4">
                            <div class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-400">
                                        ₿
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900 dark:text-white">Bitcoin</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">BTC</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-medium text-slate-900 dark:text-white">€42,000</div>
                                    <div class="text-xs text-green-600 dark:text-green-400">+5.2%</div>
                                </div>
                            </div>
                             <div class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                        
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900 dark:text-white">Apple Inc.</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">AAPL</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-medium text-slate-900 dark:text-white">€185.50</div>
                                    <div class="text-xs text-green-600 dark:text-green-400">+1.2%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-24 bg-white dark:bg-slate-900 relative transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-6 lg:px-12">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-4">Todo lo que necesitas para crecer</h2>
                    <p class="text-slate-600 dark:text-slate-400">Herramientas profesionales simplificadas para inversores modernos.</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="p-8 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 hover:shadow-lg transition duration-300 group">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400 mb-6 group-hover:scale-110 transition-transform">
                            📊
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Análisis Avanzado</h3>
                        <p class="text-slate-600 dark:text-slate-400">Gráficos interactivos y métricas detalladas de tu portafolio en tiempo real.</p>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="p-8 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 hover:shadow-lg transition duration-300 group">
                        <div class="w-12 h-12 bg-teal-100 dark:bg-teal-900/30 rounded-xl flex items-center justify-center text-teal-600 dark:text-teal-400 mb-6 group-hover:scale-110 transition-transform">
                            🛡️
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Seguridad Total</h3>
                        <p class="text-slate-600 dark:text-slate-400">Tus datos están protegidos con encriptación de grado militar.</p>
                    </div>
                    
                    <!-- Feature 3 -->
                    <div class="p-8 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 hover:shadow-lg transition duration-300 group">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center text-purple-600 dark:text-purple-400 mb-6 group-hover:scale-110 transition-transform">
                            📱
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Multi-dispositivo</h3>
                        <p class="text-slate-600 dark:text-slate-400">Accede a tu cuenta desde cualquier lugar, en cualquier dispositivo.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="py-24 bg-gray-50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-800 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-6 lg:px-12">
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white text-center mb-16">Lo que dicen nuestros usuarios</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Testimonial 1 -->
                    <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold">MR</div>
                            <div>
                                <div class="font-bold text-slate-900 dark:text-white">María Rodríguez</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">Inversora Particular</div>
                            </div>
                        </div>
                        <p class="text-slate-600 dark:text-slate-300 italic">"Desde que uso FintechPro, tengo un control total sobre mis ahorros. La visualización de datos es simplemente increíble."</p>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-full bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center text-teal-600 dark:text-teal-400 font-bold">JP</div>
                            <div>
                                <div class="font-bold text-slate-900 dark:text-white">Javier Pérez</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">Emprendedor</div>
                            </div>
                        </div>
                        <p class="text-slate-600 dark:text-slate-300 italic">"La mejor herramienta para gestionar mis finanzas personales y las de mi pequeño negocio. Totalmente recomendada."</p>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400 font-bold">LG</div>
                            <div>
                                <div class="font-bold text-slate-900 dark:text-white">Lucía García</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">Freelance</div>
                            </div>
                        </div>
                        <p class="text-slate-600 dark:text-slate-300 italic">"Me encanta la simplicidad y el diseño. Es muy fácil de usar y me ayuda a ahorrar mes a mes sin esfuerzo."</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-24 bg-slate-900 dark:bg-slate-950 text-white relative overflow-hidden transition-colors duration-300">
            <div class="absolute inset-0 bg-blue-600 opacity-20 blur-3xl rounded-full transform -translate-y-1/2 scale-150"></div>
            <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
                <h2 class="text-4xl md:text-5xl font-bold mb-6 tracking-tight">Toma el control de tu futuro financiero hoy.</h2>
                <p class="text-lg text-slate-300 mb-10 max-w-2xl mx-auto">Únete a miles de usuarios que ya están mejorando su salud financiera con nuestra plataforma inteligente.</p>
                
                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="inline-flex justify-center items-center px-10 py-5 text-lg font-bold text-blue-900 bg-white rounded-full shadow-2xl hover:bg-blue-50 hover:scale-105 transition-all duration-300"
                >
                    Comenzar Gratis
                </Link>
            </div>
        </section>
    </MainLayout>
</template>
