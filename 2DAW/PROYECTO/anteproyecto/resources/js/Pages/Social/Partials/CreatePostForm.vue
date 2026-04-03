<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

/**
 * CreatePostForm - Central de composición de noticias.
 * 
 * Permite a los usuarios redactar análisis de mercado y adjuntar evidencias gráficas.
 * Incluye gestión de previsualización de archivos local antes de subir.
 */
const props = defineProps({
    user: Object,
});

const postForm = useForm({
    content: '',
    market_asset_id: null,
    image: null,
});

const imagePreview = ref(null);
const fileInput = ref(null);

/**
 * Procesa el archivo seleccionado y genera una URL temporal para la vista previa.
 */
const handleImageUpload = (e) => {
    const file = e.target.files[0];
    postForm.image = file;
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => imagePreview.value = e.target.result;
        reader.readAsDataURL(file);
    }
};

/**
 * Envía la publicación al servidor y limpia el estado local al tener éxito.
 */
const submitPost = () => {
    postForm.post(route('social.post'), {
        onSuccess: () => {
            postForm.reset();
            imagePreview.value = null;
        },
    });
};

/**
 * Estilos del borde del avatar según el Tier.
 */
const getAvatarRingClasses = (tier) => {
    switch (tier) {
        case 'premium': return 'ring-2 ring-purple-500 ring-offset-2 dark:ring-offset-slate-900 border-transparent';
        case 'pro': return 'ring-2 ring-indigo-500 ring-offset-2 dark:ring-offset-slate-900 border-transparent';
        case 'basic': return 'ring-2 ring-blue-500 ring-offset-2 dark:ring-offset-slate-900 border-transparent';
        default: return 'border border-slate-200 dark:border-slate-700';
    }
};
</script>

<template>
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-xl border border-slate-100 dark:border-slate-700 border-b-4 border-b-blue-500">
        <div class="flex items-start gap-4">
            <!-- Usuario actual -->
            <img :src="user.avatar || `https://ui-avatars.com/api/?name=${user.name}`" 
                 :class="['w-12 h-12 rounded-2xl object-cover shrink-0', getAvatarRingClasses(user.tier)]" />
            
            <div class="flex-grow">
                <!-- Área de Texto de la Publicación -->
                <textarea 
                    v-model="postForm.content"
                    class="w-full bg-transparent border-none focus:ring-0 text-slate-800 dark:text-white placeholder-slate-400 resize-none min-h-[100px] text-sm"
                    placeholder="¿Qué está pasando en el mercado hoy?"
                ></textarea>
                
                <!-- Previsualización de Imagen Cargada -->
                <div v-if="imagePreview" class="relative mt-4 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm animate-in zoom-in-95">
                    <img :src="imagePreview" class="w-full max-h-[400px] object-cover" />
                    <button @click="imagePreview = null; postForm.image = null" class="absolute top-2 right-2 p-1.5 bg-black/50 hover:bg-black/70 text-white rounded-full backdrop-blur-md transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Barra de Herramientas Inferior -->
        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between">
            <div class="flex gap-2">
                <!-- Selector de Archivo Oculto -->
                <button @click="fileInput.click()" class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl transition-all" title="Añadir imagen">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </button>
                <input type="file" ref="fileInput" class="hidden" accept="image/*" @change="handleImageUpload" />
            </div>

            <!-- Botón de Envío Principal -->
            <button 
                @click="submitPost"
                :disabled="postForm.processing || !postForm.content"
                class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-black uppercase text-[10px] tracking-widest rounded-2xl shadow-lg shadow-blue-500/30 transition-all active:scale-95"
            >
                Publicar Análisis
            </button>
        </div>
    </div>
</template>
