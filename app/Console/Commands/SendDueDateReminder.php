<?php

namespace App\Console\Commands;

use App\Models\Peminjaman;
use App\Models\Setting;
use App\Mail\DueDateReminder;
use App\Mail\OverdueReminder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;

class SendDueDateReminder extends Command
{
    protected $signature = 'reminder:due-date';
    protected $description = 'Kirim email pengingat peminjaman yang akan jatuh tempo, hari H, dan sudah overdue';

    public function handle()
    {
        if (Setting::get('notification_enabled') != '1') {
            $this->info('Notifikasi nonaktif.');
            return;
        }

        $today = now()->startOfDay();

        // ================== 1. PENGINGAT SEBELUM JATUH TEMPO (H - n) ==================
        $daysBefore = (int) Setting::get('notification_days_before', 2);
        $targetDate = $today->copy()->addDays($daysBefore)->toDateString();

        $willDue = Peminjaman::with('details.barang')
            ->where('status_transaksi', 'aktif')
            ->whereDate('tanggal_jatuh_tempo', $targetDate)
            ->get();

        foreach ($willDue as $pinjam) {
            Mail::to($pinjam->email)->send(new DueDateReminder($pinjam, $daysBefore));
        }
        $this->info("Pengingat sebelum jatuh tempo (H-{$daysBefore}) dikirim ke " . $willDue->count() . " peminjam.");

        // ================== 2. PENGINGAT PADA HARI JATUH TEMPO (H+0) ==================
        // Kirim untuk semua peminjaman aktif yang jatuh tempo hari ini
        $dueToday = Peminjaman::with('details.barang')
            ->where('status_transaksi', 'aktif')
            ->whereDate('tanggal_jatuh_tempo', $today)
            ->get();

        foreach ($dueToday as $pinjam) {
            Mail::to($pinjam->email)->send(new DueDateReminder($pinjam, 0));
        }
        $this->info("Pengingat hari jatuh tempo (H+0) dikirim ke " . $dueToday->count() . " peminjam.");

        // ================== 3. PENGINGAT SETELAH JATUH TEMPO (OVERDUE) ==================
        $overdue = Peminjaman::with('details.barang')
            ->where('status_transaksi', 'aktif')
            ->whereDate('tanggal_jatuh_tempo', '<', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('last_overdue_reminder_date')
                  ->orWhereDate('last_overdue_reminder_date', '<', $today);
            })
            ->get();

        $overdueSent = 0;
        foreach ($overdue as $pinjam) {
            $daysOverdue = $today->diffInDays($pinjam->tanggal_jatuh_tempo);
            Mail::to($pinjam->email)->send(new OverdueReminder($pinjam, $daysOverdue));
            $pinjam->update(['last_overdue_reminder_date' => $today]);
            $overdueSent++;
        }
        $this->info("Pengingat overdue dikirim ke " . $overdueSent . " peminjam.");
    }
}