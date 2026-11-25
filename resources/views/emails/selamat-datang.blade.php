<x-mail::message>
# Selamat Datang, {{ $user->name }}!

Terima kasih telah bergabung dengan **{{ config('app.name', 'FinTrack') }}**. Akun Anda telah berhasil dibuat dan siap digunakan untuk mencatat transaksi, mengatur budget bulanan, serta memantau target tabungan Anda.

<x-mail::panel>
**Detail Akun Anda:**
- **Nama:** {{ $user->name }}
- **Email:** {{ $user->email }}
- **Role:** {{ strtoupper($user->role) }}
</x-mail::panel>

<x-mail::button :url="$dashboardUrl">
Buka Dashboard
</x-mail::button>

Jika Anda memiliki pertanyaan atau kendala, silakan hubungi tim administrator kami.

Salam hangat,<br>
{{ config('app.name', 'FinTrack') }}
</x-mail::message>
