<?php

namespace Tests\Feature\Services\Scraper;

use App\Contracts\Scraper\ScrapingOptions;
use App\Facades\Scraper;
use App\Services\Scraper\ScraperManager;
use Tests\TestCase;

class ScraperServiceTest extends TestCase
{
    public function test_facade_resolves_scraper_manager(): void
    {
        $manager = Scraper::getFacadeRoot();

        $this->assertInstanceOf(ScraperManager::class, $manager);
    }

    public function test_facade_returns_same_instance(): void
    {
        $manager1 = Scraper::getFacadeRoot();
        $manager2 = Scraper::getFacadeRoot();

        $this->assertSame($manager1, $manager2);
    }

    public function test_facade_can_call_driver_method(): void
    {
        $driver = Scraper::driver('guzzle');

        $this->assertNotNull($driver);
    }

    public function test_scraper_manager_can_switch_drivers(): void
    {
        $manager = Scraper::getFacadeRoot();

        $driver1 = $manager->driver('guzzle');
        $driver2 = $manager->driver('guzzle');

        // Should return the same driver instance
        $this->assertSame($driver1, $driver2);
    }

    public function test_scraping_options_can_be_created_and_configured(): void
    {
        $options = new ScrapingOptions();
        $options->setScrapingCountryCode('US');

        $this->assertEquals('US', $options->getScrapingCountryCode());
    }

    public function test_scraping_options_returns_null_when_not_set(): void
    {
        $options = new ScrapingOptions();

        $this->assertNull($options->getScrapingCountryCode());
    }

    public function test_scraping_options_is_fluent(): void
    {
        $options = (new ScrapingOptions())
            ->setScrapingCountryCode('FR');

        $this->assertInstanceOf(ScrapingOptions::class, $options);
        $this->assertEquals('FR', $options->getScrapingCountryCode());
    }
}
