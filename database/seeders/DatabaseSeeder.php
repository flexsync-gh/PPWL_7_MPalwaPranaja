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
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator FinTrack',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phonenumber' => '081299887766',
            ]
        );
        $admin->ensureStarterData();

        // 2. Akun User Biasa
        $regularUser = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Budi Pengguna',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'phonenumber' => '081211223344',
            ]
        );
        $regularUser->ensureStarterData();

        // Contoh data keuangan untuk user demo jika belum memiliki transaksi
        if (! $regularUser->transactions()->exists()) {
            $dompet = $regularUser->accounts()->first();
            $gajiCat = $regularUser->categories()->where('type', 'income')->first();
            $makanCat = $regularUser->categories()->where('name', 'Makanan & Minuman')->first();

            if ($dompet && $gajiCat && $makanCat) {
                $regularUser->transactions()->create([
                    'accountid' => $dompet->id,
                    'categoryid' => $gajiCat->id,
                    'amount' => 3500000,
                    'description' => 'Gaji Bulanan',
                    'type' => 'income',
                    'transaction_date' => now()->startOfMonth()->format('Y-m-d'),
                ]);

                $regularUser->transactions()->create([
                    'accountid' => $dompet->id,
                    'categoryid' => $makanCat->id,
                    'amount' => 150000,
                    'description' => 'Belanja Makan Mingguan',
                    'type' => 'expense',
                    'transaction_date' => now()->format('Y-m-d'),
                ]);

                $dompet->update(['balance' => 3350000]);

                $regularUser->budgets()->firstOrCreate(
                    [
                        'categoryid' => $makanCat->id,
                        'month' => (int) date('n'),
                        'year' => (int) date('Y'),
                    ],
                    ['amount' => 1500000]
                );
            }
        }

        if (! $regularUser->savingGoals()->exists()) {
            $goal = $regularUser->savingGoals()->create([
                'name' => 'Dana Darurat',
                'target_amount' => 10000000,
                'current_amount' => 1500000,
                'target_date' => now()->addMonths(6)->format('Y-m-d'),
                'status' => 'active',
            ]);

            $goal->savingTransactions()->create([
                'userid' => $regularUser->id,
                'amount' => 1500000,
                'type' => 'deposit',
                'note' => 'Setoran awal dana darurat',
                'transaction_date' => now()->format('Y-m-d'),
            ]);
        }

        // 3. Pastikan user lama juga memiliki role
        User::whereNull('role')->orWhere('role', '')->update(['role' => 'user']);

        // 4. Data Konten Edukasi Finansial & SEO FinTrack
        $this->call(FinancialSeoSeeder::class);
    }
}
