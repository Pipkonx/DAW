<template>
  <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-xl z-[60] flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-white/10 w-full max-w-md rounded-3xl shadow-2xl overflow-hidden relative">
      <!-- Decoration -->
      <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500"></div>
      
      <div class="p-8">
        <div class="text-center mb-8">
          <div class="w-16 h-16 bg-emerald-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-emerald-500/30">
            <span class="text-3xl">👤</span>
          </div>
          <h2 class="text-2xl font-bold text-white mb-2">{{ isLogin ? 'Bienvenido de nuevo' : 'Crear cuenta' }}</h2>
          <p class="text-slate-400 text-sm">
            {{ isLogin ? 'Introduce tus credenciales para continuar tu granja' : 'Regístrate para guardar tu progreso en la nube' }}
          </p>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div v-if="!isLogin" class="space-y-1">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Nombre</label>
            <input 
              v-model="form.name" 
              type="text" 
              required
              class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors"
              placeholder="Tu nombre de granjero"
            />
          </div>

          <div class="space-y-1">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Email</label>
            <input 
              v-model="form.email" 
              type="email" 
              required
              class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors"
              placeholder="email@ejemplo.com"
            />
          </div>

          <div class="space-y-1">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Contraseña</label>
            <input 
              v-model="form.password" 
              type="password" 
              required
              class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors"
              placeholder="••••••••"
            />
          </div>

          <div v-if="!isLogin" class="space-y-1">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Confirmar Contraseña</label>
            <input 
              v-model="form.password_confirmation" 
              type="password" 
              required
              class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors"
              placeholder="••••••••"
            />
          </div>

          <div v-if="error" class="bg-red-500/10 border border-red-500/20 text-red-400 text-xs p-3 rounded-lg">
            {{ error }}
          </div>

          <button 
            type="submit" 
            :disabled="loading"
            class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl transition-all transform hover:scale-[1.02] active:scale-95 disabled:opacity-50 shadow-lg shadow-emerald-900/20 mt-2"
          >
            <span v-if="loading" class="inline-block animate-spin mr-2">⏳</span>
            {{ isLogin ? 'Iniciar Sesión' : 'Registrarse' }}
          </button>
        </form>

        <div class="mt-6 text-center">
          <button 
            @click="isLogin = !isLogin; error = null" 
            class="text-emerald-500 hover:text-emerald-400 text-sm font-medium transition-colors"
          >
            {{ isLogin ? '¿No tienes cuenta? Regístrate' : '¿Ya tienes cuenta? Inicia sesión' }}
          </button>
        </div>

        <button 
          @click="$emit('close')" 
          class="absolute top-4 right-4 text-slate-500 hover:text-white transition-colors"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { AuthService } from '../services/AuthService';

const emit = defineEmits(['close', 'success']);

const isLogin = ref(true);
const loading = ref(false);
const error = ref(null);

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
});

const handleSubmit = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    if (isLogin.value) {
      await AuthService.login({
        email: form.email,
        password: form.password
      });
    } else {
      await AuthService.register({
        name: form.name,
        email: form.email,
        password: form.password,
        password_confirmation: form.password_confirmation
      });
    }
    emit('success');
    emit('close');
  } catch (e) {
    if (e.response?.data?.errors) {
      // Si hay errores de validación, cogemos el primero para mostrarlo de forma simple
      const firstError = Object.values(e.response.data.errors)[0][0];
      error.value = firstError;
    } else if (e.response?.data?.message) {
      // Mensajes específicos como "Credenciales no coinciden"
      error.value = e.response.data.message === 'These credentials do not match our records.' 
        ? 'El correo o la contraseña no son correctos.' 
        : e.response.data.message;
    } else {
      error.value = 'No hemos podido conectar con el servidor. Inténtalo de nuevo en un momento.';
    }
  } finally {
    loading.value = false;
  }
};
</script>
