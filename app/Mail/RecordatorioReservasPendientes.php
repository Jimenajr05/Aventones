<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

// Mailable para enviar recordatorios de reservas pendientes a los choferes.
class RecordatorioReservasPendientes extends Mailable
{
    use Queueable, SerializesModels;

    // La colección de reservas pendientes
    public $reservas; 

    // Constructor que recibe las reservas pendientes
    public function __construct(Collection $reservas)
    {
        $this->reservas = $reservas;
    }

    // Definición del sobre del correo
    public function envelope(): Envelope
    {
        $count = $this->reservas->count();
        $subject = ($count > 1) 
            ? "Tienes {$count} reservas pendientes de aprobación"
            : "Tienes una reserva pendiente de aprobación";

        return new Envelope(
            subject: $subject,
        );
    }

    // Definición del contenido del correo
    public function content(): Content
    {
        return new Content(
            view: 'emails.reservas_pendientes', 
            with: [
                'reservas' => $this->reservas, 
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
