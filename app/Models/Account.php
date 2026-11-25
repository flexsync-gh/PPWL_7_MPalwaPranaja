<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    public const MAX_PER_USER = 20;

    /**
     * Daftar tipe akun keuangan yang tersedia.
     *
     * @var array<string, string>
     */
    public const TYPES = [
        'cash' => 'Tunai (Cash)',
        'bank' => 'Bank',
        'ewallet' => 'E-Wallet',
    ];

    protected $fillable = [
        'userid',
        'name',
        'type',
        'balance',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userid');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'accountid');
    }

    public function getTypeLabelAttribute(): string
    {
        $key = strtolower((string) $this->type);

        return self::TYPES[$key] ?? ucfirst((string) $this->type);
    }

    public function getNormalizedTypeAttribute(): string
    {
        $key = strtolower((string) $this->type);

        return array_key_exists($key, self::TYPES) ? $key : 'cash';
    }
}
