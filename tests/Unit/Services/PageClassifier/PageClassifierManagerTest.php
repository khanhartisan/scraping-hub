<?php

namespace Tests\Unit\Services\PageClassifier;

use App\Contracts\OpenAI\OpenAIClient;
use App\Facades\PageClassifier;
use App\Services\PageClassifier\Drivers\OpenAIPageClassifierDriver;
use App\Services\PageClassifier\PageClassifierManager;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\TestCase;

class PageClassifierManagerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_returns_default_driver(): void
    {
        Config::set('pageclassifier.default', 'openai');

        // Mock OpenAI client for the driver
        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $this->app->instance(OpenAIClient::class, $mockOpenAIClient);

        $manager = PageClassifier::getFacadeRoot();

        $driver = $manager->driver();

        $this->assertInstanceOf(OpenAIPageClassifierDriver::class, $driver);
    }

    public function test_it_returns_specified_driver(): void
    {
        // Mock OpenAI client for the driver
        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $this->app->instance(OpenAIClient::class, $mockOpenAIClient);

        $manager = PageClassifier::getFacadeRoot();

        $driver = $manager->driver('openai');

        $this->assertInstanceOf(OpenAIPageClassifierDriver::class, $driver);
    }

    public function test_it_uses_config_for_driver_creation(): void
    {
        // Mock OpenAI client for the driver
        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $this->app->instance(OpenAIClient::class, $mockOpenAIClient);

        Config::set('pageclassifier.drivers.openai', [
            'model' => 'gpt-4o',
            'max_html_length' => 100000,
        ]);

        $manager = PageClassifier::getFacadeRoot();

        $driver = $manager->driver('openai');

        $this->assertInstanceOf(OpenAIPageClassifierDriver::class, $driver);
    }

    public function test_get_default_driver_returns_openai(): void
    {
        Config::set('pageclassifier.default', 'openai');

        $manager = PageClassifier::getFacadeRoot();

        $this->assertEquals('openai', $manager->getDefaultDriver());
    }

    public function test_get_default_driver_uses_config(): void
    {
        Config::set('pageclassifier.default', 'custom');

        $manager = PageClassifier::getFacadeRoot();

        $this->assertEquals('custom', $manager->getDefaultDriver());
    }

    public function test_facade_returns_pageclassifier_manager_instance(): void
    {
        $manager = PageClassifier::getFacadeRoot();

        $this->assertInstanceOf(PageClassifierManager::class, $manager);
    }
}
