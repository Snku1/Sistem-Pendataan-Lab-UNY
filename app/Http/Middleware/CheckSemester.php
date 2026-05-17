<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Semester;

class CheckSemester
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah session 'active_semester_id' sudah ada
        if (!Session::has('active_semester_id')) {
            // Belum ada pilihan semester, arahkan ke halaman pilih semester
            return redirect()->route('pilih-semester');
        }

        $activeId = Session::get('active_semester_id');

        // Jika nilai 0, berarti "Semua Semester" - langsung diizinkan
        if ($activeId == 0) {
            return $next($request);
        }

        // Pastikan semester yang tersimpan masih valid
        if (!Semester::where('id_semester', $activeId)->exists()) {
            // Semester tidak valid, hapus session dan redirect ke pilih semester
            Session::forget('active_semester_id');
            return redirect()->route('pilih-semester')
                ->with('error', 'Semester yang dipilih tidak valid. Silakan pilih semester lagi.');
        }

        return $next($request);
    }
}