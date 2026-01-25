<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model
{
    public function entities(): HasMany
    {
        return $this->hasMany(Entity::class);
    }

    public function sourceEntityCounts(): HasMany
    {
        return $this->hasMany(SourceEntityCount::class);
    }
}
