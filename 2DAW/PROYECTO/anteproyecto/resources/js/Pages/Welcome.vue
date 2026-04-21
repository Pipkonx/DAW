<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { ref } from 'vue';

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
});

const faqs = ref([
    {
        q: '¿Es seguro vincular mi banco con FintechPro?',
        a: 'Absolutamente. Utilizamos encriptación de grado bancario (AES-256) y nunca almacenamos tus credenciales. La conexión se realiza a través de proveedores regulados bajo la normativa PSD2.',
        open: false
    },
    {
        q: '¿Cómo funciona el analista de IA?',
        a: 'Nuestro Analista IA procesa de forma anónima tus transacciones para detectar patrones de ahorro, diversificación y riesgos potenciales, ofreciéndote consejos personalizados basados en datos reales.',
        open: false
    },
    {
        q: '¿Qué activos puedo rastrear?',
        a: 'Casi cualquiera: acciones, ETFs, criptomonedas, bienes inmuebles, materias primas e incluso coleccionables de lujo.',
        open: false
    },
    {
        q: '¿Es FintechPro gratuito?',
        a: 'Ofrecemos una versión gratuita muy potente. Para inversores avanzados, nuestras suscripciones Pro y Premium desbloquean análisis TTWROR, mapas de calor y soporte prioritario.',
        open: false
    }
]);

const toggleFaq = (index) => {
    faqs.value[index].open = !faqs.value[index].open;
};
</script>

