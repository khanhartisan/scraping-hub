<?php

namespace App\Models;

use App\Enums\EntityType;
use App\Enums\ScrapingStatus;
use Illuminate\Database\Eloquent\Model;

class EntityCount extends Model
{
    protected $casts = [
        'entity_type' => EntityType::class,
        'scraping_status' => ScrapingStatus::class,
    ];
}
