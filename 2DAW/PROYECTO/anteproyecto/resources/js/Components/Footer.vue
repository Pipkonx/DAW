<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <footer class="py-12 border-t border-slate-100 dark:border-slate-800 bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm mt-auto transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="flex flex-col md:flex-row justify-between items-center gap-8 mb-8">
                <!-- Branding & Recognition -->
                <div class="flex flex-col items-center md:items-start gap-4">
                    <Link href="/" class="text-xl font-bold tracking-tight text-slate-900 dark:text-white hover:opacity-80 transition italic uppercase tracking-tighter">
                        fintech<span class="text-blue-600 dark:text-blue-500 not-italic lowercase font-black">Pro</span>
                    </Link>
                    <div class="flex flex-col md:flex-row items-center gap-4 text-xs font-bold text-slate-500 dark:text-slate-400">
                        <span class="uppercase tracking-widest text-[9px] px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg">Creado por Rafael Cordero</span>
                        <div class="flex gap-4">
                            <a 
                                href="https://github.com/pipkonx" 
                                target="_blank" 
                                rel="noopener noreferrer"
                                class="flex items-center gap-2 hover:text-blue-600 dark:hover:text-blue-400 transition-all group"
                            >
                                <svg class="w-4 h-4 fill-current group-hover:scale-110 transition-transform" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                                @pipkonx
                            </a>
                            <a 
                                href="https://github.com/pipkon" 
                                target="_blank" 
                                rel="noopener noreferrer"
                                class="flex items-center gap-2 hover:text-blue-600 dark:hover:text-blue-400 transition-all group"
                            >
                                @pipkon
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Legal Links -->
                <div class="flex flex-wrap justify-center gap-x-8 gap-y-4">
                    <Link :href="route('legal.terms')" class="text-sm font-black text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors uppercase tracking-widest text-[9px]">
                        Términos
                    </Link>
                    <Link :href="route('legal.privacy')" class="text-sm font-black text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors uppercase tracking-widest text-[9px]">
                        Privacidad
                    </Link>
                    <Link :href="route('legal.notice')" class="text-sm font-black text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors uppercase tracking-widest text-[9px]">
                        Aviso Legal
                    </Link>
                </div>
            </div>

            <!-- Copyright -->
            <div class="pt-8 border-t border-slate-100 dark:border-slate-800 text-center">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-600">
                    &copy; 2026 fintechPro &bull; Your Finance, Under Your Control.
                </p>
            </div>
        </div>
    </footer>
</template>
