<?php

namespace App\Models\Concerns;

use App\Enums\EntityType;
use App\Enums\ScrapingStatus;
use App\Models\EntityCount;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

trait EntityCountable
{
    public function entityCounts(): HasMany
    {
        return $this
            ->hasMany(EntityCount::class, 'countable_id')
            ->where('countable_type', $this->getMorphClass());
    }

    public function adjustEntityCount(EntityType $entityType, ScrapingStatus $scrapingStatus, int $delta): bool
    {
        /** @var EntityCount $record */
        $record = $this->entityCounts()
            ->where('entity_type', $entityType)
            ->where('scraping_status', $scrapingStatus)
            ->first();

        if ($record) {
            return $record->increment('count', $delta) !== false;
        }

        return $this->entityCounts()->create([
            'id' => (string) Str::ulid(),
            'countable_type' => $this->getMorphClass(),
            'entity_type' => $entityType,
            'scraping_status' => $scrapingStatus,
            'count' => max(0, $delta),
        ]) !== null;
    }
}