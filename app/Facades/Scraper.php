<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Psr\Http\Message\ResponseInterface fetch(string $url, ?\App\Contracts\Scraper\ScrapingOptions $options = null)
 * @method static \App\Contracts\Scraper\Scraper driver(string|null $driver = null)
 *
 * @see \App\Services\Scraper\ScraperManager
 */
class Scraper extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\Scraper\ScraperManager::class;
    }
}
