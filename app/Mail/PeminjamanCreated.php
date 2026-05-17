<?php

namespace App\Mail;

use App\Models\Peminjaman;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PeminjamanCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $peminjaman;

    public function __construct(Peminjaman $peminjaman)
    {
        $this->peminjaman = $peminjaman;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi Peminjaman Barang - ' . $this->peminjaman->kode_transaksi,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.peminjaman_created',
        );
    }
}