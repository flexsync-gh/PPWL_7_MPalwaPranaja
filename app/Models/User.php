<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'google_id', 'password', 'role', 'phonenumber', 'profile_photo', 'avatar', 'bio', 'theme_color'])]
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
        'google_id',
        'password',
        'role',
        'phonenumber',
        'profile_photo',
        'avatar',
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

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'userid');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'userid');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'userid');
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class, 'userid');
    }

    public function savingGoals(): HasMany
    {
        return $this->hasMany(SavingGoal::class, 'userid');
    }

    public function savingTransactions(): HasMany
    {
        return $this->hasMany(SavingTransaction::class, 'userid');
    }

    /**
     * Buat akun dan kategori bawaan untuk pengguna baru agar langsung bisa mencatat transaksi dan budget.
     */
    public function ensureStarterData(): void
    {
        if (! $this->accounts()->exists()) {
            $this->accounts()->create([
                'name' => 'Dompet',
                'type' => 'cash',
                'balance' => 0,
            ]);
        }

        if (! $this->categories()->exists()) {
            $starterCategories = [
                ['name' => 'Gaji', 'type' => 'income'],
                ['name' => 'Pemasukan Lainnya', 'type' => 'income'],
                ['name' => 'Makanan & Minuman', 'type' => 'expense'],
                ['name' => 'Transportasi', 'type' => 'expense'],
                ['name' => 'Pengeluaran Umum', 'type' => 'expense'],
            ];

            foreach ($starterCategories as $category) {
                $this->categories()->create($category);
            }
        }
    }

    /**
     * Periksa apakah pengguna memiliki role admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Dapatkan URL publik foto profil pengguna jika tersedia.
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        if ($this->profile_photo) {
            return asset('storage/'.ltrim($this->profile_photo, '/'));
        }

        if ($this->avatar) {
            return $this->avatar;
        }

        return null;
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
