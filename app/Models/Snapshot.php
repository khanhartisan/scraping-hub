<?php

namespace App\Models;

use App\Enums\ScrapingStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Snapshot extends Model
{
    protected $casts = [
        'scraping_status' => ScrapingStatus::class,
        'content_change_percentage' => 'float',
        'cost' => 'float',
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }
}
