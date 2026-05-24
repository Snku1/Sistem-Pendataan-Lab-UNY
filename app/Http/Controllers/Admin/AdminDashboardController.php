<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Lokasi;
use App\Models\PenanggungJawab;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalLokasi = Lokasi::count();
        $totalPenanggungJawab = PenanggungJawab::count();

        return view('admin.dashboard', compact('totalUsers', 'totalLokasi', 'totalPenanggungJawab'));
    }
}