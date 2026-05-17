<?php

namespace App\Mail;

use App\Models\Peminjaman;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OverdueReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $peminjaman;
    public $daysOverdue;

    public function __construct(Peminjaman $peminjaman, $daysOverdue)
    {
        $this->peminjaman = $peminjaman;
        $this->daysOverdue = $daysOverdue;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ PERINGATAN: Peminjaman sudah melewati jatuh tempo',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.overdue_reminder',
        );
    }
}