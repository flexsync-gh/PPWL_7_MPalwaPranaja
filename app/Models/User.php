<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'phonenumber', 'profile_photo', 'bio', 'theme_color'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Pilihan warna tema profil yang tersedia (Bootstrap 5 contextual colors).
     *
     * @var array<string, string>
     */
    public const THEME_COLORS = [
        'primary' => 'Biru (Primary)',
        'success' => 'Hijau (Success)',
        'info' => 'Biru Muda (Info)',
        'warning' => 'Kuning (Warning)',
        'danger' => 'Merah (Danger)',
        'dark' => 'Gelap (Dark)',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phonenumber',
        'profile_photo',
        'bio',
        'theme_color',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Dapatkan URL publik foto profil pengguna jika tersedia.
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (! $this->profile_photo) {
            return null;
        }

        return asset('storage/'.ltrim($this->profile_photo, '/'));
    }

    /**
     * Dapatkan warna tema profil yang valid untuk avatar dan aksen UI.
     */
    public function getAvatarColorAttribute(): string
    {
        if ($this->theme_color && array_key_exists($this->theme_color, self::THEME_COLORS)) {
            return $this->theme_color;
        }

        return $this->role === 'admin' ? 'danger' : 'primary';
    }

    /**
     * Dapatkan inisial nama pengguna (maksimal 2 karakter) untuk avatar default.
     */
    public function getInitialsAttribute(): string
    {
        $words = preg_split('/\s+/', trim($this->name)) ?: [];
        $initials = '';

        foreach (array_slice($words, 0, 2) as $word) {
            if ($word !== '') {
                $initials .= mb_strtoupper(mb_substr($word, 0, 1));
            }
        }

        return $initials !== '' ? $initials : 'U';
    }
}
