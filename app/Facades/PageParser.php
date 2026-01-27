<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Contracts\PageParser\PageData parse(string $html)
 * @method static \App\Contracts\PageParser\Parser driver(string|null $driver = null)
 *
 * @see \App\Services\PageParser\PageParserManager
 */
class PageParser extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return \App\Contracts\PageParser\Parser::class;
    }
}
