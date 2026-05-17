<?php

namespace App\Mail;

use App\Models\Peminjaman;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DueDateReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $peminjaman;
    public $daysBefore;

    public function __construct(Peminjaman $peminjaman, $daysBefore)
    {
        $this->peminjaman = $peminjaman;
        $this->daysBefore = $daysBefore;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pengingat: Peminjaman akan jatuh tempo',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.due_date_reminder',
        );
    }
}