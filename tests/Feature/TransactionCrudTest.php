<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Account;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $account;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'phonenumber' => '08123456789'
        ]);
        $this->account = Account::create([
            'userid' => $this->user->id,
            'name' => 'BCA',
            'type' => 'Bank',
            'balance' => 5000000
        ]);
        $this->category = Category::create([
            'userid' => $this->user->id,
            'name' => 'Makanan',
            'type' => 'Expense'
        ]);
    }
    public function test_can_view_index_and_create_pages(): void
    {
        $res = $this->get('/transactions');
        $res->assertStatus(200);
        $res->assertSee('Tambah Transaksi');

        $resCreate = $this->get('/transactions/create');
        $resCreate->assertStatus(200);
        $resCreate->assertSee('Tambah Transaksi');
    }

    public function test_can_store_update_and_delete_transaction(): void
    {
        $cat = Category::first();
        $acc = Account::first();

        // 1. Create / Store
        $postRes = $this->post('/transactions', [
            'categoryid' => $cat->id,
            'accountid' => $acc->id,
            'amount' => 55000,
            'description' => 'Test Transaksi Otomatis',
            'type' => 'Expense',
            'transaction_date' => '2026-09-28'
        ]);
        $postRes->assertRedirect('/transactions');

        $trx = Transaction::where('description', 'Test Transaksi Otomatis')->first();
        $this->assertNotNull($trx);

        // 2. Edit page
        $editRes = $this->get('/transactions/' . $trx->id . '/edit');
        $editRes->assertStatus(200);
        $editRes->assertSee('Edit Transaksi');
        $editRes->assertSee('Test Transaksi Otomatis');

        // 3. Update
        $putRes = $this->put('/transactions/' . $trx->id, [
            'categoryid' => $cat->id,
            'accountid' => $acc->id,
            'amount' => 77000,
            'description' => 'Test Transaksi Otomatis Terupdate',
            'type' => 'Expense',
            'transaction_date' => '2026-09-28'
        ]);
        $putRes->assertRedirect('/transactions');

        $trx->refresh();
        $this->assertEquals('Test Transaksi Otomatis Terupdate', $trx->description);
        $this->assertEquals(77000, $trx->amount);

        // 4. Delete
        $delRes = $this->delete('/transactions/' . $trx->id);
        $delRes->assertRedirect('/transactions');

        $this->assertNull(Transaction::find($trx->id));
    }
}
