<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthAndRbacTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Uji Route sederhana /hello (MIDDLEWARE_RBAC.pdf Langkah 1).
     */
    public function test_hello_route_works(): void
    {
        $response = $this->get('/hello');
        $response->assertStatus(200);
        $response->assertSee('Halo, ini halaman percobaan route!');
    }

    /**
     * Uji Guest dialihkan ke login saat akses route terproteksi auth.
     */
    public function test_guest_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/profile')->assertRedirect('/login');
        $this->get('/transactions')->assertRedirect('/login');
        $this->get('/accounts')->assertRedirect('/login');
        $this->get('/categories')->assertRedirect('/login');
        $this->get('/budgets')->assertRedirect('/login');
        $this->get('/savings')->assertRedirect('/login');
        $this->get('/admin')->assertRedirect('/login');
    }

    /**
     * Uji Form Login dan Form Registrasi dapat diakses publik (AUTH_2.pdf).
     */
    public function test_guest_can_view_login_and_register(): void
    {
        $loginRes = $this->get('/login');
        $loginRes->assertStatus(200);
        $loginRes->assertSee('Login Pengguna');

        $regRes = $this->get('/register');
        $regRes->assertStatus(200);
        $regRes->assertSee('Daftar Akun Baru');
    }

    /**
     * Uji Registrasi Pengguna Baru: default role 'user' dan otomatis mendapat akun & kategori awal.
     */
    public function test_user_registration_and_default_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Mahasiswa Baru',
            'email' => 'mhs@example.com',
            'phonenumber' => '081234567890',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = User::where('email', 'mhs@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('user', $user->role);
        $this->assertTrue($user->accounts()->exists());
        $this->assertTrue($user->categories()->exists());
    }

    /**
     * Uji Login dengan kredensial benar dan salah (AUTH_2.pdf).
     */
    public function test_user_login_validation(): void
    {
        $user = User::create([
            'name' => 'Testing User',
            'email' => 'testlogin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        // Login gagal (password salah)
        $failRes = $this->post('/login', [
            'email' => 'testlogin@example.com',
            'password' => 'wrongpassword',
        ]);
        $failRes->assertSessionHasErrors('email');
        $this->assertGuest();

        // Login berhasil
        $successRes = $this->post('/login', [
            'email' => 'testlogin@example.com',
            'password' => 'password123',
        ]);
        $successRes->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Uji Akses User Biasa: Dapat mengakses & mengelola semua fitur keuangan pribadi, tapi DITOLAK dari /admin.
     */
    public function test_regular_user_access_and_rbac_restrictions(): void
    {
        $regularUser = User::create([
            'name' => 'User Biasa',
            'email' => 'regular@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        $this->actingAs($regularUser);

        // Halaman yang BOLEH diakses oleh User Biasa (Keuangan pribadi unlocked)
        $this->get('/dashboard')->assertStatus(200)->assertSee('Selamat Datang');
        $this->get('/profile')->assertStatus(200)->assertSee('Profil Pengguna');
        $this->get('/transactions')->assertStatus(200)->assertSee('Tambah Transaksi');
        $this->get('/transactions/create')->assertStatus(200)->assertSee('Tambah Transaksi');
        $this->get('/accounts')->assertStatus(200)->assertSee('Akun Keuangan');
        $this->get('/categories')->assertStatus(200)->assertSee('Kategori Transaksi');
        $this->get('/budgets')->assertStatus(200)->assertSee('Budget Bulanan');
        $this->get('/savings')->assertStatus(200)->assertSee('Target Tabungan');

        // Halaman yang DITOLAK oleh middleware isAdmin
        $adminRes = $this->get('/admin');
        $adminRes->assertRedirect('/');
        $adminRes->assertSessionHas('error');

        $roleRes = $this->post("/admin/users/{$regularUser->id}/role", ['role' => 'admin']);
        $roleRes->assertRedirect('/');
        $roleRes->assertSessionHas('error');
    }

    /**
     * Uji Akses Admin: Bisa akses /admin dan seluruh fitur keuangan.
     */
    public function test_admin_access(): void
    {
        $adminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->actingAs($adminUser);

        // Admin berhasil membuka /admin
        $adminRes = $this->get('/admin');
        $adminRes->assertStatus(200);
        $adminRes->assertSee('Halo Admin');

        // Admin berhasil membuka /transactions/create
        $createRes = $this->get('/transactions/create');
        $createRes->assertStatus(200);
        $createRes->assertSee('Tambah Transaksi');
    }

    /**
     * Uji Logout Pengguna (AUTH_2.pdf).
     */
    public function test_user_can_logout(): void
    {
        $user = User::create([
            'name' => 'User Logout',
            'email' => 'logout@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        $this->actingAs($user);
        $this->assertAuthenticated();

        $res = $this->post('/logout');
        $res->assertRedirect('/login');
        $this->assertGuest();
    }
}
