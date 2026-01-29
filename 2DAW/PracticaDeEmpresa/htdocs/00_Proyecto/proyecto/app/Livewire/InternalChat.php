<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Filament\Notifications\Notification;

use Illuminate\Support\Facades\Cache;

use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;

class InternalChat extends Component
{
    use WithFileUploads;

    public $receiverId;
    public $message = '';
    public $search = '';
    public $attachment;
    public $uploading = false;

    public function getUsersProperty()
    {
        $query = User::where('id', '!=', Auth::id())
            ->withCount(['messagesReceived as unread_messages_count' => function ($query) {
                $query->where('receiver_id', Auth::id())
                      ->where('is_read', false);
            }])
            ->addSelect([
                'last_message_at' => Message::select('created_at')
                    ->where(function ($q) {
                        $q->whereColumn('sender_id', 'users.id')
                            ->where('receiver_id', Auth::id());
                    })
                    ->orWhere(function ($q) {
                        $q->where('sender_id', Auth::id())
                            ->whereColumn('receiver_id', 'users.id');
                    })
                    ->latest()
                    ->take(1)
            ]);

        if (empty(trim($this->search))) {
            // Solo usuarios con los que hay mensajes (enviados o recibidos)
            $query->whereHas('messagesSent', function($sq) {
                $sq->where('receiver_id', Auth::id());
            })->orWhereHas('messagesReceived', function($sq) {
                $sq->where('sender_id', Auth::id());
            });
            
            // Ordenar por el mensaje más reciente
            $query->orderByDesc('last_message_at');
        } else {
            // Si hay búsqueda, buscar entre todos los usuarios
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orderBy('name', 'asc');
        }

        return $query->get();
    }

    public function getReceiverProperty()
    {
        return $this->receiverId ? User::find($this->receiverId) : null;
    }

    public function getMessagesProperty()
    {
        if (!$this->receiverId) {
            return [];
        }

        $messages = Message::where(function ($query) {
            $query->where('sender_id', Auth::id())
                ->where('receiver_id', $this->receiverId);
        })->orWhere(function ($query) {
            $query->where('sender_id', $this->receiverId)
                ->where('receiver_id', Auth::id());
        })
        ->with(['sender', 'receiver'])
        ->get();

        // Si hay mensajes nuevos del receptor, marcarlos como leídos
        $unreadFromReceiver = Message::where('sender_id', $this->receiverId)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false);

        if ($unreadFromReceiver->exists()) {
            $unreadFromReceiver->update(['is_read' => true]);
            Cache::forget("unread_count_" . Auth::id());
        }

        return $messages;
    }

    public function mount()
    {
        // ...
    }

    public function selectReceiver($userId)
    {
        $this->receiverId = $userId;
        
        // Marcar mensajes como leídos
        Message::where('sender_id', $userId)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Limpiar cache de notificaciones
        Cache::forget("unread_count_" . Auth::id());

        $this->dispatch('receiverSelected');
    }

    protected function rules()
    {
        return [
            'message' => 'required|string|max:1000',
            'receiverId' => 'required',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'message' => 'mensaje',
            'receiverId' => 'destinatario',
        ];
    }

    public function sendMessage()
    {
        if (empty(trim($this->message)) && !$this->attachment) {
            return;
        }

        $this->validate([
            'message' => 'nullable|string|max:1000',
            'receiverId' => 'required',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        $fileData = [];
        if ($this->attachment) {
            $mimeType = $this->attachment->getMimeType();
            $isImage = str_starts_with($mimeType, 'image/');

            $extension = strtolower($this->attachment->getClientOriginalExtension());

            if ($isImage && in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                // Comprimir imagen usando Intervention Image
                $img = Image::read($this->attachment->getRealPath());
                
                // Redimensionar si es muy grande (máximo 1200px de ancho/alto)
                $img->scaleDown(width: 1200, height: 1200);
                
                $filename = time() . '_' . $this->attachment->getClientOriginalName();
                $path = 'chat-attachments/' . $filename;
                
                // Codificar con calidad reducida (60%)
                $encoded = $img->encodeByExtension($extension, quality: 60);
                
                Storage::disk('public')->put($path, (string) $encoded);
                
                $fileData = [
                    'file_path' => $path,
                    'file_name' => $this->attachment->getClientOriginalName(),
                    'file_type' => $mimeType,
                    'file_size' => strlen((string) $encoded),
                ];
            } else {
                // Archivos no imagen o formatos no soportados para compresión simple
                $path = $this->attachment->store('chat-attachments', 'public');
                $fileData = [
                    'file_path' => $path,
                    'file_name' => $this->attachment->getClientOriginalName(),
                    'file_type' => $mimeType,
                    'file_size' => $this->attachment->getSize(),
                ];
            }
        }

        $newMessage = Message::create(array_merge([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->receiverId,
            'content' => $this->message,
            'is_read' => false,
        ], $fileData));

        // Limpiar cache del receptor
        Cache::forget("unread_count_" . $this->receiverId);

        $this->message = '';
        $this->attachment = null;
        $this->dispatch('messageSent');
    }

    public function downloadFile($messageId)
    {
        $message = Message::findOrFail($messageId);
        
        if ($message->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($message->file_path)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->download($message->file_path, $message->file_name);
        }

        Notification::make()
            ->title('Error')
            ->body('El archivo no existe o ha sido eliminado.')
            ->danger()
            ->send();
    }

    public function deleteMessage($messageId)
    {
        $message = Message::find($messageId);

        if ($message && $message->sender_id === Auth::id()) {
            $message->delete();
            
            Notification::make()
                ->title('Mensaje eliminado')
                ->success()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.internal-chat');
    }
}