<template>
    <Head title="FintechPro - Control Total de tus Inversiones" />
    
    <MainLayout :can-login="canLogin" :can-register="canRegister">
        <!-- Hero Section -->
        <section class="relative pt-20 pb-32 overflow-hidden bg-white dark:bg-slate-900 transition-colors duration-500">
            <div class="max-w-7xl mx-auto px-6 lg:px-12 grid lg:grid-cols-2 gap-16 items-center">
                <div class="z-10 text-center lg:text-left">
                    <div class="inline-flex items-center space-x-4 mb-6">
                        <span class="px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-sm font-bold animate-pulse">
                            +15 B $ Seguidos
                        </span>
                        <span class="text-slate-500 dark:text-slate-400 text-sm font-medium">
                            +500.000 usuarios
                        </span>
                    </div>
                    
                    <h1 class="text-5xl lg:text-7xl font-black text-slate-900 dark:text-white leading-[1.1] mb-8 tracking-tighter">
                        Todas sus inversiones. <br/>
                        <span class="text-blue-600">Una aplicación.</span> <br/>
                        Control total.
                    </h1>
                    
                    <p class="text-xl text-slate-600 dark:text-slate-400 mb-10 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                        Reúna todas sus inversiones en una sola aplicación y obtenga información valiosa mediante un análisis detallado. Diseñado para el inversor moderno.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <Link :href="route('register')" class="px-10 py-5 bg-blue-600 text-white rounded-2xl font-bold text-lg shadow-2xl shadow-blue-500/30 hover:bg-blue-700 hover:-translate-y-1 transition-all duration-300">
                            Empezar ahora
                        </Link>
                    </div>
                </div>

                <!-- Mockup Composition -->
                <div class="relative lg:-mr-24 scale-110 lg:scale-125 pt-20 lg:pt-0">
                    <div class="relative w-full aspect-video z-20 transform -rotate-2 hover:rotate-0 transition-all duration-700">
                        <img src="/images/mockups/dashboard.png" class="rounded-3xl shadow-[0_50px_100px_rgba(0,0,0,0.15)] ring-1 ring-slate-200 dark:ring-slate-800" alt="FintechPro Dashboard">
                    </div>
                    <!-- Floating Elements -->
                    <div class="absolute -top-12 -right-8 w-1/2 z-30 transform rotate-6 hover:rotate-0 transition-all duration-700 delay-100">
                        <img src="/images/mockups/portfolio.png" class="rounded-2xl shadow-2xl ring-1 ring-white/50" alt="Portfolio Analysis">
                    </div>
                    <div class="absolute -bottom-12 -left-8 w-1/2 z-30 transform -rotate-6 hover:rotate-0 transition-all duration-700 delay-200">
                        <img src="/images/mockups/social.png" class="rounded-2xl shadow-2xl ring-1 ring-white/50" alt="Community Feed">
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured In (Logos) -->
        <section class="py-12 bg-slate-50 dark:bg-slate-900/50 border-y border-slate-100 dark:border-slate-800">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <p class="text-sm font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-8">Destacado en:</p>
                <div class="flex flex-wrap justify-center items-center gap-12 opacity-50 grayscale hover:grayscale-0 transition-all cursor-default">
                    <span class="text-2xl font-serif italic font-bold text-slate-900 dark:text-white">Bloomberg</span>
                    <span class="text-2xl font-sans font-black text-slate-900 dark:text-white uppercase tracking-tighter">FINANCIAL TIMES</span>
                    <span class="text-2xl font-serif font-bold text-slate-900 dark:text-white">The Economist</span>
                    <span class="text-2xl font-sans font-bold text-slate-900 dark:text-white">Forbes</span>
                </div>
            </div>
        </section>

        <!-- Features Grid -->
        <section class="py-32 bg-white dark:bg-slate-900">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-12">
                    <!-- Feature 1: Importar -->
                    <div class="group p-8 rounded-[2rem] bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 hover:border-blue-500 transition-all cursor-default">
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 transition-transform">📥</div>
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Importar</h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Añada y realice el seguimiento de cualquier activo, incluidas acciones, ETFs, fondos, Inversiones DeFi, bienes inmuebles y más.</p>
                        <Link href="/register" class="mt-8 inline-flex font-bold text-blue-600 items-center hover:gap-2 transition-all">Empezar ahora <span class="ml-1">→</span></Link>
                    </div>

                    <!-- Feature 2: Agregación -->
                    <div class="group p-8 rounded-[2rem] bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 hover:border-blue-500 transition-all">
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 transition-transform">🌐</div>
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Agregación</h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Siga su patrimonio neto agregado en tiempo real vinculando todas sus cuentas en un único lugar.</p>
                        <Link href="/register" class="mt-8 inline-flex font-bold text-blue-600 items-center">Empezar ahora <span class="ml-1">→</span></Link>
                    </div>

                    <!-- Feature 3: Rendimiento -->
                    <div class="group p-8 rounded-[2rem] bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 hover:border-blue-500 transition-all">
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 transition-transform">📈</div>
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Rendimiento</h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">La tasa TTWROR le ofrece la visión más precisa de sus beneficios reales, eliminando distorsiones externas.</p>
                        <Link href="/register" class="mt-8 inline-flex font-bold text-blue-600 items-center">Empezar ahora <span class="ml-1">→</span></Link>
                    </div>

                    <!-- Feature 4: Dividendos -->
                    <div class="group p-8 rounded-[2rem] bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 hover:border-blue-500 transition-all">
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 transition-transform">📅</div>
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Dividendos</h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Utilice nuestro calendario para prever flujos de caja futuros y saber exactamente cuándo cobrará sus dividendos.</p>
                        <Link href="/register" class="mt-8 inline-flex font-bold text-blue-600 items-center">Empezar ahora <span class="ml-1">→</span></Link>
                    </div>

                    <!-- Feature 5: Analítica -->
                    <div class="group p-8 rounded-[2rem] bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 hover:border-blue-500 transition-all">
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 transition-transform">🔍</div>
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Analítica</h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Obtenga desgloses detallados por geografía, sector y clase de activo. Visualización inteligente de carteras.</p>
                        <Link href="/register" class="mt-8 inline-flex font-bold text-blue-600 items-center">Empezar ahora <span class="ml-1">→</span></Link>
                    </div>

                    <!-- Feature 6: Comunidad -->
                    <div class="group p-8 rounded-[2rem] bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 hover:border-blue-500 transition-all">
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 transition-transform">💬</div>
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Comunidad</h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Información pertinente adaptada a su cartera. Aprenda de otros inversores en un entorno profesional.</p>
                        <Link href="/register" class="mt-8 inline-flex font-bold text-blue-600 items-center">Empezar ahora <span class="ml-1">→</span></Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Security Section -->
        <section class="py-32 bg-slate-900 text-white overflow-hidden relative">
            <div class="absolute inset-0 bg-blue-600/10 blur-[120px] rounded-full translate-x-1/2 -translate-y-1/2"></div>
            <div class="max-w-7xl mx-auto px-6 relative z-10 grid lg:grid-cols-2 items-center gap-16">
                <div>
                    <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-3xl mb-8">🛡️</div>
                    <h2 class="text-4xl lg:text-5xl font-black mb-8">Máxima protección para sus datos</h2>
                    <p class="text-xl text-slate-400 leading-relaxed mb-8">
                        Proteger su privacidad es tan importante como asegurar sus datos. Solo recopilamos la información necesaria, garantizando el anonimato total.
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-slate-300">
                            <span class="text-blue-500 font-bold">✓</span> Cifrado AES-256 de grado militar
                        </li>
                        <li class="flex items-center gap-3 text-slate-300">
                            <span class="text-blue-500 font-bold">✓</span> Anonimización de identidad por defecto
                        </li>
                        <li class="flex items-center gap-3 text-slate-300">
                            <span class="text-blue-500 font-bold">✓</span> Infraestructura Zero-Knowledge
                        </li>
                    </ul>
                </div>
                <div class="flex justify-center">
                    <div class="w-64 h-64 bg-slate-800 rounded-full flex items-center justify-center shadow-[0_0_100px_rgba(37,99,235,0.4)] animate-bounce-slow">
                        <span class="text-8xl">🔒</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="py-32 bg-white dark:bg-slate-950 transition-colors duration-500">
            <div class="max-w-7xl mx-auto px-6">
                <h2 class="text-4xl font-black text-slate-900 dark:text-white text-center mb-24 tracking-tight">
                    Lo que los usuarios adoran de <span class="text-blue-600">FintechPro</span>
                </h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="p-8 rounded-[2rem] bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800">
                        <p class="text-lg text-slate-600 dark:text-slate-300 italic mb-8">"Fácil de usar y ofrece mucha información de un vistazo. Ha sido una herramienta excelente para ayudarme a tomar decisiones de inversión."</p>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center font-bold text-orange-600">L</div>
                            <span class="font-bold text-slate-900 dark:text-white">Lorena</span>
                        </div>
                    </div>
                    <div class="p-8 rounded-[2rem] bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800">
                        <p class="text-lg text-slate-600 dark:text-slate-300 italic mb-8">"La comunidad de inversores de FintechPro es fantástica. He aprendido muchísimo de las discusiones y he recibido feedback valioso sobre mi portafolio."</p>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center font-bold text-blue-600">F</div>
                            <span class="font-bold text-slate-900 dark:text-white">Fabzy</span>
                        </div>
                    </div>
                    <div class="p-8 rounded-[2rem] bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800">
                        <p class="text-lg text-slate-600 dark:text-slate-300 italic mb-8">"El calendario de dividendos ha hecho mucho más sencilla la gestión de mi portafolio. Lo recomendaría a cualquiera que quiera seguir sus rentas."</p>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center font-bold text-green-600">D</div>
                            <span class="font-bold text-slate-900 dark:text-white">Donkey</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQs Section -->
        <section class="py-32 bg-slate-50 dark:bg-slate-900/50">
            <div class="max-w-3xl mx-auto px-6">
                <h2 class="text-4xl font-black text-slate-900 dark:text-white text-center mb-16">Preguntas frecuentes</h2>
                <div class="space-y-4">
                    <div v-for="(faq, i) in faqs" :key="i" class="border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden bg-white dark:bg-slate-800 transition-all duration-300 hover:shadow-lg">
                        <button 
                            @click="toggleFaq(i)"
                            class="w-full flex items-center justify-between p-6 text-left hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors focus:outline-none"
                        >
                            <span class="font-bold text-slate-900 dark:text-white text-lg pr-8">{{ faq.q }}</span>
                            <span class="text-2xl transition-transform duration-300" :class="{'rotate-45 text-blue-600': faq.open}">+</span>
                        </button>
                        <div 
                            class="transition-all duration-500 ease-in-out overflow-hidden"
                            :style="{ maxHeight: faq.open ? '500px' : '0' }"
                        >
                            <div class="p-6 pt-0 text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ faq.a }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="py-24 bg-blue-600 relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-blue-400 to-transparent opacity-30"></div>
            <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
                <h2 class="text-4xl md:text-6xl font-black text-white mb-8 tracking-tighter">¿Listo para dominar tus finanzas?</h2>
                <p class="text-xl text-blue-100 mb-12 max-w-2xl mx-auto">Únete a la comunidad de inversores más avanzada y toma decisiones basadas en datos reales.</p>
                <Link :href="route('register')" class="inline-flex px-12 py-6 bg-white text-blue-600 rounded-2xl font-black text-xl shadow-2xl hover:scale-105 transition-all">
                    Crear mi cuenta gratuita
                </Link>
            </div>
        </section>
    </MainLayout>
</template>

<style scoped>
.animate-bounce-slow {
  animation: bounce 3s infinite;
}

@keyframes bounce {
  0%, 100% { transform: translateY(-5%); animation-timing-function: cubic-bezier(0.8, 0, 1, 1); }
  50% { transform: translateY(0); animation-timing-function: cubic-bezier(0, 0, 0.2, 1); }
}

.animate-fade-in-down {
  animation: fadeInDown 0.3s ease-out;
}

@keyframes fadeInDown {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
