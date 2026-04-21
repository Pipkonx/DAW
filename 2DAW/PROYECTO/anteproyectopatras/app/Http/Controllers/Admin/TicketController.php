<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Muestra el panel de gestión de tickets para el administrador.
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'messages' => function($q) {
            $q->latest()->limit(1);
        }]);

        // Filtros
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        return view('admin.tickets.index', [
            'tickets' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $request->only(['status', 'priority']),
        ]);
    }

    /**
     * Muestra el detalle de un ticket desde la perspectiva admin.
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Ticket $ticket)
    {
        return view('support.show', [ // Reutilizamos la vista premium de hilos
            'ticket' => $ticket->load(['messages.user', 'user']),
            'isAdminView' => true,
        ]);
    }

    /**
     * El administrador responde a un ticket.
     */
    public function reply(Request $request, Ticket $ticket)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        // Cambiar estado a respondido
        $ticket->update(['status' => 'answered']);

        return back()->with('message', 'Respuesta enviada.');
    }

    /**
     * Cierra un ticket permanentemente.
     */
    public function close(Ticket $ticket)
    {
        $ticket->update(['status' => 'closed']);
        return back()->with('message', 'Ticket cerrado.');
    }
}
