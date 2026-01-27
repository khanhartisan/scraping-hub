<?php

namespace App\Services\PageParser;

use Illuminate\Support\Manager;

class PageParserManager extends Manager
{
    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('pageparser.default', 'openai');
    }

    /**
     * Create an OpenAI driver instance.
     */
    protected function createOpenaiDriver(): Drivers\OpenAIPageParserDriver
    {
        $config = $this->config->get('pageparser.drivers.openai', []);

        return new Drivers\OpenAIPageParserDriver($config);
    }
}
