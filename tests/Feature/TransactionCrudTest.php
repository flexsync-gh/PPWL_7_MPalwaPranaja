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
            'phonenumber' => '08123456789',
            'role' => 'admin'
        ]);
        $this->actingAs($this->user);
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

        // 1. Create / Store with flash message (BAB 6.3 & Tugas 1)
        $postRes = $this->post('/transactions', [
            'categoryid' => $cat->id,
            'accountid' => $acc->id,
            'amount' => 55000,
            'description' => 'Test Transaksi Otomatis',
            'type' => 'Expense',
            'transaction_date' => '2026-09-28'
        ]);
        $postRes->assertRedirect('/transactions');
        $postRes->assertSessionHas('pesan', 'Data Transaksi Berhasil Disimpan');

        $trx = Transaction::where('description', 'Test Transaksi Otomatis')->first();
        $this->assertNotNull($trx);

        // 2. Edit page
        $editRes = $this->get('/transactions/' . $trx->id . '/edit');
        $editRes->assertStatus(200);
        $editRes->assertSee('Edit Transaksi');
        $editRes->assertSee('Test Transaksi Otomatis');

        // 3. Update with flash message (Tugas 1)
        $putRes = $this->put('/transactions/' . $trx->id, [
            'categoryid' => $cat->id,
            'accountid' => $acc->id,
            'amount' => 77000,
            'description' => 'Test Transaksi Otomatis Terupdate',
            'type' => 'Expense',
            'transaction_date' => '2026-09-28'
        ]);
        $putRes->assertRedirect('/transactions');
        $putRes->assertSessionHas('pesan', 'Data Transaksi Berhasil Diupdate');

        $trx->refresh();
        $this->assertEquals('Test Transaksi Otomatis Terupdate', $trx->description);
        $this->assertEquals(77000, $trx->amount);

        // 4. Delete with flash message (Tugas 1)
        $delRes = $this->delete('/transactions/' . $trx->id);
        $delRes->assertRedirect('/transactions');
        $delRes->assertSessionHas('pesan', 'Data Transaksi Berhasil Dihapus');

        $this->assertNull(Transaction::find($trx->id));
    }

    public function test_validation_errors_in_indonesian(): void
    {
        $res = $this->post('/transactions', []);
        $res->assertSessionHasErrors([
            'transaction_date',
            'categoryid',
            'accountid',
            'type',
            'amount',
            'description'
        ]);

        $errors = session('errors')->getMessages();
        $this->assertEquals('Tanggal transaksi wajib diisi.', $errors['transaction_date'][0]);
        $this->assertEquals('Nominal wajib diisi.', $errors['amount'][0]);
        $this->assertEquals('Deskripsi transaksi wajib diisi.', $errors['description'][0]);
    }

    public function test_search_feature_and_pagination(): void
    {
        for ($i = 1; $i <= 7; $i++) {
            Transaction::create([
                'userid' => $this->user->id,
                'accountid' => $this->account->id,
                'categoryid' => $this->category->id,
                'amount' => 10000 * $i,
                'description' => 'Transaksi Unik ' . $i,
                'type' => 'Expense',
                'transaction_date' => '2026-10-01'
            ]);
        }

        // Test pagination on index (5 items per page)
        $indexRes = $this->get('/transactions');
        $indexRes->assertStatus(200);
        $indexRes->assertSee('Jumlah Transaksi:');

        // Test search found
        $searchRes = $this->get('/transactions/search?kata=Unik');
        $searchRes->assertStatus(200);
        $searchRes->assertSee('Ditemukan');
        $searchRes->assertSee('Unik');

        // Test search not found
        $notFoundRes = $this->get('/transactions/search?kata=KataTidakDitemukan12345');
        $notFoundRes->assertStatus(200);
        $notFoundRes->assertSee('tidak ditemukan');
    }
}
