<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'userid', 'accountid', 'categoryid', 'amount', 'description', 'type', 'transaction_date'
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'categoryid');
    }
}