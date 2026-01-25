<?php

namespace App\Models;

use App\Models\Concerns\EntityCountable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Vertical extends Model
{
    use EntityCountable;

    public function entities(): BelongsToMany
    {
        return $this->belongsToMany(Entity::class)
            ->using(EntityVertical::class)
            ->as('entity_vertical');
    }
}
