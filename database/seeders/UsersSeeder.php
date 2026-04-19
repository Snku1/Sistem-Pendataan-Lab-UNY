<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // Admin developer
        User::create([
            'nama' => 'Super Admin Developer',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Admin penanggung jawab lab (Dr. Ponco Walipranoto)
        User::create([
            'nama' => 'Dr. Ponco Walipranoto, S.Pd.T., M.Pd.',
            'email' => 'poncowali@uny.ac.id',
            'password' => Hash::make('labav2024'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Admin teknisi
        User::create([
            'nama' => 'Siswi Dwi Ayuriyanti',
            'email' => 'siswidwiayuriyanti@uny.ac.id',
            'password' => Hash::make('teknisi123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }
}