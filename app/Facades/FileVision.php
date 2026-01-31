<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Contracts\FileVision\FileInformation describe(string $filePath)
 * @method static \App\Contracts\FileVision\FileVision driver(string|null $driver = null)
 *
 * @see \App\Services\FileVision\FileVisionManager
 */
class FileVision extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'filevision.manager';
    }
}
