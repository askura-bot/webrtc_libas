<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'petugas@polisi.go.id'],
            [
                'name'     => 'Petugas Kepolisian',
                'password' => Hash::make('petugas123'),
                'role'     => 'officer',
            ]
        );

        // Akun admin
        User::updateOrCreate(
            ['email' => 'admin@polisi.go.id'],
            [
                'name'     => 'Admin Command Center',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]
        );
    }
}
