<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Administrator
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'        => 'Administrator FinTrack',
                'password'    => Hash::make('password123'),
                'role'        => 'admin',
                'phonenumber' => '081299887766',
            ]
        );

        // 2. Akun User Biasa
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name'        => 'Budi Pengguna',
                'password'    => Hash::make('password123'),
                'role'        => 'user',
                'phonenumber' => '081211223344',
            ]
        );

        // 3. Pastikan user lama juga memiliki role
        User::whereNull('role')->orWhere('role', '')->update(['role' => 'user']);
    }
}
