<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'duration_months',
        'description',
        'active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_months' => 'integer',
        'active' => 'boolean',
    ];
}
