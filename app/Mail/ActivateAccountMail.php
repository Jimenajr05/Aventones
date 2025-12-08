<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// Clase para enviar correo de activación de cuenta
class ActivateAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    // Constructor que recibe el usuario
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    // Definición del sobre del correo
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Activa tu cuenta - Aventones',
        );
    }

    // Definición del contenido del correo
    public function content(): Content
    {
        return new Content(
            view: 'emails.activate-account', // Vista del correo
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
