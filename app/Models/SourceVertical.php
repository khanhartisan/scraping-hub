<?php

namespace App\Models;

class SourceVertical extends Pivot
{
    protected $casts = [
        'relevance' => 'float',
    ];
}
