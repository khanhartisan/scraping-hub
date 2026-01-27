<?php

namespace Tests\Unit\Services\PageParser;

use App\Contracts\OpenAI\OpenAIClient;
use App\Facades\PageParser;
use App\Services\PageParser\Drivers\OpenAIPageParserDriver;
use App\Services\PageParser\PageParserManager;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\TestCase;

class PageParserManagerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_returns_default_driver(): void
    {
        Config::set('pageparser.default', 'openai');

        // Mock OpenAI client for the driver
        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $this->app->instance(OpenAIClient::class, $mockOpenAIClient);

        $manager = PageParser::getFacadeRoot();

        $driver = $manager->driver();

        $this->assertInstanceOf(OpenAIPageParserDriver::class, $driver);
    }

    public function test_it_returns_specified_driver(): void
    {
        // Mock OpenAI client for the driver
        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $this->app->instance(OpenAIClient::class, $mockOpenAIClient);

        $manager = PageParser::getFacadeRoot();

        $driver = $manager->driver('openai');

        $this->assertInstanceOf(OpenAIPageParserDriver::class, $driver);
    }

    public function test_it_uses_config_for_driver_creation(): void
    {
        // Mock OpenAI client for the driver
        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $this->app->instance(OpenAIClient::class, $mockOpenAIClient);

        Config::set('pageparser.drivers.openai', [
            'model' => 'gpt-4o',
            'max_html_length' => 100000,
        ]);

        $manager = PageParser::getFacadeRoot();

        $driver = $manager->driver('openai');

        $this->assertInstanceOf(OpenAIPageParserDriver::class, $driver);
    }

    public function test_get_default_driver_returns_openai(): void
    {
        Config::set('pageparser.default', 'openai');

        $manager = PageParser::getFacadeRoot();

        $this->assertEquals('openai', $manager->getDefaultDriver());
    }

    public function test_get_default_driver_uses_config(): void
    {
        Config::set('pageparser.default', 'custom');

        $manager = PageParser::getFacadeRoot();

        $this->assertEquals('custom', $manager->getDefaultDriver());
    }

    public function test_facade_returns_pageparser_manager_instance(): void
    {
        $manager = PageParser::getFacadeRoot();

        $this->assertInstanceOf(PageParserManager::class, $manager);
    }
}
