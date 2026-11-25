<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\SavingGoal;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PersonalFinanceIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private function createRegularUser(string $email = 'user@example.com', string $name = 'User Biasa'): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);
    }

    public function test_regular_user_can_crud_accounts_and_cannot_delete_account_with_transactions(): void
    {
        $user = $this->createRegularUser();
        $this->actingAs($user);

        // Create account
        $this->post(route('accounts.store'), [
            'name' => 'Bank BCA',
            'type' => 'bank',
            'balance' => 1500000,
        ])->assertRedirect(route('accounts.index'))->assertSessionHas('pesan');

        $account = Account::where('userid', $user->id)->where('name', 'Bank BCA')->firstOrFail();
        $this->assertEquals(1500000.0, (float) $account->balance);

        // Update account
        $this->put(route('accounts.update', $account->id), [
            'name' => 'Bank BCA Utama',
            'type' => 'bank',
            'balance' => 2000000,
        ])->assertRedirect(route('accounts.index'));

        $account->refresh();
        $this->assertSame('Bank BCA Utama', $account->name);
        $this->assertEquals(2000000.0, (float) $account->balance);

        // Attach a transaction to block deletion
        $category = Category::create([
            'userid' => $user->id,
            'name' => 'Makanan',
            'type' => 'expense',
        ]);

        Transaction::create([
            'userid' => $user->id,
            'accountid' => $account->id,
            'categoryid' => $category->id,
            'amount' => 50000,
            'description' => 'Makan siang',
            'type' => 'expense',
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $this->delete(route('accounts.destroy', $account->id))
            ->assertRedirect(route('accounts.index'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('accounts', ['id' => $account->id]);

        // Empty account can be deleted
        $emptyAccount = Account::create([
            'userid' => $user->id,
            'name' => 'GoPay',
            'type' => 'ewallet',
            'balance' => 100000,
        ]);

        $this->delete(route('accounts.destroy', $emptyAccount->id))
            ->assertRedirect(route('accounts.index'))
            ->assertSessionHas('pesan');
        $this->assertDatabaseMissing('accounts', ['id' => $emptyAccount->id]);
    }

    public function test_regular_user_can_crud_categories_and_transactions_sync_account_balance(): void
    {
        $user = $this->createRegularUser();
        $this->actingAs($user);

        // Create category
        $this->post(route('categories.store'), [
            'name' => 'Gaji Bulanan',
            'type' => 'income',
        ])->assertRedirect(route('categories.index'));

        $this->post(route('categories.store'), [
            'name' => 'Transportasi',
            'type' => 'expense',
        ])->assertRedirect(route('categories.index'));

        $incomeCat = Category::where('userid', $user->id)->where('name', 'Gaji Bulanan')->firstOrFail();
        $expenseCat = Category::where('userid', $user->id)->where('name', 'Transportasi')->firstOrFail();

        $account = Account::create([
            'userid' => $user->id,
            'name' => 'Dompet',
            'type' => 'cash',
            'balance' => 100000,
        ]);

        // Add income transaction -> balance 100000 + 500000 = 600000
        $this->post(route('transactions.store'), [
            'accountid' => $account->id,
            'categoryid' => $incomeCat->id,
            'type' => 'income',
            'amount' => 500000,
            'description' => 'Bonus Proyek',
            'transaction_date' => '2026-11-20',
        ])->assertRedirect('/transactions');

        $account->refresh();
        $this->assertEquals(600000.0, (float) $account->balance);

        // Add expense transaction -> balance 600000 - 150000 = 450000
        $this->post(route('transactions.store'), [
            'accountid' => $account->id,
            'categoryid' => $expenseCat->id,
            'type' => 'expense',
            'amount' => 150000,
            'description' => 'Bensin Motor',
            'transaction_date' => '2026-11-21',
        ])->assertRedirect('/transactions');

        $account->refresh();
        $this->assertEquals(450000.0, (float) $account->balance);

        $expenseTrx = Transaction::where('description', 'Bensin Motor')->firstOrFail();

        // View detail page
        $this->get(route('transactions.show', $expenseTrx->id))
            ->assertOk()
            ->assertSee('Bensin Motor')
            ->assertSee('Pengeluaran Besar');

        // Update expense from 150000 to 50000 -> balance 450000 + 150000 - 50000 = 550000
        $this->put(route('transactions.update', $expenseTrx->id), [
            'accountid' => $account->id,
            'categoryid' => $expenseCat->id,
            'type' => 'expense',
            'amount' => 50000,
            'description' => 'Bensin Pertalite',
            'transaction_date' => '2026-11-21',
        ])->assertRedirect('/transactions');

        $account->refresh();
        $this->assertEquals(550000.0, (float) $account->balance);

        // Delete expense -> balance 550000 + 50000 = 600000
        $this->delete(route('transactions.destroy', $expenseTrx->id))->assertRedirect('/transactions');
        $account->refresh();
        $this->assertEquals(600000.0, (float) $account->balance);
    }

    public function test_regular_user_can_crud_budgets_and_track_monthly_spending(): void
    {
        $user = $this->createRegularUser();
        $this->actingAs($user);

        $account = Account::create([
            'userid' => $user->id,
            'name' => 'Dompet',
            'type' => 'cash',
            'balance' => 2000000,
        ]);

        $foodCat = Category::create([
            'userid' => $user->id,
            'name' => 'Makan',
            'type' => 'expense',
        ]);

        $month = 11;
        $year = 2026;

        // Create budget
        $this->post(route('budgets.store'), [
            'categoryid' => $foodCat->id,
            'amount' => 1000000,
            'month' => $month,
            'year' => $year,
        ])->assertRedirect(route('budgets.index', ['month' => $month, 'year' => $year]));

        // Duplicate budget for same category+month+year should fail
        $this->post(route('budgets.store'), [
            'categoryid' => $foodCat->id,
            'amount' => 500000,
            'month' => $month,
            'year' => $year,
        ])->assertSessionHasErrors('categoryid');

        // Spend 850000 -> 85% -> Hampir Habis
        Transaction::create([
            'userid' => $user->id,
            'accountid' => $account->id,
            'categoryid' => $foodCat->id,
            'amount' => 850000,
            'description' => 'Belanja makan',
            'type' => 'expense',
            'transaction_date' => '2026-11-15',
        ]);

        $this->get(route('budgets.index', ['month' => $month, 'year' => $year]))
            ->assertOk()
            ->assertSee('Makan')
            ->assertSee('85%')
            ->assertSee('Hampir Habis');

        $budget = Budget::where('userid', $user->id)->firstOrFail();

        // Update budget to 800000 -> spent 850000 -> Melebihi Budget
        $this->put(route('budgets.update', $budget->id), [
            'categoryid' => $foodCat->id,
            'amount' => 800000,
            'month' => $month,
            'year' => $year,
        ])->assertRedirect(route('budgets.index', ['month' => $month, 'year' => $year]));

        $this->get(route('budgets.index', ['month' => $month, 'year' => $year]))
            ->assertOk()
            ->assertSee('Melebihi Budget');

        // Delete budget
        $this->delete(route('budgets.destroy', $budget->id))
            ->assertRedirect(route('budgets.index', ['month' => $month, 'year' => $year]));
        $this->assertDatabaseMissing('budgets', ['id' => $budget->id]);
    }

    public function test_regular_user_can_crud_saving_goals_and_transactions(): void
    {
        $user = $this->createRegularUser();
        $this->actingAs($user);

        // Create goal
        $this->post(route('savings.store'), [
            'name' => 'Beli Laptop',
            'target_amount' => 5000000,
            'current_amount' => 1000000,
            'target_date' => '2027-06-01',
        ])->assertRedirect(route('savings.index'));

        $goal = SavingGoal::where('userid', $user->id)->firstOrFail();
        $this->assertSame('active', $goal->status);

        // Deposit 4000000 -> reaches 5000000 -> status completed
        $this->post(route('savings.transactions.store', $goal->id), [
            'type' => 'deposit',
            'amount' => 4000000,
            'transaction_date' => '2026-11-25',
            'note' => 'Bonus akhir tahun',
        ])->assertRedirect(route('savings.show', $goal->id));

        $goal->refresh();
        $this->assertEquals(5000000.0, (float) $goal->current_amount);
        $this->assertSame('completed', $goal->status);

        // Withdraw exceeding balance should fail
        $this->post(route('savings.transactions.store', $goal->id), [
            'type' => 'withdraw',
            'amount' => 6000000,
            'transaction_date' => '2026-11-25',
        ])->assertSessionHasErrors('amount');

        // Delete deposit transaction -> rolls back to 1000000 and status active
        $savingTrx = $goal->savingTransactions()->firstOrFail();
        $this->delete(route('savings.transactions.destroy', $savingTrx->id))
            ->assertRedirect(route('savings.show', $goal->id));

        $goal->refresh();
        $this->assertEquals(1000000.0, (float) $goal->current_amount);
        $this->assertSame('active', $goal->status);
    }

    public function test_users_cannot_access_or_modify_another_users_financial_records(): void
    {
        $owner = $this->createRegularUser('owner@example.com', 'Owner');
        $intruder = $this->createRegularUser('intruder@example.com', 'Intruder');

        $account = Account::create([
            'userid' => $owner->id,
            'name' => 'Rahasia Owner',
            'type' => 'bank',
            'balance' => 500000,
        ]);

        $category = Category::create([
            'userid' => $owner->id,
            'name' => 'Kategori Owner',
            'type' => 'expense',
        ]);

        $transaction = Transaction::create([
            'userid' => $owner->id,
            'accountid' => $account->id,
            'categoryid' => $category->id,
            'amount' => 25000,
            'description' => 'Transaksi Rahasia Owner',
            'type' => 'expense',
            'transaction_date' => '2026-11-25',
        ]);

        $budget = Budget::create([
            'userid' => $owner->id,
            'categoryid' => $category->id,
            'amount' => 300000,
            'month' => 11,
            'year' => 2026,
        ]);

        $goal = SavingGoal::create([
            'userid' => $owner->id,
            'name' => 'Tabungan Owner',
            'target_amount' => 1000000,
            'current_amount' => 100000,
            'target_date' => '2027-01-01',
            'status' => 'active',
        ]);

        $this->actingAs($intruder);

        $this->get(route('transactions.index'))->assertOk()->assertDontSee('Transaksi Rahasia Owner');
        $this->get(route('transactions.show', $transaction->id))->assertNotFound();
        $this->get(route('transactions.edit', $transaction->id))->assertNotFound();
        $this->delete(route('transactions.destroy', $transaction->id))->assertNotFound();

        $this->get(route('accounts.edit', $account->id))->assertNotFound();
        $this->delete(route('accounts.destroy', $account->id))->assertNotFound();

        $this->get(route('categories.edit', $category->id))->assertNotFound();
        $this->delete(route('categories.destroy', $category->id))->assertNotFound();

        $this->get(route('budgets.edit', $budget->id))->assertNotFound();
        $this->delete(route('budgets.destroy', $budget->id))->assertNotFound();

        $this->get(route('savings.show', $goal->id))->assertNotFound();
        $this->delete(route('savings.destroy', $goal->id))->assertNotFound();
    }
}
