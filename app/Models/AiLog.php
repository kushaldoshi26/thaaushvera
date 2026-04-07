<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'prompt',
        'response',
        'mode',
        'input_tokens',
        'output_tokens',
        'estimated_cost',
        'image_path',
    ];
}
