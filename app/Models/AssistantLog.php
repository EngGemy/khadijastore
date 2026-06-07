<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssistantLog extends Model
{
    protected $fillable = [
        'brand_id', 'session_id', 'ip', 'query', 'reply', 'products', 'response_ms',
    ];

    protected function casts(): array
    {
        return ['products' => 'array'];
    }
}
