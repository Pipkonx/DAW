<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SupportController extends Controller
{
    /**
     * Muestra la lista de tickets del usuario.
     */
    public function index(): Response
    {
        return Inertia::render('Support/Index', [
            'tickets' => Ticket::where('user_id', Auth::id())
                ->with(['messages' => function ($query) {
                    $query->latest()->limit(1);
                }])
                ->latest()
                ->get(),
        ]);
    }

    /**
     * Crea un nuevo ticket de soporte.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'priority' => $request->priority,
            'status' => 'open',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return back()->with('message', 'Ticket creado correctamente.');
    }

    /**
     * Muestra el detalle de un ticket.
     */
    public function show(Ticket $ticket): Response
    {
        // Verificar que el ticket pertenezca al usuario (o sea admin)
        if ($ticket->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        return Inertia::render('Support/Show', [
            'ticket' => $ticket->load(['messages.user', 'user']),
        ]);
    }

    /**
     * Añade un mensaje a un ticket existente.
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

        // Si es el admin el que responde, cambiamos el estado
        if (Auth::user()->is_admin) {
            $ticket->update(['status' => 'answered']);
        } else {
            $ticket->update(['status' => 'open']);
        }

        return back();
    }
}
