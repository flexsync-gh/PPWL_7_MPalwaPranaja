<x-mail::message>
# Notifikasi Login Baru

Halo **{{ $user->name }}**,

Kami mendeteksi aktivitas login baru pada akun **{{ config('app.name', 'FinTrack') }}** Anda. Berikut detail aktivitas tersebut:

<x-mail::panel>
- **Email Akun:** {{ $user->email }}
- **Waktu Login:** {{ $waktuLogin }}
- **Alamat IP:** {{ $ipAddress }}
</x-mail::panel>

Jika aktivitas ini dilakukan oleh Anda, Anda dapat mengabaikan email ini. Jika bukan Anda, segera amankan akun Anda melalui halaman profil.

<x-mail::button :url="$dashboardUrl">
Ke Dashboard
</x-mail::button>

Terima kasih,<br>
{{ config('app.name', 'FinTrack') }}
</x-mail::message>
