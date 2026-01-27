<?php

namespace App\Services\OpenAI;

use Illuminate\Support\Manager;

class OpenAIManager extends Manager
{
    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('openai.default', 'openai');
    }

    /**
     * Create an OpenAI driver instance.
     */
    protected function createOpenaiDriver(): Drivers\OpenAIDriver
    {
        $config = $this->config->get('openai.drivers.openai', []);

        return new Drivers\OpenAIDriver($config);
    }

    /**
     * Create a Grok driver instance.
     */
    protected function createGrokDriver(): Drivers\GrokDriver
    {
        $config = $this->config->get('openai.drivers.grok', []);

        return new Drivers\GrokDriver($config);
    }
}
