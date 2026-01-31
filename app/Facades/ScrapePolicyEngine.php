<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Contracts\ScrapePolicyEngine\PolicyResult evaluate(\App\Models\Entity $entity, ?\Carbon\Carbon $baseTime = null)
 * @method static \App\Contracts\ScrapePolicyEngine\ScrapePolicyEngine driver(string|null $driver = null)
 *
 * @see \App\Services\ScrapePolicyEngine\ScrapePolicyEngineManager
 */
class ScrapePolicyEngine extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'scrape_policy_engine.manager';
    }
}
