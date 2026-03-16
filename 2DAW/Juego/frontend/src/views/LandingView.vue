<template>
  <div class="min-h-screen bg-slate-950 text-white flex flex-col relative overflow-y-auto scroll-smooth">
    <!-- Animated background -->
    <div class="absolute inset-0 opacity-20 pointer-events-none">
      <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(#1e293b_1px,transparent_1px)] [background-size:32px_32px]"></div>
    </div>

    <!-- Header -->
    <header class="p-6 flex justify-between items-center relative z-10 max-w-7xl mx-auto w-full">
      <div class="flex items-center gap-3 group">
        <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20 group-hover:rotate-6 transition-transform duration-500">
          <span class="text-xl">🤖</span>
        </div>
        <span class="text-xl font-black tracking-tighter bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-400 uppercase">
          PyFarmer <span class="text-emerald-500">Pro</span>
        </span>
      </div>
      <button 
        @click="showAuth = true"
        class="px-6 py-2.5 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl font-bold transition-all hover:scale-105 active:scale-95"
      >
        Entrar al Sistema
      </button>
    </header>

    <!-- Hero Section -->
    <main class="flex-1 flex flex-col items-center justify-center p-6 relative z-10 text-center">
      <div class="max-w-3xl animate-fade-in-up">
        <h1 class="text-5xl md:text-7xl font-black mb-6 tracking-tight leading-tight">
          La Granja del <span class="text-emerald-500 italic">Futuro</span> es <span class="underline decoration-cyan-500 underline-offset-8">Programada</span>
        </h1>
        <p class="text-xl text-slate-400 mb-10 leading-relaxed max-w-2xl mx-auto">
          Domina el arte de la automatización agrícola. Escribe scripts en Python para controlar tu robot, cosecha recursos, desbloquea tecnologías y compite en el ranking global.
        </p>
        
        <div class="flex flex-wrap justify-center gap-4">
          <button 
            @click="showAuth = true"
            class="px-8 py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-2xl shadow-xl shadow-emerald-900/20 transition-all hover:-translate-y-1 active:translate-y-0 text-lg uppercase tracking-wider"
          >
            Comenzar Ahora — Es Gratis
          </button>
          <a href="#features" class="px-8 py-4 bg-white/5 hover:bg-white/10 border border-white/10 text-white font-bold rounded-2xl transition-all text-lg">
            Ver Características
          </a>
        </div>
      </div>

      <!-- Tech Preview Card -->
      <div class="mt-20 w-full max-w-5xl bg-slate-900/50 border border-white/10 rounded-3xl p-4 backdrop-blur-xl shadow-2xl relative animate-float">
        <div class="absolute -top-10 -left-10 w-40 h-40 bg-emerald-500/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-cyan-500/20 rounded-full blur-3xl"></div>
        
        <div class="bg-slate-950 rounded-2xl border border-white/5 overflow-hidden aspect-video flex items-center justify-center">
          <div class="flex flex-col items-center gap-4 text-slate-500">
            <div class="flex gap-2">
              <span class="w-3 h-3 rounded-full bg-red-500/50"></span>
              <span class="w-3 h-3 rounded-full bg-amber-500/50"></span>
              <span class="w-3 h-3 rounded-full bg-green-500/50"></span>
            </div>
            <code class="text-emerald-400/80 font-mono text-lg">while True: plant() ; move(RIGHT)</code>
          </div>
        </div>
      </div>
    </main>

    <!-- Features Section -->
    <section id="features" class="max-w-7xl mx-auto w-full p-6 py-32 grid md:grid-cols-3 gap-8 relative z-10">
      <div class="p-8 bg-white/5 border border-white/5 rounded-3xl hover:bg-white/10 transition-colors group">
        <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">💻</div>
        <h3 class="text-xl font-bold mb-2">Aprende Python</h3>
        <p class="text-slate-400">Desde movimientos básicos hasta algoritmos complejos de optimización.</p>
      </div>
      <div class="p-8 bg-white/5 border border-white/5 rounded-3xl hover:bg-white/10 transition-colors group">
        <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">📈</div>
        <h3 class="text-xl font-bold mb-2">Juego Incremental</h3>
        <p class="text-slate-400">Gana recursos, compra mejoras y observa cómo tu imperio crece pasivamente.</p>
      </div>
      <div class="p-8 bg-white/5 border border-white/5 rounded-3xl hover:bg-white/10 transition-colors group">
        <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">🏆</div>
        <h3 class="text-xl font-bold mb-2">Compite</h3>
        <p class="text-slate-400">Sube en la tabla de clasificación y demuestra que tienes el mejor código.</p>
      </div>
    </section>

    <!-- Footer -->
    <footer class="p-10 border-t border-white/5 text-center text-slate-500 text-sm">
      <p>&copy; 2026 PyFarmer Pro. Desarrollado para mentes inquietas.</p>
    </footer>

    <!-- Auth Modal -->
    <AuthModal 
      v-if="showAuth"
      @close="showAuth = false"
      @success="onAuthSuccess"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import AuthModal from '../components/AuthModal.vue';

const router = useRouter();
const showAuth = ref(false);

const onAuthSuccess = () => {
  router.push('/game');
};

onMounted(() => {
  const token = localStorage.getItem('token');
  if (token) {
    router.push('/game');
  }
});
</script>

<style scoped>
@keyframes fade-in-up {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
  animation: fade-in-up 1s ease-out forwards;
}
@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}
.animate-float {
  animation: float 4s ease-in-out infinite;
}
</style>
