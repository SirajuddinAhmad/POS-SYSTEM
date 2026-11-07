<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'expense_date',
        'category',
        'title',
        'amount',
        'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
