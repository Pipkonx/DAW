<script setup>
import { ref, onMounted, watch } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';

/**
 * Componente detector de bloqueadores de anuncios (AdBlock/Brave).
 * Bloquea el uso de la aplicación si se detecta bloqueo y el usuario no es premium.
 */
const isAdBlockActive = ref(false);
const page = usePage();

// Bloquear el scroll del body si el AdBlock está activo
watch(isAdBlockActive, (val) => {
    if (val) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = 'auto';
    }
});

const checkAdBlock = async () => {
    // Si el usuario es premium, no nos importa si usa AdBlock (ya pagó para no ver anuncios)
    if (page.props.auth.user?.is_premium) return;

    // Método 1: Intentar cargar un script trampa
    const adUrl = 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js';
    try {
        const response = await fetch(adUrl, { method: 'HEAD', mode: 'no-cors' });
        // Si llegamos aquí sin error, el dominio no está bloqueado por DNS
    } catch (e) {
        isAdBlockActive.value = true;
        return;
    }

    // Método 2: Honey-pot (Elemento trampa)
    const bait = document.createElement('div');
    bait.innerHTML = '&nbsp;';
    bait.className = 'adsbygoogle ad-unit ad-placeholder pub_300x250 pub_300x250m pub_728x90 text-ad text_ad text-ads text_ads text-ac';
    bait.style.cssText = 'position: absolute; top: -1000px; left: -1000px; width: 1px; height: 1px;';
    document.body.appendChild(bait);

    window.setTimeout(() => {
        if (bait.offsetHeight === 0 || bait.offsetWidth === 0 || window.getComputedStyle(bait).display === 'none') {
            isAdBlockActive.value = true;
        }
        document.body.removeChild(bait);
    }, 100);

    // Método 3: Detección específica de Brave
    if (navigator.brave && await navigator.brave.isBrave()) {
        isAdBlockActive.value = true;
    }
};

onMounted(() => {
    checkAdBlock();
});
</script>

<template>
    <div v-if="isAdBlockActive" class="fixed inset-0 z-[10000] bg-slate-900/95 backdrop-blur-md flex items-center justify-center p-6 text-center">
        <div class="max-w-md w-full bg-white dark:bg-slate-800 rounded-3xl p-10 shadow-2xl border border-slate-200 dark:border-slate-700">
            <div class="w-20 h-20 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-full flex items-center justify-center mx-auto mb-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            
            <h2 class="text-2xl font-black text-slate-900 dark:text-white mb-4">
                Bloqueador Detectado
            </h2>
            
            <p class="text-slate-600 dark:text-slate-400 mb-8 leading-relaxed">
                Nuestra plataforma se mantiene gracias a los anuncios. Para continuar usando **FintechPro**, por favor desactiva tu bloqueador de anuncios o el escudo de Brave.
            </p>

            <div class="space-y-4">
                <button 
                    @click="window.location.reload()"
                    class="w-full py-4 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold rounded-xl hover:scale-[1.02] transition-transform"
                >
                    Ya lo he desactivado
                </button>
                
                <p class="text-xs text-slate-400">O si prefieres no ver anuncios nunca más:</p>
                
                <Link 
                    :href="route('subscription.index')"
                    class="w-full flex items-center justify-center py-4 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition"
                >
                    Obtener Plan Premium (Sin Anuncios)
                </Link>
            </div>
        </div>
    </div>
</template>
