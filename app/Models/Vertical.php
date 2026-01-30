<?php

namespace App\Models;

use App\Models\Concerns\EntityCountable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Vertical extends Model
{
    use EntityCountable;

    protected $fillable = [
        'name',
        'description',
    ];

    public function entities(): BelongsToMany
    {
        return $this->belongsToMany(Entity::class)
            ->using(EntityVertical::class)
            ->as('entity_vertical');
    }

    public function sources(): BelongsToMany
    {
        return $this->belongsToMany(Source::class)
            ->using(SourceVertical::class)
            ->as('source_vertical');
    }
}
