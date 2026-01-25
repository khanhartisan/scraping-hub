<?php

namespace App\Models;

use App\Enums\EntityType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SourceEntityCount extends Model
{
    use HasUlids;

    protected $casts = [
        'entity_type' => EntityType::class,
        'count' => 'integer',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }
}
