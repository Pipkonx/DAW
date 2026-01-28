<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Filament\Notifications\Notification;

use Illuminate\Support\Facades\Cache;

use Livewire\Attributes\Computed;

class InternalChat extends Component
{
    public $receiver;
    public $message = '';
    public $search = '';

    #[Computed]
    public function users()
    {
        return User::where('id', '!=', Auth::id())
            ->where('name', 'like', '%' . $this->search . '%')
            ->withCount(['messagesReceived as unread_messages_count' => function ($query) {
                $query->where('receiver_id', Auth::id())
                      ->where('is_read', false);
            }])
            ->orderBy('unread_messages_count', 'desc')
            ->orderBy('name', 'asc')
            ->get();
    }

    #[Computed]
    public function messages()
    {
        if (!$this->receiver) {
            return [];
        }

        $receiverId = $this->receiver instanceof User ? $this->receiver->id : $this->receiver['id'];
        
        return Message::where(function ($query) use ($receiverId) {
            $query->where('sender_id', Auth::id())
                ->where('receiver_id', $receiverId);
        })->orWhere(function ($query) use ($receiverId) {
            $query->where('sender_id', $receiverId)
                ->where('receiver_id', Auth::id());
        })
        ->with(['sender', 'receiver'])
        ->get();
    }

    public function mount()
    {
        // ...
    }

    public function selectReceiver($userId)
    {
        $this->receiver = User::find($userId);
        
        // Marcar mensajes como leídos
        Message::where('sender_id', $userId)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Limpiar cache de notificaciones
        Cache::forget("unread_count_" . Auth::id());

        $this->dispatch('receiverSelected');
    }

    public function sendMessage()
    {
        $this->validate([
            'message' => 'required|string|max:1000',
            'receiver' => 'required',
        ]);

        $receiverId = $this->receiver instanceof User ? $this->receiver->id : $this->receiver['id'];

        $newMessage = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiverId,
            'content' => $this->message,
            'is_read' => false,
        ]);

        // Limpiar cache del receptor
        Cache::forget("unread_count_" . $receiverId);

        $this->message = '';
        $this->dispatch('messageSent');
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
