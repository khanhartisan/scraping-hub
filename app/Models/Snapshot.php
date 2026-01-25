<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Snapshot extends Model
{
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }
}
