<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil dan kustomisasi pengguna.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $themeColors = User::THEME_COLORS;

        return view('profile', compact('user', 'themeColors'));
    }

    /**
     * Perbarui informasi profil, tema, dan unggah foto profil pengguna.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:70'],
            'email' => [
                'required',
                'string',
                'email',
                'max:80',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phonenumber' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'phonenumber')->ignore($user->id),
            ],
            'bio' => ['nullable', 'string', 'max:500'],
            'theme_color' => ['required', 'string', Rule::in(array_keys(User::THEME_COLORS))],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama maksimal 70 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 80 karakter.',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
            'phonenumber.max' => 'Nomor telepon maksimal 20 karakter.',
            'phonenumber.unique' => 'Nomor telepon sudah digunakan oleh pengguna lain.',
            'bio.max' => 'Bio maksimal 500 karakter.',
            'theme_color.required' => 'Warna tema profil wajib dipilih.',
            'theme_color.in' => 'Warna tema profil tidak valid.',
            'profile_photo.image' => 'File foto profil harus berupa gambar yang valid.',
            'profile_photo.mimes' => 'Format foto profil harus JPG, JPEG, PNG, atau WEBP.',
            'profile_photo.max' => 'Ukuran foto profil maksimal 2 MB.',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phonenumber' => filled($validated['phonenumber'] ?? null) ? $validated['phonenumber'] : null,
            'bio' => filled($validated['bio'] ?? null) ? $validated['bio'] : null,
            'theme_color' => $validated['theme_color'],
        ];

        if ($request->hasFile('profile_photo')) {
            $newPhotoPath = $request->file('profile_photo')->store('profile-photos', 'public');

            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $data['profile_photo'] = $newPhotoPath;
        }

        $user->update($data);

        return redirect()->route('profile')->with('pesan', 'Profil Anda berhasil diperbarui!');
    }

    /**
     * Hapus foto profil pengguna yang tersimpan di storage.
     */
    public function destroyPhoto(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user->profile_photo) {
            return redirect()->route('profile')->with('error', 'Anda belum memiliki foto profil untuk dihapus.');
        }

        if (Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->update(['profile_photo' => null]);

        return redirect()->route('profile')->with('pesan', 'Foto profil berhasil dihapus.');
    }

    /**
     * Perbarui password pengguna dari halaman profil.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validateWithBag('passwordUpdate', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile')->with('pesan', 'Password akun Anda berhasil diperbarui!');
    }
}
