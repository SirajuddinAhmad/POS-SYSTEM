<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'product_id',
        'product_code',
        'product_name',
        'cost_price',
        'selling_price',
        'quantity',
        'subtotal',
        'profit',
    ];

    protected $casts = [
        'cost_price' => 'float',
        'selling_price' => 'float',
        'quantity' => 'integer',
        'subtotal' => 'float',
        'profit' => 'float',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
