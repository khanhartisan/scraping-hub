<?php

namespace App\Enums\OpenAI;

use App\Contracts\DescribableEnum;

enum ResponseStatus: string implements DescribableEnum
{
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case IN_PROGRESS = 'in_progress';
    case CANCELLED = 'cancelled';
    case QUEUED = 'queued';
    case INCOMPLETE = 'incomplete';

    public static function describe(DescribableEnum $enum): string
    {
        return match ($enum) {
            self::COMPLETED => 'The response has been completed successfully.',
            self::FAILED => 'The response generation failed.',
            self::IN_PROGRESS => 'The response is currently being generated.',
            self::CANCELLED => 'The response was cancelled.',
            self::QUEUED => 'The response is queued for processing.',
            default => 'The response status is incomplete or unknown.',
        };
    }
}
