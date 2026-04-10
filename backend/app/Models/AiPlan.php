<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'token_limit',
        'monthly_price',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
    ];
}
