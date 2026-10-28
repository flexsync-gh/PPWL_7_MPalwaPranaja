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
     * Uji Guest dialihkan ke login saat akses route terproteksi auth (MIDDLEWARE_RBAC.pdf Langkah 3).
     */
    public function test_guest_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/profile')->assertRedirect('/login');
        $this->get('/transactions')->assertRedirect('/login');
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
     * Uji Registrasi Pengguna Baru dan default role adalah 'user' (MIDDLEWARE_RBAC.pdf & AUTH_2.pdf).
     */
    public function test_user_registration_and_default_role(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Mahasiswa Baru',
            'email'                 => 'mhs@example.com',
            'phonenumber'           => '081234567890',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = User::where('email', 'mhs@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('user', $user->role);
    }

    /**
     * Uji Login dengan kredensial benar dan salah (AUTH_2.pdf).
     */
    public function test_user_login_validation(): void
    {
        $user = User::create([
            'name'     => 'Testing User',
            'email'    => 'testlogin@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'user',
        ]);

        // Login gagal (password salah)
        $failRes = $this->post('/login', [
            'email'    => 'testlogin@example.com',
            'password' => 'wrongpassword',
        ]);
        $failRes->assertSessionHasErrors('email');
        $this->assertGuest();

        // Login berhasil
        $successRes = $this->post('/login', [
            'email'    => 'testlogin@example.com',
            'password' => 'password123',
        ]);
        $successRes->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Uji Akses User Biasa: Bisa akses dashboard, profile, transactions tapi DITOLAK dari /admin (MIDDLEWARE_RBAC.pdf).
     */
    public function test_regular_user_access_and_rbac_restrictions(): void
    {
        $regularUser = User::create([
            'name'     => 'User Biasa',
            'email'    => 'regular@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'user',
        ]);

        $this->actingAs($regularUser);

        // Halaman yang BOLEH diakses
        $this->get('/dashboard')->assertStatus(200)->assertSee('Selamat Datang');
        $this->get('/profile')->assertStatus(200)->assertSee('Profil Pengguna');
        $this->get('/transactions')->assertStatus(200);

        // Halaman yang DITOLAK oleh middleware isAdmin
        $adminRes = $this->get('/admin');
        $adminRes->assertRedirect('/');
        $adminRes->assertSessionHas('error');

        // Fitur tambah transaksi juga ditolak untuk user biasa
        $createRes = $this->get('/transactions/create');
        $createRes->assertRedirect('/');
        $createRes->assertSessionHas('error');
    }

    /**
     * Uji Akses Admin: Bisa akses /admin dan fitur create/edit/delete (MIDDLEWARE_RBAC.pdf).
     */
    public function test_admin_access(): void
    {
        $adminUser = User::create([
            'name'     => 'Super Admin',
            'email'    => 'superadmin@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
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
            'name'     => 'User Logout',
            'email'    => 'logout@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'user',
        ]);

        $this->actingAs($user);
        $this->assertAuthenticated();

        $res = $this->post('/logout');
        $res->assertRedirect('/login');
        $this->assertGuest();
    }
}
