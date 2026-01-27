<?php

namespace Tests\Unit\Services\Scraper;

use App\Facades\Scraper;
use App\Services\Scraper\Drivers\GuzzleDriver;
use App\Services\Scraper\ScraperManager;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\TestCase;

class ScraperManagerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_returns_default_driver(): void
    {
        Config::set('scraper.default', 'guzzle');

        $manager = Scraper::getFacadeRoot();

        $driver = $manager->driver();

        $this->assertInstanceOf(GuzzleDriver::class, $driver);
    }

    public function test_it_returns_specified_driver(): void
    {
        $manager = Scraper::getFacadeRoot();

        $driver = $manager->driver('guzzle');

        $this->assertInstanceOf(GuzzleDriver::class, $driver);
    }

    public function test_it_uses_config_for_driver_creation(): void
    {
        Config::set('scraper.drivers.guzzle', [
            'timeout' => 60,
            'headers' => [
                'User-Agent' => 'Test Agent',
            ],
        ]);

        $manager = Scraper::getFacadeRoot();

        $driver = $manager->driver('guzzle');

        $this->assertInstanceOf(GuzzleDriver::class, $driver);
    }

    public function test_get_default_driver_returns_guzzle(): void
    {
        Config::set('scraper.default', 'guzzle');

        $manager = Scraper::getFacadeRoot();

        $this->assertEquals('guzzle', $manager->getDefaultDriver());
    }

    public function test_get_default_driver_uses_config(): void
    {
        Config::set('scraper.default', 'custom');

        $manager = Scraper::getFacadeRoot();

        $this->assertEquals('custom', $manager->getDefaultDriver());
    }

    public function test_facade_returns_scraper_manager_instance(): void
    {
        $manager = Scraper::getFacadeRoot();

        $this->assertInstanceOf(ScraperManager::class, $manager);
    }
}
