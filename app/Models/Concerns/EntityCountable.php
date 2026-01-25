<?php

namespace App\Models\Concerns;

use App\Enums\EntityType;
use App\Models\EntityCount;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait EntityCountable
{
    public function entityCounts(): HasMany
    {
        return $this
            ->hasMany(EntityCount::class, 'countable_id')
            ->where('countable_type', $this->getMorphClass());
    }

    public function adjustEntityCount(EntityType $entityType, int $delta): bool
    {
        return !!$this
            ->entityCounts()
            ->where('entity_type', $entityType)
            ->increment('count', $delta);
    }
}