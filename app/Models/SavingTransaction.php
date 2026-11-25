<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavingTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'saving_goal_id',
        'userid',
        'amount',
        'type',
        'note',
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

    public function savingGoal(): BelongsTo
    {
        return $this->belongsTo(SavingGoal::class, 'saving_goal_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userid');
    }
}
