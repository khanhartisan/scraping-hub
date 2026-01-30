<?php

namespace App\Models;

use App\Models\Concerns\EntityCountable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model
{
    use EntityCountable;

    protected $fillable = [
        'base_url',
        'authority_score',
        'priority',
    ];

    protected $casts = [
        'authority_score' => 'integer',
    ];

    public function entities(): HasMany
    {
        return $this->hasMany(Entity::class);
    }

    public function verticals(): BelongsToMany
    {
        return $this->belongsToMany(Vertical::class)
            ->using(SourceVertical::class)
            ->as('source_vertical');
    }
}
