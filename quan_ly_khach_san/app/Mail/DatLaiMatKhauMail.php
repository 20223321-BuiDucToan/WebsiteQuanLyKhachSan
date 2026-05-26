<?php

namespace App\Mail;

use App\Models\NguoiDung;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DatLaiMatKhauMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public NguoiDung $nguoiDung,
        public string $resetLink,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Dat lai mat khau tai khoan khach san',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password_reset',
        );
    }
}
