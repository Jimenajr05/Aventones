<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class RecordatorioReservasPendientes extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * La colección de reservas pendientes para el chofer.
     * @var Collection
     */
    public $reservas; 

    /**
     * Create a new message instance.
     *
     * @param Collection $reservas La colección de reservas
     */
    public function __construct(Collection $reservas)
    {
        $this->reservas = $reservas;
    }

    /**
     * Get the message envelope.
     */
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

    /**
     * Get the message content.
     */
    public function content(): Content
    {
        return new Content(
            // 🎯 CORRECCIÓN CLAVE AQUÍ: Cambiamos 'recordatorio_reservas' a 'reservas_pendientes'
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
