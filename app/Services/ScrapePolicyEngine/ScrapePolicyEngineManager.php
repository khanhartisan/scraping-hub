<?php

namespace App\Services\ScrapePolicyEngine;

use App\Contracts\OpenAI\OpenAIClient;
use Illuminate\Support\Manager;

class ScrapePolicyEngineManager extends Manager
{
    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('scrapepolicyengine.default', 'dummy');
    }

    /**
     * Create a dummy driver instance.
     */
    protected function createDummyDriver(): Drivers\DummyScrapePolicyEngineDriver
    {
        $config = $this->config->get('scrapepolicyengine.drivers.dummy', []);

        return new Drivers\DummyScrapePolicyEngineDriver($config);
    }

    /**
     * Create an OpenAI driver instance.
     */
    protected function createOpenaiDriver(): Drivers\OpenAIScrapePolicyEngineDriver
    {
        $config = $this->config->get('scrapepolicyengine.drivers.openai', []);

        return new Drivers\OpenAIScrapePolicyEngineDriver($this->container->make(OpenAIClient::class), $config);
    }
}
