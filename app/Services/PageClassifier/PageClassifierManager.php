<?php

namespace App\Services\PageClassifier;

use Illuminate\Support\Manager;

class PageClassifierManager extends Manager
{
    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('pageclassifier.default', 'openai');
    }

    /**
     * Create an OpenAI driver instance.
     */
    protected function createOpenaiDriver(): Drivers\OpenAIPageClassifierDriver
    {
        $config = $this->config->get('pageclassifier.drivers.openai', []);

        return new Drivers\OpenAIPageClassifierDriver($config);
    }
}
