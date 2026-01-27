<?php

namespace App\Services\FileVision;

use Illuminate\Support\Manager;

class FileVisionManager extends Manager
{
    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('filevision.default', 'basic');
    }

    /**
     * Create a basic driver instance.
     */
    protected function createBasicDriver(): Drivers\BasicFileVisionDriver
    {
        $config = $this->config->get('filevision.drivers.basic', []);

        return new Drivers\BasicFileVisionDriver($config);
    }

    /**
     * Create an OpenAI driver instance.
     */
    protected function createOpenaiDriver(): Drivers\OpenAIFileVisionDriver
    {
        $config = $this->config->get('filevision.drivers.openai', []);

        return new Drivers\OpenAIFileVisionDriver($config);
    }
}
