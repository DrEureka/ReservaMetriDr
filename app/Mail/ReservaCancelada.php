<?php

namespace App\Mail;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservaCancelada extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Reserva $reserva) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Reserva cancelada · #') . $this->reserva->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reserva-cancelada',
            text: 'emails.reserva-cancelada-text',
        );
    }
}
