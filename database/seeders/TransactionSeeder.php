<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Dummy User
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'phonenumber' => '081234567890',
        ]);

        // 2. Dummy Accounts
        $account1 = Account::create([
            'userid' => $user->id,
            'name' => 'BCA Rekening',
            'type' => 'Bank',
            'balance' => 10000000,
        ]);

        // 3. Dummy Categories
        $cat1 = Category::create(['userid' => $user->id, 'name' => 'Makanan & Minuman', 'type' => 'Expense']);
        $cat2 = Category::create(['userid' => $user->id, 'name' => 'Gaji Bulanan', 'type' => 'Income']);
        $cat3 = Category::create(['userid' => $user->id, 'name' => 'Transportasi', 'type' => 'Expense']);
        $cat4 = Category::create(['userid' => $user->id, 'name' => 'Hiburan', 'type' => 'Expense']);

        $categoryIds = [$cat1->id, $cat2->id, $cat3->id, $cat4->id];

        // 4. Soal 1: 20 Data Transaksi
        for ($i = 0; $i < 20; $i++) {
            Transaction::create([
                'userid' => $user->id,
                'accountid' => $account1->id,
                'categoryid' => fake()->randomElement($categoryIds),
                'amount' => fake()->numberBetween(15000, 750000),
                'description' => fake()->sentence(4),
                'type' => fake()->randomElement(['Income', 'Expense']),
                'transaction_date' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            ]);
        }
    }
}