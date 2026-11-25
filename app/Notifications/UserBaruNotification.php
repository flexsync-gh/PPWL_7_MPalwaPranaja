<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserBaruNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public User $newUser,
        public string $metodePendaftaran = 'Google SSO',
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Notifikasi User Baru Terdaftar: '.$this->newUser->name)
            ->greeting('Halo Admin '.$notifiable->name.',')
            ->line('Ada pengguna baru yang baru saja mendaftar ke aplikasi '.config('app.name', 'FinTrack').'.')
            ->line('Nama: '.$this->newUser->name)
            ->line('Email: '.$this->newUser->email)
            ->line('Metode Pendaftaran: '.$this->metodePendaftaran)
            ->line('Waktu Daftar: '.now()->format('d M Y H:i:s'))
            ->action('Buka Admin Panel', route('admin.index'))
            ->line('Silakan periksa panel admin untuk mengelola pengguna dan hak akses.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->newUser->id,
            'name' => $this->newUser->name,
            'email' => $this->newUser->email,
            'metode' => $this->metodePendaftaran,
            'message' => "User baru {$this->newUser->name} ({$this->newUser->email}) mendaftar melalui {$this->metodePendaftaran}.",
            'registered_at' => now()->format('d/m/Y H:i:s'),
        ];
    }
}
