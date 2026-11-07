<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'customer_name',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'payment_method',
        'payment_status',
        'payment_reference',
        'amount_paid',
        'change_amount',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'discount' => 'float',
        'tax' => 'float',
        'total_amount' => 'float',
        'amount_paid' => 'float',
        'change_amount' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function getTotalProfitAttribute(): float
    {
        return $this->items->sum('profit');
    }
}
