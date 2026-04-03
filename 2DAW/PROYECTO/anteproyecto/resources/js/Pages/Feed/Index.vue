                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Derecha: Tendencias y Creadores -->
                    <div class="hidden lg:block space-y-6">
                        <!-- Tendencias actuales -->
                        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700">
                            <h3 class="text-sm font-black uppercase tracking-widest text-slate-400 mb-6">Tendencias actuales</h3>
                            <div class="space-y-5">
                                <div v-for="trend in trends" :key="trend.ticker" class="flex items-center gap-4 group cursor-pointer" @click="$inertia.get(route('assets.show', trend.ticker))">
                                    <div class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-700 flex items-center justify-center border border-slate-100 dark:border-slate-600 overflow-hidden">
                                        <img :src="trend.logo" class="w-7 h-7 object-contain" />
                                    </div>
                                    <div class="flex-grow">
                                        <div class="text-sm font-bold text-slate-800 dark:text-white group-hover:text-blue-600 transition-colors">{{ trend.name }}</div>
                                        <div class="flex items-center justify-between mt-0.5">
                                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">{{ trend.count }} menciones</span>
                                            <span class="text-[10px] text-emerald-500 font-black">+{{ trend.change }}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Principales creadores de la semana -->
                        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700">
                            <h3 class="text-sm font-black uppercase tracking-widest text-slate-400 mb-6">Mejores Creadores</h3>
                            <div class="space-y-6">
                                <Link v-for="(creator, idx) in topCreators" :key="creator.id" :href="route('social.profile', creator.username || creator.id)" class="flex items-start gap-4 group cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 p-2 -mx-2 rounded-2xl transition-all">
                                    <div class="relative">
                                        <img :src="creator.avatar || `https://ui-avatars.com/api/?name=${creator.name}`" class="w-10 h-10 rounded-xl object-cover border border-slate-100 dark:border-slate-700" />
                                        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-blue-600 text-white text-[8px] font-black rounded-lg flex items-center justify-center border-2 border-white dark:border-slate-800 shadow-sm">
                                            #{{ idx + 1 }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-slate-800 dark:text-white group-hover:text-blue-600 transition-colors">{{ creator.name }}</div>
                                        <div class="text-[10px] text-slate-500 font-bold tracking-tight">
                                            {{ creator.reactions_count }} reacciones a los mensajes
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <!-- Report Modal -->
        <div v-if="reportingPost" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-md p-8 shadow-2xl border border-slate-100 dark:border-slate-700 animate-in zoom-in-95 duration-200">
                <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2">Reportar contenido</h3>
                <p class="text-sm text-slate-500 font-bold mb-6 italic">Ayúdanos a mantener la comunidad libre de spam y toxicidad.</p>
                
                <div class="space-y-3 mb-8">
                    <div v-for="reason in ['Contenido ofensivo', 'Spam / Estafa', 'Información falsa', 'Inapropiado']" :key="reason"
                         @click="reportReason = reason"
                         class="p-4 rounded-2xl border-2 cursor-pointer transition-all flex items-center justify-between"
                         :class="reportReason === reason ? 'border-rose-500 bg-rose-50 dark:bg-rose-900/20' : 'border-slate-100 dark:border-slate-700 hover:border-slate-300'">
                        <span class="text-sm font-bold" :class="reportReason === reason ? 'text-rose-600 dark:text-rose-400' : 'text-slate-600 dark:text-slate-400'">{{ reason }}</span>
                        <div v-if="reportReason === reason" class="w-2 h-2 rounded-full bg-rose-500"></div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button @click="reportingPost = null" class="flex-grow px-6 py-3 text-sm font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">Cancelar</button>
                    <button 
                        @click="submitReport"
                        :disabled="!reportReason"
                        class="flex-grow px-6 py-3 bg-rose-500 hover:bg-rose-600 disabled:opacity-50 text-white text-sm font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-rose-500/30 transition-all active:scale-95">
                        Enviar Reporte
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.scrollbar-thin::-webkit-scrollbar {
  width: 4px;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
.dark .scrollbar-thin::-webkit-scrollbar-thumb {
  background: #475569;
}
</style>
