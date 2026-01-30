<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ScrapingStatus: int implements HasLabel
{
    case PENDING = 0;
    case QUEUED = 1;
    case FETCHING = 2;
    case SUCCESS = 3;
    case FAILED = 4;
    case TIMEOUT = 5;
    case BLOCKED = 6;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::QUEUED => 'Queued',
            self::FETCHING => 'Fetching',
            self::SUCCESS => 'Success',
            self::FAILED => 'Failed',
            self::TIMEOUT => 'Timeout',
            self::BLOCKED => 'Blocked',
        };
    }
}