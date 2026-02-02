<?php

namespace App\Mail;

use App\Models\Incidencia;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IncidenciaRegistrada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Incidencia $incidencia
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Nueva Incidencia Registrada: ' . $this->incidencia->titulo,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.incidencias.registrada',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
