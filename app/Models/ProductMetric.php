<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMetric extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'views',
        'sales',
        'stock',
        'profit_margin',
        'current_price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
