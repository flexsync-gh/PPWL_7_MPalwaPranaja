<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Proses autentikasi user (Login).
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return redirect()->intended(route('admin.index'))->with('pesan', 'Selamat datang Admin, ' . Auth::user()->name . '!');
            }

            return redirect()->intended(route('dashboard'))->with('pesan', 'Selamat datang, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang dimasukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Tampilkan form registrasi.
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    /**
     * Proses pendaftaran user baru (Register).
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:70',
            'email'        => 'required|string|email|max:80|unique:users,email',
            'phonenumber'  => 'nullable|string|max:20',
            'password'     => 'required|string|min:6|confirmed',
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'name.max'          => 'Nama maksimal 70 karakter.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah terdaftar. Silakan gunakan email lain atau login.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal harus 6 karakter.',
            'password.confirmed'=> 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'phonenumber' => $validated['phonenumber'] ?? null,
            'password'    => Hash::make($validated['password']),
            'role'        => 'user', // Default role adalah 'user' sesuai modul
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('pesan', 'Registrasi berhasil! Akun Anda aktif dengan role: User.');
    }

    /**
     * Proses logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('pesan', 'Anda telah berhasil logout.');
    }
}
