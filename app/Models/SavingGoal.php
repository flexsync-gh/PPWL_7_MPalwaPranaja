<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SavingGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'userid',
        'name',
        'target_amount',
        'current_amount',
        'start_date',
        'target_date',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'current_amount' => 'decimal:2',
            'start_date' => 'date',
            'target_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userid');
    }

    public function savingTransactions(): HasMany
    {
        return $this->hasMany(SavingTransaction::class, 'saving_goal_id');
    }

    public function syncStatus(): void
    {
        $newStatus = (float) $this->current_amount >= (float) $this->target_amount ? 'completed' : 'active';
        if ($this->status !== $newStatus) {
            $this->status = $newStatus;
            $this->save();
        }
    }

    public function getProgressPercentAttribute(): float
    {
        $target = (float) $this->target_amount;
        if ($target <= 0) {
            return 0.0;
        }

        return min(100.0, ((float) $this->current_amount / $target) * 100);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0.0, (float) $this->target_amount - (float) $this->current_amount);
    }
}
