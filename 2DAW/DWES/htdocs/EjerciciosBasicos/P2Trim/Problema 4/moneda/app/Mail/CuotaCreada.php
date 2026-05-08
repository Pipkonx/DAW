<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CuotaCreada extends Mailable
{
    use Queueable, SerializesModels;

    public $cuota;

    /**
     * Crear una nueva instancia de mensaje.
     */
    public function __construct($cuota)
    {
        $this->cuota = $cuota;
    }

    /**
     * Obtener el sobre del mensaje.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva Cuota Generada - Nosecaen S.L.',
        );
    }

    /**
     * Obtener la definición del contenido del mensaje.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.cuotas.creada',
            with: [
                'clientName' => $this->cuota->cliente->name,
                'amount' => $this->cuota->amount,
                'currency' => $this->cuota->currency,
            ],
        );
    }

    /**
     * Obtener los archivos adjuntos para el mensaje.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
