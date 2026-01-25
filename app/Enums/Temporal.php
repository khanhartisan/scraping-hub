<?php

namespace App\Enums;

use App\Contracts\DescribableEnum;

enum Temporal: string implements DescribableEnum
{
    case EVERGREEN = 'evergreen';
    case BREAKING = 'breaking';
    case SEASONAL = 'seasonal';
    case TRENDING = 'trending';
    case TOPICAL = 'topical';

    public static function describe(DescribableEnum $enum): string
    {
        return match ($enum) {
            self::EVERGREEN => 'Evergreen content with long-term relevance.',
            self::BREAKING => 'Breaking or time-sensitive content.',
            self::SEASONAL => 'Seasonal content relevant at specific times of year.',
            self::TRENDING => 'Trending content driven by current interest.',
            self::TOPICAL => 'Topical content tied to a specific subject or moment.',
            default => 'Unknown temporal classification.',
        };
    }
}