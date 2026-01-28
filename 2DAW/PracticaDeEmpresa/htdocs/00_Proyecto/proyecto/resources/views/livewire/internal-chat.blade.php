<div class="flex h-[600px] bg-white dark:bg-gray-900 rounded-xl shadow-2xl overflow-hidden border border-gray-200 dark:border-gray-800 transition-colors duration-300">
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
    </style>
    <!-- Lista de Usuarios -->
    <div class="w-1/3 border-r border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 flex flex-col">
        <div class="p-4 border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-3">Mensajes</h2>
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
                    wire:click="selectReceiver({{ $user->id }})"
                    class="w-full text-left p-4 hover:bg-white dark:hover:bg-gray-800 transition-all border-b border-gray-100 dark:border-gray-800/50 flex items-center gap-3 {{ $receiver && $receiver->id == $user->id ? 'bg-white dark:bg-gray-800 shadow-md z-10 scale-[1.02]' : '' }}"
                >
                    <div class="relative">
                        <img src="{{ $user->getFilamentAvatarUrl() }}" class="w-11 h-11 rounded-full border-2 border-white dark:border-gray-700 shadow-sm object-cover">
                        @if($unreadCount > 0)
                            <span class="absolute -top-1 -right-1 bg-red-600 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full border-2 border-red-50 dark:border-gray-900 animate-pulse shadow-sm">
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
    <div class="flex-1 flex flex-col bg-white dark:bg-gray-900">
        @if($receiver)
            <!-- Cabecera del Chat -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-800 flex items-center gap-3 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md sticky top-0 z-20">
                <img src="{{ $receiver->getFilamentAvatarUrl() }}" class="w-10 h-10 rounded-full border border-gray-200 dark:border-gray-700 object-cover">
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-gray-900 dark:text-white truncate">{{ $receiver->name }}</h3>
                    <div class="flex items-center gap-1.5">
                        @if($receiver->isOnline())
                            <span class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse border-2 border-white dark:border-gray-900"></span>
                            <span class="text-[11px] font-bold text-green-600 dark:text-green-500 uppercase tracking-wider">Activo ahora</span>
                        @else
                            <span class="w-2.5 h-2.5 bg-gray-400 rounded-full border-2 border-white dark:border-gray-900"></span>
                            <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ $receiver->lastSeen() ? 'Visto ' . $receiver->lastSeen() : 'Desconectado' }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

        <!-- Mensajes -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50 dark:bg-gray-950/50 custom-scrollbar" 
             id="chat-container" 
             wire:poll.5s
             x-data="{ 
                scrollToBottom() { 
                    $el.scrollTop = $el.scrollHeight; 
                } 
             }"
             x-init="scrollToBottom()"
             x-on:message-sent.window="$nextTick(() => scrollToBottom())"
             x-on:receiver-selected.window="$nextTick(() => scrollToBottom())">
            @forelse($this->messages as $msg)
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
                                <p class="text-[14px] leading-relaxed whitespace-pre-wrap break-words {{ $msg->sender_id == auth()->id() ? 'text-white' : 'text-gray-900 dark:text-gray-100' }}">{{ $msg->content }}</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-medium text-gray-500 dark:text-gray-500 mt-1.5 px-1 flex items-center gap-1">
                            {{ $msg->created_at->format('H:i') }}
                            @if($msg->sender_id == auth()->id())
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 {{ $msg->is_read ? 'text-indigo-500' : 'text-gray-400' }}" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
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
            <div class="p-4 border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
                <form wire:submit.prevent="sendMessage" class="flex gap-3 items-center">
                    <div class="relative flex-1">
                        <input 
                            type="text" 
                            wire:model="message" 
                            placeholder="Escribe un mensaje..."
                            class="w-full bg-gray-100 dark:bg-gray-800 border-none rounded-[15px] px-4 py-3 focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-gray-100 transition-all placeholder-gray-500 dark:placeholder-gray-400 text-sm shadow-inner"
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
            <div class="flex-1 flex flex-col items-center justify-center text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/40 px-6 text-center">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-6 shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 opacity-40">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.303.025-.607.047-.912.066a2.022 2.022 0 01-1.124-.265l-2.404-1.397a.75.75 0 00-.712 0l-2.404 1.397a2.022 2.022 0 01-1.124.265 11.308 11.308 0 01-.912-.066 2.181 2.181 0 01-1.98-2.193V10.608c0-.969.616-1.813 1.5-2.097a17.514 17.514 0 0111.412 0zm0 0V4.497c0-1.126-.92-2.07-2.081-2.07-1.012 0-1.88.73-2.012 1.73l-.117.9a.75.75 0 01-1.492-.194l.117-.9c.218-1.657 1.611-2.866 3.397-2.866 2.015 0 3.578 1.716 3.578 3.53v3.911z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">Tu bandeja de entrada</h3>
                <p class="max-w-xs text-sm">Selecciona a uno de tus contactos de la izquierda para comenzar una conversación.</p>
            </div>
        @endif
    </div>
</div>
