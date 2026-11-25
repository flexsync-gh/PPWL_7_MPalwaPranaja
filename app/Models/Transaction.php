<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'userid',
        'accountid',
        'categoryid',
        'amount',
        'description',
        'type',
        'transaction_date',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Transaction $transaction): void {
            if (! empty($transaction->type)) {
                $transaction->type = strtolower($transaction->type);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userid');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'accountid');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'categoryid');
    }

    public function isIncome(): bool
    {
        return strtolower((string) $this->type) === 'income';
    }

    /**
     * Status transaksi seperti fungsi SQL Finote (Pemasukan / Pengeluaran Besar / Pengeluaran Kecil).
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->isIncome()) {
            return 'Pemasukan';
        }

        return (float) $this->amount >= 100000 ? 'Pengeluaran Besar' : 'Pengeluaran Kecil';
    }
}
