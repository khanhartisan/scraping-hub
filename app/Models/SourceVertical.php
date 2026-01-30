<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SourceVertical extends Pivot
{
    protected $casts = [
        'relevance' => 'float',
    ];
}
