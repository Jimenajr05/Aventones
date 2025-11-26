<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Reserva;

class RecordatorioReservasPendientes extends Mailable
{
    use Queueable, SerializesModels;

    public $reserva;

    public function __construct(Reserva $reserva)
    {
        $this->reserva = $reserva;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recordatorio: Tienes solicitudes de reserva pendientes',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reservas_pendientes',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
