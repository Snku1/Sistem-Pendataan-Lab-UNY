<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // Admin super (pengelola penuh)
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'nama' => 'Super Admin Developer',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Koordinator Laboratorium (koorlap)
        User::updateOrCreate(
            ['email' => 'koorlap@lab.com'],
            [
                'nama' => 'Koordinator Lab',
                'password' => Hash::make('koorlap123'),
                'role' => 'koorlap',
                'email_verified_at' => now(),
            ]
        );

        // Teknisi Laboratorium
        User::updateOrCreate(
            ['email' => 'teknisi@lab.com'],
            [
                'nama' => 'Teknisi Lab',
                'password' => Hash::make('teknisi123'),
                'role' => 'teknisi',
                'email_verified_at' => now(),
            ]
        );
    }
}