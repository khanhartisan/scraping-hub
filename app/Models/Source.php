<?php

namespace App\Models;

use App\Models\Concerns\EntityCountable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model
{
    use EntityCountable;

    public function entities(): HasMany
    {
        return $this->hasMany(Entity::class);
    }
}
