<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'code',
        'barcode',
        'name',
        'description',
        'cost_price',
        'selling_price',
        'stock',
        'min_stock',
        'unit',
        'image',
        'is_active',
    ];

    protected $casts = [
        'cost_price' => 'float',
        'selling_price' => 'float',
        'stock' => 'integer',
        'min_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'image_url',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function getImageUrlAttribute(): string
    {
        if (!empty($this->image)) {
            if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                return $this->image;
            }
            if (file_exists(public_path('storage/' . $this->image))) {
                return asset('storage/' . $this->image);
            }
            if (file_exists(public_path($this->image))) {
                return asset($this->image);
            }
        }
        return asset('images/default-product.svg');
    }

    public function getMarginAttribute(): float
    {
        return $this->selling_price - $this->cost_price;
    }
}
