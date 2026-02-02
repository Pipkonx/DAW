<?php

namespace App\Mail;

use App\Models\Practice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PracticeNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Practice $practice,
        public string $type = 'creada' // 'creada' o 'actualizada'
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->type === 'creada' 
            ? '📅 Nueva Tarea de Prácticas: ' . $this->practice->title
            : '🔄 Tarea Actualizada: ' . $this->practice->title;

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.practicas.notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
