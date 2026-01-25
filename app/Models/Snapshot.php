<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Snapshot extends Model
{
    use HasUlids;

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }
}
