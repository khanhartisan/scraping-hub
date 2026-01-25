<?php

namespace App\Models;

use App\Enums\EntityType;
use App\Enums\ScrapingStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Entity extends Model
{
    use HasUlids;

    protected $casts = [
        'type' => EntityType::class,
        'scraping_status' => ScrapingStatus::class,
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function snapshots(): HasMany
    {
        return $this->hasMany(Snapshot::class);
    }

    public function currentSnapshot(): HasOne
    {
        return $this->hasOne(Snapshot::class)->orderByDesc('version');
    }

    public function relatedEntities(): BelongsToMany
    {
        return $this->belongsToMany(Entity::class,
            'entity_relations',
            'source_entity_id',
            'related_entity_id'
        );
    }

    public function relatedByEntities(): BelongsToMany
    {
        return $this->belongsToMany(Entity::class,
            'entity_relations',
            'related_entity_id',
            'source_entity_id'
        );
    }
}
