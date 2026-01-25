<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntityRelation extends Model
{
    use HasUlids;

    public function sourceEntity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'source_entity_id');
    }

    public function relatedEntity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'related_entity_id');
    }
}
