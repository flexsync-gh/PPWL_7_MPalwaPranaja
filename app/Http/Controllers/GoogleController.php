<?php

namespace App\Http\Controllers;

use App\Jobs\KirimEmailSelamatDatang;
use App\Mail\LoginBaruMail;
use App\Models\User;
use App\Notifications\UserBaruNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{
    /**
     * Arahkan pengguna ke halaman autentikasi Google OAuth dengan hint domain gmail.com.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')
            ->with(['hd' => 'gmail.com'])
            ->redirect();
    }

    /**
     * Tangani callback dari Google OAuth, validasi domain email, dan login/daftarkan user.
     */
    public function callback(Request $request): RedirectResponse
    {
        if ($request->filled('error')) {
            return redirect()->route('login')->with('error', 'Login dengan Google dibatalkan.');
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            return redirect()->route('login')->with('error', 'Gagal memverifikasi akun Google. Silakan coba lagi.');
        }

        $email = strtolower((string) $googleUser->getEmail());

        if (! str_ends_with($email, '@gmail.com')) {
            return redirect()->route('login')->with('error', 'Login SSO ditolak! Hanya email berakhiran @gmail.com yang diperbolehkan.');
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        $isNewUser = false;

        if ($user) {
            $user->google_id = $user->google_id ?: $googleUser->getId();
            $user->avatar = $googleUser->getAvatar() ?: $user->avatar;
            $user->save();
        } else {
            $user = User::create([
                'name' => $googleUser->getName() ?: explode('@', $email)[0],
                'email' => $email,
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => null,
                'role' => 'user',
            ]);

            $user->ensureStarterData();
            $isNewUser = true;
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        if ($isNewUser) {
            KirimEmailSelamatDatang::dispatch($user);

            $admins = User::where('role', 'admin')->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new UserBaruNotification($user, 'Google SSO'));
            }
        }

        Mail::to($user->email)->queue(new LoginBaruMail(
            $user,
            now()->format('d M Y H:i:s'),
            (string) $request->ip(),
        ));

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.index'))->with('pesan', 'Selamat datang Admin, '.$user->name.'!');
        }

        return redirect()->intended(route('dashboard'))->with('pesan', 'Berhasil login dengan Google sebagai '.$user->name.'!');
    }
}
