<?php

namespace App\Enums;

use App\Contracts\DescribableEnum;

enum PageType: string implements DescribableEnum
{
    case LISTING = 'listing';
    case DETAIL = 'detail';
    case REDIRECT = 'redirect';
    case UNKNOWN = 'unknown';

    public static function describe(DescribableEnum $enum): string
    {
        return match ($enum) {
            self::LISTING => 'Listing or index page (multiple items).',
            self::DETAIL => 'Detail page (single item).',
            self::REDIRECT => 'Redirect page (canonicalization or routing).',
            default => 'Unknown page type.',
        };
    }
}