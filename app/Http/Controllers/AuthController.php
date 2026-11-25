<?php

namespace App\Http\Controllers;

use App\Jobs\KirimEmailSelamatDatang;
use App\Mail\LoginBaruMail;
use App\Models\User;
use App\Notifications\UserBaruNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi user (Login).
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            Mail::to($user->email)->queue(new LoginBaruMail(
                $user,
                now()->format('d M Y H:i:s'),
                (string) $request->ip(),
            ));

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.index'))->with('pesan', 'Selamat datang Admin, '.$user->name.'!');
            }

            return redirect()->intended(route('dashboard'))->with('pesan', 'Selamat datang, '.$user->name.'!');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang dimasukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Tampilkan form registrasi.
     */
    public function showRegisterForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Proses pendaftaran user baru (Register) beserta akun & kategori awal.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:70',
            'email' => 'required|string|email|max:80|unique:users,email',
            'phonenumber' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama maksimal 70 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar. Silakan gunakan email lain atau login.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phonenumber' => $validated['phonenumber'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        $user->ensureStarterData();

        KirimEmailSelamatDatang::dispatch($user);

        $admins = User::where('role', 'admin')->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new UserBaruNotification($user, 'Form Registrasi'));
        }

        Auth::login($user);
        $request->session()->regenerate();

        Mail::to($user->email)->queue(new LoginBaruMail(
            $user,
            now()->format('d M Y H:i:s'),
            (string) $request->ip(),
        ));

        return redirect()->route('dashboard')->with('pesan', 'Registrasi berhasil! Akun, kategori dasar, dan fitur keuangan Anda sudah siap digunakan.');
    }

    /**
     * Proses logout user.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('pesan', 'Anda telah berhasil logout.');
    }
}
