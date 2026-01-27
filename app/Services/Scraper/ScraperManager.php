<?php

namespace App\Services\Scraper;

use Illuminate\Support\Manager;

class ScraperManager extends Manager
{
    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('scraper.default', 'guzzle');
    }

    /**
     * Create a Guzzle driver instance.
     */
    protected function createGuzzleDriver(): Drivers\GuzzleDriver
    {
        $config = $this->config->get('scraper.drivers.guzzle', []);

        return new Drivers\GuzzleDriver($config);
    }
}
