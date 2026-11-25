<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    public const MAX_PER_USER = 50;

    /**
     * @var array<string, string>
     */
    public const TYPES = [
        'expense' => 'Expense (Pengeluaran)',
        'income' => 'Income (Pemasukan)',
    ];

    protected $fillable = ['userid', 'name', 'category_seo', 'type'];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Category $category): void {
            if (! empty($category->type)) {
                $category->type = strtolower($category->type);
            }

            if (empty($category->category_seo) && ! empty($category->name)) {
                $category->category_seo = Str::slug($category->name);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userid');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'categoryid');
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class, 'categoryid');
    }
}
