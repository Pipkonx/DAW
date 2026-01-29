<div class="flex h-[400px] bg-white dark:bg-gray-900 rounded-xl shadow-2xl overflow-hidden border border-gray-200 dark:border-gray-800 transition-colors duration-300">
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        @keyframes progress-stripe {
            from { background-position: 1rem 0; }
            to { background-position: 0 0; }
        }
        .animate-progress-striped {
            background-image: linear-gradient(
                45deg, 
                rgba(255, 255, 255, 0.15) 25%, 
                transparent 25%, 
                transparent 50%, 
                rgba(255, 255, 255, 0.15) 50%, 
                rgba(255, 255, 255, 0.15) 75%, 
                transparent 75%, 
                transparent
            );
            background-size: 1rem 1rem;
            animation: progress-stripe 1s linear infinite;
        }
    </style>
    <!-- Lista de Usuarios -->
    <div class="w-1/3 border-r border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 flex flex-col">
        <div class="p-4 border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-3">
                {{ empty(trim($search)) ? 'Conversaciones' : 'Resultados' }}
            </h2>
            <!-- Buscador -->
            <div class="relative">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.500ms="search"
                    placeholder="Buscar chat..."
                    class="w-full bg-gray-100 dark:bg-gray-800 border-none rounded-lg pl-9 pr-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-gray-100 transition-all placeholder-gray-500"
                >
            </div>
        </div>
        <div class="overflow-y-auto flex-1 custom-scrollbar">
            @foreach($this->users as $user)
                @php
                    $unreadCount = $user->unread_messages_count;
                @endphp
                <button 
                    wire:key="user-{{ $user->id }}"
                    wire:click="selectReceiver({{ $user->id }})"
                    class="w-full text-left p-4 hover:bg-white dark:hover:bg-gray-800 transition-all border-b border-gray-100 dark:border-gray-800/50 flex items-center gap-3 {{ $this->receiver && $this->receiver->id == $user->id ? 'bg-white dark:bg-gray-800 shadow-md z-10 scale-[1.02]' : '' }}"
                >
                    <div class="relative">
                        <img src="{{ $user->getFilamentAvatarUrl() }}" class="w-11 h-11 rounded-full border-2 border-white dark:border-gray-700 shadow-sm object-cover">
                        @if($unreadCount > 0)
                            <span class="absolute -top-1 -right-1 bg-blue-50 text-blue-600 text-[10px] font-black px-1.5 py-0.5 rounded-full border-2 border-blue-600 animate-pulse shadow-sm">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 dark:text-gray-100 truncate text-sm">{{ $user->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 capitalize truncate">{{ $user->role_name }}</p>
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Área de Chat -->
    <div class="flex-1 flex flex-col bg-white dark:bg-gray-900 min-w-0 h-full overflow-hidden">
        @if($this->receiver)
            <!-- Cabecera del Chat -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-800 flex items-center gap-3 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md sticky top-0 z-10 flex-shrink-0">
                <img src="{{ $this->receiver->getFilamentAvatarUrl() }}" class="w-10 h-10 rounded-full border border-gray-200 dark:border-gray-700 object-cover">
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-gray-900 dark:text-white truncate">{{ $this->receiver->name }}</h3>
                    <div class="flex items-center gap-1.5">
                        @if($this->receiver->isOnline())
                            <span class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse border-2 border-white dark:border-gray-900"></span>
                            <span class="text-[11px] font-bold text-green-600 dark:text-green-500 uppercase tracking-wider">Activo ahora</span>
                        @else
                            <span class="w-2.5 h-2.5 bg-gray-400 rounded-full border-2 border-white dark:border-gray-900"></span>
                            <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ $this->receiver->lastSeen() ? 'Visto ' . $this->receiver->lastSeen() : 'Desconectado' }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Mensajes -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50 dark:bg-gray-950/50 custom-scrollbar relative" 
                 id="chat-container" 
                 style="max-height: calc(100% - 130px); min-height: 200px;"
                 wire:poll.5s
                 x-data="{ 
                    scrollToBottom() { 
                        this.$el.scrollTop = this.$el.scrollHeight; 
                    } 
                 }"
                 x-init="scrollToBottom()"
                 x-on:message-sent.window="$nextTick(() => scrollToBottom())"
                 x-on:receiver-selected.window="$nextTick(() => scrollToBottom())">
                @php $lastDate = null; @endphp
                @forelse($this->messages as $msg)
                @php
                    $messageDate = $msg->created_at->translatedFormat('j \d\e F, Y');
                    $today = now()->translatedFormat('j \d\e F, Y');
                    $yesterday = now()->subDay()->translatedFormat('j \d\e F, Y');
                    
                    if ($messageDate === $today) {
                        $displayDate = 'Hoy';
                    } elseif ($messageDate === $yesterday) {
                        $displayDate = 'Ayer';
                    } else {
                        $displayDate = $messageDate;
                    }
                @endphp

                @if($lastDate !== $messageDate)
                    <div class="flex justify-center my-6">
                        <span class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-[11px] font-bold px-4 py-1.5 rounded-full shadow-sm border border-indigo-100 dark:border-indigo-800/50 uppercase tracking-wider">
                            {{ $displayDate }}
                        </span>
                    </div>
                    @php $lastDate = $messageDate; @endphp
                @endif

                <div class="flex {{ $msg->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }} group" wire:key="msg-{{ $msg->id }}">
                    <div class="flex flex-col {{ $msg->sender_id == auth()->id() ? 'items-end' : 'items-start' }} max-w-[85%]">
                        <div class="relative flex items-center gap-2">
                            @if($msg->sender_id == auth()->id())
                                <button 
                                    wire:click="deleteMessage({{ $msg->id }})"
                                    wire:confirm="¿Borrar este mensaje?"
                                    class="p-1 text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity focus:opacity-100"
                                    title="Eliminar mensaje"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            @endif

                            <div class="rounded-[20px] px-4 py-2.5 shadow-md border {{ $msg->sender_id == auth()->id() ? 'bg-indigo-600 border-indigo-500 text-white rounded-tr-none' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-tl-none' }}" style="{{ $msg->sender_id == auth()->id() ? 'background-color: #4f46e5;' : '' }}">
                                @if($msg->content)
                                    <p class="text-[14px] leading-relaxed whitespace-pre-wrap break-words {{ $msg->sender_id == auth()->id() ? 'text-white' : 'text-gray-900 dark:text-gray-100' }}">{{ $msg->content }}</p>
                                @endif

                                @if($msg->file_path)
                                    <div class="mt-2 flex flex-col gap-2">
                                        @if(str_starts_with($msg->file_type, 'image/'))
                                            <div class="relative group/img max-w-[200px]">
                                                <img src="{{ asset('storage/' . $msg->file_path) }}" class="rounded-lg shadow-sm border border-white/20 max-h-40 object-cover cursor-pointer hover:opacity-90 transition-opacity" wire:click="downloadFile({{ $msg->id }})">
                                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover/img:opacity-100 transition-opacity bg-black/20 rounded-lg pointer-events-none">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <div class="flex items-center gap-3 p-3 rounded-lg {{ $msg->sender_id == auth()->id() ? 'bg-indigo-500/50' : 'bg-gray-100 dark:bg-gray-700' }} border border-white/10 shadow-inner group/file">
                                            <div class="p-2 bg-white/20 rounded-md">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 {{ $msg->sender_id == auth()->id() ? 'text-white' : 'text-indigo-600 dark:text-indigo-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-bold truncate {{ $msg->sender_id == auth()->id() ? 'text-white' : 'text-gray-900 dark:text-white' }}">{{ $msg->file_name }}</p>
                                                <p class="text-[10px] {{ $msg->sender_id == auth()->id() ? 'text-indigo-100' : 'text-gray-500 dark:text-gray-400' }}">{{ number_format($msg->file_size / 1024, 1) }} KB</p>
                                            </div>
                                            <button 
                                                wire:click="downloadFile({{ $msg->id }})"
                                                class="p-2 bg-white/10 hover:bg-white/30 rounded-full transition-colors"
                                                title="Descargar archivo"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <span class="text-[10px] font-medium text-gray-500 dark:text-gray-500 mt-1.5 px-1 flex items-center gap-1">
                            {{ $msg->created_at->format('H:i') }}
                            @if($msg->sender_id == auth()->id())
                                <div class="flex items-center">
                                    @if($msg->is_read)
                                        {{-- Doble check azul (Leído) --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M18 6L7 17l-5-5"></path>
                                            <path d="M22 10L13.5 18.5l-2-2"></path>
                                        </svg>
                                    @else
                                        {{-- Doble check gris (Recibido) --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M18 6L7 17l-5-5"></path>
                                            <path d="M22 10L13.5 18.5l-2-2"></path>
                                        </svg>
                                    @endif
                                </div>
                            @endif
                        </span>
                    </div>
                </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-full text-gray-500 dark:text-gray-400">
                        <p>No hay mensajes aún. ¡Inicia la conversación!</p>
                    </div>
                @endforelse
            </div>

            <!-- Input de Mensaje -->
            <div class="p-4 border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 flex-shrink-0">
                @if($attachment)
                    <div class="mb-3 flex flex-col gap-2 p-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg border border-indigo-100 dark:border-indigo-800 animate-in fade-in slide-in-from-bottom-2">
                        <div class="flex items-center gap-3">
                            @if(str_starts_with($attachment->getMimeType(), 'image/'))
                                <div class="relative w-12 h-12 rounded-lg overflow-hidden shadow-sm border border-indigo-200">
                                    <img src="{{ $attachment->temporaryUrl() }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="p-2 bg-indigo-600 rounded text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-indigo-900 dark:text-indigo-100 truncate">{{ $attachment->getClientOriginalName() }}</p>
                                <p class="text-[10px] text-indigo-600 dark:text-indigo-400">Listo para enviar</p>
                            </div>
                            <button wire:click="$set('attachment', null)" class="p-1 hover:bg-indigo-200 dark:hover:bg-indigo-800 rounded-full text-indigo-600 dark:text-indigo-400 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                <div x-data="{ uploading: false, progress: 0 }" 
                     x-on:livewire-upload-start="uploading = true" 
                     x-on:livewire-upload-finish="uploading = false" 
                     x-on:livewire-upload-error="uploading = false" 
                     x-on:livewire-upload-progress="progress = $event.detail.progress">
                    
                    <div x-show="uploading" class="mb-5 bg-indigo-50 dark:bg-indigo-900/20 p-3 rounded-lg border border-indigo-100 dark:border-indigo-800/50 transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-indigo-600 rounded-full animate-ping"></div>
                                <span class="text-[10px] font-black text-indigo-700 dark:text-indigo-400 uppercase tracking-widest">Subiendo archivo...</span>
                            </div>
                            <span class="text-[10px] font-black text-indigo-700 dark:text-indigo-400 bg-white dark:bg-indigo-900 px-2 py-0.5 rounded-full shadow-sm" x-text="progress + '%'"></span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden shadow-inner border border-gray-300/50 dark:border-gray-600/50">
                            <div class="bg-indigo-600 h-full transition-all duration-300 animate-progress-striped shadow-[0_0_10px_rgba(79,70,229,0.5)]" :style="'width: ' + progress + '%'"></div>
                        </div>
                    </div>

                    <form wire:submit.prevent="sendMessage" class="flex gap-3 items-center">
                    <div class="relative flex-1 flex gap-2">
                        <label class="flex-shrink-0 cursor-pointer p-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-full transition-colors group" title="Adjuntar archivo">
                            <input type="file" wire:model="attachment" class="hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 group-hover:text-indigo-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                        </label>
                        <input 
                            type="text" 
                            wire:model="message" 
                            placeholder="Escribe un mensaje..."
                            class="flex-1 bg-gray-100 dark:bg-gray-800 border-none rounded-[15px] px-4 py-3 focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-gray-100 transition-all placeholder-gray-500 dark:placeholder-gray-400 text-sm shadow-inner"
                            autocomplete="off"
                        >
                    </div>
                    <button 
                        type="submit" 
                        class="flex-shrink-0 bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-400 text-white w-12 h-12 rounded-full transition-all shadow-lg hover:shadow-indigo-500/30 active:scale-95 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed group"
                        style="background-color: #4f46e5;"
                        wire:loading.attr="disabled"
                    >
                        <div wire:loading.remove wire:target="sendMessage" class="group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 rotate-45" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                            </svg>
                        </div>
                        <div wire:loading wire:target="sendMessage">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </button>
                </form>
            </div>
        @else
            <div class="flex-1 flex flex-col items-center justify-center text-gray-400 dark:text-gray-600 bg-slate-50/50 dark:bg-gray-950/20">
                <div class="relative">
                    <div class="absolute -inset-4 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-2xl animate-pulse"></div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mb-6 relative opacity-20 dark:opacity-10 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">Tu mensajería interna</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-xs text-center px-4">Selecciona una conversación de la lista de la izquierda para empezar a chatear.</p>
            </div>
        @endif
    </div>
</div>
