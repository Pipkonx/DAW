<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TicketController extends Controller
{
    /**
     * Muestra el panel de gestión de tickets para el administrador.
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

        return Inertia::render('Admin/Tickets/Index', [
            'tickets' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $request->only(['status', 'priority']),
        ]);
    }

    /**
     * Muestra el detalle de un ticket desde la perspectiva admin.
     */
    public function show(Ticket $ticket)
    {
        return Inertia::render('Support/Show', [ // Reutilizamos la vista premium de hilos
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
